import 'package:flutter/material.dart';
import 'package:just_audio/just_audio.dart';
import 'package:flutter_sound/flutter_sound.dart';
import 'package:permission_handler/permission_handler.dart';
import 'package:path_provider/path_provider.dart';
import 'package:http/http.dart' as http;
import 'dart:async';
import 'dart:convert';
import 'dart:ui'; // Import for ImageFilter.blur
import 'dart:ui' as ui; // Import for image decoding
import 'dart:typed_data'; // Import for image decoding
import 'config.dart'; // Ensure you have your AppConfig
import 'equalizer_page.dart';

// --- UI Constants ---
const Color kHighlightColor = Color(0xFFFF2688);
const Color kInactiveColor = Colors.white60;
const Color kBackgroundColor = Color(0xFF120C18); // Dark purple background

// --- UPDATED CustomSliderThumbShape ---
class CustomSliderThumbShape extends SliderComponentShape {
  final double radius;
  final ui.Image? image;

  const CustomSliderThumbShape({
    this.radius = 12.0,
    this.image,
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

    final Paint circlePaint = Paint()
      ..color = sliderTheme.thumbColor ?? Colors.pink
      ..style = PaintingStyle.fill;

    canvas.drawCircle(center, radius, circlePaint);

    if (image != null) {
      final Path clipPath = Path()
        ..addOval(Rect.fromCircle(center: center, radius: radius));
      canvas.save();
      canvas.clipPath(clipPath);
      final Rect destRect = Rect.fromCircle(center: center, radius: radius);
      paintImage(
        canvas: canvas,
        rect: destRect,
        image: image!,
        fit: BoxFit.cover,
        alignment: Alignment.center,
      );
      canvas.restore();
    } else {
      TextPainter textPainter = TextPainter(textDirection: textDirection);
      textPainter.text = TextSpan(
        text: String.fromCharCode(Icons.person.codePoint),
        style: TextStyle(
          fontSize: radius * 1.4,
          fontFamily: Icons.person.fontFamily,
          color: Colors.white,
        ),
      );
      textPainter.layout();
      Offset iconOffset = Offset(
        center.dx - (textPainter.width / 2),
        center.dy - (textPainter.height / 2),
      );
      textPainter.paint(canvas, iconOffset);
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

class _RecordingPageState extends State<RecordingPage> {
  // --- STATE VARIABLES ---
  final AudioPlayer _instrumentalPlayer = AudioPlayer();
  final FlutterSoundRecorder _audioRecorder = FlutterSoundRecorder();
  final ScrollController _scrollController = ScrollController();

  StreamSubscription<Duration>? _positionSub;

  bool _isLoading = true;
  bool _isInitialized = false;
  String? _vocalFilePath;

  // This is now only used for playback on this page
  String _instrumentalUrlForPlayback = '';
  List<Map<String, dynamic>> _lyricsLines = [];
  String _title = 'Loading...';
  String _artist = '';
  String _posterUrl = '';

  String _singerPhotoUrl = '';
  ui.Image? _singerThumbImage;

  // --- UI STATE ---
  bool _isPlaying = false;
  bool _isRecording = false;

  Duration _duration = Duration.zero;
  Duration _position = Duration.zero;
  int _currentLyricIndex = -1;

  @override
  void initState() {
    super.initState();
    _fetchSongDetailsAndInitialize();
  }

  Future<void> _fetchSongDetailsAndInitialize() async {
    try {
      await _audioRecorder.openRecorder();
      final res = await http.get(Uri.parse("${AppConfig.baseUrl}getSongDetails.php?sid=${widget.sid}"));
      if (res.statusCode == 200) {
        final data = jsonDecode(res.body);

        // Only get the URL needed for this page
        _instrumentalUrlForPlayback = data["instrumental"] ?? "";
        _title = data["name"] ?? "Unknown";
        _artist = data["singer"] ?? "Unknown";
        _posterUrl = data["image"] ?? "";

        String rawPhotos = data["singer_photo"] ?? "";
        if (rawPhotos.isNotEmpty) {
          _singerPhotoUrl = rawPhotos.split(',').first.trim();
        }

        String rawLyrics = (data["lyrics"] as String? ?? "").replaceAll('\r', '');
        _parseLyrics(rawLyrics);
        if (_instrumentalUrlForPlayback.isEmpty) {
          throw Exception("Instrumental track not found for this song.");
        }
        await _initializeAudio(); // This uses _instrumentalUrlForPlayback

        if (_singerPhotoUrl.isNotEmpty) {
          _singerThumbImage = await _loadUiImage(_singerPhotoUrl);
        }
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
      debugPrint("Failed to load and decode singer thumb image: $e");
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
      // Use the playback URL
      await _instrumentalPlayer.setUrl(_instrumentalUrlForPlayback);
      _instrumentalPlayer.durationStream.listen((d) {
        if (d != null && mounted) setState(() => _duration = d);
      });
      _positionSub = _instrumentalPlayer.positionStream.listen(_updatePlaybackPosition);
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
      if (!(await _audioRecorder.isEncoderSupported(Codec.aacMP4))) {
        _showErrorDialog("Audio codec not supported.");
        return;
      }
      final dir = await getTemporaryDirectory();
      final path = '${dir.path}/temp_rec_${DateTime.now().millisecondsSinceEpoch}.mp4';

      if (!mounted) return;
      setState(() {
        _isRecording = true;
        _isPlaying = true;
      });

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
        await _instrumentalPlayer.pause();
        await _audioRecorder.pauseRecorder();
      } else {
        await _audioRecorder.resumeRecorder();
        await Future.delayed(const Duration(milliseconds: 250));
        if (!mounted) return;
        await _instrumentalPlayer.play();
      }
    } catch (e) {
      debugPrint("Error in _onPlayPausePressed: $e");
      _showErrorDialog("Error playing/pausing audio.");
      if (mounted) {
        setState(() => _isPlaying = wasPlaying);
      }
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
    } catch (e) {
      debugPrint("Error in _onReRecordPressed: $e");
      _showErrorDialog("Error restarting recording.");
    }
  }

  // --- 3. _onFinishPressed IS UPDATED ---
  Future<void> _onFinishPressed() async {
    if (!_isRecording) return;
    String? vocalPath;

    try {
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
        debugPrint('Recording complete, navigating to equalizer...');

        // --- THIS IS THE NAVIGATION CHANGE ---
        Navigator.of(context).push(
          MaterialPageRoute(
            builder: (context) => EqualizerPage(
              sid: widget.sid, // <-- PASS THE SONG ID
              vocalFilePath: _vocalFilePath!, // Pass the user's recording
              title: _title,
              artist: _artist,
              // instrumentalUrl and singerVocalUrl are now fetched inside EqualizerPage
            ),
          ),
        );
        // -------------------------------------

      } else if (vocalPath == null) {
        _showErrorDialog("Recording file was not created.");
      }
    } catch (e) {
      debugPrint("Error in _onFinishPressed: $e");
      _showErrorDialog("Error finishing recording.");
    }
  }
  // ---------------------------------------------------

  void _onLikePressed() {
    debugPrint("Like button pressed!");
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

  // --- BUILD METHOD ---
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: kBackgroundColor,
      extendBodyBehindAppBar: true,
      extendBody: true,
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        centerTitle: true,
        title: Text(
          _isLoading ? "Loading..." : _title,
          style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
        ),
        leading: IconButton(
          icon: const Icon(Icons.close),
          iconSize: 28.0,
          onPressed: () => Navigator.of(context).pop(),
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.favorite, color: kHighlightColor),
            iconSize: 28.0,
            onPressed: _onLikePressed,
          ),
        ],
      ),
      body: Stack(
        children: [
          if (!_isLoading && _posterUrl.isNotEmpty)
            Image.network(
              _posterUrl,
              width: double.infinity,
              height: double.infinity,
              fit: BoxFit.cover,
              loadingBuilder: (context, child, loadingProgress) {
                if (loadingProgress == null) return child;
                return Container(color: kBackgroundColor);
              },
              errorBuilder: (context, error, stackTrace) {
                debugPrint("Error loading background image: $error");
                return Container(color: kBackgroundColor);
              },
            ),
          Positioned.fill(
            child: BackdropFilter(
              filter: ImageFilter.blur(sigmaX: 12.0, sigmaY: 12.0),
              child: Container(
                color: Colors.transparent,
              ),
            ),
          ),
          Positioned.fill(
            child: Container(
              color: kBackgroundColor.withAlpha((255 * 0.75).round()),
            ),
          ),
          SafeArea(
            child: _isLoading
                ? const Center(child: CircularProgressIndicator(color: kHighlightColor))
                : !_isInitialized
                ? const Center(child: Text("Could not initialize audio.", style: TextStyle(color: Colors.white)))
                : Column(
              children: [
                _buildProgressBar(),
                _buildLyricsList(),
              ],
            ),
          ),
        ],
      ),
      bottomNavigationBar: _buildBottomControlBar(),
    );
  }

  // --- BUILD HELPER WIDGETS ---
  Widget _buildProgressBar() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 20.0, vertical: 10.0),
      child: Column(
        children: [
          SliderTheme(
            data: SliderTheme.of(context).copyWith(
              trackHeight: 4,
              thumbShape: CustomSliderThumbShape(
                image: _singerThumbImage,
                radius: 12.0,
              ),
              overlayShape: const RoundSliderOverlayShape(overlayRadius: 0),
              disabledActiveTrackColor: kHighlightColor,
              disabledInactiveTrackColor: Colors.white30,
              disabledThumbColor: kHighlightColor,
            ),
            child: Slider(
              min: 0.0,
              max: _duration.inMilliseconds.toDouble(),
              value: _position.inMilliseconds.toDouble().clamp(0.0, _duration.inMilliseconds.toDouble()),
              onChanged: null,
            ),
          ),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 12.0),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(_formatDuration(_position), style: const TextStyle(color: Colors.white70, fontSize: 12)),
                Text(_formatDuration(_duration), style: const TextStyle(color: Colors.white70, fontSize: 12)),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildLyricsList() {
    return Expanded(
      child: ListView.builder(
        controller: _scrollController,
        itemCount: _lyricsLines.length,
        padding: EdgeInsets.only(
          top: MediaQuery.of(context).size.height / 12,
          bottom: MediaQuery.of(context).size.height / 8,
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
              style: TextStyle(
                color: isActive ? kHighlightColor : kInactiveColor,
                fontSize: isActive ? 28 : 24,
                fontWeight: isActive ? FontWeight.bold : FontWeight.w600,
                height: 1.4,
                shadows: isActive
                    ? [const Shadow(blurRadius: 10.0, color: kHighlightColor)]
                    : [],
              ),
            ),
          );
        },
      ),
    );
  }

  Widget _buildBottomControlBar() {
    return BottomAppBar(
      color: Colors.transparent,
      elevation: 0,
      height: 90,
      child: Container(
        decoration: BoxDecoration(
          border: Border(
            top: BorderSide(
              color: Colors.white.withAlpha((250 * 0.2).round()),
              width: 1.0,
            ),
          ),
        ),
        child: Padding(
          padding: const EdgeInsets.only(top: .0),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceAround,
            children: [
              _buildControlIcon(
                Icons.refresh,
                'Re-record',
                _isRecording ? _onReRecordPressed : null,
                disabled: !_isRecording,
              ),
              _isRecording ? _buildPlayPauseButton() : _buildStartButton(),
              _buildControlIcon(
                Icons.check_circle,
                'Finish',
                _isRecording ? _onFinishPressed : null,
                disabled: !_isRecording,
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildControlIcon(IconData icon, String label, VoidCallback? onPressed, {bool disabled = false}) {
    final color = disabled ? Colors.grey.withAlpha((255 * 0.5).round()) : Colors.white;
    return InkWell(
      onTap: onPressed,
      borderRadius: BorderRadius.circular(30),
      child: Padding(
        padding: const EdgeInsets.all(8.0),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(icon, color: color, size: 28),
            const SizedBox(height: 4),
            Text(label, style: TextStyle(color: color, fontSize: 12)),
          ],
        ),
      ),
    );
  }

  Widget _buildStartButton() {
    return ElevatedButton.icon(
      onPressed: _onStartPressed,
      icon: const Icon(Icons.mic, color: Colors.white),
      label: const Text(
        'Tap to start',
        style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
      ),
      style: ElevatedButton.styleFrom(
        backgroundColor: kHighlightColor,
        foregroundColor: Colors.white,
        shape: const StadiumBorder(),
        padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
      ),
    );
  }

  Widget _buildPlayPauseButton() {
    return InkWell(
      onTap: _onPlayPausePressed,
      borderRadius: BorderRadius.circular(30),
      child: Container(
        padding: const EdgeInsets.all(12),
        decoration: BoxDecoration(
          color: kHighlightColor,
          shape: BoxShape.circle,
        ),
        child: Icon(
          _isPlaying ? Icons.pause : Icons.play_arrow,
          color: Colors.white,
          size: 40,
        ),
      ),
    );
  }

  String _formatDuration(Duration d) {
    final minutes = d.inMinutes.remainder(60).toString().padLeft(2, '0');
    final seconds = d.inSeconds.remainder(60).toString().padLeft(2, '0');
    return "$minutes:$seconds";
  }
}