import 'package:flutter/material.dart';
import 'package:youtube_player_flutter/youtube_player_flutter.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'config.dart'; // This file provides AppConfig.baseUrl
import 'player2.dart'; // Assuming this file contains the SongPlayerPage class
import 'recording_page.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:shared_preferences/shared_preferences.dart'; // For getting user ID
import 'login.dart';
import 'dart:ui'; // For the blur effect

class SongDetailPage extends StatefulWidget {
  final String sid;

  const SongDetailPage({
    Key? key,
    required this.sid,
  }) : super(key: key);

  @override
  State<SongDetailPage> createState() => _SongDetailPageState();
}

class _SongDetailPageState extends State<SongDetailPage> {
  YoutubePlayerController? _controller;
  Map<String, dynamic>? _songDetails;
  bool _isLoading = true;
  bool _isFetchingVideoId = false;

  List<Map<String, String>> _relatedSongs = [];
  bool _isRelatedLoading = true;
  bool _isLyricsExpanded = false;

  static const String youtubeApiKey = 'AIzaSyA0sMAaxPr_QMDzo1TunF2a68QQAfKTpMs';

  bool _isFavourite = false;
  String? _currentUserId;

  @override
  void initState() {
    super.initState();
    _loadInitialData();
  }

  @override
  void deactivate() {
    _controller?.pause();
    super.deactivate();
  }

  @override
  void dispose() {
    _controller?.dispose();
    super.dispose();
  }

  Future<void> _loadInitialData() async {
    final prefs = await SharedPreferences.getInstance();
    if (mounted) {
      setState(() {
        _currentUserId = prefs.getString('user_id');
      });
    }
    await _fetchSongDetails();
    await _checkFavouriteStatus();
  }

