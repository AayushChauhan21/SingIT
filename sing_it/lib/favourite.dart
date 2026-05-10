import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'config.dart'; // Your AppConfig
import 'player2.dart'; // Import your player page
import 'package:shared_preferences/shared_preferences.dart'; // For user_id

class FavouritePage extends StatefulWidget {
  const FavouritePage({Key? key}) : super(key: key);

  @override
  _FavouritePageState createState() => _FavouritePageState();
}

class _FavouritePageState extends State<FavouritePage> {
  int _songCount = 0;
  List<Map<String, dynamic>> _favouriteSongs = [];
  bool _isLoading = true;
  String? _error;

  // --- 1. ADDED: To store the user's ID ---
  String? _currentUserId;

  @override
  void initState() {
    super.initState();
    _fetchFavouriteSongs();
  }

  Future<void> _fetchFavouriteSongs() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      final prefs = await SharedPreferences.getInstance();
      final userId = prefs.getString('user_id');

      // --- Store the User ID for later ---
      setState(() {
        _currentUserId = userId;
      });

      if (userId == null || userId.isEmpty) {
        throw Exception('You must be logged in to see your favourites.');
      }

      final url = Uri.parse(
          "${AppConfig.baseUrl}getFavourites.php?user_id=$userId");

      final response = await http.get(url);
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);

        if (data is Map && data.containsKey('error')) {
          throw Exception(data['error']);
        }
        if (data is Map && data.containsKey('error_songs')) {
          throw Exception(data['error_songs']);
        }

        _songCount = data['songs_count'] ?? 0;

        if (data['songs'] != null) {
          _favouriteSongs = List<Map<String, dynamic>>.from(data['songs']);
        } else {
          _favouriteSongs = [];
        }

        setState(() {
          _isLoading = false;
        });

      } else {
        throw Exception(
            'Failed to load songs (Status code: ${response.statusCode})');
      }
    } catch (e) {
      print("Error fetching favourite songs: $e");
      setState(() {
        _error = e.toString().replaceAll("Exception: ", "");
        _isLoading = false;
      });
    }
  }

  // --- 2. ADDED: Function to call favourit.php and update UI ---
  Future<void> _removeFromFavourites(String songId) async {
    if (_currentUserId == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Error: Not logged in.')),
      );
      return;
    }

    try {
      final response = await http.post(
        Uri.parse("${AppConfig.baseUrl}favourit.php"),
        body: {
          'user_id': _currentUserId,
          'song_id': songId,
        },
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['status'] == 'success' && data['action'] == 'deleted') {
          // Success! Remove the song from the local list
          setState(() {
            _favouriteSongs.removeWhere((song) => song['sid'] == songId);
            _songCount = _favouriteSongs.length;
          });
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text('Removed from favourites',
                  style: GoogleFonts.cabinSketch(color: Colors.white)),
              backgroundColor: Colors.green,
            ),
          );
        } else {
          // Show PHP error
          throw Exception(data['message'] ?? 'Failed to remove favourite');
        }
      } else {
        // Show server error
        throw Exception('Server error: ${response.statusCode}');
      }
    } catch (e) {
      print("Error removing favourite: $e");
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Error: ${e.toString()}',
              style: GoogleFonts.cabinSketch(color: Colors.white)),
          backgroundColor: Colors.redAccent,
        ),
      );
    }
  }


  @override
  Widget build(BuildContext context) {
    final bool canPlaySongs = !_isLoading && _favouriteSongs.isNotEmpty;

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
          _buildHeader(canPlaySongs), // Header
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16.0),
            child: _buildSongsSection(),
          ),
          SizedBox(height: 80), // Padding
        ],
      ),
    );
  }

  Widget _buildHeader(bool canPlaySongs) {
    final String headerName = 'Favourites';
    final String songCountText = (_songCount == 1) ? '1 Song' : '$_songCount Songs';

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
          Image.asset(
            'assets/favourite.jpeg', // Using your static asset
            fit: BoxFit.cover,
            errorBuilder: (c, e, s) => Image.asset(
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
                      TextSpan(
                        text: headerName[0],
                        style: titleStyle.copyWith(color: Colors.pinkAccent),
                      ),
                      TextSpan(
                        text: headerName.substring(1),
                        style: titleStyle,
                      ),
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
    if (_favouriteSongs.isEmpty) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.symmetric(vertical: 40.0),
          child: Text(
            'You haven\'t favourited any songs yet.',
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
      itemCount: _favouriteSongs.length,
      itemBuilder: (context, index) {
        final song = _favouriteSongs[index];
        return _buildSongTile(song, index);
      },
      separatorBuilder: (context, index) {
        return SizedBox(height: 8);
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
                  SizedBox(height: 4),
                  Text(
                    singerName,
                    style: singerNameStyle,
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                ],
              ),
              subtitle: null,

              // --- 3. ADDED: The remove button ---
              trailing: IconButton(
                icon: Icon(
                  Icons.favorite, // Solid heart, as it's already a favourite
                  color: Colors.redAccent.withOpacity(0.8),
                ),
                onPressed: () {
                  // Call the remove function
                  _removeFromFavourites(item['sid'].toString());
                },
              ),
              // --- END OF ADDITION ---

              onTap: () {
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (context) => SongPlayerPage(
                      songList: _favouriteSongs,
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

  Widget _buildPlayAllButton() {
    return GestureDetector(
      onTap: () {
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (context) => SongPlayerPage(
              songList: _favouriteSongs,
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