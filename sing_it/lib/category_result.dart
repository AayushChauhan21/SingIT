import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'dart:convert';
import 'dart:ui'; // Required for ImageFilter.blur
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart'; // Required to check login
import 'config.dart'; // Your AppConfig
import 'login.dart'; // Required for Login routing
import 'player2.dart'; // Import your player page

class CategoryResultPage extends StatefulWidget {
  final String categoryId;
  final String categoryName;

  const CategoryResultPage({
    Key? key,
    required this.categoryId,
    required this.categoryName,
  }) : super(key: key);

  @override
  _CategoryResultPageState createState() => _CategoryResultPageState();
}

class _CategoryResultPageState extends State<CategoryResultPage> {
  Map<String, dynamic>? _genreInfo;
  List<Map<String, dynamic>> _categorySongs = [];
  bool _isLoading = true;
  String? _error;
  String? _currentUserId; // Track login status

  @override
  void initState() {
    super.initState();
    _checkLoginStatus();
    _fetchCategorySongs();
  }

  // Retrieve user ID from SharedPreferences
  Future<void> _checkLoginStatus() async {
    final prefs = await SharedPreferences.getInstance();
    setState(() {
      _currentUserId = prefs.getString('user_id');
    });
  }

  Future<void> _fetchCategorySongs() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    final url = Uri.parse(
        "${AppConfig.baseUrl}getSongsByCategory.php?gid=${widget.categoryId}");

    try {
      final response = await http.get(url);
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);

        if (data is Map && data.containsKey('error')) {
          throw Exception(data['error']);
        }

        if (data['genre_info'] != null) {
          _genreInfo = data['genre_info'] as Map<String, dynamic>;
        }

        if (data['songs'] != null) {
          _categorySongs = List<Map<String, dynamic>>.from(data['songs']);
        } else {
          _categorySongs = [];
        }