  Future<void> _checkFavouriteStatus() async {
    if (_currentUserId == null || widget.sid.isEmpty) {
      if (mounted) setState(() => _isFavourite = false);
      return;
    }

    try {
      final res = await http.get(Uri.parse(
          "${AppConfig.baseUrl}checkFavourite.php?user_id=$_currentUserId&song_id=${widget.sid}"));

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

  Future<void> _toggleFavourite() async {
    if (_currentUserId == null || widget.sid.isEmpty) {
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
          'song_id': widget.sid,
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
                    // --- MODIFICATION 1 ---
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
            songId: widget.sid,
          );
        },
      );
    }
  }

  // --- 1. NEW FUNCTION ADDED ---
  // This function shows the login popup
  void _showLoginRequiredDialog() {
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
                        'Please log in to use this feature.', // Generic message
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
  }
  // --- END OF NEW FUNCTION ---

  String _cleanLyrics(String rawLyrics) {
    if (rawLyrics.isEmpty) return 'No lyrics available.';
    final regex = RegExp(r'\[\d{2}:\d{2}(?:\.\d{2})?\]\s*', multiLine: true);
    String cleaned = rawLyrics.replaceAll(regex, '');
    cleaned = cleaned.replaceAll('\r\n', '\n').replaceAll(RegExp(r'\n\n+'), '\n\n');
    return cleaned.trim();
  }

  Future<void> _fetchSongDetails() async {
    final url = Uri.parse("${AppConfig.baseUrl}getSongDetails.php?sid=${widget.sid}");
    try {
      final response = await http.get(url);
      if (response.statusCode == 200) {
        final Map<String, dynamic> fetchedData = jsonDecode(response.body);

        if (fetchedData.containsKey('error') || fetchedData['sid'] == null) {
          setState(() {
            _songDetails = null;
            _isLoading = false;
          });
          if (_isRelatedLoading) setState(() => _isRelatedLoading = false);
          return;
        }

        List<String> singersList = [];
        if (fetchedData['singers'] is String) {
          final String singersString = fetchedData['singers'] ?? 'Unknown Singer';
          singersList = singersString.split(',').map((s) => s.trim()).toList();
        } else if (fetchedData['singers'] is List) {
          singersList = List<dynamic>.from(fetchedData['singers'])
              .map((s) => s is Map ? s['name']?.toString() ?? 'Unknown' : s.toString())
              .toList();
        } else if (fetchedData['singer'] is String){
          final String singersString = fetchedData['singer'] ?? 'Unknown Singer';
          singersList = singersString.split(',').map((s) => s.trim()).toList();
        } else {
          singersList = ['Unknown Singer'];
        }

        List<String> genresList = [];
        if (fetchedData['genres'] is String) {
          final String genresString = fetchedData['genres'] ?? '';
          genresList = genresString.split(',').map((s) => s.trim()).where((s) => s.isNotEmpty).toList();
        } else if (fetchedData['genres'] is List) {
          genresList = List<dynamic>.from(fetchedData['genres'])
              .map((g) => g is Map ? g['name']?.toString() ?? '' : g.toString())
              .where((s) => s.isNotEmpty)
              .toList();
        }

        final String languagesString = fetchedData['languages'] ?? '';
        final List<String> languagesList = languagesString.split(',').map((s) => s.trim()).where((s) => s.isNotEmpty).toList();

        setState(() {
          _songDetails = {
            'sid': fetchedData['sid'],
            'name': fetchedData['name'],
            'image': fetchedData['image'],
            'poster': fetchedData['poster'],
            'singers': singersList,
            'genres': genresList,
            'languages': languagesList,
            'lyrics': fetchedData['lyrics'] ?? 'No lyrics available.',
            'videoId': null,
          };
          _isLoading = false;
        });
        _fetchRelatedSongsBySid(widget.sid);
      } else {
        setState(() {
          _songDetails = null;
          _isLoading = false;
          if (_isRelatedLoading) setState(() => _isRelatedLoading = false);
        });
      }
    } catch (e) {
      print("Error fetching song details: $e");
      setState(() {
        _songDetails = null;
        _isLoading = false;
        if (_isRelatedLoading) setState(() => _isRelatedLoading = false);
      });
    }
  }


  Future<void> _fetchRelatedSongsBySid(String sid) async {
    setState(() => _isRelatedLoading = true);
    final url = Uri.parse("${AppConfig.baseUrl}getRelatedSongs.php?sid=$sid");
    try {
      final response = await http.get(url);
      if (response.statusCode == 200) {
        final List<dynamic> fetchedData = jsonDecode(response.body);
        final List<Map<String, String>> songs = fetchedData.map((song) {
          return {
            'name': song['name']?.toString() ?? 'Unknown',
            'image': song['image']?.toString() ?? '',
            'artist': song['singer']?.toString() ?? 'Unknown',
            'sid': song['sid']?.toString() ?? '',
          };
        }).where((song) => song['sid']!.isNotEmpty).toList();
        setState(() {
          _relatedSongs = songs;
          _isRelatedLoading = false;
        });
      } else {
        throw Exception('Failed to load related songs. Status: ${response.statusCode}');
      }
    } catch (e) {
      print("Error fetching related songs: $e");
      setState(() => _isRelatedLoading = false);
    }
  }

  Future<void> _fetchYouTubeVideoId(String songName, String singers) async {
    final query = "$songName $singers official music video";
    final url = Uri.parse(
      'https://www.googleapis.com/youtube/v3/search?part=snippet&q=${Uri.encodeComponent(query)}&type=video&maxResults=1&key=$youtubeApiKey',
    );
    try {
      final response = await http.get(url);
      if (response.statusCode == 200) {
        final responseBody = jsonDecode(response.body);
        final videoId = responseBody['items']?[0]?['id']?['videoId'];
        if (videoId != null) {
          _initializePlayer(videoId);
        } else {
          setState(() => _isFetchingVideoId = false);
        }
      } else {
        setState(() => _isFetchingVideoId = false);
      }
    } catch (e) {
      setState(() => _isFetchingVideoId = false);
    }
  }

  void _initializePlayer(String videoId) {
    setState(() {
      _controller = YoutubePlayerController(
        initialVideoId: videoId,
        flags: const YoutubePlayerFlags(autoPlay: true, mute: false),
      );
      if (_songDetails != null) {
        _songDetails!['videoId'] = videoId;
      }
      _isFetchingVideoId = false;
    });
  }

  Future<void> _playVideo() async {
    if (_songDetails == null || _isLoading) return;
    if (_songDetails!['videoId'] != null) {
      if (_controller != null) {
        if (!_controller!.value.isPlaying) {
          _controller!.play();
        }
      } else {
        _initializePlayer(_songDetails!['videoId']!);
      }
      return;
    }
    if (!_isFetchingVideoId) {
      setState(() => _isFetchingVideoId = true);
      final songName = _songDetails!['name'] ?? '';
      final singers = (_songDetails!['singers'] as List<dynamic>?)?.cast<String>().join(', ') ?? '';
      if (songName.isNotEmpty) {
        await _fetchYouTubeVideoId(songName, singers);
      } else {
        setState(() => _isFetchingVideoId = false);
      }
    }
  }

  Future<void> _addHistoryRecord() async {
    // Only save history if the user is logged in
    if (_currentUserId == null) {
      print("User not logged in. Skipping history.");
      return;
    }

    try {
      // We use POST, which is what your history.php script expects
      final response = await http.post(
        Uri.parse("${AppConfig.baseUrl}history.php"),
        body: {
          'user_id': _currentUserId!, // We know it's not null here
          'song_id': widget.sid,
        },
      );

      if (response.statusCode == 200) {
        // You can check the response if you want
        print('History record response: ${response.body}');
      } else {
        // Log server errors
        print('Failed to save history. Status: ${response.statusCode}');
      }
    } catch (e) {
      // Log network or other errors
      print('Error saving history: $e');
    }
  }


  @override
  Widget build(BuildContext context) {
    if (_isLoading) {
      return const Scaffold(
        backgroundColor: Colors.black,
        body: Center(child: CircularProgressIndicator(color: Colors.pinkAccent)),
      );
    }
    if (_songDetails == null) {
      return const Scaffold(
        backgroundColor: Colors.black,
        body: Center(child: Text('Failed to load song details.', style: TextStyle(color: Colors.white))),
      );
    }
    final songName = _songDetails!['name'] ?? 'Unknown Song';
    final singers = (_songDetails!['singers'] as List<dynamic>?)?.cast<String>() ?? ['Unknown Singer'];
    final genres = (_songDetails!['genres'] as List<dynamic>?)?.cast<String>() ?? [];
    final languages = (_songDetails!['languages'] as List<dynamic>?)?.cast<String>() ?? [];

    final rawLyrics = _songDetails!['lyrics'] ?? 'No lyrics available.';
    final cleanedLyrics = _cleanLyrics(rawLyrics);

    return Scaffold(
      backgroundColor: Colors.black,
      body: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            const SizedBox(height: 20),
            _buildVideoPlayer(),
            const SizedBox(height: 20),
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _buildSongInfoRow(songName, singers, genres, languages),
                  const SizedBox(height: 20),
                  _buildDetailsCard(cleanedLyrics),
                  const SizedBox(height: 20),
                  _buildButtons(),
                  const SizedBox(height: 20),
                  _buildTabBar(),
                  const SizedBox(height: 10),
                  _buildTrendingSongsSection(_relatedSongs, _isRelatedLoading),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  // --- 2. MODIFIED: _buildButtons widget ---
  Widget _buildButtons() {
    final playItTextStyle = GoogleFonts.cabinSketch(
      color: Colors.white,
      fontWeight: FontWeight.bold,
      fontSize: 20,
    );
    final pinkTextStyle = playItTextStyle.copyWith(color: Colors.pinkAccent);

    return Row(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        Expanded(
          child: Container(
            decoration: BoxDecoration(
              gradient: LinearGradient(
                colors: [
                  Colors.pinkAccent,
                  Colors.blueAccent.withOpacity(0.8),
                ],
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              ),
              borderRadius: BorderRadius.circular(30),
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
            child: ElevatedButton.icon(
              onPressed: () {
                // --- MODIFICATION START ---
                if (_currentUserId == null) {
                  _showLoginRequiredDialog(); // Show popup if not logged in
                } else {
                  // This is the original code, now in the 'else' block
                  _addHistoryRecord();

                  final Map<String, dynamic> currentSongData = {
                    'sid': _songDetails?['sid']?.toString() ?? widget.sid,
                    'name': _songDetails?['name']?.toString() ?? 'Song',
                    'image': _songDetails?['image']?.toString() ?? '',
                  };

                  final List<Map<String, dynamic>> relatedSongsList =
                  _relatedSongs.map((song) {
                    return {
                      'sid': song['sid'],
                      'name': song['name'],
                      'image': song['image'],
                    };
                  }).toList();

                  final List<Map<String, dynamic>> fullPlaylist = [
                    currentSongData,
                    ...relatedSongsList
                  ];

                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (context) => SongPlayerPage(
                          songList: fullPlaylist,
                          initialIndex: 0
                      ),
                    ),
                  );
                }
                // --- MODIFICATION END ---
              },
              icon: const Icon(
                  Icons.play_arrow_rounded,
                  color: Colors.white,
                  size: 33
              ),
              label: RichText(
                  text: TextSpan(
                      children: [
                        TextSpan(text: 'Play', style: playItTextStyle),
                        TextSpan(text: 'IT', style: pinkTextStyle),
                      ]
                  )
              ),
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.transparent,
                shadowColor: Colors.transparent,
                padding: const EdgeInsets.symmetric(vertical: 10),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(30)),
                elevation: 0,
              ),
            ),
          ),
        ),
        const SizedBox(width: 15),
        Expanded(
          child: Container(
            decoration: BoxDecoration(
              gradient: LinearGradient(
                colors: [
                  Colors.pinkAccent,
                  Colors.blueAccent.withOpacity(0.8),
                ],
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              ),
              borderRadius: BorderRadius.circular(30),
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
            child: Padding(
              padding: const EdgeInsets.all(3.0),
              child: Container(
                decoration: BoxDecoration(
                  color: Colors.black,
                  borderRadius: BorderRadius.circular(27),
                ),
                child: InkWell(
                  onTap: () {
                    // --- MODIFICATION START ---
                    if (_currentUserId == null) {
                      _showLoginRequiredDialog(); // Show popup if not logged in
                    } else {
                      // This is the original code, now in the 'else' block
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (context) => RecordingPage(sid: widget.sid),
                        ),
                      );
                    }
                    // --- MODIFICATION END ---
                  },
                  borderRadius: BorderRadius.circular(27),
                  child: Padding(
                    padding: const EdgeInsets.symmetric(vertical: 4.0),
                    child: Center(
                      child: Image.asset(
                        'assets/logo3.jpg',
                        height: 40,
                        fit: BoxFit.contain,
                      ),
                    ),
                  ),
                ),
              ),
            ),
          ),
        ),
      ],
    );
  }
  // --- END OF MODIFICATION ---


  Widget _buildSongInfoRow(String songName, List<String> singers, List<String> genres, List<String> languages) {
    final imageUrl = _songDetails?['image'];

    final nameStyle = GoogleFonts.cabinSketch(
      fontSize: 20,
      fontWeight: FontWeight.bold,
      color: Colors.white,
    );

    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        ClipRRect(
          borderRadius: BorderRadius.circular(10),
          child: imageUrl != null && imageUrl.isNotEmpty
              ? FadeInImage.assetNetwork(
            placeholder: 'assets/placeholder.png',
            image: imageUrl,
            fit: BoxFit.cover,
            width: 100,
            height: 100,
            imageErrorBuilder: (context, error, stackTrace) {
              return Image.asset('assets/placeholder.png', fit: BoxFit.cover, width: 100, height: 100);
            },
          ) : Image.asset('assets/placeholder.png', width: 100, height: 100, fit: BoxFit.cover),
        ),
        const SizedBox(width: 25),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              RichText(
                text: TextSpan(
                  children: [
                    if (songName.isNotEmpty) ...[
                      TextSpan(
                        text: songName[0],
                        style: nameStyle.copyWith(color: Colors.pinkAccent),
                      ),
                      if (songName.length > 1)
                        TextSpan(
                          text: songName.substring(1),
                          style: nameStyle,
                        ),
                    ] else ...[
                      TextSpan(text: 'Unknown Song', style: nameStyle),
                    ]
                  ],
                ),
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
              ),
              const SizedBox(height: 4),
              _buildHeaderSectionContent(singers),
              const SizedBox(height: 4),
              _buildInfoSection(genres, languages),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildVideoPlayer() {
    return Column(
      children: [
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 10.0, vertical: 8.0),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              IconButton(icon: const Icon(Icons.arrow_back_ios, color: Colors.white, size: 24), onPressed: () => Navigator.pop(context)),
              Image.asset('assets/logo3.jpg', height: 40),
              Row(
                children: [
                  IconButton(
                      onPressed: _toggleFavourite,
                      icon: Icon(
                        _isFavourite ? Icons.favorite : Icons.favorite_border,
                        color: Colors.redAccent,
                        size: 24,
                      )
                  ),
                  IconButton(
                      onPressed: _showPlaylistDialog,
                      icon: const Icon(
                        Icons.playlist_add,
                        color: Colors.blueAccent,
                        size: 24,
                      )
                  ),
                ],
              ),
            ],
          ),
        ),
        _controller != null
            ? YoutubePlayerBuilder(
            player: YoutubePlayer(controller: _controller!),
            builder: (context, player) => player
        )
            : Container(
          height: 250,
          color: Colors.black,
          child: Stack(
            alignment: Alignment.center,
            children: [
              if (_songDetails?['poster'] != null && _songDetails!['poster'].isNotEmpty)
                FadeInImage.assetNetwork(
                    placeholder: 'assets/placeholder.png',
                    image: _songDetails!['poster']!,
                    fit: BoxFit.cover,
                    width: double.infinity,
                    height: double.infinity,
                    imageErrorBuilder: (context, error, stackTrace) {
                      return Image.asset('assets/placeholder.png', fit: BoxFit.cover, width: double.infinity, height: double.infinity);
                    }
                )
              else
                Image.asset('assets/placeholder.png', fit: BoxFit.cover, width: double.infinity, height: double.infinity),

              if (_isFetchingVideoId)
                const CircularProgressIndicator(color: Colors.pinkAccent)
              else
                GestureDetector(
                  onTap: _playVideo,
                  child: Container(
                    width: 65,
                    height: 65,
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
                      Icons.play_arrow_rounded,
                      color: Colors.white,
                      size: 40,
                    ),
                  ),
                ),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildHeaderSectionContent(List<String> singers) {
    final singersText = singers.join(' / ');
    final labelStyle = GoogleFonts.cabinSketch(color: Colors.pinkAccent, fontSize: 14, fontWeight: FontWeight.bold);
    final contentStyle = GoogleFonts.cabinSketch(color: Colors.white70, fontSize: 14);

    return RichText(
      text: TextSpan(
        children: [
          TextSpan(text: 'Singer: ', style: labelStyle),
          TextSpan(text: singersText, style: contentStyle),
        ],
      ),
      maxLines: 1,
      overflow: TextOverflow.ellipsis,
    );
  }

  Widget _buildInfoSection(List<String> genres, List<String> languages) {
    final genreText = genres.isNotEmpty ? genres.join(' / ') : 'Not Available';
    final languageText = languages.join(' / ');

    final labelStyle = GoogleFonts.cabinSketch(color: Colors.pinkAccent, fontSize: 14, fontWeight: FontWeight.bold);
    final contentStyle = GoogleFonts.cabinSketch(color: Colors.white60, fontSize: 14);

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        RichText(
          text: TextSpan(
            children: [
              TextSpan(text: 'Genre: ', style: labelStyle),
              TextSpan(text: genreText, style: contentStyle),
            ],
          ),
          maxLines: 1,
          overflow: TextOverflow.ellipsis,
        ),
        if (languageText.isNotEmpty)
          Padding(
            padding: const EdgeInsets.only(top: 4.0),
            child: RichText(
              text: TextSpan(
                children: [
                  TextSpan(text: 'Language: ', style: labelStyle),
                  TextSpan(text: languageText, style: contentStyle),
                ],
              ),
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
            ),
          ),
      ],
    );
  }

  Widget _buildTabBar() {
    return Row(mainAxisAlignment: MainAxisAlignment.spaceAround, children: [_buildTab("Related Songs", true), _buildTab("Singers", false)]);
  }

  Widget _buildTab(String title, bool isActive) {
    final baseStyle = GoogleFonts.cabinSketch(
      fontWeight: FontWeight.bold,
      fontSize: 16,
    );
    final activeColor = Colors.white;
    final inactiveColor = Colors.white60;
    final firstLetterColor = Colors.pinkAccent;

    final firstLetterStyle = baseStyle.copyWith(color: firstLetterColor);
    final restOfWordStyle = baseStyle.copyWith(color: isActive ? activeColor : inactiveColor);

    return Column(
      children: [
        RichText(
            text: TextSpan(
                children: [
                  if (title.isNotEmpty) ...[
                    TextSpan(
                      text: title[0],
                      style: firstLetterStyle,
                    ),
                    if (title.length > 1)
                      TextSpan(
                        text: title.substring(1),
                        style: restOfWordStyle,
                      ),
                  ] else ...[
                    TextSpan(text: '', style: restOfWordStyle),
                  ]
                ]
            )
        ),
        if (isActive) Container(margin: const EdgeInsets.only(top: 5), height: 3, width: 30, decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(1.5))),
      ],
    );
  }

  Widget _buildDetailsCard(String lyrics) {
    final int? maxLines = _isLyricsExpanded ? null : 3;

    final baseTextStyle = GoogleFonts.cabinSketch(
      fontWeight: FontWeight.bold,
      fontSize: 14,
    );
    final pinkStyle = baseTextStyle.copyWith(color: Colors.pinkAccent);
    final whiteStyle = baseTextStyle.copyWith(color: Colors.white);

    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(color: Colors.white.withOpacity(0.05), borderRadius: BorderRadius.circular(16)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            'Lyrics',
            style: GoogleFonts.cabinSketch(
              color: Colors.pinkAccent,
              fontSize: 16,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 8),
          Text(lyrics, style: const TextStyle(color: Colors.white70, fontSize: 14), maxLines: maxLines, overflow: maxLines != null ? TextOverflow.ellipsis : TextOverflow.clip),
          const SizedBox(height: 10),
          Align(
            alignment: Alignment.centerRight,
            child: TextButton(
              onPressed: () => setState(() => _isLyricsExpanded = !_isLyricsExpanded),
              child: RichText(
                text: _isLyricsExpanded
                    ? TextSpan( // "Show Less <"
                  children: [
                    TextSpan(text: 'Show Less ', style: pinkStyle),
                    TextSpan(text: '<', style: whiteStyle),
                  ],
                )
                    : TextSpan( // "More >"
                  children: [
                    TextSpan(text: 'More ', style: pinkStyle),
                    TextSpan(text: '>', style: whiteStyle),
                  ],
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildTrendingSongsSection(List<Map<String, String>> songs, bool isLoading) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const SizedBox(height: 10),
        isLoading
            ? const Center(child: CircularProgressIndicator(color: Colors.pinkAccent))
            : songs.isEmpty
            ? const Center(child: Text('No related songs found.', style: TextStyle(color: Colors.white70)))
            : SizedBox(
          height: 150,
          child: ListView.builder(
            scrollDirection: Axis.horizontal,
            itemCount: songs.length,
            itemBuilder: (context, index) {
              final song = songs[index];
              return Padding(
                padding: const EdgeInsets.only(right: 12.0),
                child: GestureDetector(
                  onTap: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (context) => SongDetailPage(sid: song['sid'] ?? ''),
                      ),
                    ).then((_) {
                    });
                  },
                  child: Column(
                    children: [
                      ClipRRect(
                        borderRadius: BorderRadius.circular(10),
                        child: song['image'] != null && song['image']!.isNotEmpty
                            ? FadeInImage.assetNetwork(
                          placeholder: 'assets/placeholder.png',
                          image: song['image']!,
                          width: 100,
                          height: 100,
                          fit: BoxFit.cover,
                          imageErrorBuilder: (context, error, stackTrace) => Image.asset('assets/placeholder.png', width: 100, height: 100, fit: BoxFit.cover),
                        )
                            : Image.asset('assets/placeholder.png', width: 100, height: 100, fit: BoxFit.cover),
                      ),
                      const SizedBox(height: 5),
                      SizedBox(
                        width: 100,
                        child: Text(
                            song['name'] ?? 'Unknown',
                            style: const TextStyle(color: Colors.white, fontSize: 14),
                            textAlign: TextAlign.center, maxLines: 1, overflow: TextOverflow.ellipsis),
                      ),
                    ],
                  ),
                ),
              );
            },
          ),
        ),
      ],
    );
  }
}

