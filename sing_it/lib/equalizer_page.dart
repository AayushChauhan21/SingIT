import 'package:flutter/material.dart';
import 'package:flutter_soloud/flutter_soloud.dart';
import 'package:http/http.dart' as http;
import 'dart:async';
import 'dart:convert';
import 'dart:io';
import 'package:path_provider/path_provider.dart';
import 'package:video_player/video_player.dart';
import 'config.dart';
import 'package:google_fonts/google_fonts.dart';

class EqualizerPage extends StatefulWidget {
  final String vocalFilePath;
  final String sid;
  final String title;
  final String artist;

  const EqualizerPage({
    Key? key,
    required this.vocalFilePath,
    required this.sid,
    required this.title,
    required this.artist,
  }) : super(key: key);

  @override
  State<EqualizerPage> createState() => _EqualizerPageState();
}

class _EqualizerPageState extends State<EqualizerPage> {
  // --- SoLoud Engine Components ---
  AudioSource? _instSource;
  AudioSource? _singerSource;
  AudioSource? _userVocalSource;

  SoundHandle? _instHandle;
  SoundHandle? _singerHandle;
  SoundHandle? _userVocalHandle;

  VideoPlayerController? _videoController;
  Timer? _uiUpdateTimer;

  // --- Loading Sync Flags ---
  bool _isLoading = true;
  bool _isVideoFinished = false;
  bool _isAudioReady = false;
  bool _isVideoReadyToDisplay = false;
  String _loadingMessage = "Initializing Engine...";

  // --- Playback State ---
  bool _isPlaying = false;
  Duration _duration = Duration.zero;
  Duration _currentPosition = Duration.zero;
  Duration? _dragPosition;

  // --- Controls State ---
  double _vocalVolume = 1.0;
  double _instrumentalVolume = 0.5;
  double _singerVocalVolume = 0.7;

  final Duration _defaultVocalOffset = const Duration(milliseconds: 1200);
  double _latencyAdjustment = 0.0;

  String? _localInstPath;
  String? _localSingerPath;

  @override
  void initState() {
    super.initState();

    // 1. Initialize Loading Video
    _videoController = VideoPlayerController.asset('assets/logo.mp4');
    _videoController!.initialize().then((_) async {
      await _videoController!.setVolume(0.0);
      await _videoController!.setLooping(false); // Stop at last frame
      await _videoController!.play();

      Future.delayed(const Duration(milliseconds: 150), () {
        if (mounted) setState(() => _isVideoReadyToDisplay = true);
      });
    });

    _videoController?.addListener(_videoListener);

    // 2. Start loading audio data
    _loadDataAndInitializeEngine();
  }

  void _videoListener() {
    final value = _videoController?.value;
    if (value != null && value.isInitialized) {
      if (value.position >= value.duration && value.duration > Duration.zero) {
        if (!_isVideoFinished) {
          _isVideoFinished = true;
          _checkLoadingStatus();
        }
      }
    }
  }

  void _checkLoadingStatus() {
    if (_isAudioReady && _isVideoFinished) {
      if (mounted) {
        setState(() {
          _isLoading = false;
        });
      }
    }
  }

  Future<String?> _downloadAndCacheFile(String url, String type) async {
    if (url.isEmpty) return null;
    try {
      final dir = await getTemporaryDirectory();
      final uniqueFilename = '${widget.sid}_$type.mp3';
      final file = File('${dir.path}/$uniqueFilename');

      if (await file.exists() && await file.length() > 0) return file.path;

      final response = await http.get(Uri.parse(url));
      if (response.statusCode == 200) {
        await file.writeAsBytes(response.bodyBytes);
        return file.path;
      }
    } catch (e) {
      debugPrint("Download error for $type: $e");
    }
    return null;
  }

