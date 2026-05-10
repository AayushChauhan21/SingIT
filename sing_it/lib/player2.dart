import 'package:flutter/material.dart';
import 'package:just_audio/just_audio.dart';
import 'package:http/http.dart' as http;
import 'dart:async';
import 'dart:convert';
import 'dart:ui'; // For BackdropFilter
import 'config.dart'; // Ensure this import is correct
import 'package:shared_preferences/shared_preferences.dart'; // To get user ID
// --- IMPORTS ADDED ---
import 'package:google_fonts/google_fonts.dart';
import 'login.dart';
// --- END IMPORTS ---

// --- 1. ENUM MODIFIED ---
/// Defines the repeat modes for the player.
enum RepeatMode {
  /// Repeats the entire list (plays next song).
  all,
  /// Repeats the current song.
  one
}
// --- END MODIFIED ---


class SongPlayerPage extends StatefulWidget {
  final List<Map<String, dynamic>> songList;
  final int initialIndex;

  const SongPlayerPage({
    Key? key,
    required this.songList,
    required this.initialIndex,
  }) : super(key: key);

  @override
  State<SongPlayerPage> createState() => _SongPlayerPageState();
}

class _SongPlayerPageState extends State<SongPlayerPage> {
  // --- STATE VARIABLES ---
  final AudioPlayer _vocalPlayer = AudioPlayer();
  final AudioPlayer _instrumentalPlayer = AudioPlayer();

  bool _isPlaying = false;
  bool _isLoading = true;

  Duration _duration = Duration.zero;
  Duration? _dragPosition;

  StreamSubscription<Duration>? _positionSub;
  StreamSubscription<Duration?>? _durationSub;
  StreamSubscription<PlayerState>? _playerStateSub; // For completion events
  Timer? _syncTimer;

  final Duration _vocalOffset = Duration.zero;

  // song details from API
  String title = "Loading...";
  String artist = "";
  String coverUrl = "";
  String audioUrl = "";
  String instrumentalUrl = "";

  List<Map<String, dynamic>> _lyricsLines = [];
  int _currentLyricIndex = -1;

  late int _currentIndex;

  // --- NEW ---
  // State variables for the favourite button
  bool _isFavourite = false;
  String? _currentUserId;
  String? _currentSongId;
  // --- END NEW ---

  // --- 2. REPEAT MODE STATE VARIABLE MODIFIED ---
  RepeatMode _repeatMode = RepeatMode.all; // Default to repeating the list
  // --- END MODIFIED ---

  // --- LIFECYCLE ---
  @override
  void initState() {
    super.initState();
    _currentIndex = widget.initialIndex;

    // --- MODIFIED ---
    // This function will now get the user ID, then load song data
    _loadUserAndSongData(autoPlay: true);
    // --- END MODIFIED ---
  }

  @override
  void dispose() {
    _positionSub?.cancel();
    _durationSub?.cancel();
    _playerStateSub?.cancel();
    _vocalPlayer.dispose();
    _instrumentalPlayer.dispose();
    _syncTimer?.cancel();
    super.dispose();
  }

  // --- MODIFIED HELPER FUNCTIONS ---

  /// Gets the user ID from SharedPreferences, then calls _loadSongData
  Future<void> _loadUserAndSongData({bool autoPlay = false}) async {
    final prefs = await SharedPreferences.getInstance();
    _currentUserId = prefs.getString('user_id');

    if (_currentUserId == null) {
      if (mounted) {
        // ScaffoldMessenger.of(context).showSnackBar(
        //   const SnackBar(content: Text('Please log in to use this feature.')),
        // );
      }
    }

    // Now load the song data (which will then trigger the favourite check)
    await _loadSongData(autoPlay: autoPlay);
  }

  /// Toggles the favourite status by calling favourit.php
  Future<void> _toggleFavourite() async {
    if (_currentUserId == null || _currentSongId == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Error: Not logged in or song not loaded.')),
      );
      return;
    }

    // 1. Optimistic UI update
    final bool newFavouriteState = !_isFavourite;
    setState(() {
      _isFavourite = newFavouriteState;
    });