//
// --- 4. THIS ENTIRE WIDGET IS MODIFIED ---
//
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

  // --- THIS IS THE MODIFIED FUNCTION ---
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
              child: Container( // No gradient border, just the content container
                width: 300,
                decoration: BoxDecoration(
                  color: Color(0xFF000000).withOpacity(0.3),
                  borderRadius: BorderRadius.circular(16.0),
                  // --- MODIFICATION 2 ---
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
                    // --- MODIFIED BUTTON LAYOUT ---
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
                    // --- END MODIFIED BUTTON LAYOUT ---
                  ],
                ),
              ),
            ),
          ),
        );
      },
    );
  }

  // --- NEW HELPER WIDGET FOR GRADIENT BUTTONS ---
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

  // --- THIS IS THE MODIFIED FUNCTION ---
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
            // --- IMAGE IS ON THE LEFT ---
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
            // --- ICON BUTTON IS ON THE RIGHT ---
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
  // --- END MODIFIED FUNCTION ---

  @override
  Widget build(BuildContext context) {
    return Dialog(
      backgroundColor: Colors.transparent,
      elevation: 0,
      child: ClipRRect(
        borderRadius: BorderRadius.circular(16.0),
        child: BackdropFilter(
          filter: ImageFilter.blur(sigmaX: 5.0, sigmaY: 5.0),
          child: Container( // No gradient border, just the content container
            width: 300,
            height: 400,
            decoration: BoxDecoration(
              color: Color(0xFF000000).withOpacity(0.3),
              borderRadius: BorderRadius.circular(16.0),
              // --- MODIFICATION 3 ---
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