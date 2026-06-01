import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'config.dart'; // Your AppConfig
import 'package:shared_preferences/shared_preferences.dart'; // For user_id
import 'playlist_detail.dart'; // Navigate to this page
import 'dart:ui'; // For the blur effect

class PlaylistPage extends StatefulWidget {
  const PlaylistPage({Key? key}) : super(key: key);

  @override
  _PlaylistPageState createState() => _PlaylistPageState();
}

class _PlaylistPageState extends State<PlaylistPage> {
  List<Map<String, dynamic>> _playlists = [];
  bool _isLoading = true;
  String? _error;

  String? _currentUserId;
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
      final prefs = await SharedPreferences.getInstance();
      final userId = prefs.getString('user_id');

      _currentUserId = userId;

      if (userId == null || userId.isEmpty) {
        throw Exception('You must be logged in to see your playlists.');
      }

      final url =
      Uri.parse("${AppConfig.baseUrl}getPlaylists.php?user_id=$userId");

      final response = await http.get(url);
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);

        if (data is Map && data.containsKey('error')) {
          throw Exception(data['error']);
        }

        if (data is List) {
          _playlists = List<Map<String, dynamic>>.from(data);
        } else {
          _playlists = [];
        }

        setState(() {
          _isLoading = false;
        });

      } else {
        throw Exception(
            'Failed to load playlists (Status code: ${response.statusCode})');
      }
    } catch (e) {
      print("Error fetching playlists: $e");
      setState(() {
        _error = e.toString();
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

  Future<void> _showCreatePlaylistDialog() async {
    _newPlaylistController.clear();
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
                  border: Border.all(color: Colors.pinkAccent, width: 1.5),
                ),
                child: Column(
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
                            TextSpan(text: 'New '),
                            TextSpan(
                              text: 'Playlist',
                              style: TextStyle(color: Colors.pinkAccent),
                            ),
                          ],
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
                                      "${AppConfig.baseUrl}createPlaylist.php?user_id=$_currentUserId&name=$safeName"
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

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.black,
      appBar: AppBar(
        iconTheme: IconThemeData(color: Colors.white),
        // Modified here: 'P' in Playlists is now colored pink
        title: RichText(
          text: TextSpan(
            style: GoogleFonts.cabinSketch(
              fontSize: 24,
              fontWeight: FontWeight.bold,
            ),
            children: const [
              TextSpan(
                text: 'P',
                style: TextStyle(color: Colors.pinkAccent),
              ),
              TextSpan(
                text: 'laylists',
                style: TextStyle(color: Colors.white),
              ),
            ],
          ),
        ),
        centerTitle: true,
        backgroundColor: Colors.black,
        elevation: 0,
      ),
      body: _buildBody(),
      floatingActionButton: FloatingActionButton(
        onPressed: () {
          if (_currentUserId != null) {
            _showCreatePlaylistDialog();
          } else {
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(content: Text('Please log in to create a playlist.')),
            );
          }
        },
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
            style: GoogleFonts.cabinSketch(color: Colors.redAccent, fontSize: 16),
            textAlign: TextAlign.center,
          ),
        ),
      );
    }

    if (_playlists.isEmpty) {
      return Center(
        child: Text(
          'No playlists found.',
          style: GoogleFonts.cabinSketch(color: Colors.white70, fontSize: 18),
        ),
      );
    }

    return GridView.builder(
      padding: const EdgeInsets.fromLTRB(8.0, 8.0, 8.0, 80.0),
      gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: 2,
        crossAxisSpacing: 16,
        mainAxisSpacing: 16,
        childAspectRatio: 1.0,
      ),
      itemCount: _playlists.length,
      itemBuilder: (context, index) {
        final playlist = _playlists[index];
        return _buildPlaylistTile(playlist);
      },
    );
  }

  Widget _buildSingleImage(List<dynamic> images) {
    final String? imageUrl = images.isNotEmpty ? images.first.toString() : null;

    return ClipRRect(
      borderRadius: BorderRadius.circular(24),
      child: (imageUrl != null && imageUrl.isNotEmpty)
          ? FadeInImage.assetNetwork(
        placeholder: 'assets/placeholder.png',
        image: imageUrl,
        fit: BoxFit.cover,
        width: double.infinity,
        height: double.infinity,
        imageErrorBuilder: (c, e, s) => Image.asset(
          'assets/placeholder.png',
          fit: BoxFit.cover,
          width: double.infinity,
          height: double.infinity,
        ),
      )
          : Image.asset(
        'assets/placeholder.png',
        fit: BoxFit.cover,
        width: double.infinity,
        height: double.infinity,
      ),
    );
  }

  Widget _buildGridImage(List<dynamic> images) {
    final imageList = List<String?>.from(images);
    while (imageList.length < 4) {
      imageList.add(null);
    }

    return ClipRRect(
      borderRadius: BorderRadius.circular(24),
      child: Container(
        color: Color(0xFF282828),
        child: GridView.builder(
          padding: EdgeInsets.zero,
          physics: NeverScrollableScrollPhysics(),
          itemCount: 4,
          gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
            crossAxisCount: 2,
            mainAxisSpacing: 1,
            crossAxisSpacing: 1,
          ),
          itemBuilder: (context, index) {
            final imageUrl = imageList[index];
            if (imageUrl != null && imageUrl.isNotEmpty) {
              return FadeInImage.assetNetwork(
                placeholder: 'assets/placeholder.png',
                image: imageUrl,
                fit: BoxFit.cover,
                imageErrorBuilder: (c, e, s) => Image.asset(
                  'assets/placeholder.png',
                  fit: BoxFit.cover,
                ),
              );
            }
            return Image.asset(
              'assets/placeholder.png',
              fit: BoxFit.cover,
            );
          },
        ),
      ),
    );
  }

  Widget _buildPlaylistTile(Map<String, dynamic> item) {
    final String playlistName = item['name']?.toString() ?? 'Unknown Playlist';
    final String playlistId = item['id']?.toString() ?? '0';

    final String songCountStr = item['song_count']?.toString() ?? '0';
    final int songCount = int.tryParse(songCountStr) ?? 0;
    final String songCountText = (songCount == 1) ? '1 Song' : '$songCount Songs';

    final List<dynamic> images = item['images'] as List<dynamic>? ?? [];

    final nameStyle = GoogleFonts.cabinSketch(
      color: Colors.white,
      fontSize: 20,
      fontWeight: FontWeight.bold,
    );

    return GestureDetector(
      onTap: () {
        print("Tapped on playlist: $playlistName (ID: $playlistId)");
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (context) => PlaylistDetailPage(
              playlistId: playlistId,
              playlistName: playlistName,
              headerImage: images.isNotEmpty ? images.first.toString() : null,
            ),
          ),
        ).then((_) {
          _fetchPlaylists();
        });
      },
      child: Container(
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(25),
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
          padding: const EdgeInsets.all(1.5),
          child: ClipRRect(
            borderRadius: BorderRadius.circular(24),
            child: Container(
              color: Color(0xFF282828),
              child: Stack(
                fit: StackFit.expand,
                children: [
                  if (songCount >= 3)
                    _buildGridImage(images)
                  else
                    _buildSingleImage(images),

                  // Gradient overlay
                  Container(
                    decoration: BoxDecoration(
                      gradient: LinearGradient(
                        colors: [
                          Colors.black.withOpacity(0.8),
                          Colors.transparent,
                        ],
                        begin: Alignment.centerLeft,
                        end: Alignment.centerRight,
                      ),
                    ),
                  ),

                  Positioned(
                    bottom: 10,
                    left: 10,
                    right: 10,
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        RichText(
                          text: TextSpan(
                            children: [
                              if (playlistName.isNotEmpty) ...[
                                TextSpan(
                                  text: playlistName[0],
                                  style: nameStyle.copyWith(color: Colors.pinkAccent),
                                ),
                                if (playlistName.length > 1)
                                  TextSpan(
                                    text: playlistName.substring(1),
                                    style: nameStyle,
                                  ),
                              ] else ...[
                                TextSpan(text: 'Unknown', style: nameStyle),
                              ]
                            ],
                          ),
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                        ),
                        SizedBox(height: 4),
                        Text(
                          songCountText,
                          style: GoogleFonts.cabinSketch(
                              fontSize: 12,
                              color: Colors.pinkAccent,
                              fontWeight: FontWeight.bold
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
      ),
    );
  }
}