import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:just_audio/just_audio.dart' as ja;
import 'package:flutter_sound/flutter_sound.dart';
import 'package:permission_handler/permission_handler.dart';
import 'package:path_provider/path_provider.dart';
import 'package:http/http.dart' as http;
import 'dart:async';
import 'dart:convert';
import 'dart:math' as math;
import 'dart:ui';
import 'dart:ui' as ui;
import 'dart:typed_data';
import 'package:shared_preferences/shared_preferences.dart';
import 'config.dart';
import 'equalizer_page.dart';

const Color kHighlightColor = Color(0xFFFF2688);
const Color kInactiveColor = Colors.white60;

class CustomSliderThumbShape extends SliderComponentShape {
  final double radius;
  final ui.Image? image;
  final double rotationAngle;

  const CustomSliderThumbShape({
    this.radius = 15.0,
    this.image,
    this.rotationAngle = 0.0,
  });

  @override
  Size getPreferredSize(bool isEnabled, bool isDiscrete) {
    return Size.fromRadius(radius);
  }

  @override
  void paint(
      PaintingContext context,
      Offset center, {
        required Animation<double> activationAnimation,
        required Animation<double> enableAnimation,
        required bool isDiscrete,
        required TextPainter labelPainter,
        required RenderBox parentBox,
        required SliderThemeData sliderTheme,
        required TextDirection textDirection,
        required double value,
        required double textScaleFactor,
        required Size sizeWithOverflow,
      }) {
    final Canvas canvas = context.canvas;

    if (image != null) {
      canvas.save();
      canvas.translate(center.dx, center.dy);
      canvas.rotate(rotationAngle);

      final Path clipPath = Path()
        ..addOval(Rect.fromCircle(center: Offset.zero, radius: radius));
      canvas.clipPath(clipPath);

      final Rect destRect = Rect.fromCircle(center: Offset.zero, radius: radius);
      paintImage(
        canvas: canvas,
        rect: destRect,
        image: image!,
        fit: BoxFit.cover,
        alignment: Alignment.center,
      );
      canvas.restore();
    } else {
      final Paint fallbackPaint = Paint()
        ..color = Colors.black
        ..style = PaintingStyle.fill;
      canvas.drawCircle(center, radius, fallbackPaint);
    }
  }
}

class RecordingPage extends StatefulWidget {
  final String sid;

  const RecordingPage({
    Key? key,
    required this.sid,
  }) : super(key: key);

  @override
  State<RecordingPage> createState() => _RecordingPageState();
}

class _RecordingPageState extends State<RecordingPage> with TickerProviderStateMixin {
  final ja.AudioPlayer _instrumentalPlayer = ja.AudioPlayer();
  final FlutterSoundRecorder _audioRecorder = FlutterSoundRecorder();
  final ScrollController _scrollController = ScrollController();

  StreamSubscription<Duration>? _positionSub;

  bool _isLoading = true;
  bool _isInitialized = false;
  String? _vocalFilePath;

  String _instrumentalUrlForPlayback = '';
  List<Map<String, dynamic>> _lyricsLines = [];
  String _title = 'Loading...';
  String _artist = '';

  String _posterUrl = '';
  ui.Image? _posterThumbImage;

  bool _isPlaying = false;
  bool _isRecording = false;

  Duration _duration = Duration.zero;
  Duration _position = Duration.zero;
  int _currentLyricIndex = -1;

  bool _isFavourite = false;
  String? _currentUserId;
  late AnimationController _heartBeatController;
  late Animation<double> _heartBeatAnimation;

  late AnimationController _rotationController;