  Future<void> _loadDataAndInitializeEngine() async {
    try {
      setState(() => _loadingMessage = "Fetching track details...");

      // 1. Fetch URLs
      final res = await http.get(Uri.parse("${AppConfig.baseUrl}getSongDetails.php?sid=${widget.sid}"));
      if (res.statusCode != 200) throw Exception("Failed to fetch song details.");

      final data = jsonDecode(res.body);
      final instrumentalUrl = data["instrumental"] ?? "";
      final singerVocalUrl = data["vocal"] ?? "";

      if (instrumentalUrl.isEmpty || singerVocalUrl.isEmpty) {
        throw Exception("Instrumental or vocal track missing.");
      }

      setState(() => _loadingMessage = "Downloading tracks for mixing...");

      // 2. Download to local cache (required for SoLoud 0ms latency sync)
      _localInstPath = await _downloadAndCacheFile(instrumentalUrl, 'inst');
      _localSingerPath = await _downloadAndCacheFile(singerVocalUrl, 'singer');

      if (_localInstPath == null || _localSingerPath == null) {
        throw Exception("Failed to download audio files.");
      }

      // 3. Initialize SoLoud Engine
      if (!SoLoud.instance.isInitialized) {
        await SoLoud.instance.init();
      }

      // 4. Load Sources into Memory
      _instSource = await SoLoud.instance.loadFile(_localInstPath!);
      _singerSource = await SoLoud.instance.loadFile(_localSingerPath!);
      _userVocalSource = await SoLoud.instance.loadFile(widget.vocalFilePath);

      // Get exact duration
      _duration = SoLoud.instance.getLength(_instSource!);

      // Start UI ticker to track position
      _startUiTicker();

      _isAudioReady = true;
      _checkLoadingStatus();

    } catch (e) {
      debugPrint("Error initializing players: $e");
      if (mounted) {
        showDialog(
          context: context,
          barrierDismissible: false,
          builder: (context) => AlertDialog(
            title: const Text('Error'),
            content: const Text("Could not load audio for mixing. Please try again."),
            actions: [
              TextButton(
                onPressed: () {
                  Navigator.of(context).pop();
                  Navigator.of(context).pop();
                },
                child: const Text('OK'),
              ),
            ],
          ),
        );
      }
    }
  }

  void _startUiTicker() {
    _uiUpdateTimer?.cancel();
    _uiUpdateTimer = Timer.periodic(const Duration(milliseconds: 50), (_) {
      if (_isPlaying && _instHandle != null) {
        bool isValid = SoLoud.instance.getIsValidVoiceHandle(_instHandle!);
        if (!isValid) {
          // Song ended
          _pausePlayback();
          if (mounted) {
            setState(() {
              _currentPosition = Duration.zero;
              _isPlaying = false;
            });
          }
          return;
        }

        // Pull position from C++ engine
        if (mounted) {
          setState(() {
            _currentPosition = SoLoud.instance.getPosition(_instHandle!);
          });
        }
      }
    });
  }

  // --- PLAYBACK & SYNC LOGIC ---
  Future<void> _startPlaybackAndSync() async {
    if (_instSource == null || _singerSource == null || _userVocalSource == null) return;

    // Create fresh handles if they don't exist or have finished
    if (_instHandle == null || !SoLoud.instance.getIsValidVoiceHandle(_instHandle!)) {
      _instHandle = await SoLoud.instance.play(_instSource!, paused: true, volume: _instrumentalVolume);
      _singerHandle = await SoLoud.instance.play(_singerSource!, paused: true, volume: _singerVocalVolume);
      _userVocalHandle = await SoLoud.instance.play(_userVocalSource!, paused: true, volume: _vocalVolume);
    }

    // Calculate total offset
    final int adjustmentMilliseconds = (_latencyAdjustment * 1000).round();
    final Duration sliderAdjustment = Duration(milliseconds: adjustmentMilliseconds);
    final Duration totalVocalOffset = _defaultVocalOffset + sliderAdjustment;

    final vocalPosition = _currentPosition + totalVocalOffset;
    final clampedVocalPosition = vocalPosition.isNegative ? Duration.zero : vocalPosition;

    // Seek all simultaneously
    SoLoud.instance.seek(_instHandle!, _currentPosition);
    SoLoud.instance.seek(_singerHandle!, _currentPosition);
    SoLoud.instance.seek(_userVocalHandle!, clampedVocalPosition);

    // Unpause all simultaneously
    SoLoud.instance.setPause(_instHandle!, false);
    SoLoud.instance.setPause(_singerHandle!, false);
    SoLoud.instance.setPause(_userVocalHandle!, false);

    if (mounted) setState(() => _isPlaying = true);
  }

