import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'config.dart'; // Your AppConfig
// import 'song_detail.dart'; // No longer needed for song tile tap
import 'player2.dart'; // <-- Correct import for your player page

class ArtistDetailPage extends StatefulWidget {
  final String artistId;
  final String? initialArtistName; // Optional: Show name while loading

  const ArtistDetailPage({
    Key? key,
    required this.artistId,
    this.initialArtistName,
  }) : super(key: key);

  @override
  _ArtistDetailPageState createState() => _ArtistDetailPageState();
}

class _ArtistDetailPageState extends State<ArtistDetailPage> {
  Map<String, dynamic>? _artistDetails;
  List<Map<String, dynamic>> _artistSongs = [];
  bool _isLoading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _fetchArtistData();
  }

  Future<void> _fetchArtistData() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });
    final url = Uri.parse(
        "${AppConfig.baseUrl}getArtistDetails.php?arid=${widget.artistId}");

    try {
      final response = await http.get(url);
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);

        if (data is Map && data.containsKey('error')) {
          throw Exception(data['error']);
        }

        if (data['details'] == null) {
          throw Exception('Artist details not found in response.');
        }

        setState(() {
          _artistDetails = data['details'];
          if (data['songs'] != null) {
            _artistSongs = List<Map<String, dynamic>>.from(data['songs']);
          } else {
            _artistSongs = [];
          }
          _isLoading = false;
        });
      } else {
        throw Exception(
            'Failed to load artist details (Status code: ${response.statusCode})');
      }
    } catch (e) {
      print("Error fetching artist details: $e");
      setState(() {
        _error = e.toString();
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final bool canPlaySongs = !_isLoading && _artistSongs.isNotEmpty;

    return Scaffold(
      backgroundColor: Colors.black,
      appBar: AppBar(
        title: Text(
          'Singer',
          style: GoogleFonts.cabinSketch(color: Colors.white),
        ),
        centerTitle: true,
        backgroundColor: Colors.black,
        elevation: 0,
        iconTheme: IconThemeData(color: Colors.white), // Back button color
      ),
      body: _buildBody(),
      floatingActionButton: canPlaySongs ? _buildPlayAllButton() : null,
    );
  }

  Widget _buildBody() {
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
            style: GoogleFonts.cabinSketch(color: Colors.redAccent),
            textAlign: TextAlign.center,
          ),
        ),
      );
    }

    if (_artistDetails == null) {
      return Center(
        child: Text(
          'Artist not found.',
          style: GoogleFonts.cabinSketch(color: Colors.white70),
        ),
      );
    }

    return SingleChildScrollView(
      padding: const EdgeInsets.all(16.0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Center(child: _buildIntroductionSection()),
          SizedBox(height: 20),
          _buildSongsSection(),
          SizedBox(height: 80), // Padding for FAB
        ],
      ),
    );
  }

  Widget _buildIntroductionSection() {
    final details = _artistDetails!;
    final songCount = details['song_count']?.toString() ?? '0';
    final songCountText = (songCount == '1') ? '1 Song' : '$songCount Songs';
    final description =
        details['description']?.toString() ?? 'No description available.';
    final imageUrl = details['image']?.toString() ?? ''; // Use 'photo' key
    final artistName = details['name']?.toString() ?? 'Unknown Artist';

    final nameStyle = GoogleFonts.cabinSketch(
      fontSize: 28,
      fontWeight: FontWeight.bold,
      color: Colors.white,
    );

    return Row(
      crossAxisAlignment: CrossAxisAlignment.end,
      children: [
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              RichText(
                text: TextSpan(
                  children: [
                    if (artistName.isNotEmpty) ...[
                      TextSpan(
                        text: artistName[0], // First letter
                        style:
                        nameStyle.copyWith(color: Colors.pinkAccent), // Pink color
                      ),
                      if (artistName.length > 1)
                        TextSpan(
                          text: artistName.substring(1), // Rest of the name
                          style: nameStyle, // Default white color
                        ),
                    ] else ...[
                      TextSpan(text: 'Unknown Artist', style: nameStyle), // Fallback
                    ]
                  ],
                ),
              ),
              SizedBox(height: 8),
              Text(
                songCountText,
                style: GoogleFonts.cabinSketch(
                    fontSize: 16,
                    color: Colors.pinkAccent, // Changed color
                    fontWeight: FontWeight.bold // Optional: make it bold
                ),
              ),
              SizedBox(height: 16),
              Text(
                description,
                style: TextStyle(
                  fontSize: 14,
                  color: Colors.white.withOpacity(0.8),
                  height: 1.5,
                ),
              ),
            ],
          ),
        ),
        SizedBox(
          width: 200,
          child: FadeInImage.assetNetwork(
            placeholder: 'assets/placeholder.png',
            image: imageUrl,
            fit: BoxFit.contain,
            imageErrorBuilder: (c, e, s) => Image.asset(
              'assets/placeholder.png',
              fit: BoxFit.contain,
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildSongsSection() {
    if (_artistSongs.isEmpty) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.symmetric(vertical: 20.0),
          child: Text(
            'No songs found for this artist.',
            style: GoogleFonts.cabinSketch(color: Colors.white70),
          ),
        ),
      );
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Center(
          child: Text(
            'Songs',
            style: GoogleFonts.cabinSketch(
              fontSize: 22,
              fontWeight: FontWeight.bold,
              color: Colors.pinkAccent,
            ),
          ),
        ),
        SizedBox(height: 5),
        ListView.builder(
          shrinkWrap: true,
          physics: NeverScrollableScrollPhysics(),
          itemCount: _artistSongs.length,
          itemBuilder: (context, index) {
            final song = _artistSongs[index];
            return _buildArtistSongTile(song, index);
          },
        ),
      ],
    );
  }

  // --- MODIFICATIONS BELOW ---

  Widget _buildArtistSongTile(Map<String, dynamic> item, int index) {
    final songName = item['name']?.toString() ?? 'Unknown Song';
    final artistNames = item['artist_names']?.toString() ?? 'Unknown Artist';

    // 1. Define the title style with new font size
    final titleStyle = GoogleFonts.cabinSketch(
      color: Colors.white,
      fontWeight: FontWeight.bold,
      fontSize: 17, // <-- Adjusted font size
    );

    // 2. Define the artist name style with new font size
    final artistStyle = GoogleFonts.cabinSketch(
      color: Colors.pinkAccent,
      fontSize: 14, // <-- Adjusted font size
    );

    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
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
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    text: TextSpan(
                      children: [
                        if (songName.isNotEmpty) ...[
                          TextSpan(
                            text: songName[0], // First letter
                            style: titleStyle.copyWith(color: Colors.pinkAccent),
                          ),
                          if (songName.length > 1)
                            TextSpan(
                              text: songName.substring(1), // Rest of the name
                              style: titleStyle,
                            ),
                        ] else ...[
                          TextSpan(text: 'Unknown Song', style: titleStyle),
                        ]
                      ],
                    ),
                  ),
                  // 4. Manual padding between title and subtitle
                  SizedBox(height: 4),
                  // --- Artist Name ---
                  Text(
                    artistNames,
                    style: artistStyle,
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
                      songList: _artistSongs,
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
        // Pass the full list and index 0
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (context) => SongPlayerPage(
              songList: _artistSongs,
              initialIndex: 0, // <-- Play from the beginning
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