  @override
  void initState() {
    super.initState();

    _rotationController = AnimationController(
      vsync: this,
      duration: const Duration(seconds: 10),
    );

    _heartBeatController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 3000),
    );

    _heartBeatAnimation = TweenSequence(
      <TweenSequenceItem<double>>[
        TweenSequenceItem<double>(
          tween: Tween<double>(begin: 1.0, end: 1.15).chain(CurveTween(curve: Curves.easeInOutSine)),
          weight: 12.0,
        ),
        TweenSequenceItem<double>(
          tween: Tween<double>(begin: 1.15, end: 1.0).chain(CurveTween(curve: Curves.easeInOutSine)),
          weight: 12.0,
        ),
        TweenSequenceItem<double>(
          tween: Tween<double>(begin: 1.0, end: 1.1).chain(CurveTween(curve: Curves.easeInOutSine)),
          weight: 12.0,
        ),
        TweenSequenceItem<double>(
          tween: Tween<double>(begin: 1.1, end: 1.0).chain(CurveTween(curve: Curves.easeInOutSine)),
          weight: 12.0,
        ),
        TweenSequenceItem<double>(
          tween: ConstantTween<double>(1.0),
          weight: 52.0,
        ),
      ],
    ).animate(_heartBeatController);

    _fetchSongDetailsAndInitialize();
  }

  Future<void> _fetchSongDetailsAndInitialize() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      _currentUserId = prefs.getString('user_id');

      await _audioRecorder.openRecorder();

      final res = await http.get(Uri.parse("${AppConfig.baseUrl}getSongDetails.php?sid=${widget.sid}"));
      if (res.statusCode == 200) {
        final data = jsonDecode(res.body);

        _instrumentalUrlForPlayback = data["instrumental"] ?? "";
        _title = data["name"] ?? "Unknown";

        if (data['singers'] is List) {
          _artist = (data['singers'] as List).map((s) => s['name'] ?? '').join(', ');
        } else {
          _artist = data["singer"] ?? data["singers"] ?? "Unknown";
        }

        _posterUrl = data["image"] ?? data["poster"] ?? "";

        String rawLyrics = (data["lyrics"] as String? ?? "").replaceAll('\r', '');
        _parseLyrics(rawLyrics);

        if (_instrumentalUrlForPlayback.isEmpty) {
          throw Exception("Instrumental track not found for this song.");
        }
        await _initializeAudio();

        if (_posterUrl.isNotEmpty) {
          _posterThumbImage = await _loadUiImage(_posterUrl);
        }

        await _checkFavouriteStatus();

      } else {
        throw Exception("Failed to fetch song details.");
      }
    } catch (e) {
      debugPrint("Error during setup: $e");
      if (mounted) {
        _showErrorDialog("Could not prepare the song. Please try again.", popOnClose: true);
      }
    }
    if (mounted) {
      setState(() => _isLoading = false);
    }
  }

  Future<void> _checkFavouriteStatus() async {
    if (_currentUserId == null) return;

    try {
      final res = await http.get(Uri.parse(
          "${AppConfig.baseUrl}checkFavourite.php?user_id=$_currentUserId&song_id=${widget.sid}"));

      if (res.statusCode == 200) {
        final data = jsonDecode(res.body);
        if (mounted) {
          setState(() {
            _isFavourite = data['is_favourite'] ?? false;
            _updateHeartbeat();
          });
        }
      }
    } catch (e) {
      debugPrint("Error checking favourite: $e");
    }
  }

  void _updateHeartbeat() {
    if (_isFavourite) {
      _heartBeatController.repeat();
    } else {
      _heartBeatController.stop();
      _heartBeatController.value = 0.0;
    }
  }

  Future<void> _toggleFavourite() async {
    if (_currentUserId == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please log in to favourite songs.')),
      );
      return;
    }

    final bool newFavouriteState = !_isFavourite;
    setState(() {
      _isFavourite = newFavouriteState;
      _updateHeartbeat();
    });

    try {
      final response = await http.post(
        Uri.parse("${AppConfig.baseUrl}favourit.php"),
        body: {
          'user_id': _currentUserId,
          'song_id': widget.sid,
        },
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['status'] != 'success') {
          if (mounted) setState(() {
            _isFavourite = !newFavouriteState;
            _updateHeartbeat();
          });
        }
      }
    } catch (e) {}
  }

  Future<ui.Image?> _loadUiImage(String url) async {
    try {
      final response = await http.get(Uri.parse(url));
      if (response.statusCode == 200) {
        final Uint8List bytes = response.bodyBytes;
        final codec = await ui.instantiateImageCodec(bytes);
        final frame = await codec.getNextFrame();
        return frame.image;
      }
    } catch (e) {
      debugPrint("Failed to load image: $e");
    }
    return null;
  }

  void _parseLyrics(String rawLyrics) {
    final RegExp lyricRegex = RegExp(r"\[(\d{2}):(\d{2})(?:\.(\d{2,3}))?\](.*)");
    _lyricsLines = rawLyrics.split("\n").map((line) {
      final match = lyricRegex.firstMatch(line.trim());
      if (match != null) {
        final min = int.parse(match.group(1)!);
        final sec = int.parse(match.group(2)!);
        final msString = match.group(3);
        final ms = msString != null ? int.parse(msString.padRight(3, '0').substring(0, 3)) : 0;
        final text = match.group(4)!.trim();
        if (text.isNotEmpty) return {"time": Duration(minutes: min, seconds: sec, milliseconds: ms), "text": text};
      }
      return null;
    }).whereType<Map<String, dynamic>>().toList();
  }

  Future<void> _initializeAudio() async {
    final hasPermission = await _checkAndRequestPermissions();
    if (!mounted) return;
    if (hasPermission) {
      await _instrumentalPlayer.setUrl(_instrumentalUrlForPlayback);

      _instrumentalPlayer.durationStream.listen((d) {
        if (d != null && mounted) setState(() => _duration = d);
      });

      _positionSub = _instrumentalPlayer.positionStream.listen(_updatePlaybackPosition);

      // AUTO REDIRECT TRIGGER
      _instrumentalPlayer.playerStateStream.listen((state) {
        if (state.processingState == ja.ProcessingState.completed) {
          if (_isRecording && mounted) {
            _onFinishPressed(); // Safely triggers redirect
          } else if (mounted) {
            setState(() => _isPlaying = false);
            _instrumentalPlayer.seek(Duration.zero);
            _rotationController.stop();
          }
        }
      });

      setState(() => _isInitialized = true);
    } else {
      throw Exception("Microphone permission is required.");
    }
  }

  Future<bool> _checkAndRequestPermissions() async {
    var status = await Permission.microphone.status;
    if (!status.isGranted) {
      status = await Permission.microphone.request();
    }
    return status.isGranted;
  }

  @override
  void dispose() {
    _positionSub?.cancel();
    _instrumentalPlayer.dispose();
    _audioRecorder.closeRecorder();
    _scrollController.dispose();
    _heartBeatController.dispose();
    _rotationController.dispose();
    super.dispose();
  }

  void _updatePlaybackPosition(Duration position) {
    if (!mounted) return;
    setState(() => _position = position);
    int newIndex = -1;
    for (int i = 0; i < _lyricsLines.length; i++) {
      if (position >= _lyricsLines[i]["time"]) {
        newIndex = i;
      } else {
        break;
      }
    }
    if (newIndex != _currentLyricIndex && mounted) {
      setState(() => _currentLyricIndex = newIndex);
      _scrollToActiveLyric();
    }
  }

  void _scrollToActiveLyric() {
    if (_currentLyricIndex < 0 || !_scrollController.hasClients) return;
    const itemExtent = 60.0;
    final screenHeight = MediaQuery.of(context).size.height;
    final targetOffset = (_currentLyricIndex * itemExtent) - (screenHeight / 3);
    _scrollController.animateTo(
      targetOffset.clamp(0.0, _scrollController.position.maxScrollExtent),
      duration: const Duration(milliseconds: 400),
      curve: Curves.easeInOut,
    );
  }

  Future<void> _onStartPressed() async {
    if (_isRecording || !_isInitialized) return;
    try {
      final dir = await getTemporaryDirectory();
      // AAC MP4 is standard and robust for Just_Audio on all phones
      final path = '${dir.path}/temp_rec_${DateTime.now().millisecondsSinceEpoch}.mp4';

      if (!mounted) return;
      setState(() {
        _isRecording = true;
        _isPlaying = true;
      });

      _rotationController.repeat();

      await _audioRecorder.startRecorder(toFile: path, codec: Codec.aacMP4);
      await _instrumentalPlayer.play();
    } catch (e) {
      debugPrint("Error in _onStartPressed: $e");
      _showErrorDialog("Could not start recording. Please try again.");
      if (mounted) {
        setState(() {
          _isRecording = false;
          _isPlaying = false;
        });
      }
    }
  }

  Future<void> _onPlayPausePressed() async {
    final bool wasPlaying = _isPlaying;
    try {
      if (mounted) {
        setState(() => _isPlaying = !wasPlaying);
      }

      if (wasPlaying) {
        _rotationController.stop();
        await _instrumentalPlayer.pause();
        await _audioRecorder.pauseRecorder();
      } else {
        _rotationController.repeat();
        await _audioRecorder.resumeRecorder();
        await Future.delayed(const Duration(milliseconds: 250));
        if (!mounted) return;
        await _instrumentalPlayer.play();
      }
    } catch (e) {
      debugPrint("Error in _onPlayPausePressed: $e");
      if (mounted) setState(() => _isPlaying = wasPlaying);
    }
  }

  Future<void> _onReRecordPressed() async {
    try {
      if (_isPlaying) {
        await _instrumentalPlayer.pause();
      }
      if (_audioRecorder.isRecording || _audioRecorder.isPaused) {
        await _audioRecorder.stopRecorder();
      }
      await _instrumentalPlayer.seek(Duration.zero);

      if (mounted) {
        setState(() {
          _isRecording = false;
          _isPlaying = false;
          _position = Duration.zero;
          _currentLyricIndex = -1;
        });
      }
      _rotationController.stop();
      _rotationController.reset();
    } catch (e) {
      debugPrint("Error in _onReRecordPressed: $e");
    }
  }

  Future<void> _onFinishPressed() async {
    if (!_isRecording) return;
    String? vocalPath;

    try {
      _rotationController.stop();
      await _instrumentalPlayer.stop();
      if (_audioRecorder.isRecording || _audioRecorder.isPaused) {
        vocalPath = await _audioRecorder.stopRecorder();
      }

      if (mounted) {
        setState(() {
          _isRecording = false;
          _isPlaying = false;
          _currentLyricIndex = -1;
        });
      }

      await _instrumentalPlayer.seek(Duration.zero);

      if (vocalPath != null && mounted) {
        _vocalFilePath = vocalPath;
        Navigator.of(context).pushReplacement(
          MaterialPageRoute(
            builder: (context) => EqualizerPage(
              sid: widget.sid,
              vocalFilePath: _vocalFilePath!,
              title: _title,
              artist: _artist,
            ),
          ),
        );
      } else if (vocalPath == null) {
        _showErrorDialog("Recording file was not created.");
      }
    } catch (e) {
      debugPrint("Error in _onFinishPressed: $e");
    }
  }

  void _showErrorDialog(String message, {bool popOnClose = false}) {
    if (!mounted) return;
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Error'),
        content: Text(message),
        actions: [
          TextButton(
            onPressed: () {
              Navigator.of(context).pop();
              if (popOnClose) Navigator.of(context).pop();
            },
            child: const Text('OK'),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.black,
      extendBodyBehindAppBar: false,
      extendBody: true,
      appBar: AppBar(
        backgroundColor: Colors.black,
        scrolledUnderElevation: 0,
        elevation: 0,
        centerTitle: true,
        title: _isLoading
            ? Text("Loading...", style: GoogleFonts.cabinSketch(color: Colors.white, fontSize: 24))
            : Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            RichText(
              text: TextSpan(
                children: [
                  if (_title.isNotEmpty) ...[
                    TextSpan(
                      text: _title[0],
                      style: GoogleFonts.cabinSketch(color: Colors.pinkAccent, fontSize: 24, fontWeight: FontWeight.bold),
                    ),
                    if (_title.length > 1)
                      TextSpan(
                        text: _title.substring(1),
                        style: GoogleFonts.cabinSketch(color: Colors.white, fontSize: 24, fontWeight: FontWeight.bold),
                      ),
                  ]
                ],
              ),
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
            ),
            Text(
              _artist,
              style: GoogleFonts.cabinSketch(color: Colors.pinkAccent, fontSize: 14, fontWeight: FontWeight.bold),
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
            ),
          ],
        ),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded, color: Colors.white),
          iconSize: 28.0,
          onPressed: () => Navigator.of(context).pop(),
        ),
        actions: [
          GestureDetector(
            onTap: _toggleFavourite,
            child: Container(
              width: 48,
              height: 48,
              alignment: Alignment.center,
              padding: const EdgeInsets.all(1.0),
              child: _isFavourite
                  ? AnimatedBuilder(
                animation: _heartBeatAnimation,
                builder: (context, child) {
                  return Transform.scale(
                    scale: _heartBeatAnimation.value,
                    child: Container(
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        boxShadow: [
                          BoxShadow(
                            color: Colors.pinkAccent.withOpacity(0.5),
                            blurRadius: 10 * _heartBeatAnimation.value,
                            spreadRadius: 0.2 * _heartBeatAnimation.value,
                          ),
                        ],
                      ),
                      child: const Text("❤️", style: TextStyle(fontSize: 20)),
                    ),
                  );
                },
              )
                  : const Icon(Icons.favorite_border_rounded, color: Colors.redAccent, size: 28),
            ),
          ),
          const SizedBox(width: 8),
        ],
      ),
      body: SafeArea(
        child: _isLoading
            ? const Center(child: CircularProgressIndicator(color: kHighlightColor))
            : !_isInitialized
            ? const Center(child: Text("Could not initialize audio.", style: TextStyle(color: Colors.white)))
            : Column(
          children: [
            const SizedBox(height: 10),
            _buildProgressBar(),
            _buildLyricsList(),
          ],
        ),
      ),
      bottomNavigationBar: _buildBottomControlBar(),
    );
  }

  Widget _buildProgressBar() {
    final sliderValue = _position.inMilliseconds.toDouble().clamp(0.0, _duration.inMilliseconds.toDouble());

    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 20.0, vertical: 10.0),
      child: Column(
        children: [
          AnimatedBuilder(
            animation: _rotationController,
            builder: (context, child) {
              return SliderTheme(
                data: SliderTheme.of(context).copyWith(
                  trackHeight: 4,
                  trackShape: const GradientRectSliderTrackShape(
                    gradient: LinearGradient(colors: [Colors.pinkAccent, Colors.blueAccent]),
                  ),
                  thumbShape: CustomSliderThumbShape(
                    image: _posterThumbImage,
                    radius: 15.0,
                    rotationAngle: _rotationController.value * 2 * math.pi,
                  ),
                  overlayShape: const RoundSliderOverlayShape(overlayRadius: 0),
                  inactiveTrackColor: Colors.white24,
                ),
                child: Slider(
                  min: 0.0,
                  max: _duration.inMilliseconds.toDouble(),
                  value: _duration.inMilliseconds > 0 ? sliderValue : 0.0,
                  onChanged: null,
                ),
              );
            },
          ),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 8.0, vertical: 4.0),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(_formatDuration(_position), style: GoogleFonts.cabinSketch(color: Colors.white70, fontSize: 14)),
                Text(_formatDuration(_duration), style: GoogleFonts.cabinSketch(color: Colors.white70, fontSize: 14)),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildLyricsList() {
    return Expanded(
      child: ShaderMask(
        shaderCallback: (Rect rect) {
          return const LinearGradient(
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
            colors: [Colors.transparent, Colors.black, Colors.black, Colors.transparent],
            stops: [0.0, 0.15, 0.75, 1.0],
          ).createShader(rect);
        },
        blendMode: BlendMode.dstIn,
        child: ListView.builder(
          controller: _scrollController,
          itemCount: _lyricsLines.length,
          padding: EdgeInsets.only(
            top: 20.0,
            bottom: MediaQuery.of(context).size.height / 6,
            left: 24.0,
            right: 24.0,
          ),
          itemBuilder: (context, index) {
            final isActive = index == _currentLyricIndex;
            final line = _lyricsLines[index];
            return Padding(
              padding: const EdgeInsets.symmetric(vertical: 12.0),
              child: Text(
                line["text"] as String,
                textAlign: TextAlign.center,
                style: GoogleFonts.cabinSketch(
                  color: isActive ? kHighlightColor : kInactiveColor,
                  fontSize: isActive ? 30 : 24,
                  fontWeight: isActive ? FontWeight.bold : FontWeight.w600,
                  height: 1.2,
                  shadows: isActive ? [const Shadow(blurRadius: 10.0, color: kHighlightColor)] : [],
                ),
              ),
            );
          },
        ),
      ),
    );
  }

  Widget _buildBottomControlBar() {
    return BottomAppBar(
      color: Colors.transparent,
      elevation: 0,
      height: 100,
      child: Padding(
        padding: const EdgeInsets.only(bottom: 10.0),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceEvenly,
          children: [
            _buildControlIcon(Icons.refresh_rounded, _isRecording ? _onReRecordPressed : null, disabled: !_isRecording),
            _isRecording ? _buildPlayPauseButton() : _buildStartButton(),
            _buildControlIcon(Icons.check_circle_outline_rounded, _isRecording ? _onFinishPressed : null, disabled: !_isRecording),
          ],
        ),
      ),
    );
  }

  Widget _buildControlIcon(IconData icon, VoidCallback? onPressed, {bool disabled = false}) {
    final color = disabled ? Colors.white24 : Colors.white;
    return IconButton(icon: Icon(icon, color: color, size: 38), onPressed: onPressed, padding: EdgeInsets.zero);
  }

  Widget _buildStartButton() {
    return GestureDetector(
      onTap: _onStartPressed,
      child: Container(
        width: 70,
        height: 70,
        decoration: BoxDecoration(
          shape: BoxShape.circle,
          gradient: LinearGradient(
            colors: [Colors.pinkAccent, Colors.blueAccent.withOpacity(0.8)],
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
          ),
          boxShadow: [
            BoxShadow(color: Colors.pinkAccent.withOpacity(0.5), blurRadius: 12, spreadRadius: 2, offset: const Offset(0, 4)),
            BoxShadow(color: Colors.blueAccent.withOpacity(0.4), blurRadius: 10, spreadRadius: 1, offset: const Offset(0, 2)),
          ],
        ),
        child: const Icon(Icons.mic_rounded, color: Colors.white, size: 40),
      ),
    );
  }

  Widget _buildPlayPauseButton() {
    return GestureDetector(
      onTap: _onPlayPausePressed,
      child: Container(
        width: 70,
        height: 70,
        decoration: BoxDecoration(
          shape: BoxShape.circle,
          gradient: LinearGradient(
            colors: [Colors.pinkAccent, Colors.blueAccent.withOpacity(0.8)],
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
          ),
          boxShadow: [
            BoxShadow(color: Colors.pinkAccent.withOpacity(0.5), blurRadius: 12, spreadRadius: 2, offset: const Offset(0, 4)),
            BoxShadow(color: Colors.blueAccent.withOpacity(0.4), blurRadius: 10, spreadRadius: 1, offset: const Offset(0, 2)),
          ],
        ),
        child: Icon(_isPlaying ? Icons.pause_rounded : Icons.play_arrow_rounded, color: Colors.white, size: 40),
      ),
    );
  }

  String _formatDuration(Duration d) {
    final minutes = d.inMinutes.remainder(60).toString().padLeft(2, '0');
    final seconds = d.inSeconds.remainder(60).toString().padLeft(2, '0');
    return "$minutes:$seconds";
  }
}

