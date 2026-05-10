import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'config.dart'; // Your AppConfig
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

  @override
  void initState() {
    super.initState();
    _fetchCategorySongs();
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

  @override
  Widget build(BuildContext context) {
    final bool canPlaySongs = !_isLoading && _categorySongs.isNotEmpty;

    return Scaffold(
      backgroundColor: Colors.black,
      appBar: AppBar(
        iconTheme: IconThemeData(color: Colors.white),
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
      return Center(
        child: CircularProgressIndicator(color: Colors.pinkAccent),
      );
    }

    if (_error != null) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(20.0),
          child: Text(
            'Error: $_error',
            // --- MODIFIED: Use CabinSketch ---
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
          SizedBox(height: 80), // Padding
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
    final String songCount = _genreInfo?['song_count'] ?? '0';
    final String songCountText = (songCount == '1') ? '1 Song' : '$songCount Songs';

    final titleStyle = GoogleFonts.cabinSketch(
        color: Colors.white,
        fontSize: 32,
        fontWeight: FontWeight.bold,
        shadows: [
          Shadow(color: Colors.black.withOpacity(0.5), blurRadius: 4, offset: Offset(0, 2)),
        ]
    );
    final subtitleStyle = GoogleFonts.cabinSketch(
      color: Colors.pinkAccent,
      fontSize: 14,
      fontWeight: FontWeight.bold,
    );

    return Container(
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
                stops: [0.0, 0.6],
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
                SizedBox(height: 5.0),
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
            // --- MODIFIED: Use CabinSketch ---
            style: GoogleFonts.cabinSketch(color: Colors.white70, fontSize: 18),
            textAlign: TextAlign.center,
          ),
        ),
      );
    }

    return ListView.separated(
      padding: EdgeInsets.zero,
      shrinkWrap: true,
      physics: NeverScrollableScrollPhysics(),
      itemCount: _categorySongs.length,
      itemBuilder: (context, index) {
        final song = _categorySongs[index];
        return _buildSongTile(song, index);
      },
      separatorBuilder: (context, index) {
        return SizedBox(height: 8);
      },
    );
  }

  // --- MODIFICATIONS BELOW ---

  Widget _buildSongTile(Map<String, dynamic> item, int index) {
    final String songName = item['name']?.toString() ?? 'Unknown Song';
    final String singerName = item['singer_name']?.toString() ?? 'Unknown Artist';

    // 1. Define the title style with new font size
    final songNameStyle = GoogleFonts.cabinSketch(
        color: Colors.white,
        fontSize: 17, // <-- Increased font size
        fontWeight: FontWeight.bold
    );
    final firstLetterStyle = songNameStyle.copyWith(color: Colors.pinkAccent);

    // 2. Define the singer name style with new font size
    final singerNameStyle = GoogleFonts.cabinSketch(
        color: Colors.pinkAccent,
        fontSize: 14, // <-- Increased font size
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
            offset: Offset(-3, -3),
          ),
          BoxShadow(
            color: Colors.blueAccent.withOpacity(0.3),
            blurRadius: 6,
            spreadRadius: 1,
            offset: Offset(3, 3),
          ),
        ],
      ),
      child: Padding(
        padding: const EdgeInsets.all(1),
        child: ClipRRect(
          borderRadius: BorderRadius.circular(7),
          child: Container(
            color: Color(0xFF282828),
            child: ListTile(
              // Make sure content fits vertically
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
              // 3. Use a Column in the 'title' property for manual spacing
              title: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  // --- Song Name ---
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
                  // 4. Manual padding between title and subtitle
                  SizedBox(height: 4),
                  // --- Artist Name ---
                  Text(
                    singerName,
                    style: singerNameStyle,
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                ],
              ),
              // 5. Set subtitle to null since we handled it in the title
              subtitle: null,
              onTap: () {
                Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (context) => SongPlayerPage(
                        songList: _categorySongs,
                        initialIndex: index,
                      ),
                    ),
                );
              },
            ),
          ),
        ),
      ),
    );
  }

  // --- END OF MODIFICATIONS ---

  Widget _buildPlayAllButton() {
    return GestureDetector(
      onTap: () {
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (context) => SongPlayerPage(
              songList: _categorySongs,
              initialIndex: 0,
            ),
          ),
        );
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
          size: 35,
        ),
      ),
    );
  }
}