    // 2. Call favourit.php
    try {
      final response = await http.post(
        Uri.parse("${AppConfig.baseUrl}favourit.php"),
        body: {
          'user_id': _currentUserId,
          'song_id': _currentSongId,
        },
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['status'] != 'success') {
          // 3. On error, revert the change
          setState(() {
            _isFavourite = !newFavouriteState; // Revert
          });
          if (mounted) {
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(content: Text('Error: ${data['message']}')),
            );
          }
        }
      } else {
        setState(() {
          _isFavourite = !newFavouriteState; // Revert
        });
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text('Error connecting to server.')),
          );
        }
      }
    } catch (e) {
      setState(() {
        _isFavourite = !newFavouriteState; // Revert
      });
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('An error occurred.')),
        );
      }
    }
  }

  // --- NEW ---
  /// Calls the new checkFavourite.php API
  Future<void> _checkFavouriteStatus() async {
    if (_currentUserId == null || _currentSongId == null) {
      setState(() => _isFavourite = false); // Not logged in, can't be favourite
      return;
    }

    try {
      final res = await http.get(Uri.parse(
          "${AppConfig.baseUrl}checkFavourite.php?user_id=$_currentUserId&song_id=$_currentSongId"));

      if (res.statusCode == 200) {
        final data = jsonDecode(res.body);
        if (mounted) {
          setState(() {
            _isFavourite = data['is_favourite'] ?? false;
          });
        }
      } else {
        if (mounted) setState(() => _isFavourite = false); // Default to false on error
      }
    } catch (e) {
      if (mounted) setState(() => _isFavourite = false); // Default to false on error
    }
  }
  // --- END NEW ---


  // --- 1. FUNCTION ADDED (to call history.php) ---
  Future<void> _addHistoryRecord() async {
    // Only save history if the user is logged in and song ID is valid
    if (_currentUserId == null || _currentSongId == null) {
      print("User or Song ID is null. Skipping history.");
      return;
    }

    try {
      // Build the URL with GET parameters, as your PHP script expects
      final url = Uri.parse(
          "${AppConfig.baseUrl}history.php?user_id=$_currentUserId&song_id=$_currentSongId");

      // Use http.get()
      final response = await http.get(url);

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        // Log the success message from PHP
        print('History record response: ${data['message']}');
      } else {
        // Log server errors
        print('Failed to save history. Status: ${response.statusCode}');
      }
    } catch (e) {
      // Log network or other errors
      print('Error saving history: $e');
    }
  }
  // --- END OF FUNCTION ---

  // --- 2. FUNCTION ADDED FOR PLAYLIST DIALOG ---
  void _showPlaylistDialog() {
    if (_currentUserId == null) {
      // --- User is NOT logged in ---
      showDialog(
        context: context,
        builder: (BuildContext context) {
          return Dialog(
            backgroundColor: Colors.transparent,
            elevation: 0,
            child: ClipRRect(
              borderRadius: BorderRadius.circular(16.0),
              child: BackdropFilter(
                filter: ImageFilter.blur(sigmaX: 5.0, sigmaY: 5.0),
                child: Container(
                  width: 300,
                  decoration: BoxDecoration(
                    color: Color(0xFF000000).withOpacity(0.3),
                    borderRadius: BorderRadius.circular(16.0),
                    border: Border.all(color: Colors.pinkAccent),
                  ),
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Padding(
                        padding: const EdgeInsets.fromLTRB(24.0, 24.0, 24.0, 20.0),
                        child: RichText(
                          text: TextSpan(
                            style: GoogleFonts.cabinSketch(
                              fontSize: 22,
                              fontWeight: FontWeight.bold,
                            ),
                            children: [
                              TextSpan(
                                text: 'Login',
                                style: TextStyle(color: Colors.pinkAccent),
                              ),
                              TextSpan(
                                text: ' Required',
                                style: TextStyle(color: Colors.white),
                              ),
                            ],
                          ),
                        ),
                      ),
                      Padding(
                        padding: const EdgeInsets.fromLTRB(24.0, 0, 24.0, 20.0),
                        child: Text(
                          'Please log in to manage your playlists.',
                          style: GoogleFonts.cabinSketch(
                            color: Colors.white70,
                            fontSize: 16,
                          ),
                        ),
                      ),
                      Padding(
                        padding: const EdgeInsets.all(8.0),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.end,
                          children: [
                            TextButton(
                              child: Text(
                                'Cancel',
                                style: GoogleFonts.cabinSketch(
                                  color: Colors.white70,
                                  fontWeight: FontWeight.bold,
                                  fontSize: 16,
                                ),
                              ),
                              onPressed: () => Navigator.of(context).pop(),
                            ),
                            TextButton(
                              child: Text(
                                'Login',
                                style: GoogleFonts.cabinSketch(
                                  color: Colors.pinkAccent,
                                  fontWeight: FontWeight.bold,
                                  fontSize: 16,
                                ),
                              ),
                              onPressed: () {
                                Navigator.of(context).pop();
                                Navigator.push(
                                  context,
                                  MaterialPageRoute(builder: (context) => const LoginPage()),
                                );
                              },
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),
          );
        },
      );
    } else {
      // --- User IS logged in ---
      showDialog(
        context: context,
        builder: (BuildContext context) {
          return _AddSongToPlaylistDialog(
            userId: _currentUserId!,
            songId: _currentSongId!, // Use the currently loaded song ID
          );
        },
      );
    }
  }
  // --- END OF FUNCTION ---

  // --- DATA FETCHING & SETUP (MODIFIED) ---

  Future<void> _loadSongData({bool autoPlay = false}) async {
    // 1. Set loading state
    setState(() {
      _isLoading = true;
      title = "Loading...";
      artist = "";
      coverUrl = "";
      _lyricsLines = [];
      _currentLyricIndex = -1;
      _duration = Duration.zero;
      _dragPosition = null;
      _isPlaying = false;
      _isFavourite = false; // Reset favourite status
    });

    _syncTimer?.cancel();
    await Future.wait([
      _vocalPlayer.stop(),
      _instrumentalPlayer.stop()
    ]);

    // 2. Get song data
    if (_currentIndex < 0 || _currentIndex >= widget.songList.length) {
      if (mounted) Navigator.pop(context);
      return;
    }

    final Map<String, dynamic> currentSong = widget.songList[_currentIndex];

    // --- MODIFIED ---
    _currentSongId = currentSong['sid']?.toString(); // Store the song ID

    // --- 2. FUNCTION CALL ADDED ---
    _addHistoryRecord();
    // --- END OF ADDITION ---

    if (_currentSongId == null) {
      setState(() => _isLoading = false);
      return;
    }
    // --- END MODIFIED ---

    // 3. Set basic info
    setState(() {
      title = currentSong["name"] ?? "Loading...";
      coverUrl = currentSong["image"] ?? "";
    });

    // --- NEW ---
    // After getting the song ID, immediately check its favourite status
    // We do this *at the same time* as fetching the song details
    _checkFavouriteStatus();
    // --- END NEW ---

    // 4. Fetch detailed data (from getSongDetails.php)
    try {
      final res = await http.get(Uri.parse(
          "${AppConfig.baseUrl}getSongDetails.php?sid=$_currentSongId"));

      if (res.statusCode == 200) {
        final data = jsonDecode(res.body);

        setState(() {
          title = data["name"] ?? currentSong["name"] ?? "Unknown";
          artist = data["singer"] ?? "Unknown";
          coverUrl = data["image"] ?? data["poster"] ?? currentSong["image"] ?? "";
          audioUrl = data["vocal"] ?? "";
          instrumentalUrl = data["instrumental"] ?? "";

          String rawLyrics = (data["lyrics"] as String? ?? "").replaceAll('\r', '');
          // _isLoading is set to false AFTER setupAudio completes

          final RegExp lyricRegex =
          RegExp(r"\[(\d{2}):(\d{2})(?:\.(\d{2,3}))?\](.*)");

          _lyricsLines = rawLyrics
              .split("\n")
              .map((line) {
            final match = lyricRegex.firstMatch(line.trim());

            if (match != null) {
              final min = int.parse(match.group(1)!);
              final sec = int.parse(match.group(2)!);
              final msString = match.group(3);
              final ms = msString != null ? int.parse(msString.padRight(3, '0').substring(0, 3)) : 0;
              final text = match.group(4)!.trim();

              if (text.isNotEmpty) {
                return {"time": Duration(minutes: min, seconds: sec, milliseconds: ms), "text": text};
              }
            }
            return null;
          }).where((e) => e != null).cast<Map<String, dynamic>>().toList();

          _currentLyricIndex = -1;
        });

        // --- MODIFICATION: Wait for audio to be ready ---
        await setupAudio(); // This now waits for buffering

        // Now that audio is fully buffered, set loading to false
        setState(() {
          _isLoading = false;
        });

        if (autoPlay) {
          await _startPlaybackAndSync();
        }
        // --- END OF MODIFICATION ---
      } else {
        setState(() => _isLoading = false);
      }
    } catch (e) {
      setState(() => _isLoading = false);
    }
  }


  Future<void> setupAudio() async {
    // --- 3. MODIFIED ---
    _durationSub?.cancel();
    _positionSub?.cancel();
    _playerStateSub?.cancel(); // Cancel previous subscription
    // --- END MODIFIED ---
    try {
      // --- THIS IS THE MODIFICATION ---
      // We are NOT using LockCachingAudioSource, just the default.
      // We wait for setUrl to finish, which includes initial buffering.
      await Future.wait([
        _vocalPlayer.setUrl(audioUrl),
        _instrumentalPlayer.setUrl(instrumentalUrl),
      ]);
      // --- END OF MODIFICATION ---

      // 3. Now that they are loaded, set up the listeners
      _durationSub = _instrumentalPlayer.durationStream.listen((d) {
        if (d != null && d > Duration.zero) {
          if (mounted) setState(() => _duration = d);
        }
      });
      _positionSub = _instrumentalPlayer.positionStream.listen((pos) {
        _updateLyricPosition();
        if (_dragPosition == null) {
          if (mounted) setState(() {});
        }
      });

      // --- 4. ADDED PLAYER STATE LISTENER ---
      _playerStateSub = _instrumentalPlayer.playerStateStream.listen((state) {
        if (state.processingState == ProcessingState.completed) {
          // Check if we are still playing (to avoid double-triggers
          // from seek-to-end) and that the component is still mounted.
          if (_isPlaying && mounted) {
            _handleSongCompletion();
          }
        }
      });
      // --- END ADDITION ---

    } catch (e) {
      debugPrint("❌ Audio setup error: $e");
    }
  }

  // --- LYRICS & UI ---

  void _updateLyricPosition({Duration? atPosition}) {
    final currentPos = atPosition ?? _instrumentalPlayer.position;
    int newIndex = -1;
    for (int i = 0; i < _lyricsLines.length; i++) {
      if (currentPos >= _lyricsLines[i]["time"]) {
        newIndex = i;
      } else {
        break;
      }
    }
    if (newIndex != _currentLyricIndex) {
      if (mounted) {
        setState(() {
          _currentLyricIndex = newIndex;
        });
      }
    }
  }

  void _showFullScreenLyrics() {
    Navigator.of(context).push(PageRouteBuilder(
      opaque: false,
      pageBuilder: (BuildContext context, _, __) => FullScreenLyricPage(
        lyricsLines: _lyricsLines,
        initialLyricIndex: _currentLyricIndex,
        positionStream: _instrumentalPlayer.positionStream,
        title: title,
        artist: artist,
      ),
      transitionsBuilder: (context, animation, secondaryAnimation, child) =>
          FadeTransition(opacity: animation, child: child),
    ));
  }

  // --- PLAYBACK CONTROLS (MODIFIED) ---

  // This function is for starting playback and sync.
  Future<void> _startPlaybackAndSync() async {
    if (_isLoading) return;
    _syncTimer?.cancel();
    if (mounted) setState(() => _isPlaying = true);

    // This Future.wait ensures both play() commands are sent at the same time.
    await Future.wait([_vocalPlayer.play(), _instrumentalPlayer.play()]);

    _syncTimer = Timer.periodic(const Duration(milliseconds: 0), (_) async {
      try {
        final vPos = _vocalPlayer.position;
        final iPos = _instrumentalPlayer.position;
        final expectedVPos = iPos + _vocalOffset;
        final diff = (vPos - expectedVPos).inMilliseconds;
        if (diff.abs() > 20) {
          await _vocalPlayer.seek(expectedVPos);
        }
      } catch (e) {
        print("Sync timer error: $e");
        _syncTimer?.cancel();
      }
    });
  }

  // This function is just for pausing.
  Future<void> _pausePlayback() async {
    _syncTimer?.cancel();
    await Future.wait([_vocalPlayer.pause(), _instrumentalPlayer.pause()]);
    if (mounted) setState(() => _isPlaying = false);
  }

  // This function is now simple
  Future<void> togglePlayPause() async {
    if (_isPlaying) {
      await _pausePlayback();
    } else {
      await _startPlaybackAndSync();
    }
  }

  // This function is now simple
  Future<void> seek(Duration newPos) async {
    _updateLyricPosition(atPosition: newPos);
    if (mounted) setState(() {}); // Update slider UI immediately

    // 1. Get the current playing state
    final bool wasPlaying = _isPlaying;

    // 2. Pause both players
    await _pausePlayback();

    // 3. Seek the instrumental player FIRST.
    // The 'await' waits for the seek (and buffering) to complete.
    await _instrumentalPlayer.seek(newPos);

    // 4. Get the instrumental's EXACT new position.
    final Duration actualInstrumentalPos = _instrumentalPlayer.position;

    // 5. Calculate the vocal's position based on the instrumental's.
    var vocalSeekPos = actualInstrumentalPos + _vocalOffset;
    if (vocalSeekPos < Duration.zero) vocalSeekPos = Duration.zero;

    // 6. Seek the vocal player to that EXACT position.
    await _vocalPlayer.seek(vocalSeekPos);

    // 7. If it was playing, play both together
    if (wasPlaying) {
      await _startPlaybackAndSync();
    }
    // If it was paused, both players are now at the new, synced position, and paused.
  }
  // --- END OF PLAYBACK MODIFICATIONS ---


  String formatTime(Duration d) {
    final m = d.inMinutes.remainder(60).toString().padLeft(2, '0');
    final s = d.inSeconds.remainder(60).toString().padLeft(2, '0');
    return "$m:$s";
  }

  // --- 5. REPEAT LOGIC FUNCTIONS MODIFIED ---

  /// Handles the logic when a song finishes playing.
  void _handleSongCompletion() {
    switch (_repeatMode) {
      case RepeatMode.one:
      // Seek the current song to the beginning and play again.
        seek(Duration.zero);
        break;
      case RepeatMode.all:
      default:
      // Play the next song, wrapping around the list.
        _playNext();
        break;
    }
  }

  /// Cycles through the repeat modes.
  void _toggleRepeatMode() {
    setState(() {
      if (_repeatMode == RepeatMode.all) {
        _repeatMode = RepeatMode.one;
      } else {
        _repeatMode = RepeatMode.all;
      }
    });
  }

  /// Returns the correct icon based on the current repeat mode.
  Widget _getRepeatIcon() {
    // "i want both icon in white"
    const Color iconColor = Colors.white;
    const double iconSize = 24.0;

    // --- THIS IS THE MODIFICATION ---
    switch (_repeatMode) {
      case RepeatMode.one:
      // This is "play this song again"
      // User requested the shuffle icon for this state.
        return const Icon(Icons.shuffle, color: iconColor, size: iconSize); // <-- CHANGED
      case RepeatMode.all:
      default:
      // This is "play next song automatically"
        return const Icon(Icons.repeat_one, color: iconColor, size: iconSize); // <-- From previous swap
    }
    // --- END OF MODIFICATION ---
  }
  // --- END OF REPEAT LOGIC ---


  void _playNext() {
    int newIndex = _currentIndex + 1;
    if (newIndex >= widget.songList.length) {
      newIndex = 0; // Wrap around to the beginning
    }
    if (mounted) setState(() => _currentIndex = newIndex);
    _loadSongData(autoPlay: true);
  }

  void _playPrevious() {
    int newIndex = _currentIndex - 1;
    if (newIndex < 0) {
      newIndex = widget.songList.length - 1; // Wrap around to the end
    }
    if (mounted) setState(() => _currentIndex = newIndex);
    _loadSongData(autoPlay: true);
  }


  @override
  Widget build(BuildContext context) {
    // --- MODIFIED: Show loading indicator while _isLoading is true ---
    if (_isLoading) {
      return Scaffold(
        backgroundColor: const Color(0xFF1C1C1E),
        body: Center(
          child: CircularProgressIndicator(color: const Color(0xFFFF00FF)),
        ),
      );
    }
    // --- END MODIFICATION ---

    final currentPosition = _dragPosition ?? _instrumentalPlayer.position;
    final displayDuration = formatTime(_duration);
    final displayPosition = formatTime(currentPosition);
    final sliderValue = currentPosition.inMilliseconds.toDouble().clamp(0.0, _duration.inMilliseconds.toDouble());
    const Color darkBackground = Color(0xFF1C1C1E);
    const Color neonPink = Color(0xFFFF2688);
    const double borderRadius = 20.0;
    String currentLyricText = '';
    if (_lyricsLines.isNotEmpty && _currentLyricIndex >= 0 && _currentLyricIndex < _lyricsLines.length) {
      currentLyricText = _lyricsLines[_currentLyricIndex]["text"] as String;
    } else {
      currentLyricText = _lyricsLines.isEmpty ? "No synchronized lyrics available." : "Starting soon...";
    }

    return Scaffold(
      backgroundColor: darkBackground,
      body: Stack(
        children: [
          Positioned.fill(
            child: Image.network(
              coverUrl,
              fit: BoxFit.cover,
              errorBuilder: (context, error, stackTrace) => Container(color: darkBackground),
            ),
          ),
          Positioned.fill(
            child: BackdropFilter(
              filter: ImageFilter.blur(sigmaX: 10.0, sigmaY: 10.0),
              child: Container(color: Colors.black54),
            ),
          ),
          Positioned.fill(
            child: Container(
              decoration: const BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.topCenter,
                  end: Alignment.bottomCenter,
                  colors: [Colors.black26, darkBackground],
                  stops: [0.0, 1.0],
                ),
              ),
            ),
          ),
          SafeArea(
            child: Column(
              children: [
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 16.0, vertical: 8.0),
                  // --- 3. MODIFIED APPBAR ROW ---
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      IconButton(icon: const Icon(Icons.arrow_back, color: Colors.white, size: 28), onPressed: () => Navigator.pop(context)),
                      Expanded(
                        child: Text(
                          "Now Playing",
                          style: TextStyle(color: Colors.white, fontSize: 17, fontWeight: FontWeight.w600),
                          textAlign: TextAlign.center,
                        ),
                      ),
                      IconButton(
                        icon: Icon(
                          _isFavourite ? Icons.favorite : Icons.favorite_border,
                          color: _isFavourite ? Colors.redAccent : Colors.red,
                          size: 28,
                        ),
                        onPressed: _toggleFavourite,
                      ),
                    ],
                  ),
                  // --- END OF MODIFICATION ---
                ),

                const SizedBox(height: 20),
                Container(
                  width: 350,
                  height: 350,
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(borderRadius),
                    boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.4), blurRadius: 20, spreadRadius: 5)],
                  ),
                  child: ClipRRect(
                    borderRadius: BorderRadius.circular(borderRadius),
                    child: Image.network(
                      coverUrl,
                      fit: BoxFit.cover,
                      errorBuilder: (context, error, stackTrace) => Container(color: Colors.grey[800], child: const Icon(Icons.music_note, color: Colors.white70, size: 120)),
                    ),
                  ),
                ),
                const SizedBox(height: 30),
                Text(title, style: const TextStyle(color: Colors.white, fontSize: 28, fontWeight: FontWeight.bold, letterSpacing: 0.5)),
                const SizedBox(height: 4),
                Text(artist, style: const TextStyle(color: Colors.white70, fontSize: 16)),
                const SizedBox(height: 30),
                Expanded(
                  child: GestureDetector(
                    onTap: _showFullScreenLyrics,
                    child: Container(
                      padding: const EdgeInsets.symmetric(horizontal: 32.0),
                      alignment: Alignment.center,
                      child: AnimatedSwitcher(
                        duration: const Duration(milliseconds: 250),
                        transitionBuilder: (Widget child, Animation<double> animation) => FadeTransition(opacity: animation, child: child),
                        child: Text(
                          currentLyricText,
                          key: ValueKey<String>(currentLyricText),
                          textAlign: TextAlign.center,
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                          style: const TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.bold, height: 1.4),
                        ),
                      ),
                    ),
                  ),
                ),
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 20.0, vertical: 8.0),
                  child: Column(
                    children: [
                      SliderTheme(
                        data: SliderTheme.of(context).copyWith(
                          trackHeight: 3.0,
                          thumbShape: const RoundSliderThumbShape(enabledThumbRadius: 6.0),
                          overlayShape: const RoundSliderOverlayShape(overlayRadius: 10.0),
                          activeTrackColor: neonPink,
                          inactiveTrackColor: Colors.white24,
                          thumbColor: neonPink,
                          overlayColor: neonPink.withOpacity(0.3),
                        ),
                        child: Slider(
                          min: 0.0,
                          max: _duration.inMilliseconds.toDouble(),
                          value: _duration.inMilliseconds > 0 ? sliderValue : 0.0, // Prevent crash if max is 0
                          onChanged: (double value) {
                            if (mounted) setState(() => _dragPosition = Duration(milliseconds: value.toInt()));
                          },
                          onChangeEnd: (double value) {
                            final newPos = Duration(milliseconds: value.toInt());
                            if (mounted) _dragPosition = null;
                            seek(newPos);
                          },
                        ),
                      ),
                      Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 4.0),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Text(displayPosition, style: const TextStyle(color: Colors.white70, fontSize: 12)),
                            Text(displayDuration, style: const TextStyle(color: Colors.white70, fontSize: 12)),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
                Padding(
                  padding: const EdgeInsets.only(top: 20.0, bottom: 40.0),
                  // --- 6. MODIFIED CONTROL ROW ---
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                    children: [
                      // This is the new repeat toggle button
                      IconButton(
                        icon: _getRepeatIcon(),
                        onPressed: _toggleRepeatMode,
                      ),
                      // --- END ---
                      IconButton(
                          icon: const Icon(Icons.skip_previous, color: Colors.white, size: 36),
                          onPressed: _playPrevious
                      ),
                      GestureDetector(
                        onTap: togglePlayPause,
                        child: Container(
                          width: 60,
                          height: 60,
                          decoration: BoxDecoration(
                            shape: BoxShape.circle,
                            gradient: LinearGradient(
                              colors: [
                                Colors.pinkAccent,
                                Colors.blueAccent.withOpacity(0.8),
                              ],
                              begin: Alignment.topLeft,
                              end: Alignment.bottomRight,
                            ),
                            boxShadow: [
                              BoxShadow(
                                color: Colors.pinkAccent.withOpacity(0.5),
                                blurRadius: 12,
                                spreadRadius: 2,
                                offset: Offset(0, 4),
                              ),
                              BoxShadow(
                                color: Colors.blueAccent.withOpacity(0.4),
                                blurRadius: 10,
                                spreadRadius: 1,
                                offset: Offset(0, 2),
                              ),
                            ],
                          ),
                          child: Icon(
                            _isPlaying ? Icons.pause_rounded : Icons.play_arrow_rounded,
                            color: Colors.white,
                            size: 35,
                          ),
                        ),
                      ),
                      IconButton(
                          icon: const Icon(Icons.skip_next, color: Colors.white, size: 36),
                          onPressed: _playNext
                      ),
                      IconButton(
                          icon: const Icon(Icons.queue_music, color: Colors.white, size: 28), // Color changed to white
                          onPressed: () {
                            if (_currentSongId == null) {
                              ScaffoldMessenger.of(context).showSnackBar(
                                const SnackBar(content: Text('Song is not fully loaded yet.')),
                              );
                              return;
                            }
                            _showPlaylistDialog();
                          }
                      ),
                    ],
                  ),
                  // --- END OF MODIFICATION ---
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}