  void _pausePlayback() {
    if (_instHandle != null) SoLoud.instance.setPause(_instHandle!, true);
    if (_singerHandle != null) SoLoud.instance.setPause(_singerHandle!, true);
    if (_userVocalHandle != null) SoLoud.instance.setPause(_userVocalHandle!, true);
    if (mounted) setState(() => _isPlaying = false);
  }

  Future<void> _togglePlayPause() async {
    if (_isPlaying) {
      _pausePlayback();
    } else {
      await _startPlaybackAndSync();
    }
  }

  void _seekTo(Duration newPos) {
    if (mounted) setState(() => _currentPosition = newPos);

    if (_isPlaying) {
      final int adjustmentMilliseconds = (_latencyAdjustment * 1000).round();
      final Duration sliderAdjustment = Duration(milliseconds: adjustmentMilliseconds);
      final Duration totalVocalOffset = _defaultVocalOffset + sliderAdjustment;
      final vocalPosition = newPos + totalVocalOffset;
      final clampedVocalPosition = vocalPosition.isNegative ? Duration.zero : vocalPosition;

      if (_instHandle != null) SoLoud.instance.seek(_instHandle!, newPos);
      if (_singerHandle != null) SoLoud.instance.seek(_singerHandle!, newPos);
      if (_userVocalHandle != null) SoLoud.instance.seek(_userVocalHandle!, clampedVocalPosition);
    }
  }

  Future<void> _resetPlayback() async {
    _pausePlayback();
    if (mounted) {
      setState(() {
        _currentPosition = Duration.zero;
        _isPlaying = false;
      });
    }
    if (_instHandle != null) SoLoud.instance.seek(_instHandle!, Duration.zero);
    if (_singerHandle != null) SoLoud.instance.seek(_singerHandle!, Duration.zero);
    if (_userVocalHandle != null) {
      final int adjustmentMilliseconds = (_latencyAdjustment * 1000).round();
      final Duration sliderAdjustment = Duration(milliseconds: adjustmentMilliseconds);
      final Duration totalVocalOffset = _defaultVocalOffset + sliderAdjustment;
      final clampedVocalPosition = totalVocalOffset.isNegative ? Duration.zero : totalVocalOffset;
      SoLoud.instance.seek(_userVocalHandle!, clampedVocalPosition);
    }
  }

  // --- VOLUME CONTROLS ---
  void _updateInstVolume(double v) {
    setState(() => _instrumentalVolume = v);
    if (_instHandle != null) SoLoud.instance.setVolume(_instHandle!, v);
  }

  void _updateSingerVolume(double v) {
    setState(() => _singerVocalVolume = v);
    if (_singerHandle != null) SoLoud.instance.setVolume(_singerHandle!, v);
  }

  void _updateUserVolume(double v) {
    setState(() => _vocalVolume = v);
    if (_userVocalHandle != null) SoLoud.instance.setVolume(_userVocalHandle!, v);
  }

  @override
  void dispose() {
    _uiUpdateTimer?.cancel();
    _videoController?.removeListener(_videoListener);
    _videoController?.dispose();

    if (_instHandle != null) SoLoud.instance.stop(_instHandle!);
    if (_singerHandle != null) SoLoud.instance.stop(_singerHandle!);
    if (_userVocalHandle != null) SoLoud.instance.stop(_userVocalHandle!);

    if (_instSource != null) SoLoud.instance.disposeSource(_instSource!);
    if (_singerSource != null) SoLoud.instance.disposeSource(_singerSource!);
    if (_userVocalSource != null) SoLoud.instance.disposeSource(_userVocalSource!);

    _cleanupLocalFiles();
    super.dispose();
  }

