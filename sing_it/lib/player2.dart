import 'package:flutter/material.dart';
import 'package:just_audio/just_audio.dart';
import 'package:http/http.dart' as http;
import 'dart:async';
import 'dart:convert';
import 'dart:ui';
import 'config.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:google_fonts/google_fonts.dart';
import 'login.dart';
import 'artist_detail.dart';

enum RepeatMode {
  all,
  one
}

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
  final AudioPlayer _vocalPlayer = AudioPlayer();
  final AudioPlayer _instrumentalPlayer = AudioPlayer();

  bool _isPlaying = false;
  bool _isLoading = true;

  Duration _duration = Duration.zero;
  Duration? _dragPosition;

  StreamSubscription<Duration>? _positionSub;
  StreamSubscription<Duration?>? _durationSub;
  StreamSubscription<PlayerState>? _playerStateSub;
  Timer? _syncTimer;

  final Duration _vocalOffset = Duration.zero;

  String title = "Loading...";
  String artist = "";
  List<Map<String, String>> _singersList = [];
  String coverUrl = "";
  String audioUrl = "";
  String instrumentalUrl = "";

  List<Map<String, dynamic>> _lyricsLines = [];
  int _currentLyricIndex = -1;

  late int _currentIndex;

  bool _isFavourite = false;
  String? _currentUserId;
  String? _currentSongId;

  RepeatMode _repeatMode = RepeatMode.all;

  @override
  void initState() {
    super.initState();
    _currentIndex = widget.initialIndex;
    _loadUserAndSongData(autoPlay: true);
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

  Future<void> _loadUserAndSongData({bool autoPlay = false}) async {
    final prefs = await SharedPreferences.getInstance();
    _currentUserId = prefs.getString('user_id');
    await _loadSongData(autoPlay: autoPlay);
  }

  Future<void> _toggleFavourite() async {
    if (_currentUserId == null || _currentSongId == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Error: Not logged in or song not loaded.')),
      );
      return;
    }

    final bool newFavouriteState = !_isFavourite;
    setState(() {
      _isFavourite = newFavouriteState;
    });

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
          setState(() {
            _isFavourite = !newFavouriteState;
          });
          if (mounted) {
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(content: Text('Error: ${data['message']}')),
            );
          }
        }
      } else {
        setState(() {
          _isFavourite = !newFavouriteState;
        });
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text('Error connecting to server.')),
          );
        }
      }
    } catch (e) {
      setState(() {
        _isFavourite = !newFavouriteState;
      });
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('An error occurred.')),
        );
      }
    }
  }

  Future<void> _checkFavouriteStatus() async {
    if (_currentUserId == null || _currentSongId == null) {
      setState(() => _isFavourite = false);
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
        if (mounted) setState(() => _isFavourite = false);
      }
    } catch (e) {
      if (mounted) setState(() => _isFavourite = false);
    }
  }

  Future<void> _addHistoryRecord() async {
    if (_currentUserId == null || _currentSongId == null) return;

    try {
      final url = Uri.parse(
          "${AppConfig.baseUrl}history.php?user_id=$_currentUserId&song_id=$_currentSongId");
      await http.get(url);
    } catch (e) {
      print('Error saving history: $e');
    }
  }

  void _showPlaylistDialog() {
    if (_currentUserId == null) {
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
                    color: const Color(0xFF000000).withOpacity(0.3),
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
                            children: const [
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
      showDialog(
        context: context,
        builder: (BuildContext context) {
          return _AddSongToPlaylistDialog(
            userId: _currentUserId!,
            songId: _currentSongId!,
          );
        },
      );
    }
  }

  Future<void> _loadSongData({bool autoPlay = false}) async {
    setState(() {
      _isLoading = true;
      title = "Loading...";
      artist = "";
      _singersList = [];
      coverUrl = "";
      _lyricsLines = [];
      _currentLyricIndex = -1;
      _duration = Duration.zero;
      _dragPosition = null;
      _isPlaying = false;
      _isFavourite = false;
    });

    _syncTimer?.cancel();
    await Future.wait([
      _vocalPlayer.stop(),
      _instrumentalPlayer.stop()
    ]);

    if (_currentIndex < 0 || _currentIndex >= widget.songList.length) {
      if (mounted) Navigator.pop(context);
      return;
    }

    final Map<String, dynamic> currentSong = widget.songList[_currentIndex];

    _currentSongId = currentSong['sid']?.toString();

    _addHistoryRecord();

    if (_currentSongId == null) {
      setState(() => _isLoading = false);
      return;
    }

    setState(() {
      title = currentSong["name"] ?? "Loading...";
      coverUrl = currentSong["image"] ?? "";
    });

    _checkFavouriteStatus();

    try {
      final res = await http.get(Uri.parse(
          "${AppConfig.baseUrl}getSongDetails.php?sid=$_currentSongId"));

      if (res.statusCode == 200) {
        final data = jsonDecode(res.body);

        List<Map<String, String>> parsedSingers = [];
        if (data['singers'] is List) {
          parsedSingers = List<dynamic>.from(data['singers']).map((s) {
            if (s is Map) {
              return {
                'id': s['id']?.toString() ?? s['arid']?.toString() ?? '',
                'name': s['name']?.toString() ?? 'Unknown Singer',
              };
            } else {
              return {
                'id': '',
                'name': s.toString(),
              };
            }
          }).toList();
        } else if (data['singers'] is String || data['singer'] is String) {
          String sStr = data['singers'] ?? data['singer'] ?? '';
          parsedSingers = sStr.split(',').map((s) => {
            'id': data['arid']?.toString() ?? '',
            'name': s.trim(),
          }).where((s) => s['name']!.isNotEmpty).toList();
        } else {
          parsedSingers = [{'id': '', 'name': 'Unknown Singer'}];
        }

        setState(() {
          title = data["name"] ?? currentSong["name"] ?? "Unknown";
          _singersList = parsedSingers;
          artist = parsedSingers.isNotEmpty ? parsedSingers.map((s) => s['name']).join(', ') : "Unknown Singer";
          coverUrl = data["image"] ?? data["poster"] ?? currentSong["image"] ?? "";
          audioUrl = data["vocal"] ?? "";
          instrumentalUrl = data["instrumental"] ?? "";

          String rawLyrics = (data["lyrics"] as String? ?? "").replaceAll('\r', '');

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

        await setupAudio();

        setState(() {
          _isLoading = false;
        });

        if (autoPlay) {
          await _startPlaybackAndSync();
        }
      } else {
        setState(() => _isLoading = false);
      }
    } catch (e) {
      setState(() => _isLoading = false);
    }
  }

  Future<void> setupAudio() async {
    _durationSub?.cancel();
    _positionSub?.cancel();
    _playerStateSub?.cancel();
    try {
      await Future.wait([
        _vocalPlayer.setUrl(audioUrl),
        _instrumentalPlayer.setUrl(instrumentalUrl),
      ]);

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

      _playerStateSub = _instrumentalPlayer.playerStateStream.listen((state) {
        if (state.processingState == ProcessingState.completed) {
          if (_isPlaying && mounted) {
            _handleSongCompletion();
          }
        }
      });
    } catch (e) {
      debugPrint("❌ Audio setup error: $e");
    }
  }

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

  Future<void> _startPlaybackAndSync() async {
    if (_isLoading) return;
    _syncTimer?.cancel();
    if (mounted) setState(() => _isPlaying = true);

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

  Future<void> _pausePlayback() async {
    _syncTimer?.cancel();
    await Future.wait([_vocalPlayer.pause(), _instrumentalPlayer.pause()]);
    if (mounted) setState(() => _isPlaying = false);
  }

  Future<void> togglePlayPause() async {
    if (_isPlaying) {
      await _pausePlayback();
    } else {
      await _startPlaybackAndSync();
    }
  }

  Future<void> seek(Duration newPos) async {
    _updateLyricPosition(atPosition: newPos);
    if (mounted) setState(() {});

    final bool wasPlaying = _isPlaying;

    await _pausePlayback();
    await _instrumentalPlayer.seek(newPos);

    final Duration actualInstrumentalPos = _instrumentalPlayer.position;
    var vocalSeekPos = actualInstrumentalPos + _vocalOffset;
    if (vocalSeekPos < Duration.zero) vocalSeekPos = Duration.zero;

    await _vocalPlayer.seek(vocalSeekPos);

    if (wasPlaying) {
      await _startPlaybackAndSync();
    }
  }

  String formatTime(Duration d) {
    final m = d.inMinutes.remainder(60).toString().padLeft(2, '0');
    final s = d.inSeconds.remainder(60).toString().padLeft(2, '0');
    return "$m:$s";
  }

  void _handleSongCompletion() {
    switch (_repeatMode) {
      case RepeatMode.one:
        seek(Duration.zero);
        break;
      case RepeatMode.all:
      default:
        _playNext();
        break;
    }
  }

  void _toggleRepeatMode() {
    setState(() {
      if (_repeatMode == RepeatMode.all) {
        _repeatMode = RepeatMode.one;
      } else {
        _repeatMode = RepeatMode.all;
      }
    });
  }

  Widget _getRepeatIcon() {
    const Color iconColor = Colors.white;
    const double iconSize = 24.0;

    switch (_repeatMode) {
      case RepeatMode.one:
        return const Icon(Icons.shuffle, color: iconColor, size: iconSize);
      case RepeatMode.all:
      default:
        return const Icon(Icons.repeat_one, color: iconColor, size: iconSize);
    }
  }

  void _playNext() {
    int newIndex = _currentIndex + 1;
    if (newIndex >= widget.songList.length) {
      newIndex = 0;
    }
    if (mounted) setState(() => _currentIndex = newIndex);
    _loadSongData(autoPlay: true);
  }

  void _playPrevious() {
    int newIndex = _currentIndex - 1;
    if (newIndex < 0) {
      newIndex = widget.songList.length - 1;
    }
    if (mounted) setState(() => _currentIndex = newIndex);
    _loadSongData(autoPlay: true);
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoading) {
      return const Scaffold(
        backgroundColor: Color(0xFF1C1C1E),
        body: Center(
          child: CircularProgressIndicator(color: Color(0xFFFF00FF)),
        ),
      );
    }

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
      currentLyricText = _lyricsLines.isEmpty ? "No synchronized lyrics available." : "";
    }

    final nameStyle = GoogleFonts.cabinSketch(
      fontSize: 28,
      fontWeight: FontWeight.bold,
      color: Colors.white,
      letterSpacing: 0.5,
    );

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
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      IconButton(icon: const Icon(Icons.arrow_back, color: Colors.white, size: 28), onPressed: () => Navigator.pop(context)),
                      Expanded(
                        child: RichText(
                          textAlign: TextAlign.center,
                          text: TextSpan(
                            style: GoogleFonts.cabinSketch(fontSize: 22, fontWeight: FontWeight.bold),
                            children: const [
                              TextSpan(text: "Now ", style: TextStyle(color: Colors.pinkAccent)),
                              TextSpan(text: "Playing", style: TextStyle(color: Colors.white)),
                            ],
                          ),
                        ),
                      ),
                      IconButton(
                        icon: Icon(
                          _isFavourite ? Icons.favorite : Icons.favorite_border,
                          color: _isFavourite ? Colors.redAccent : Colors.white,
                          size: 28,
                        ),
                        onPressed: _toggleFavourite,
                      ),
                    ],
                  ),
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

                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 16.0),
                  child: RichText(
                    textAlign: TextAlign.center,
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                    text: TextSpan(
                      children: [
                        if (title.isNotEmpty) ...[
                          TextSpan(
                            text: title[0],
                            style: nameStyle.copyWith(color: Colors.pinkAccent),
                          ),
                          if (title.length > 1)
                            TextSpan(
                              text: title.substring(1),
                              style: nameStyle,
                            ),
                        ] else ...[
                          TextSpan(text: 'Unknown Song', style: nameStyle),
                        ]
                      ],
                    ),
                  ),
                ),

                const SizedBox(height: 8),

                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 16.0),
                  child: Wrap(
                    alignment: WrapAlignment.center,
                    crossAxisAlignment: WrapCrossAlignment.center,
                    children: _singersList.asMap().entries.map((entry) {
                      int idx = entry.key;
                      Map<String, String> singerMap = entry.value;
                      String artistId = singerMap['id'] ?? '';
                      String artistName = singerMap['name'] ?? 'Unknown Singer';

                      return Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          GestureDetector(
                            onTap: () {
                              if (artistId.isNotEmpty && artistId != 'null') {
                                Navigator.push(
                                  context,
                                  MaterialPageRoute(
                                    builder: (context) => ArtistDetailPage(
                                      artistId: artistId,
                                      initialArtistName: artistName,
                                    ),
                                  ),
                                );
                              } else {
                                ScaffoldMessenger.of(context).showSnackBar(
                                  const SnackBar(
                                    content: Text('Artist details not available.'),
                                    backgroundColor: Colors.redAccent,
                                  ),
                                );
                              }
                            },
                            child: Text(
                              artistName,
                              style: GoogleFonts.cabinSketch(color: Colors.pinkAccent, fontSize: 18),
                            ),
                          ),
                          if (idx < _singersList.length - 1)
                            Text(' / ', style: GoogleFonts.cabinSketch(color: Colors.white70, fontSize: 16)),
                        ],
                      );
                    }).toList(),
                  ),
                ),

                const SizedBox(height: 10),
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
                          style: GoogleFonts.cabinSketch(color: Colors.white, fontSize: 20, fontWeight: FontWeight.bold, height: 1.2),
                        ),
                      ),
                    ),
                  ),
                ),
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 20.0, vertical: 4.0),
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
                          value: _duration.inMilliseconds > 0 ? sliderValue : 0.0,
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
                            Text(displayPosition, style: GoogleFonts.cabinSketch(color: Colors.white70, fontSize: 14)),
                            Text(displayDuration, style: GoogleFonts.cabinSketch(color: Colors.white70, fontSize: 14)),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
                Padding(
                  padding: const EdgeInsets.only(top: 10.0, bottom: 40.0),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                    children: [
                      IconButton(
                        icon: _getRepeatIcon(),
                        onPressed: _toggleRepeatMode,
                      ),
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
                                offset: const Offset(0, 4),
                              ),
                              BoxShadow(
                                color: Colors.blueAccent.withOpacity(0.4),
                                blurRadius: 10,
                                spreadRadius: 1,
                                offset: const Offset(0, 2),
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
                          icon: const Icon(Icons.queue_music, color: Colors.white, size: 28),
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
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

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

  @override
  void initState() {
    super.initState();
    _currentLyricIndex = widget.initialLyricIndex;

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
      }
    });
  }

  @override
  void dispose() {
    _positionSub?.cancel();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
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
          crossAxisAlignment: CrossAxisAlignment.center,
          children: [
            RichText(
              textAlign: TextAlign.center,
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
              text: TextSpan(
                children: [
                  if (widget.title.isNotEmpty) ...[
                    TextSpan(
                      text: widget.title[0],
                      style: GoogleFonts.cabinSketch(color: Colors.pinkAccent, fontSize: 18, fontWeight: FontWeight.bold),
                    ),
                    if (widget.title.length > 1)
                      TextSpan(
                        text: widget.title.substring(1),
                        style: GoogleFonts.cabinSketch(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold),
                      ),
                  ] else ...[
                    TextSpan(text: 'Unknown Song', style: GoogleFonts.cabinSketch(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold)),
                  ]
                ],
              ),
            ),
            Text(widget.artist, style: GoogleFonts.cabinSketch(color: Colors.pinkAccent, fontSize: 14)),
          ],
        ),
        centerTitle: true,
      ),
      body: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 24.0),
        child: ListView.builder(
          itemCount: widget.lyricsLines.length,
          padding: const EdgeInsets.symmetric(vertical: 40.0),
          itemBuilder: (context, index) {
            final isActive = index == _currentLyricIndex;
            final text = widget.lyricsLines[index]["text"] as String;

            return Padding(
              padding: const EdgeInsets.symmetric(vertical: 8.0),
              child: Text(
                text,
                textAlign: TextAlign.center,
                style: GoogleFonts.cabinSketch(
                  color: isActive ? Colors.pinkAccent : Colors.white70,
                  fontSize: isActive ? 28 : 23,
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
                  color: const Color(0xFF000000).withOpacity(0.3),
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
                          enabledBorder: const UnderlineInputBorder(borderSide: BorderSide(color: Colors.white)),
                          focusedBorder: const UnderlineInputBorder(borderSide: BorderSide(color: Colors.pinkAccent)),
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
          borderRadius: BorderRadius.circular(30.0),
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
            onTap: null,
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
              color: const Color(0xFF000000).withOpacity(0.3),
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
                          children: const [
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
                      child: SizedBox(
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
                    icon: const Icon(Icons.close, color: Colors.white),
                    onPressed: () => Navigator.of(context).pop(),
                  ),
                ),
                Positioned(
                  bottom: 16,
                  right: 16,
                  child: FloatingActionButton(
                    onPressed: _showCreatePlaylistDialog,
                    shape: const CircleBorder(),
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
                      child: const Icon(Icons.add, color: Colors.white),
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