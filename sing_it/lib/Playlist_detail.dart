import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'config.dart'; // Your AppConfig
import 'player2.dart'; // Import your player page
import 'dart:ui'; // For blur effect (used in dialogs)

class PlaylistDetailPage extends StatefulWidget {
  final String playlistId;
  final String playlistName;
  final String? headerImage; // Image to show in the header

  const PlaylistDetailPage({
    Key? key,
    required this.playlistId,
    required this.playlistName,
    this.headerImage,
  }) : super(key: key);

  @override
  _PlaylistDetailPageState createState() => _PlaylistDetailPageState();
}

class _PlaylistDetailPageState extends State<PlaylistDetailPage> {
  int _songCount = 0;
  List<Map<String, dynamic>> _songs = [];
  bool _isLoading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _fetchPlaylistSongs();
  }

  Future<void> _fetchPlaylistSongs() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      final url = Uri.parse(
          "${AppConfig.baseUrl}getPlaylistData.php?playlist_id=${widget.playlistId}");

      final response = await http.get(url);
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);

        if (data is Map && data.containsKey('error')) {
          throw Exception(data['error']);
        }

        if (data is List) {
          _songs = List<Map<String, dynamic>>.from(data);
          _songCount = _songs.length;
        } else {
          _songs = [];
          _songCount = 0;
        }

        setState(() {
          _isLoading = false;
        });

      } else {
        throw Exception(
            'Failed to load songs (Status code: ${response.statusCode})');
      }
    } catch (e) {
      print("Error fetching playlist songs: $e");
      setState(() {
        _error = e.toString();
        _isLoading = false;
      });
    }
  }

  Future<void> _showDeleteConfirmation() async {
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
                  border: Border.all(color: Colors.redAccent.withOpacity(0.5)),
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
                            color: Colors.white,
                          ),
                          children: [
                            TextSpan(text: 'Delete '),
                            TextSpan(
                              text: 'Playlist?',
                              style: TextStyle(color: Colors.pinkAccent),
                            ),
                          ],
                        ),
                      ),
                    ),
                    Padding(
                      padding: const EdgeInsets.fromLTRB(24.0, 0, 24.0, 20.0),
                      child: RichText(
                        textAlign: TextAlign.center,
                        text: TextSpan(
                          style: GoogleFonts.cabinSketch(
                            color: Colors.white70,
                            fontSize: 16,
                          ),
                          children: [
                            TextSpan(text: 'Are you sure you want to delete "'),
                            TextSpan(
                              text: widget.playlistName,
                              style: TextStyle(
                                  color: Colors.pinkAccent,
                                  fontWeight: FontWeight.bold
                              ),
                            ),
                            TextSpan(text: '"?'),
                          ],
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
                            onPressed: () => Navigator.of(dialogContext).pop(),
                          ),
                          TextButton(
                            child: Text(
                              'Delete',
                              style: GoogleFonts.cabinSketch(
                                color: Colors.redAccent,
                                fontWeight: FontWeight.bold,
                                fontSize: 16,
                              ),
                            ),
                            onPressed: () {
                              Navigator.of(dialogContext).pop();
                              _deletePlaylist();
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

  Future<void> _deletePlaylist() async {
    try {
      final url = Uri.parse(
          "${AppConfig.baseUrl}deletePlaylist.php?playlist_id=${widget.playlistId}");

      final response = await http.get(url);

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['status'] == 'success') {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text('Playlist deleted', style: TextStyle(color: Colors.white)),
              backgroundColor: Colors.green,
            ),
          );
          Navigator.of(context).pop();
        } else {
          throw Exception(data['message'] ?? 'Failed to delete playlist');
        }
      } else {
        throw Exception('Server error');
      }
    } catch (e) {
      print("Error deleting playlist: $e");
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Error: ${e.toString()}', style: TextStyle(color: Colors.white)),
          backgroundColor: Colors.redAccent,
        ),
      );
    }
  }

  // --- 1. NEW FUNCTION ADDED ---
  Future<void> _removeSongFromPlaylist(String songId) async {
    try {
      // Call the PHP script that toggles (and in this case, removes)
      final url = Uri.parse(
          "${AppConfig.baseUrl}addSongToPlaylist.php?playlist_id=${widget.playlistId}&song_id=$songId");

      final response = await http.get(url);

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['status'] == 'success') {
          // Check if it was removed (as per your PHP script)
          if (data['message'] == 'Song removed from playlist!') {
            // Update the UI locally
            setState(() {
              _songs.removeWhere((song) => song['sid'] == songId);
              _songCount = _songs.length;
            });
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(
                content: Text('Song removed from playlist', style: GoogleFonts.cabinSketch(color: Colors.white)),
                backgroundColor: Colors.green,
              ),
            );
          }
        } else {
          throw Exception(data['message'] ?? 'Failed to update playlist');
        }
      } else {
        throw Exception('Server error');
      }
    } catch (e) {
      print("Error removing song from playlist: $e");
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Error: ${e.toString()}', style: GoogleFonts.cabinSketch(color: Colors.white)),
          backgroundColor: Colors.redAccent,
        ),
      );
    }
  }
  // --- END OF NEW FUNCTION ---

  @override
  Widget build(BuildContext context) {
    final bool canPlaySongs = !_isLoading && _songs.isNotEmpty;

    final titleStyle = GoogleFonts.cabinSketch(
      color: Colors.white,
      fontWeight: FontWeight.bold,
      fontSize: 20,
    );

    return Scaffold(
      backgroundColor: Colors.black,
      appBar: AppBar(
        iconTheme: IconThemeData(color: Colors.white),
        title: RichText(
          text: TextSpan(
            style: titleStyle,
            children: [
              TextSpan(
                text: widget.playlistName.isNotEmpty ? widget.playlistName[0] : '',
                style: titleStyle.copyWith(color: Colors.pinkAccent),
              ),
              if (widget.playlistName.length > 1)
                TextSpan(
                  text: widget.playlistName.substring(1),
                  style: titleStyle,
                ),
            ],
          ),
        ),
        centerTitle: true,
        backgroundColor: Colors.black,
        elevation: 0,
        actions: [
          IconButton(
            icon: Icon(Icons.delete_forever, color: Colors.redAccent),
            onPressed: _showDeleteConfirmation,
          ),
        ],
      ),

      body: _buildBody(),

      floatingActionButton: canPlaySongs
          ? _buildPlayAllButton()
          : null,
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

    return _buildSongsSection();
  }

  Widget _buildSongsSection() {
    if (_songs.isEmpty) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.symmetric(vertical: 40.0),
          child: Text(
            'This playlist is empty.',
            style: GoogleFonts.cabinSketch(color: Colors.white70, fontSize: 18),
            textAlign: TextAlign.center,
          ),
        ),
      );
    }

    return ListView.separated(
      padding: const EdgeInsets.symmetric(horizontal: 16.0, vertical: 16.0),
      shrinkWrap: false,
      physics: AlwaysScrollableScrollPhysics(),
      itemCount: _songs.length,
      itemBuilder: (context, index) {
        final song = _songs[index];
        return _buildSongTile(song, index);
      },
      separatorBuilder: (context, index) {
        return SizedBox(height: 8);
      },
    );
  }

  // --- 2. MODIFIED: _buildSongTile ---
  Widget _buildSongTile(Map<String, dynamic> item, int index) {
    final String songName = item['name']?.toString() ?? 'Unknown Song';
    final String singerName = item['singer']?.toString() ?? 'Unknown Artist';

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

              // --- ICON BUTTON ADDED HERE ---
              trailing: IconButton(
                icon: Icon(
                  Icons.remove_circle_outline, // Remove icon
                  color: Colors.redAccent.withOpacity(0.8),
                ),
                onPressed: () {
                  // Call the remove function
                  _removeSongFromPlaylist(item['sid'].toString());
                },
              ),
              // --- END OF ADDITION ---

              onTap: () {
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (context) => SongPlayerPage(
                      songList: _songs,
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
  // --- END OF MODIFICATION ---

  Widget _buildPlayAllButton() {
    return GestureDetector(
      onTap: () {
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (context) => SongPlayerPage(
              songList: _songs,
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