// --- WIDGET FOR FULL SCREEN LYRICS ---
// (This widget remains unchanged)

class FullScreenLyricPage extends StatefulWidget {
  final List<Map<String, dynamic>> lyricsLines;
  final int initialLyricIndex;
  final Stream<Duration> positionStream;
  final String title;
  final String artist;

  const FullScreenLyricPage({
    Key? key,
    required this.lyricsLines,
    required this.initialLyricIndex,
    required this.positionStream,
    required this.title,
    required this.artist,
  }) : super(key: key);

  @override
  State<FullScreenLyricPage> createState() => _FullScreenLyricPageState();
}

class _FullScreenLyricPageState extends State<FullScreenLyricPage> {
  late int _currentLyricIndex;
  StreamSubscription<Duration>? _positionSub;
  final ScrollController _scrollController = ScrollController();

  @override
  void initState() {
    super.initState();
    _currentLyricIndex = widget.initialLyricIndex;

    WidgetsBinding.instance.addPostFrameCallback((_) {
      _scrollToActiveLyric(isInitial: true);
    });

    _positionSub = widget.positionStream.listen((position) {
      int newIndex = -1;
      for (int i = 0; i < widget.lyricsLines.length; i++) {
        if (position >= widget.lyricsLines[i]["time"]) {
          newIndex = i;
        } else {
          break;
        }
      }

      if (newIndex != _currentLyricIndex) {
        if (mounted) {
          setState(() {
            _currentLyricIndex = newIndex;
          });
        }
        _scrollToActiveLyric();
      }
    });
  }