        setState(() {
          _isLoading = false;
        });

      } else {
        throw Exception(
            'Failed to load songs (Status code: ${response.statusCode})');
      }
    } catch (e) {
      print("Error fetching category songs: $e");
      setState(() {
        _error = e.toString();
        _isLoading = false;
      });
    }
  }

  // The Blur-Effect Login Dialog
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
                        'Please log in to play songs.',
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

  @override
  Widget build(BuildContext context) {
    final bool canPlaySongs = !_isLoading && _categorySongs.isNotEmpty;

    return Scaffold(
      backgroundColor: Colors.black,
      appBar: AppBar(
        iconTheme: const IconThemeData(color: Colors.white),
        title: null,
        centerTitle: true,
        backgroundColor: Colors.transparent,
        elevation: 0,
      ),
      extendBodyBehindAppBar: true,
      body: _buildBody(canPlaySongs),
      floatingActionButton: null,
    );
  }

  Widget _buildBody(bool canPlaySongs) {
    if (_isLoading) {
      return const Center(
        child: CircularProgressIndicator(color: Colors.pinkAccent),
      );
    }

    if (_error != null) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(20.0),
          child: Text(
            'Error: $_error',
            style: GoogleFonts.cabinSketch(color: Colors.redAccent, fontSize: 16),
            textAlign: TextAlign.center,
          ),
        ),
      );
    }

    return SingleChildScrollView(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          _buildGenreHeader(canPlaySongs),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16.0),
            child: _buildSongsSection(),
          ),
          const SizedBox(height: 80), // Padding
        ],
      ),
    );
  }

  Widget _buildGenreHeader(bool canPlaySongs) {
    if (_genreInfo == null) {
      // Return a sized box to maintain layout even if header info is null
      return SizedBox(
          height: 280,
          child: Center(
              child: Text("Loading...",
                  style: GoogleFonts.cabinSketch(color: Colors.white))));
    }

    final String genreName = _genreInfo?['name'] ?? widget.categoryName;
    final String imageUrl = _genreInfo?['image'] ?? '';
    final String songCount = _genreInfo?['song_count']?.toString() ?? '0';
    final String songCountText = (songCount == '1') ? '1 Song' : '$songCount Songs';

    final titleStyle = GoogleFonts.cabinSketch(
        color: Colors.white,
        fontSize: 32,
        fontWeight: FontWeight.bold,
        shadows: [
          Shadow(color: Colors.black.withOpacity(0.5), blurRadius: 4, offset: const Offset(0, 2)),
        ]
    );
    final subtitleStyle = GoogleFonts.cabinSketch(
      color: Colors.pinkAccent,
      fontSize: 14,
      fontWeight: FontWeight.bold,
    );

    return SizedBox(
      height: 280.0,
      child: Stack(
        fit: StackFit.expand,
        children: [
          // 1. Background Image
          FadeInImage.assetNetwork(
            placeholder: 'assets/placeholder.png',
            image: imageUrl,
            fit: BoxFit.cover,
            imageErrorBuilder: (c, e, s) => Image.asset(
              'assets/placeholder.png',
              fit: BoxFit.cover,
            ),
          ),
          // 2. Gradient Overlay
          Container(
            decoration: BoxDecoration(
              gradient: LinearGradient(
                begin: Alignment.bottomCenter,
                end: Alignment.topCenter,
                colors: [
                  Colors.black.withOpacity(0.9),
                  Colors.black.withOpacity(0.0),
                ],
                stops: const [0.0, 0.6],
              ),
            ),
          ),
          // 3. Text Content
          Positioned(
            bottom: 20.0,
            left: 16.0,
            right: 16.0,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                RichText(
                  text: TextSpan(
                    children: [
                      if (genreName.isNotEmpty) ...[
                        TextSpan(
                          text: genreName[0],
                          style: titleStyle.copyWith(color: Colors.pinkAccent),
                        ),
                        if (genreName.length > 1)
                          TextSpan(
                            text: genreName.substring(1),
                            style: titleStyle,
                          ),
                      ] else ...[
                        TextSpan(text: 'Category', style: titleStyle),
                      ]
                    ],
                  ),
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                ),
                const SizedBox(height: 5.0),
                Text(
                  songCountText,
                  style: subtitleStyle,
                ),
              ],
            ),
          ),
          // 4. Play Button
          if (canPlaySongs)
            Positioned(
              bottom: 20.0,
              right: 16.0,
              child: _buildPlayAllButton(),
            ),
        ],
      ),
    );
  }

  Widget _buildSongsSection() {
    if (_categorySongs.isEmpty) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.symmetric(vertical: 40.0),
          child: Text(
            'No songs found for this category.',
            style: GoogleFonts.cabinSketch(color: Colors.white70, fontSize: 18),
            textAlign: TextAlign.center,
          ),
        ),
      );
    }

    return ListView.separated(
      padding: EdgeInsets.zero,
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      itemCount: _categorySongs.length,
      itemBuilder: (context, index) {
        final song = _categorySongs[index];
        return _buildSongTile(song, index);
      },
      separatorBuilder: (context, index) {
        return const SizedBox(height: 8);
      },
    );
  }

  Widget _buildSongTile(Map<String, dynamic> item, int index) {
    final String songName = item['name']?.toString() ?? 'Unknown Song';
    final String singerName = item['singer_name']?.toString() ?? 'Unknown Artist';

    final songNameStyle = GoogleFonts.cabinSketch(
        color: Colors.white,
        fontSize: 17,
        fontWeight: FontWeight.bold
    );
    final firstLetterStyle = songNameStyle.copyWith(color: Colors.pinkAccent);

    final singerNameStyle = GoogleFonts.cabinSketch(
        color: Colors.pinkAccent,
        fontSize: 14,
        fontWeight: FontWeight.bold
    );

    return Container(
      margin: EdgeInsets.zero,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(8),
        gradient: LinearGradient(
          colors: [
            Colors.pinkAccent.withOpacity(0.6),
            Colors.blueAccent.withOpacity(0.6),
          ],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        boxShadow: [
          BoxShadow(
            color: Colors.pinkAccent.withOpacity(0.3),
            blurRadius: 6,
            spreadRadius: 1,
            offset: const Offset(-3, -3),
          ),
          BoxShadow(
            color: Colors.blueAccent.withOpacity(0.3),
            blurRadius: 6,
            spreadRadius: 1,
            offset: const Offset(3, 3),
          ),
        ],
      ),
      child: Padding(
        padding: const EdgeInsets.all(1),
        child: ClipRRect(
          borderRadius: BorderRadius.circular(7),
          child: Container(
            color: const Color(0xFF282828),
            child: ListTile(
              contentPadding:
              const EdgeInsets.symmetric(vertical: 4.0, horizontal: 16.0),
              leading: ClipRRect(
                borderRadius: BorderRadius.circular(4),
                child: FadeInImage.assetNetwork(
                  placeholder: 'assets/placeholder.png',
                  image: item['image']?.toString() ?? '',
                  width: 55,
                  height: 55,
                  fit: BoxFit.cover,
                  imageErrorBuilder: (c, e, s) => Image.asset(
                    'assets/placeholder.png',
                    width: 55,
                    height: 55,
                    fit: BoxFit.cover,
                  ),
                ),
              ),
              title: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  RichText(
                    text: TextSpan(
                        children: [
                          TextSpan(
                              text: songName.isNotEmpty ? songName[0] : '',
                              style: firstLetterStyle
                          ),
                          if (songName.length > 1)
                            TextSpan(
                                text: songName.substring(1),
                                style: songNameStyle
                            ),
                        ]
                    ),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                  const SizedBox(height: 4),
                  Text(
                    singerName,
                    style: singerNameStyle,
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                ],
              ),
              subtitle: null,
              onTap: () {
                // --- LOGIN CHECK HERE ---
                if (_currentUserId == null) {
                  _showLoginRequiredDialog();
                } else {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (context) => SongPlayerPage(
                        songList: _categorySongs,
                        initialIndex: index,
                      ),
                    ),
                  );
                }
              },
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildPlayAllButton() {
    return GestureDetector(
      onTap: () {
        // --- LOGIN CHECK HERE ---
        if (_currentUserId == null) {
          _showLoginRequiredDialog();
        } else {
          Navigator.push(
            context,
            MaterialPageRoute(
              builder: (context) => SongPlayerPage(
                songList: _categorySongs,
                initialIndex: 0,
              ),
            ),
          );
        }
      },
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
        child: const Icon(
          Icons.play_arrow_rounded,
          color: Colors.white,
          size: 35,
        ),
      ),
    );
  }
}