class GradientRectSliderTrackShape extends SliderTrackShape with BaseSliderTrackShape {
  final LinearGradient gradient;
  const GradientRectSliderTrackShape({this.gradient = const LinearGradient(colors: [Colors.pinkAccent, Colors.blueAccent])});

  @override
  void paint(
      PaintingContext context,
      Offset offset, {
        required RenderBox parentBox,
        required SliderThemeData sliderTheme,
        required Animation<double> enableAnimation,
        required TextDirection textDirection,
        required Offset thumbCenter,
        Offset? secondaryOffset,
        bool isDiscrete = false,
        bool isEnabled = false,
        double additionalActiveTrackHeight = 0,
      }) {
    if (sliderTheme.trackHeight == null || sliderTheme.trackHeight! <= 0) return;
    final Rect trackRect = getPreferredRect(parentBox: parentBox, offset: offset, sliderTheme: sliderTheme, isEnabled: isEnabled, isDiscrete: isDiscrete);
    final activeTrackRadius = Radius.circular(trackRect.height / 2);
    context.canvas.drawRRect(RRect.fromLTRBAndCorners(thumbCenter.dx, trackRect.top, trackRect.right, trackRect.bottom, topRight: activeTrackRadius, bottomRight: activeTrackRadius), Paint()..color = sliderTheme.inactiveTrackColor!);
    final activeRect = Rect.fromLTRB(trackRect.left, trackRect.top, thumbCenter.dx, trackRect.bottom);
    if (activeRect.width > 0) {
      context.canvas.drawRRect(RRect.fromLTRBAndCorners(activeRect.left, activeRect.top, activeRect.right, activeRect.bottom, topLeft: activeTrackRadius, bottomLeft: activeTrackRadius), Paint()..shader = gradient.createShader(activeRect));
    }
  }
}