  @override
  void dispose() {
    _positionSub?.cancel();
    _scrollController.dispose();
    super.dispose();
  }

  void _scrollToActiveLyric({bool isInitial = false}) {
    if (_currentLyricIndex >= 0 && _scrollController.hasClients) {
      const double lineHeight = 44.0;
      final double centerOffset = _scrollController.position.viewportDimension / 2;
      final double targetOffset = (_currentLyricIndex * lineHeight) - centerOffset + (lineHeight / 2);

      if (isInitial) {
        _scrollController.jumpTo(
          targetOffset.clamp(0.0, _scrollController.position.maxScrollExtent),
        );
      } else {
        _scrollController.animateTo(
          targetOffset.clamp(0.0, _scrollController.position.maxScrollExtent),
          duration: const Duration(milliseconds: 300),
          curve: Curves.easeOut,
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    const Color highlightColor = Color(0xFFFF00FF);

    return Scaffold(
      backgroundColor: Colors.black,
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.close, color: Colors.white, size: 28),
          onPressed: () => Navigator.pop(context),
        ),
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(widget.title, style: const TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.bold)),
            Text(widget.artist, style: const TextStyle(color: Colors.white70, fontSize: 12)),
          ],
        ),
        centerTitle: true,
      ),
      body: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 24.0),
        child: ListView.builder(
          controller: _scrollController,
          itemCount: widget.lyricsLines.length,
          padding: EdgeInsets.symmetric(vertical: MediaQuery.of(context).size.height / 3),
          itemBuilder: (context, index) {
            final isActive = index == _currentLyricIndex;
            final text = widget.lyricsLines[index]["text"] as String;

            return Padding(
              padding: const EdgeInsets.symmetric(vertical: 8.0),
              child: Text(
                text,
                textAlign: TextAlign.center,
                style: TextStyle(
                  color: isActive ? highlightColor : Colors.white70,
                  fontSize: isActive ? 28 : 22,
                  fontWeight: isActive ? FontWeight.bold : FontWeight.normal,
                  height: 1.2,
                ),
              ),
            );
          },
        ),
      ),
    );
  }
}