  Future<void> _cleanupLocalFiles() async {
    try {
      if (_localInstPath != null) {
        final file = File(_localInstPath!);
        if (await file.exists()) await file.delete();
      }
      if (_localSingerPath != null) {
        final file = File(_localSingerPath!);
        if (await file.exists()) await file.delete();
      }
    } catch (e) {
      debugPrint("Error cleaning up cache: $e");
    }
  }

  String _formatDuration(Duration d) {
    final minutes = d.inMinutes.remainder(60).toString().padLeft(2, '0');
    final seconds = d.inSeconds.remainder(60).toString().padLeft(2, '0');
    return "$minutes:$seconds";
  }

  @override
  Widget build(BuildContext context) {
    // --- VIDEO LOADING SCREEN ---
    if (_isLoading) {
      return Scaffold(
        backgroundColor: Colors.black,
        body: Center(
          child: AnimatedOpacity(
            opacity: _isVideoReadyToDisplay ? 1.0 : 0.0,
            duration: const Duration(milliseconds: 300),
            child: _videoController != null && _videoController!.value.isInitialized
                ? Container(
              width: MediaQuery.of(context).size.width * 0.8,
              decoration: BoxDecoration(
                color: Colors.black,
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(1.0),
                    blurRadius: 50.0,
                    spreadRadius: 20.0,
                  ),
                ],
              ),
              child: AspectRatio(
                aspectRatio: _videoController!.value.aspectRatio,
                child: VideoPlayer(_videoController!),
              ),
            )
                : const SizedBox.shrink(),
          ),
        ),
      );
    }
    // --- END LOADING SCREEN ---

    final currentPosition = _dragPosition ?? _currentPosition;
    final sliderValue = currentPosition.inMilliseconds.toDouble().clamp(0.0, _duration.inMilliseconds.toDouble());

    return Scaffold(
      backgroundColor: Colors.black, // Player2 Dark Theme
      appBar: AppBar(
        backgroundColor: Colors.black,
        elevation: 0,
        centerTitle: true,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded, color: Colors.white),
          iconSize: 28.0,
          onPressed: () => Navigator.of(context).pop(),
        ),
        title: RichText(
          text: TextSpan(
            children: [
              TextSpan(
                text: 'M',
                style: GoogleFonts.cabinSketch(color: Colors.pinkAccent, fontSize: 24, fontWeight: FontWeight.bold),
              ),
              TextSpan(
                text: 'ix Studio',
                style: GoogleFonts.cabinSketch(color: Colors.white, fontSize: 24, fontWeight: FontWeight.bold),
              ),
            ],
          ),
        ),
      ),
      body: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 24.0, vertical: 10.0),
        child: SingleChildScrollView(
          child: ConstrainedBox(
            constraints: BoxConstraints(minHeight: MediaQuery.of(context).size.height - kToolbarHeight - MediaQuery.of(context).padding.top),
            child: IntrinsicHeight(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const SizedBox(height: 20),

                  // Song Header
                  RichText(
                    text: TextSpan(
                      children: [
                        if (widget.title.isNotEmpty) ...[
                          TextSpan(
                            text: widget.title[0],
                            style: GoogleFonts.cabinSketch(color: Colors.pinkAccent, fontSize: 32, fontWeight: FontWeight.bold),
                          ),
                          if (widget.title.length > 1)
                            TextSpan(
                              text: widget.title.substring(1),
                              style: GoogleFonts.cabinSketch(color: Colors.white, fontSize: 32, fontWeight: FontWeight.bold),
                            ),
                        ] else ...[
                          TextSpan(text: 'Unknown Song', style: GoogleFonts.cabinSketch(color: Colors.white, fontSize: 32, fontWeight: FontWeight.bold)),
                        ]
                      ],
                    ),
                    textAlign: TextAlign.center,
                  ),
                  const SizedBox(height: 8),
                  Text(widget.artist, style: GoogleFonts.cabinSketch(color: Colors.pinkAccent, fontSize: 18, fontWeight: FontWeight.bold)),
                  const Spacer(flex: 2),

                  // Volume Sliders
                  _buildVolumeSlider(
                    label: 'Your Vocals',
                    icon: Icons.mic_rounded,
                    value: _vocalVolume,
                    onChanged: _updateUserVolume,
                  ),
                  const SizedBox(height: 25),
                  _buildVolumeSlider(
                    label: 'Original Singer',
                    icon: Icons.person_rounded,
                    value: _singerVocalVolume,
                    onChanged: _updateSingerVolume,
                  ),
                  const SizedBox(height: 25),
                  _buildVolumeSlider(
                    label: 'Instrumental',
                    icon: Icons.music_note_rounded,
                    value: _instrumentalVolume,
                    onChanged: _updateInstVolume,
                  ),
                  const SizedBox(height: 30),

                  // Latency Control
                  _buildLatencySlider(),

                  const Spacer(flex: 2),

                  // Progress Bar (Gradient from Player2 Theme)
                  SliderTheme(
                    data: SliderTheme.of(context).copyWith(
                      trackHeight: 4.0,
                      trackShape: GradientRectSliderTrackShape(
                        gradient: LinearGradient(
                          colors: [
                            Colors.pinkAccent,
                            Colors.blueAccent.withOpacity(0.8),
                          ],
                        ),
                      ),
                      thumbShape: const RoundSliderThumbShape(enabledThumbRadius: 6.0),
                      overlayShape: const RoundSliderOverlayShape(overlayRadius: 10.0),
                      inactiveTrackColor: Colors.white24,
                      thumbColor: Colors.pinkAccent,
                      overlayColor: Colors.blueAccent.withOpacity(0.3),
                    ),
                    child: Slider(
                      min: 0.0,
                      max: _duration.inMilliseconds.toDouble(),
                      value: _duration.inMilliseconds > 0 ? sliderValue : 0.0,
                      onChanged: (value) {
                        if (mounted) setState(() => _dragPosition = Duration(milliseconds: value.toInt()));
                      },
                      onChangeEnd: (value) {
                        final newPos = Duration(milliseconds: value.round());
                        if (mounted) _dragPosition = null;
                        _seekTo(newPos);
                      },
                    ),
                  ),
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 16.0),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(_formatDuration(currentPosition), style: GoogleFonts.cabinSketch(color: Colors.white70, fontSize: 14)),
                        Text(_formatDuration(_duration), style: GoogleFonts.cabinSketch(color: Colors.white70, fontSize: 14)),
                      ],
                    ),
                  ),
                  const SizedBox(height: 30),

                  // Bottom Controls
                  Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      IconButton(
                        icon: const Icon(Icons.replay_rounded, color: Colors.white, size: 36),
                        onPressed: _resetPlayback,
                      ),
                      const SizedBox(width: 40),
                      GestureDetector(
                        onTap: _togglePlayPause,
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
                      ),
                      const SizedBox(width: 40),
                      IconButton(
                        icon: const Icon(Icons.save_alt_rounded, color: Colors.white, size: 36),
                        onPressed: () {
                          ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text("Mix saved to device!")));
                        },
                      ),
                    ],
                  ),
                  const Spacer(flex: 1),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildVolumeSlider({
    required String label,
    required IconData icon,
    required double value,
    required ValueChanged<double> onChanged,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label, style: GoogleFonts.cabinSketch(color: Colors.white, fontSize: 20, fontWeight: FontWeight.bold)),
        const SizedBox(height: 4),
        Row(
          children: [
            Icon(icon, color: Colors.white70, size: 28),
            Expanded(
              child: SliderTheme(
                data: SliderTheme.of(context).copyWith(
                  trackHeight: 4,
                  activeTrackColor: Colors.pinkAccent,
                  inactiveTrackColor: Colors.white24,
                  thumbColor: Colors.white,
                ),
                child: Slider(
                  value: value,
                  onChanged: onChanged,
                  min: 0.0,
                  max: 1.0,
                ),
              ),
            ),
            SizedBox(
              width: 45,
              child: Text('${(value * 100).toInt()}%', style: GoogleFonts.cabinSketch(color: Colors.pinkAccent, fontSize: 16, fontWeight: FontWeight.bold), textAlign: TextAlign.right),
            ),
          ],
        ),
      ],
    );
  }

  Widget _buildLatencySlider() {
    final int adjustmentMilliseconds = (_latencyAdjustment * 1000).round();

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        RichText(
            text: TextSpan(
                style: GoogleFonts.cabinSketch(color: Colors.white, fontSize: 20, fontWeight: FontWeight.bold),
                children: [
                  const TextSpan(text: "Vocal Adjustment "),
                  TextSpan(text: "($adjustmentMilliseconds ms)", style: const TextStyle(color: Colors.pinkAccent)),
                ]
            )
        ),
        const SizedBox(height: 4),
        Row(
          children: [
            Text("Late", style: GoogleFonts.cabinSketch(color: Colors.white70, fontSize: 16)),
            Expanded(
              child: SliderTheme(
                data: SliderTheme.of(context).copyWith(
                  trackHeight: 4,
                  activeTrackColor: Colors.blueAccent,
                  inactiveTrackColor: Colors.white24,
                  thumbColor: Colors.white,
                ),
                child: Slider(
                  value: _latencyAdjustment,
                  onChanged: (newValue) {
                    setState(() {
                      _latencyAdjustment = newValue;
                    });

                    if (_isPlaying && _userVocalHandle != null) {
                      final int adjustMs = (_latencyAdjustment * 1000).round();
                      final Duration sliderAdjust = Duration(milliseconds: adjustMs);
                      final Duration totalVocalOffset = _defaultVocalOffset + sliderAdjust;
                      final vocalPosition = _currentPosition + totalVocalOffset;
                      final clampedVocalPosition = vocalPosition.isNegative ? Duration.zero : vocalPosition;

                      SoLoud.instance.seek(_userVocalHandle!, clampedVocalPosition);
                    }
                  },
                  min: -1.0,
                  max: 1.0,
                  divisions: 20,
                ),
              ),
            ),
            Text("Early", style: GoogleFonts.cabinSketch(color: Colors.white70, fontSize: 16)),
          ],
        ),
      ],
    );
  }
}

