import 'package:flutter/material.dart';
import 'package:just_audio/just_audio.dart';
import 'dart:async';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'config.dart';

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
  final AudioPlayer _vocalPlayer = AudioPlayer();
  final AudioPlayer _instrumentalPlayer = AudioPlayer();
  final AudioPlayer _singerVocalPlayer = AudioPlayer();

  bool _isInitialized = false;
  bool _isPlaying = false;
  double _vocalVolume = 1.0;
  double _instrumentalVolume = 0;
  double _singerVocalVolume = 0.7;
  Duration _duration = Duration.zero;
  Duration _position = Duration.zero;

  // --- 1. Define the DEFAULT offset ---
  final Duration _defaultVocalOffset = const Duration(milliseconds: 1200);
  // ------------------------------------

  // --- 2. Slider controls the ADJUSTMENT relative to the default ---
  // Value from -1.0 (-1000ms adjustment) to +1.0 (+1000ms adjustment).
  double _latencyAdjustment = 0.0;
  // -------------------------------------------------------------

  List<StreamSubscription> _subscriptions = [];

  String _instrumentalUrl = '';
  String _singerVocalUrl = '';

  @override
  void initState() {
    super.initState();
    _initializePlayers();
  }

  Future<void> _initializePlayers() async {
    try {
      final res = await http.get(Uri.parse("${AppConfig.baseUrl}getSongDetails.php?sid=${widget.sid}"));
      if (res.statusCode != 200) {
        throw Exception("Failed to fetch song details for mixing.");
      }

      final data = jsonDecode(res.body);
      _instrumentalUrl = data["instrumental"] ?? "";
      _singerVocalUrl = data["vocal"] ?? "";

      if (_instrumentalUrl.isEmpty || _singerVocalUrl.isEmpty) {
        throw Exception("Instrumental or vocal track missing from API response.");
      }

      await _vocalPlayer.setFilePath(widget.vocalFilePath);
      await _instrumentalPlayer.setUrl(_instrumentalUrl);
      await _singerVocalPlayer.setUrl(_singerVocalUrl);

      // --- 3. Seek vocal player to default offset initially ---
      await _vocalPlayer.seek(_defaultVocalOffset);
      // ----------------------------------------------------

      _vocalPlayer.setVolume(_vocalVolume);
      _instrumentalPlayer.setVolume(_instrumentalVolume);
      _singerVocalPlayer.setVolume(_singerVocalVolume);

      _subscriptions.add(_instrumentalPlayer.playerStateStream.listen((state) {
        if (state.processingState == ProcessingState.completed) {
          // --- 4. Reset vocal player seek to default offset ---
          _vocalPlayer.seek(_defaultVocalOffset);
          // --------------------------------------------------
          _instrumentalPlayer.seek(Duration.zero);
          _singerVocalPlayer.seek(Duration.zero);
          if (mounted) {
            setState(() {
              _position = Duration.zero;
              _isPlaying = false;
            });
          }
        }
      }));

      _subscriptions.add(_instrumentalPlayer.durationStream.listen((d) {
        if (d != null && mounted) setState(() => _duration = d);
      }));
      _subscriptions.add(_instrumentalPlayer.positionStream.listen((p) {
        if (mounted) setState(() => _position = p);
      }));

      if (mounted) {
        setState(() => _isInitialized = true);
      }
    } catch (e) {
      debugPrint("Error initializing players: $e");
      if (mounted) {
        showDialog(
          context: context,
          barrierDismissible: false,
          builder: (context) => AlertDialog(
            title: const Text('Error'),
            content: Text("Could not load audio for mixing. Please try again."),
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

  // --- 5. UPDATED to apply BOTH default and slider adjustment ---
  Future<void> _togglePlayPause() async {
    if (!_isInitialized) return;

    setState(() => _isPlaying = !_isPlaying);

    if (_isPlaying) {
      final basePosition = _position;
      // Calculate adjustment in milliseconds from the slider value
      final int adjustmentMilliseconds = (_latencyAdjustment * 1000).round();
      final Duration sliderAdjustment = Duration(milliseconds: adjustmentMilliseconds);

      // Apply BOTH offsets: default + slider adjustment
      final Duration totalVocalOffset = _defaultVocalOffset + sliderAdjustment;
      final vocalPosition = basePosition + totalVocalOffset;

      // Ensure vocalPosition doesn't go below zero
      final clampedVocalPosition = vocalPosition.isNegative ? Duration.zero : vocalPosition;

      await Future.wait([
        _vocalPlayer.seek(clampedVocalPosition), // Use total offset
        _instrumentalPlayer.seek(basePosition),
        _singerVocalPlayer.seek(basePosition),
      ]);
      await Future.wait([
        _vocalPlayer.play(),
        _instrumentalPlayer.play(),
        _singerVocalPlayer.play(),
      ]);
    } else {
      await Future.wait([
        _vocalPlayer.pause(),
        _instrumentalPlayer.pause(),
        _singerVocalPlayer.pause(),
      ]);
    }
  }
  // -----------------------------------------------------------

  // --- 6. UPDATED reset to use default offset ---
  Future<void> _resetPlayback() async {
    if (!_isInitialized) return;

    try {
      await Future.wait([
        _vocalPlayer.pause(),
        _instrumentalPlayer.pause(),
        _singerVocalPlayer.pause(),
      ]);

      // Seek players to their respective starting points (vocal has offset)
      await Future.wait([
        _vocalPlayer.seek(_defaultVocalOffset), // Reset vocal to default offset
        _instrumentalPlayer.seek(Duration.zero),
        _singerVocalPlayer.seek(Duration.zero),
      ]);

      if (mounted) {
        setState(() {
          _position = Duration.zero; // UI position resets to 0
          _isPlaying = false;
        });
      }
    } catch (e) {
      debugPrint("Error resetting playback: $e");
    }
  }
  // -------------------------------------------

  @override
  void dispose() {
    for (var sub in _subscriptions) {
      sub.cancel();
    }
    _vocalPlayer.dispose();
    _instrumentalPlayer.dispose();
    _singerVocalPlayer.dispose();
    super.dispose();
  }

  String _formatDuration(Duration d) {
    final minutes = d.inMinutes.remainder(60).toString().padLeft(2, '0');
    final seconds = d.inSeconds.remainder(60).toString().padLeft(2, '0');
    return "$minutes:$seconds";
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF1C1C1E),
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        title: const Text('Mix Your Track'),
      ),
      body: !_isInitialized
          ? const Center(child: CircularProgressIndicator(color: Color(0xFFFF2688)))
          : Padding(
        padding: const EdgeInsets.all(24.0),
        child: SingleChildScrollView( // Added ScrollView for smaller screens
          child: ConstrainedBox( // Ensures content takes at least screen height
            constraints: BoxConstraints(minHeight: MediaQuery.of(context).size.height - kToolbarHeight - MediaQuery.of(context).padding.top),
            child: IntrinsicHeight( // Helps Column take available space
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const Spacer(flex: 1), // Add some space at the top
                  Text(widget.title, style: const TextStyle(color: Colors.white, fontSize: 28, fontWeight: FontWeight.bold), textAlign: TextAlign.center),
                  const SizedBox(height: 8),
                  Text(widget.artist, style: const TextStyle(color: Colors.white70, fontSize: 16)),
                  const Spacer(flex: 2), // More space before sliders
                  _buildVolumeSlider(
                    label: 'Your Vocals',
                    icon: Icons.mic,
                    value: _vocalVolume,
                    onChanged: (val) {
                      setState(() => _vocalVolume = val);
                      _vocalPlayer.setVolume(val);
                    },
                  ),
                  const SizedBox(height: 20),
                  _buildVolumeSlider(
                    label: 'Original Singer',
                    icon: Icons.person,
                    value: _singerVocalVolume,
                    onChanged: (val) {
                      setState(() => _singerVocalVolume = val);
                      _singerVocalPlayer.setVolume(val);
                    },
                  ),
                  const SizedBox(height: 20),
                  _buildVolumeSlider(
                    label: 'Instrumental',
                    icon: Icons.music_note,
                    value: _instrumentalVolume,
                    onChanged: (val) {
                      setState(() => _instrumentalVolume = val);
                      _instrumentalPlayer.setVolume(val);
                    },
                  ),
                  const SizedBox(height: 20),
                  _buildLatencySlider(),
                  const Spacer(flex: 2), // More space before progress bar
                  Slider(
                    min: 0.0,
                    max: _duration.inMilliseconds.toDouble(),
                    value: _position.inMilliseconds.toDouble().clamp(0.0, _duration.inMilliseconds.toDouble()),
                    onChanged: (value) {
                      setState(() {
                        _position = Duration(milliseconds: value.round());
                      });
                    },
                    // --- 7. UPDATED onChangeEnd to use BOTH offsets ---
                    onChangeEnd: (value) {
                      final newPos = Duration(milliseconds: value.round());
                      // Calculate adjustment
                      final int adjustmentMilliseconds = (_latencyAdjustment * 1000).round();
                      final Duration sliderAdjustment = Duration(milliseconds: adjustmentMilliseconds);
                      // Apply total offset
                      final Duration totalVocalOffset = _defaultVocalOffset + sliderAdjustment;
                      final vocalPosition = newPos + totalVocalOffset;
                      final clampedVocalPosition = vocalPosition.isNegative ? Duration.zero : vocalPosition;

                      _vocalPlayer.seek(clampedVocalPosition);
                      _instrumentalPlayer.seek(newPos);
                      _singerVocalPlayer.seek(newPos);
                    },
                    // -----------------------------------------------
                    activeColor: const Color(0xFFFF2688),
                  ),
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 16.0),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(_formatDuration(_position), style: const TextStyle(color: Colors.white70)),
                        Text(_formatDuration(_duration), style: const TextStyle(color: Colors.white70)),
                      ],
                    ),
                  ),
                  const SizedBox(height: 20),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      IconButton(
                        icon: Icon(Icons.replay, color: Colors.white70, size: 36),
                        onPressed: _resetPlayback,
                      ),
                      const SizedBox(width: 32),
                      IconButton(
                        icon: Icon(_isPlaying ? Icons.pause_circle_filled : Icons.play_circle_filled, color: Colors.white, size: 72),
                        onPressed: _togglePlayPause,
                      ),
                      const SizedBox(width: 32),
                      const SizedBox(width: 36),
                    ],
                  ),
                  const Spacer(flex: 1), // Space at the bottom
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
        Text(label, style: const TextStyle(color: Colors.white, fontSize: 18)),
        const SizedBox(height: 10),
        Row(
          children: [
            Icon(icon, color: Colors.white70),
            Expanded(
              child: Slider(
                value: value,
                onChanged: onChanged,
                min: 0.0,
                max: 1.0,
                activeColor: const Color(0xFFFF2688),
                inactiveColor: Colors.white30,
              ),
            ),
            Text('${(value * 100).toInt()}%', style: const TextStyle(color: Colors.white70)),
          ],
        ),
      ],
    );
  }

  // --- 8. UPDATED Latency Slider's onChanged logic ---
  Widget _buildLatencySlider() {
    // Show the ADJUSTMENT value
    final int adjustmentMilliseconds = (_latencyAdjustment * 1000).round();

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text("Vocal Adjustment ($adjustmentMilliseconds ms)", // Label shows adjustment
            style: const TextStyle(color: Colors.white, fontSize: 18)),
        const SizedBox(height: 10),
        Row(
          children: [
            Text("Late", style: const TextStyle(color: Colors.white70)),
            Expanded(
              child: Slider(
                value: _latencyAdjustment,
                onChanged: (newValue) {
                  setState(() {
                    _latencyAdjustment = newValue;
                  });

                  // If playing, apply TOTAL offset in real-time
                  if (_isPlaying) {
                    final basePosition = _position;
                    // Calculate total offset
                    final int adjustMs = (_latencyAdjustment * 1000).round();
                    final Duration sliderAdjust = Duration(milliseconds: adjustMs);
                    final Duration totalVocalOffset = _defaultVocalOffset + sliderAdjust;
                    final vocalPosition = basePosition + totalVocalOffset;
                    final clampedVocalPosition = vocalPosition.isNegative ? Duration.zero : vocalPosition;

                    _vocalPlayer.seek(clampedVocalPosition);
                  }
                },
                min: -1.0, // -1000ms adjustment
                max: 1.0,  // +1000ms adjustment
                divisions: 20,
                activeColor: const Color(0xFFFF2688),
                inactiveColor: Colors.white30,
              ),
            ),
            Text("Early", style: const TextStyle(color: Colors.white70)),
          ],
        ),
      ],
    );
  }
// ------------------------------------------------
}