// --- 4. ADD THIS CLASS AT THE END OF THE FILE ---
// This is the dialog widget from SongDetailPage
class _AddSongToPlaylistDialog extends StatefulWidget {
  final String userId;
  final String songId;

  const _AddSongToPlaylistDialog({
    Key? key,
    required this.userId,
    required this.songId,
  }) : super(key: key);

  @override
  State<_AddSongToPlaylistDialog> createState() => _AddSongToPlaylistDialogState();
}

class _AddSongToPlaylistDialogState extends State<_AddSongToPlaylistDialog> {
  bool _isLoading = true;
  List<Map<String, dynamic>> _playlists = [];
  String? _error;
  final TextEditingController _newPlaylistController = TextEditingController();

  @override
  void initState() {
    super.initState();
    _fetchPlaylists();
  }

  @override
  void dispose() {
    _newPlaylistController.dispose();
    super.dispose();
  }

  Future<void> _fetchPlaylists() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      final url = Uri.parse(
          "${AppConfig.baseUrl}getUserPlaylists.php?user_id=${widget.userId}&song_id=${widget.songId}");
      final response = await http.get(url);

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data is List) {
          setState(() {
            _playlists = List<Map<String, dynamic>>.from(data);
            _isLoading = false;
          });
        } else if (data is Map && data.containsKey('error')) {
          throw Exception(data['error']);
        }
        else {
          setState(() {
            _playlists = [];
            _isLoading = false;
          });
        }
      } else {
        throw Exception('Failed to load playlists. Status: ${response.statusCode}');
      }
    } catch (e) {
      setState(() {
        _error = "No playlists found or error fetching.";
        _isLoading = false;
      });
    }
  }

  void _showGlowSnackBar(String message, bool isSuccess) {
    if (!mounted) return;

    final color = isSuccess ? Colors.green : Colors.redAccent;

    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        behavior: SnackBarBehavior.floating,
        margin: const EdgeInsets.all(10),
        content: Container(
          padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
          decoration: BoxDecoration(
            color: color,
            borderRadius: BorderRadius.circular(20),
            boxShadow: [
              BoxShadow(
                color: color.withOpacity(0.6),
                blurRadius: 10.0,
                spreadRadius: 2.0,
                offset: const Offset(0, 0),
              ),
            ],
          ),
          child: Text(
            message,
            style: GoogleFonts.cabinSketch(
              color: Colors.white,
              fontWeight: FontWeight.bold,
              fontSize: 16,
            ),
            textAlign: TextAlign.center,
          ),
        ),
      ),
    );
  }

  Future<void> _showCreatePlaylistDialog() async {
    showDialog(
      context: context,
      builder: (BuildContext dialogContext) {
        return Dialog(
          backgroundColor: Colors.transparent,
          elevation: 0,
          child: ClipRRect(
            borderRadius: BorderRadius.circular(16.0),
            child: BackdropFilter(
              filter: ImageFilter.blur(sigmaX: 5.0, sigmaY: 5.0),
              child: Container(
                width: 300,
                decoration: BoxDecoration(
                  color: Color(0xFF000000).withOpacity(0.3),
                  borderRadius: BorderRadius.circular(16.0),
                  border: Border.all(color: Colors.pinkAccent),
                ),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Padding(
                      padding: const EdgeInsets.fromLTRB(24.0, 24.0, 24.0, 20.0),
                      child: Text(
                        'New Playlist',
                        style: GoogleFonts.cabinSketch(
                            color: Colors.white,
                            fontSize: 22,
                            fontWeight: FontWeight.bold
                        ),
                      ),
                    ),
                    Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 24.0),
                      child: TextField(
                        controller: _newPlaylistController,
                        autofocus: true,
                        style: GoogleFonts.cabinSketch(color: Colors.white, fontSize: 16),
                        decoration: InputDecoration(
                          hintText: 'Playlist name',
                          hintStyle: GoogleFonts.cabinSketch(color: Colors.white70, fontSize: 16),
                          enabledBorder: UnderlineInputBorder(borderSide: BorderSide(color: Colors.white)),
                          focusedBorder: UnderlineInputBorder(borderSide: BorderSide(color: Colors.pinkAccent)),
                        ),
                      ),
                    ),
                    Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 20.0, vertical: 16.0),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Expanded(
                            child: _buildGradientButton(
                              text: 'Cancel',
                              gradient: LinearGradient(
                                colors: [Colors.grey[700]!, Colors.grey[800]!],
                                begin: Alignment.topLeft,
                                end: Alignment.bottomRight,
                              ),
                              onTap: () => Navigator.of(dialogContext).pop(),
                            ),
                          ),
                          const SizedBox(width: 10),
                          Expanded(
                            child: _buildGradientButton(
                              text: 'Create',
                              gradient: LinearGradient(
                                colors: [
                                  Colors.pinkAccent,
                                  Colors.blueAccent.withOpacity(0.8)
                                ],
                                begin: Alignment.topLeft,
                                end: Alignment.bottomRight,
                              ),
                              onTap: () async {
                                final String name = _newPlaylistController.text.trim();
                                if (name.isEmpty) return;

                                String message = '';
                                bool isSuccess = false;

                                try {
                                  final String safeName = Uri.encodeComponent(name);
                                  final url = Uri.parse(
                                      "${AppConfig.baseUrl}createPlaylist.php?user_id=${widget.userId}&name=$safeName"
                                  );
                                  final res = await http.get(url);

                                  if (!mounted) return;

                                  if (res.statusCode == 200) {
                                    final data = jsonDecode(res.body);
                                    message = data['message'] ?? 'An error occurred.';
                                    if (data['status'] == 'success') {
                                      isSuccess = true;
                                      _newPlaylistController.clear();
                                      _fetchPlaylists();
                                    }
                                  } else {
                                    message = 'Server error creating playlist.';
                                  }
                                } catch(e) {
                                  message = 'An error occurred: $e';
                                }

                                _showGlowSnackBar(message, isSuccess);

                                if (isSuccess) {
                                  Navigator.of(dialogContext).pop();
                                }
                              },
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),
        );
      },
    );
  }

  Widget _buildGradientButton({
    required String text,
    required Gradient gradient,
    required VoidCallback onTap,
  }) {
    return InkWell(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 12.0),
        decoration: BoxDecoration(
          gradient: gradient,
          borderRadius: BorderRadius.circular(30.0), // Rounded shape
          boxShadow: [
            BoxShadow(
              color: gradient.colors.first.withOpacity(0.5),
              blurRadius: 10,
              spreadRadius: 1,
            ),
          ],
        ),
        child: Text(
          text,
          textAlign: TextAlign.center,
          style: GoogleFonts.cabinSketch(
            color: Colors.white,
            fontWeight: FontWeight.bold,
            fontSize: 16,
          ),
        ),
      ),
    );
  }

  Future<void> _addSongToPlaylist(String playlistId) async {
    String message = '';
    bool isSuccess = false;

    try {
      final url = Uri.parse(
          "${AppConfig.baseUrl}addSongToPlaylist.php?playlist_id=$playlistId&song_id=${widget.songId}"
      );
      final res = await http.get(url);

      if (res.statusCode == 200) {
        final data = jsonDecode(res.body);
        message = data['message'] ?? 'An error occurred.';
        if (data['status'] == 'success') {
          isSuccess = true;
        }
      } else {
        message = "Server error.";
      }
    } catch (e) {
      message = 'An error occurred: $e';
    }

    if (!mounted) return;

    _showGlowSnackBar(message, isSuccess);

    if (isSuccess) {
      // Refresh the list instead of popping
      _fetchPlaylists();
    }
  }

  Widget _buildContent() {
    if (_isLoading) {
      return const Center(child: CircularProgressIndicator(color: Colors.pinkAccent));
    }

    return ListView(
      padding: const EdgeInsets.only(bottom: 80.0, top: 0),
      children: [
        if (_error != null && _playlists.isEmpty)
          Padding(
            padding: const EdgeInsets.all(16.0),
            child: Text(
                _error!,
                style: GoogleFonts.cabinSketch(color: Colors.white70, fontSize: 16, fontWeight: FontWeight.bold),
                textAlign: TextAlign.center
            ),
          ),
        if (_playlists.isEmpty && _error == null)
          Padding(
            padding: const EdgeInsets.all(16.0),
            child: Text(
              'You have no playlists. Create one!',
              style: GoogleFonts.cabinSketch(color: Colors.white70, fontSize: 16, fontWeight: FontWeight.bold),
              textAlign: TextAlign.center,
            ),
          ),
        ..._playlists.map((playlist) {
          final String playlistId = (playlist['id'] ?? '').toString();
          final String? imageUrl = playlist['image']?.toString();
          final String songCount = playlist['song_count']?.toString() ?? '0';
          final String songText = (songCount == '1') ? 'Song' : 'Songs';
          final bool containsSong = playlist['contains_song'] ?? false;
          final IconData iconData = containsSong ? Icons.remove : Icons.add;
          final Color iconColor = containsSong ? Colors.redAccent : Colors.green;

          return ListTile(
            leading: ClipRRect(
              borderRadius: BorderRadius.circular(8.0),
              child: (imageUrl != null && imageUrl.isNotEmpty)
                  ? FadeInImage.assetNetwork(
                placeholder: 'assets/placeholder.png',
                image: imageUrl,
                width: 40,
                height: 40,
                fit: BoxFit.cover,
                imageErrorBuilder: (context, error, stackTrace) {
                  return Image.asset('assets/placeholder.png', width: 40, height: 40, fit: BoxFit.cover);
                },
              )
                  : Image.asset(
                'assets/placeholder.png',
                width: 40,
                height: 40,
                fit: BoxFit.cover,
              ),
            ),
            title: Text(
                playlist['name'] ?? 'Untitled',
                style: GoogleFonts.cabinSketch(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold)
            ),
            subtitle: Text(
              "$songCount $songText",
              style: GoogleFonts.cabinSketch(
                color: Colors.pinkAccent,
                fontSize: 14,
                fontWeight: FontWeight.bold,
              ),
            ),
            trailing: IconButton(
              icon: Icon(iconData, color: iconColor),
              onPressed: () {
                if (playlistId.isNotEmpty) {
                  _addSongToPlaylist(playlistId);
                }
              },
            ),
            onTap: null, // Row is no longer tappable, only the button
          );
        }).toList(),
      ],
    );
  }

  @override
  Widget build(BuildContext context) {
    return Dialog(
      backgroundColor: Colors.transparent,
      elevation: 0,
      child: ClipRRect(
        borderRadius: BorderRadius.circular(16.0),
        child: BackdropFilter(
          filter: ImageFilter.blur(sigmaX: 5.0, sigmaY: 5.0),
          child: Container(
            width: 300,
            height: 400,
            decoration: BoxDecoration(
              color: Color(0xFF000000).withOpacity(0.3),
              borderRadius: BorderRadius.circular(16.0),
              border: Border.all(color: Colors.pinkAccent),
            ),
            child: Stack(
              children: [
                Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Padding(
                      padding: const EdgeInsets.fromLTRB(24.0, 24.0, 24.0, 20.0),
                      child: RichText(
                        text: TextSpan(
                          style: GoogleFonts.cabinSketch(
                              color: Colors.white,
                              fontSize: 22,
                              fontWeight: FontWeight.bold
                          ),
                          children: [
                            TextSpan(text: 'Add to '),
                            TextSpan(
                              text: 'Playlist',
                              style: TextStyle(color: Colors.pinkAccent),
                            ),
                          ],
                        ),
                      ),
                    ),
                    Expanded(
                      child: Container(
                        width: double.maxFinite,
                        child: _buildContent(),
                      ),
                    ),
                  ],
                ),
                Positioned(
                  top: 0,
                  right: 0,
                  child: IconButton(
                    icon: Icon(Icons.close, color: Colors.white),
                    onPressed: () => Navigator.of(context).pop(),
                  ),
                ),
                Positioned(
                  bottom: 16,
                  right: 16,
                  child: FloatingActionButton(
                    onPressed: _showCreatePlaylistDialog,
                    shape: CircleBorder(),
                    child: Container(
                      width: 56,
                      height: 56,
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        gradient: LinearGradient(
                          colors: [
                            Colors.pinkAccent,
                            Colors.blueAccent.withOpacity(0.8),
                          ],
                          begin: Alignment.topLeft,
                          end: Alignment.bottomRight,
                        ),
                      ),
                      child: Icon(Icons.add, color: Colors.white),
                    ),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
// --- END OF ADDED CLASS ---