// ==========================================
// CUSTOM GRADIENT SLIDER TRACK SHAPE
// ==========================================
class GradientRectSliderTrackShape extends SliderTrackShape with BaseSliderTrackShape {
  final LinearGradient gradient;

  const GradientRectSliderTrackShape({
    this.gradient = const LinearGradient(
      colors: [Colors.pinkAccent, Colors.blueAccent],
    ),
  });

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

    final Rect trackRect = getPreferredRect(
      parentBox: parentBox,
      offset: offset,
      sliderTheme: sliderTheme,
      isEnabled: isEnabled,
      isDiscrete: isDiscrete,
    );

    final activeTrackRadius = Radius.circular(trackRect.height / 2);

    // Background (Inactive) Track
    context.canvas.drawRRect(
      RRect.fromLTRBAndCorners(
        thumbCenter.dx,
        trackRect.top,
        trackRect.right,
        trackRect.bottom,
        topRight: activeTrackRadius,
        bottomRight: activeTrackRadius,
      ),
      Paint()..color = sliderTheme.inactiveTrackColor!,
    );

    // Active Gradient Track
    final activeRect = Rect.fromLTRB(
      trackRect.left,
      trackRect.top,
      thumbCenter.dx,
      trackRect.bottom,
    );

    if (activeRect.width > 0) {
      context.canvas.drawRRect(
        RRect.fromLTRBAndCorners(
          activeRect.left,
          activeRect.top,
          activeRect.right,
          activeRect.bottom,
          topLeft: activeTrackRadius,
          bottomLeft: activeTrackRadius,
        ),
        Paint()..shader = gradient.createShader(activeRect),
      );
    }
  }
}