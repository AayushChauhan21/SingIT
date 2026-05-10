import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'dart:ui';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'dart:async';
import 'package:shared_preferences/shared_preferences.dart';
import 'config.dart';
import 'song_detail.dart' hide AppConfig;
import 'artist_detail.dart';
import 'category_page.dart';
import 'login.dart';
import 'profile.dart';
import 'favourite.dart';
import 'playlist.dart';
import 'history.dart'; // Make sure this file exists

void main() {
  runApp(SingITApp());
}

class SingITApp extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      home: TopSection(),
      theme: ThemeData(
        textTheme: GoogleFonts.cabinSketchTextTheme(
          Theme.of(context).textTheme.copyWith(
            bodyMedium: TextStyle(color: Colors.white),
            bodyLarge: TextStyle(color: Colors.white),
            headlineMedium: TextStyle(color: Colors.white),
          ),
        ),
        primaryColor: Colors.pinkAccent,
        scaffoldBackgroundColor: Colors.black,
        colorScheme: ColorScheme.dark(
          primary: Colors.pinkAccent,
          background: Colors.black,
        ),
      ),
    );
  }
}

class TopSection extends StatefulWidget {
  @override
  _TopSectionState createState() => _TopSectionState();
}

class _TopSectionState extends State<TopSection> {
  final GlobalKey<ScaffoldState> _scaffoldKey = GlobalKey<ScaffoldState>();

  FocusNode _focusNode = FocusNode();
  bool _isFocused = false;

  final TextEditingController _searchController = TextEditingController();
  List _searchResults = [];
  bool _isSearching = false;
  Timer? _debounce;

  List artists = [];
  bool isLoading = true;

  List songs = [];
  bool isSongsLoading = true;

  String? _loggedInUserId;
  String _userName = "Guest User";
  String _userEmail = "Please log in";
  String? _userImage;
  bool _isUserDataLoading = true;

  // --- 1. STATE VARIABLES ADDED ---
  List _historySongs = [];
  bool _isHistoryLoading = true;
  List _genreSections = [];
  bool _isGenreLoading = true;
  // --- NEW ---
  List _recommendedSongs = [];
  bool _isRecLoading = true;
  // --- END OF ADDITION ---

  @override
  void initState() {
    super.initState();
    _loadUserData();

    _focusNode.addListener(() {
      setState(() {
        _isFocused = _focusNode.hasFocus;
        if (!_isFocused) {
          setState(() {
            _searchResults = [];
            _isSearching = false;
          });
        }
      });
    });

    _searchController.addListener(_onSearchChanged);
    fetchArtists();
    fetchSongs();
    fetchGenreSections();
    // Note: fetchHistory() & fetchRecommendations() are called inside _loadUserData
  }

  /// 1. Checks SharedPreferences for a user ID
  Future<void> _loadUserData() async {
    final prefs = await SharedPreferences.getInstance();
    final String? userId = prefs.getString('user_id');

    if (userId != null) {
      setState(() {
        _loggedInUserId = userId;
      });
      await _fetchUserData(userId);
      // Fetch history and recommendations only if user is logged in
      await fetchHistory(userId);
      // --- 2. FUNCTION CALL ADDED ---
      await fetchRecommendations(userId);
      // --- END OF ADDITION ---
    } else {
      setState(() {
        _isUserDataLoading = false;
        _loggedInUserId = null;
        _userName = "Guest User";
        _userEmail = "Please log in";
        _userImage = null;
      });
    }
  }

  /// 2. Calls your new PHP script
  Future<void> _fetchUserData(String userId) async {
    final url = Uri.parse(
        "${AppConfig.baseUrl}getUserData.php?uid=${Uri.encodeComponent(userId)}");

    try {
      final response = await http.get(url);
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);

        if (data['success'] == true) {
          setState(() {
            _userName = data['user']['name'] ?? 'Unknown Name';
            _userEmail = data['user']['email'] ?? 'No Email';
            _userImage = data['user']['photo'];
          });
        } else {
          print("API Error: ${data['error']}");
          _handleLogout(closeDrawer: false);
        }
      } else {
        print("HTTP Error: ${response.statusCode}");
      }
    } catch (e) {
      print("Error fetching user data: $e");
    } finally {
      setState(() {
        _isUserDataLoading = false;
      });
    }
  }

  /// 3. Clears the session and resets the UI
  Future<void> _handleLogout({bool closeDrawer = true}) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('user_id');

    setState(() {
      _loggedInUserId = null;
      _userName = "Guest User";
      _userEmail = "Please log in";
      _userImage = null;
      _isUserDataLoading = false;
      // Clear user-specific data
      _historySongs = [];
      _isHistoryLoading = true;
      // --- 3. ADDED ---
      _recommendedSongs = [];
      _isRecLoading = true;
      // --- END OF ADDITION ---
    });

    if (closeDrawer && Navigator.canPop(context)) {
      Navigator.pop(context);
    }
  }

  @override
  void dispose() {
    _focusNode.dispose();
    _searchController.removeListener(_onSearchChanged);
    _searchController.dispose();
    _debounce?.cancel();
    super.dispose();
  }

  Future<void> fetchArtists() async {
    final url = Uri.parse("${AppConfig.baseUrl}getArtists.php");
    try {
      final response = await http.get(url);
      if (response.statusCode == 200) {
        final decodedResponse = jsonDecode(response.body);
        if (decodedResponse is List) {
          setState(() {
            artists = decodedResponse;
            isLoading = false;
          });
        } else {
          throw Exception("Invalid format for artists list");
        }
      } else {
        throw Exception("Failed to load artists");
      }
    } catch (e) {
      print("Error fetching artists: $e");
      setState(() => isLoading = false);
    }
  }

  Future<void> fetchSongs() async {
    final url = Uri.parse("${AppConfig.baseUrl}getSongs.php");
    try {
      final response = await http.get(url);
      if (response.statusCode == 200) {
        final decodedResponse = jsonDecode(response.body);
        if (decodedResponse is List) {
          setState(() {
            songs = decodedResponse;
            isSongsLoading = false;
          });
        } else {
          throw Exception("Invalid format for songs list");
        }
      } else {
        throw Exception("Failed to load songs");
      }
    } catch (e) {
      print("Error fetching songs: $e");
      setState(() => isSongsLoading = false);
    }
  }

  Future<void> fetchGenreSections() async {
    final url = Uri.parse("${AppConfig.baseUrl}get_all_genres_with_songs.php");
    try {
      final response = await http.get(url);
      if (response.statusCode == 200) {
        final decodedResponse = jsonDecode(response.body);
        if (decodedResponse is List) {
          setState(() {
            _genreSections = decodedResponse;
            _isGenreLoading = false;
          });
        } else {
          throw Exception("Invalid format for genre list");
        }
      } else {
        throw Exception("Failed to load genres");
      }
    } catch (e) {
      print("Error fetching genres: $e");
      setState(() => _isGenreLoading = false);
    }
  }

  Future<void> fetchHistory(String userId) async {
    if (_loggedInUserId == null) {
      setState(() {
        _isHistoryLoading = false;
        _historySongs = [];
      });
      return;
    }
    setState(() => _isHistoryLoading = true);
    final url = Uri.parse("${AppConfig.baseUrl}get_history.php?user_id=$userId");
    try {
      final response = await http.get(url);
      if (response.statusCode == 200) {
        final decodedResponse = jsonDecode(response.body);
        if (decodedResponse is List) {
          setState(() {
            _historySongs = decodedResponse;
            _isHistoryLoading = false;
          });
        } else {
          throw Exception("Invalid format for history list");
        }
      } else {
        throw Exception("Failed to load history");
      }
    } catch (e) {
      print("Error fetching history: $e");
      setState(() => _isHistoryLoading = false);
    }
  }

  // --- 4. NEW FUNCTION ADDED ---
  Future<void> fetchRecommendations(String userId) async {
    if (_loggedInUserId == null) {
      setState(() {
        _isRecLoading = false;
        _recommendedSongs = [];
      });
      return;
    }
    setState(() => _isRecLoading = true);
    final url = Uri.parse("${AppConfig.baseUrl}get_recommendations.php?user_id=$userId");
    try {
      final response = await http.get(url);
      if (response.statusCode == 200) {
        final decodedResponse = jsonDecode(response.body);
        if (decodedResponse is List) {
          setState(() {
            _recommendedSongs = decodedResponse;
            _isRecLoading = false;
          });
        } else {
          throw Exception("Invalid format for recommendations list");
        }
      } else {
        throw Exception("Failed to load recommendations");
      }
    } catch (e) {
      print("Error fetching recommendations: $e");
      setState(() => _isRecLoading = false);
    }
  }
  // --- END OF ADDITION ---

  // --- SEARCH METHODS ---
  void _onSearchChanged() {
    setState(() {});
    if (_debounce?.isActive ?? false) _debounce!.cancel();
    final query = _searchController.text;
    if (query.isEmpty) {
      setState(() {
        _searchResults = [];
        _isSearching = false;
      });
      return;
    }
    _debounce = Timer(const Duration(milliseconds: 500), () {
      fetchSearchResults(query);
    });
  }

  Future<void> fetchSearchResults(String query) async {
    if (query.isEmpty || !_isFocused) return;
    setState(() {
      _isSearching = true;
      _searchResults = [];
    });
    final url = Uri.parse(
        "${AppConfig.baseUrl}search.php?query=${Uri.encodeComponent(query)}");
    try {
      final response = await http.get(url);
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data is Map && data.containsKey('error')) {
          print("PHP Error: ${data['error']}");
          setState(() => _searchResults = []);
          return;
        }
        List combinedResults = [];
        combinedResults.addAll(data['songs'] as List? ?? []);
        combinedResults.addAll(data['artists'] as List? ?? []);
        setState(() {
          _searchResults = combinedResults;
        });
      } else {
        throw Exception("Failed to load search results");
      }
    } catch (e) {
      print("Error fetching search results: $e");
    } finally {
      if (_isFocused) {
        setState(() {
          _isSearching = false;
        });
      }
    }
  }

  void _clearSearch() {
    _searchController.clear();
  }

  Widget _buildSearchHeader(String title) {
    return Padding(
      padding: const EdgeInsets.only(left: 16, top: 16, bottom: 8),
      child: Text(
        title,
        style: GoogleFonts.cabinSketch(
          color: Colors.pinkAccent,
          fontSize: 16,
          fontWeight: FontWeight.bold,
        ),
      ),
    );
  }

  Widget _buildSongTile(Map<String, dynamic> item) {
    final String songName = item['name'] ?? 'Unknown Song';
    final String singerName = item['singer_name'] ?? 'Unknown Artist';

    final songNameStyle = GoogleFonts.cabinSketch(
        color: Colors.white, fontSize: 17, fontWeight: FontWeight.bold);
    final singerNameStyle = GoogleFonts.cabinSketch(
        color: Colors.pinkAccent, fontSize: 14, fontWeight: FontWeight.bold);

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
              contentPadding:
              const EdgeInsets.symmetric(vertical: 4.0, horizontal: 16.0),
              leading: ClipRRect(
                borderRadius: BorderRadius.circular(4),
                child: FadeInImage.assetNetwork(
                  placeholder: 'assets/placeholder.png',
                  image: item['image'] ?? '',
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
                    text: TextSpan(children: [
                      TextSpan(
                          text: songName.isNotEmpty ? songName[0] : '',
                          style:
                          songNameStyle.copyWith(color: Colors.pinkAccent)),
                      if (songName.length > 1)
                        TextSpan(
                            text: songName.substring(1), style: songNameStyle),
                    ]),
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
              subtitle: null, // Subtitle is now part of the Column in title
              onTap: () {
                _focusNode.unfocus();
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (context) =>
                        SongDetailPage(sid: item["sid"].toString()),
                  ),
                );
              },
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildArtistTile(Map<String, dynamic> item) {
    final String artistName = item['name']?.toString() ?? 'Unknown Artist';
    final songCount = item['song_count'] ?? '0';
    final String songCountText =
    (songCount == '1') ? '1 Song' : '$songCount Songs';

    final artistNameStyle = GoogleFonts.cabinSketch(
        color: Colors.white, fontSize: 17, fontWeight: FontWeight.bold);
    final songCountStyle = GoogleFonts.cabinSketch(
        color: Colors.pinkAccent, fontSize: 14, fontWeight: FontWeight.bold);

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
              contentPadding:
              const EdgeInsets.symmetric(vertical: 4.0, horizontal: 16.0),
              leading: ClipRRect(
                borderRadius:
                BorderRadius.circular(27.5), // Make it circular
                child: FadeInImage.assetNetwork(
                  placeholder: 'assets/placeholder.png',
                  image: item['photo']?.toString() ?? '',
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
                    text: TextSpan(children: [
                      TextSpan(
                          text: artistName.isNotEmpty ? artistName[0] : '',
                          style: artistNameStyle.copyWith(
                              color: Colors.pinkAccent)),
                      if (artistName.length > 1)
                        TextSpan(
                            text: artistName.substring(1),
                            style: artistNameStyle),
                    ]),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                  SizedBox(height: 4),
                  Text(
                    songCountText,
                    style: songCountStyle,
                  ),
                ],
              ),
              subtitle: null, // Subtitle is now part of the Column in title
              onTap: () {
                _focusNode.unfocus();
                final artistId = item['arid']?.toString();
                final artistName = item['name']?.toString();
                if (artistId != null) {
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
                  print(
                      "Error: Artist ID (arid) is missing for ${item['name']}");
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(
                      content:
                      Text('Cannot open artist details. ID missing.'),
                      backgroundColor: Colors.redAccent,
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

  Widget _buildSearchResults() {
    if (!_isFocused || _searchController.text.isEmpty) {
      return SizedBox(height: 20);
    }
    if (_isSearching) {
      return Padding(
        padding: const EdgeInsets.symmetric(vertical: 20.0),
        child: Center(
            child: CircularProgressIndicator(color: Colors.pinkAccent)),
      );
    }
    final songsFound =
    _searchResults.where((item) => item['type'] == 'song').toList();
    final artistsFound =
    _searchResults.where((item) => item['type'] == 'artist').toList();
    if (_searchResults.isEmpty) {
      return Padding(
        padding: const EdgeInsets.symmetric(vertical: 20.0),
        child: Center(
          child: Text(
            "No songs or artists found.",
            style: GoogleFonts.cabinSketch(color: Colors.white70, fontSize: 16),
          ),
        ),
      );
    }
    List<dynamic> itemsWithHeaders = [];
    if (songsFound.isNotEmpty) {
      itemsWithHeaders.add("Songs");
      itemsWithHeaders.addAll(songsFound);
    }
    if (artistsFound.isNotEmpty) {
      itemsWithHeaders.add("Singers");
      itemsWithHeaders.addAll(artistsFound);
    }
    return Padding(
      padding: const EdgeInsets.only(top: 16.0, bottom: 20.0),
      child: Container(
        constraints: BoxConstraints(
            maxHeight: 400), // Increased height for better scrolling
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(12),
        ),
        child: ListView.builder(
          padding: EdgeInsets.symmetric(vertical: 8.0),
          shrinkWrap: true,
          itemCount: itemsWithHeaders.length,
          itemBuilder: (context, index) {
            final item = itemsWithHeaders[index];
            if (item is String) {
              return _buildSearchHeader(item);
            } else if (item is Map) {
              final itemType = item['type'];
              if (itemType == 'song') {
                return _buildSongTile(item as Map<String, dynamic>);
              } else if (itemType == 'artist') {
                return _buildArtistTile(item as Map<String, dynamic>);
              }
            }
            return SizedBox.shrink();
          },
        ),
      ),
    );
  }
  // --- END OF SEARCH METHODS ---

  /// Builds the list of genre sections
  Widget _buildGenreSections() {
    if (_isGenreLoading) {
      return Padding(
        padding: const EdgeInsets.all(20.0),
        child: Center(
          child: CircularProgressIndicator(color: Colors.pinkAccent),
        ),
      );
    }

    if (_genreSections.isEmpty) {
      return SizedBox.shrink(); // No genres, show nothing
    }

    // Use Column to list genres vertically
    return Column(
      // Use .asMap().entries to get both the index and the genre
      children: _genreSections.asMap().entries.map((entry) {
        int index = entry.key;
        Map<String, dynamic> genre = entry.value;

        // We need to cast the songs list
        final List songsInGenre = genre['songs'] as List? ?? [];
        if (songsInGenre.isEmpty) {
          return SizedBox.shrink(); // Don't show genres with no songs
        }

        // Create a new, sorted list.
        // We must cast to Map<String, dynamic> for sorting to work.
        final List<Map<String, dynamic>> sortedSongs =
        List<Map<String, dynamic>>.from(songsInGenre);

        // User: odd (1, 3) = asc, even (2, 4) = desc
        // Index: 0, 2 (even) = asc, 1, 3 (odd) = desc
        if (index % 2 == 0) {
          // This is the 1st, 3rd, etc. (ODD) genre. Sort ASCENDING.
          sortedSongs.sort((a, b) => (a['name'] ?? '').compareTo(b['name'] ?? ''));
        } else {
          // This is the 2nd, 4th, etc. (EVEN) genre. Sort DESCENDING.
          sortedSongs.sort((a, b) => (b['name'] ?? '').compareTo(a['name'] ?? ''));
        }

        // Create a new map to pass to the widget, replacing old songs with sorted songs
        final Map<String, dynamic> sortedGenre = Map.from(genre);
        sortedGenre['songs'] = sortedSongs;

        // Now pass this new map to the build widget
        return _buildSingleGenreSection(sortedGenre);
      }).toList(),
    );
  }


  /// Builds a single horizontal-scrolling song row for a genre
  Widget _buildSingleGenreSection(Map<String, dynamic> genre) {
    final String genreName = genre['name'] ?? 'Unknown Genre';
    final List songList = genre['songs'] as List? ?? [];

    // This is a copy of the "Recommended Albums" widget structure
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        // --- EMOJI ADDED ---
        sectionTitle("$genreName "), // Use the existing sectionTitle helper
        SizedBox(height: 12),
        Container(
          height: 130, // Same height as "Recommended Albums"
          child: ListView.builder(
            scrollDirection: Axis.horizontal,
            padding: EdgeInsets.symmetric(horizontal: 16),
            itemCount: songList.length,
            itemBuilder: (context, index) {
              final song = songList[index];
              return GestureDetector(
                onTap: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (context) => SongDetailPage(
                          sid: song["sid"].toString()),
                    ),
                  );
                },
                // Use the existing songAlbumCard helper
                child: songAlbumCard(
                  song["name"] ?? "Unknown Song",
                  song["image"],
                ),
              );
            },
          ),
        ),
        SizedBox(height: 20), // Add spacing after each genre
      ],
    );
  }

  /// Builds the horizontal-scrolling "History" section
  Widget _buildHistorySection() {
    if (_isHistoryLoading || _historySongs.isEmpty || _loggedInUserId == null) {
      // Don't show anything if loading, empty, or logged out
      return SizedBox.shrink();
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        // --- EMOJI ADDED ---
        sectionTitle("Recently Played"), // The special title
        SizedBox(height: 12),
        Container(
          height: 130, // Same height as "Recommended Albums"
          child: ListView.builder(
            scrollDirection: Axis.horizontal,
            padding: EdgeInsets.symmetric(horizontal: 16),
            itemCount: _historySongs.length,
            itemBuilder: (context, index) {
              final song = _historySongs[index];
              return GestureDetector(
                onTap: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (context) => SongDetailPage(
                          sid: song["sid"].toString()),
                    ),
                  );
                },
                // Use the existing songAlbumCard helper
                child: songAlbumCard(
                  song["name"] ?? "Unknown Song",
                  song["image"],
                ),
              );
            },
          ),
        ),
        SizedBox(height: 20), // Add spacing after the section
      ],
    );
  }

  // --- 5. NEW WIDGET ADDED ---
  /// Builds the horizontal-scrolling "Recommended" section
  Widget _buildRecommendedSection() {
    if (_isRecLoading || _recommendedSongs.isEmpty || _loggedInUserId == null) {
      // Don't show anything if loading, empty, or logged out
      return SizedBox.shrink();
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        sectionTitle("Just For You 💖"), // The special title
        SizedBox(height: 12),
        Container(
          height: 130, // Same height as "Recommended Albums"
          child: ListView.builder(
            scrollDirection: Axis.horizontal,
            padding: EdgeInsets.symmetric(horizontal: 16),
            itemCount: _recommendedSongs.length,
            itemBuilder: (context, index) {
              final song = _recommendedSongs[index];
              return GestureDetector(
                onTap: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (context) => SongDetailPage(
                          sid: song["sid"].toString()),
                    ),
                  );
                },
                // Use the existing songAlbumCard helper
                child: songAlbumCard(
                  song["name"] ?? "Unknown Song",
                  song["image"],
                ),
              );
            },
          ),
        ),
        SizedBox(height: 20), // Add spacing after the section
      ],
    );
  }
  // --- END OF ADDITION ---


  @override
  Widget build(BuildContext context) {
    return Scaffold(
      key: _scaffoldKey,
      backgroundColor: Colors.black,
      body: SafeArea(
        child: GestureDetector(
          onTap: () {
            FocusScope.of(context).unfocus();
          },
          child: SingleChildScrollView(
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Image.asset("assets/logo3.jpg", height: 43),
                      Spacer(),
                      profileAvatar(),
                    ],
                  ),
                  SizedBox(height: 20),
                  searchBar(),
                  _buildSearchResults(),
                  SliderWidget(),
                  SizedBox(height: 20),
                  // --- EMOJI ADDED ---
                  sectionTitle("Artists️‍❤️‍🔥"),
                  SizedBox(height: 12),
                  Container(
                    height: 140,
                    child: isLoading
                        ? Center(
                      child: CircularProgressIndicator(
                          color: Colors.pinkAccent),
                    )
                        : ListView.builder(
                      scrollDirection: Axis.horizontal,
                      padding: EdgeInsets.symmetric(horizontal: 16),
                      itemCount: artists.length,
                      itemBuilder: (context, index) {
                        final artist = artists[index];
                        return GestureDetector(
                          onTap: () {
                            final artistId = artist['arid']?.toString();
                            final artistName = artist['name']?.toString();
                            if (artistId != null) {
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
                              print(
                                  "Artist ID (arid) is missing for ${artist['name']}");
                              ScaffoldMessenger.of(context)
                                  .showSnackBar(SnackBar(
                                content: Text(
                                    'Cannot open artist details. ID missing.'),
                                backgroundColor: Colors.redAccent,
                              ));
                            }
                          },
                          child: artistCard(artist["name"] ?? 'Unknown',
                              artist["photo"] ?? ''),
                        );
                      },
                    ),
                  ),
                  SizedBox(height: 20),

                  _buildHistorySection(),

                  // --- 6. WIDGET PLACED HERE ---
                  _buildRecommendedSection(),
                  // --- END OF ADDITION ---

                  sectionTitle("Trending Songs🔥"),
                  SizedBox(height: 12),
                  Container(
                    height: 130,
                    child: isSongsLoading
                        ? Center(
                      child: CircularProgressIndicator(
                          color: Colors.pinkAccent),
                    )
                        : ListView.builder(
                      scrollDirection: Axis.horizontal,
                      padding: EdgeInsets.symmetric(horizontal: 16),
                      itemCount: songs.length,
                      itemBuilder: (context, index) {
                        final song = songs[index];
                        return GestureDetector(
                          onTap: () {
                            Navigator.push(
                              context,
                              MaterialPageRoute(
                                builder: (context) => SongDetailPage(
                                    sid: song["sid"].toString()),
                              ),
                            );
                          },
                          child: songAlbumCard(
                            song["name"] ?? "Unknown Song",
                            song["image"],
                          ),
                        );
                      },
                    ),
                  ),
                  SizedBox(height: 20),
                  // --- EMOJI ADDED ---
                  sectionTitle("Today's Special ✨"),
                  SizedBox(height: 12),
                  TodaysSpecialWidget(),

                  SizedBox(height: 30), // Margin after Today's Special

                  _buildGenreSections(),

                  SizedBox(height: 20),
                ],
              ),
            ),
          ),
        ),
      ),

      // --- DRAWER ---
      endDrawer: Drawer(
        backgroundColor: Color(0xFF000000).withOpacity(0.5),
        child: BackdropFilter(
          filter: ImageFilter.blur(sigmaX: 4, sigmaY: 4),
          child: ListView(
            padding: EdgeInsets.zero,
            children: [
              DrawerHeader(
                decoration: BoxDecoration(
                  color: Colors.transparent,
                  border: Border(
                    bottom: BorderSide(
                      color: Colors.white.withOpacity(0.1),
                      width: 1.0,
                    ),
                  ),
                ),
                child: Stack(
                  children: [
                    // --- MODIFICATION 1 ---
                    // Close Button
                    Align(
                      alignment: Alignment.topRight,
                      child: IconButton(
                        icon: Icon(Icons.close, color: Colors.white), // <-- Changed color
                        onPressed: () {
                          Navigator.pop(context);
                        },
                      ),
                    ),
                    // User Info
                    Row(
                      crossAxisAlignment: CrossAxisAlignment.center,
                      mainAxisAlignment: MainAxisAlignment.start,
                      children: [
                        CircleAvatar(
                          radius: 30,
                          backgroundColor: Colors.transparent,
                          backgroundImage: (_userImage != null &&
                              _userImage!.isNotEmpty)
                              ? NetworkImage(_userImage!)
                              : AssetImage("assets/user.png")
                          as ImageProvider,
                          onBackgroundImageError: (_userImage != null &&
                              _userImage!.isNotEmpty)
                              ? (e, s) {
                            print("Failed to load drawer image: $e");
                          }
                              : null,
                        ),
                        SizedBox(width: 16),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              // User Name Widget
                              Builder(
                                builder: (context) {
                                  final String displayName =
                                  _userName.isNotEmpty
                                      ? _userName
                                      : "Guest User";
                                  final nameStyle =
                                  GoogleFonts.cabinSketch(
                                    color: Colors.white,
                                    fontSize: 20,
                                    fontWeight: FontWeight.bold,
                                  );
                                  final pinkLetterStyle = nameStyle.copyWith(
                                    color: Colors.pinkAccent,
                                  );

                                  return RichText(
                                    text: TextSpan(
                                      children: [
                                        TextSpan(
                                            text: displayName[0],
                                            style: pinkLetterStyle),
                                        if (displayName.length > 1)
                                          TextSpan(
                                              text: displayName.substring(1),
                                              style: nameStyle),
                                      ],
                                    ),
                                    maxLines: 1,
                                    overflow: TextOverflow.ellipsis,
                                  );
                                },
                              ),
                              SizedBox(height: 4),
                              // Email Widget
                              Text(
                                _userEmail,
                                style: GoogleFonts.cabinSketch(
                                  color: Colors.pinkAccent,
                                  fontSize: 14,
                                  fontWeight: FontWeight.w600,
                                ),
                                maxLines: 1,
                                overflow: TextOverflow.ellipsis,
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),

              // --- MODIFICATION 2 ---
              // --- CONDITIONAL "EDIT PROFILE" TILE ---
              if (_loggedInUserId != null)
                ListTile(
                  leading: Icon(Icons.person, color: Colors.blueAccent), // <-- Changed color
                  title: Text(
                    'Edit Profile',
                    style: GoogleFonts.cabinSketch(
                      color: Colors.white.withOpacity(0.9),
                      fontWeight: FontWeight.w600,
                      fontSize: 16,
                    ),
                  ),
                  onTap: () {
                    Navigator.pop(context); // Close the drawer
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (context) =>
                        const ProfilePage(), // Navigate to ProfilePage
                      ),
                    );
                  },
                ),

              // --- MODIFICATION 3 ---
              // --- CONDITIONAL "LOGOUT" TILE / "LOGIN" BUTTON ---
              if (_loggedInUserId != null)
                ListTile(
                  leading: Icon(Icons.logout, color: Colors.blueAccent), // <-- Changed color
                  title: Text(
                    'Logout',
                    style: GoogleFonts.cabinSketch(
                      color: Colors.white.withOpacity(0.9),
                      fontWeight: FontWeight.w600,
                      fontSize: 16,
                    ),
                  ),
                  onTap: () {
                    _handleLogout();
                  },
                )
              else
                _buildLoginGradientButton(),
            ],
          ),
        ),
      ),
      // --- END OF DRAWER ---

      // --- BOTTOM NAVIGATION BAR ---
      // --- THIS ENTIRE WIDGET IS MODIFIED ---
      bottomNavigationBar: Padding(
        padding: const EdgeInsets.only(bottom: 2),
        child: ClipRRect(
          borderRadius: BorderRadius.only(
            topLeft: Radius.circular(25),
            topRight: Radius.circular(25),
          ),
          child: BackdropFilter(
            filter: ImageFilter.blur(sigmaX: 12, sigmaY: 12),
            child: Container(
              decoration: BoxDecoration(
                color: Colors.black.withOpacity(0.65),
                borderRadius: BorderRadius.only(
                  topLeft: Radius.circular(25),
                  topRight: Radius.circular(25),
                ),
                border: Border(
                  top: BorderSide(color: Colors.pink, width: 2),
                ),
              ),
              child: BottomNavigationBar(
                backgroundColor: Colors.transparent,
                selectedItemColor: Colors.pinkAccent,
                unselectedItemColor: Colors.white70,
                showUnselectedLabels: true,
                type: BottomNavigationBarType.fixed,
                // 1. Current index changed to 2 (for Home)
                currentIndex: 2,
                onTap: (index) {
                  // 2. onTap logic reordered
                  if (index == 0) {
                    // Category
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                          builder: (context) => const CategoryPage()),
                    );
                  } else if (index == 1) {
                    // Playlist
                    if (_loggedInUserId != null) {
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (context) => const PlaylistPage(),
                        ),
                      );
                    } else {
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (context) => const LoginPage(),
                        ),
                      ).then((_) => _loadUserData());
                    }
                  } else if (index == 2) {
                    // Home
                    print("Home tapped");
                  } else if (index == 3) {
                    // Favourite
                    if (_loggedInUserId != null) {
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (context) => const FavouritePage(),
                        ),
                      );
                    } else {
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (context) => const LoginPage(),
                        ),
                      ).then((_) => _loadUserData());
                    }
                  } else if (index == 4) {
                    // History
                    if (_loggedInUserId != null) {
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (context) => const HistoryPage(),
                        ),
                      );
                    } else {
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (context) => const LoginPage(),
                        ),
                      ).then((_) => _loadUserData());
                    }
                  }
                },
                // 3. Items list reordered
                items: [
                  BottomNavigationBarItem(
                      icon: Icon(Icons.library_music), label: "Category"),
                  BottomNavigationBarItem(
                      icon: Icon(Icons.playlist_play), label: "Playlist"),
                  BottomNavigationBarItem(
                      icon: Icon(Icons.home), label: "Home"),
                  BottomNavigationBarItem(
                      icon: Icon(Icons.favorite), label: "Favourite"),
                  BottomNavigationBarItem(
                      icon: Icon(Icons.history), label: "History"),
                ],
              ),
            ),
          ),
        ),
      ),
      // --- END OF MODIFIED WIDGET ---
    );
  }

  // --- THIS WIDGET IS MODIFIED ---
  Widget searchBar() {
    final hintStyle = GoogleFonts.cabinSketch(
      fontSize: 16,
      color: Colors.pink[900],
    );
    final hintFirstLetterStyle = GoogleFonts.cabinSketch(
      fontSize: 16,
      color: Colors.pink[900],
    );
    final inputStyle = GoogleFonts.cabinSketch(
      fontSize: 16,
      color: Colors.white,
      fontWeight: FontWeight.normal,
    );
    final inputFirstLetterStyle = GoogleFonts.cabinSketch(
      fontSize: 16,
      color: Colors.pinkAccent,
      fontWeight: FontWeight.bold,
    );
    final textFieldStyle = GoogleFonts.cabinSketch(
      fontSize: 16,
      color: Colors.transparent,
    );
    return Container(
      height: 50,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(30),
        gradient: LinearGradient(
          colors: [
            Colors.pinkAccent.withOpacity(0.8),
            Colors.blueAccent.withOpacity(0.8),
          ],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
      ),
      child: Padding(
        padding: const EdgeInsets.all(1.5),
        child: Container(
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(28.5),
            // --- MODIFICATION HERE ---
            color: Colors.black, // Replaced gradient with solid color
            // --- END MODIFICATION ---
          ),
          child: Row(
            children: [
              SizedBox(width: 16),
              Icon(Icons.search, color: Color(0xFFC2185B), size: 28),
              SizedBox(width: 12),
              Expanded(
                child: Stack(
                  alignment: Alignment.centerLeft,
                  children: [
                    TextField(
                      controller: _searchController,
                      focusNode: _focusNode,
                      style: textFieldStyle,
                      cursorColor: Colors.pinkAccent,
                      decoration: InputDecoration(
                        hintText: "",
                        hintStyle: TextStyle(color: Colors.transparent),
                        border: InputBorder.none,
                        isDense: true,
                        contentPadding: EdgeInsets.symmetric(vertical: 12.0),
                      ),
                    ),
                    if (_searchController.text.isEmpty)
                      IgnorePointer(
                        child: RichText(
                          text: TextSpan(children: [
                            TextSpan(text: "S", style: hintFirstLetterStyle),
                            TextSpan(
                                text: "earch songs and artists...",
                                style: hintStyle),
                          ]),
                        ),
                      ),
                    if (_searchController.text.isNotEmpty)
                      IgnorePointer(
                        child: RichText(
                          text: TextSpan(children: [
                            TextSpan(
                                text: _searchController.text[0],
                                style: inputFirstLetterStyle),
                            if (_searchController.text.length > 1)
                              TextSpan(
                                  text: _searchController.text.substring(1),
                                  style: inputStyle),
                          ]),
                        ),
                      ),
                  ],
                ),
              ),
              if (_isFocused && _searchController.text.isNotEmpty)
                IconButton(
                  icon: Icon(Icons.clear, color: Colors.white54),
                  onPressed: _clearSearch,
                )
              else
                SizedBox(width: 16),
            ],
          ),
        ),
      ),
    );
  }
  // --- END OF MODIFICATION ---

  Widget profileAvatar() {
    ImageProvider backgroundImage;

    if (_userImage != null && _userImage!.isNotEmpty) {
      backgroundImage = NetworkImage(_userImage!);
    } else {
      backgroundImage = AssetImage("assets/user.png");
    }

    return GestureDetector(
      onTap: () {
        _scaffoldKey.currentState?.openEndDrawer();
      },
      child: Container(
        decoration: BoxDecoration(
          shape: BoxShape.circle,
          boxShadow: [
            BoxShadow(
              color: Colors.blueAccent.withOpacity(0.4),
              offset: Offset(0, 0),
              blurRadius: 20,
              spreadRadius: 2,
              blurStyle: BlurStyle.outer,
            ),
          ],
        ),
        child: CircleAvatar(
          radius: 22,
          backgroundColor: Colors.transparent,
          backgroundImage: backgroundImage,
          onBackgroundImageError: (backgroundImage is NetworkImage)
              ? (e, s) {
            print("Failed to load user network image: $e");
          }
              : null,
        ),
      ),
    );
  }

  Widget sectionTitle(String text,
      {Color firstColor = const Color(0xFFFF5096)}) {
    final kaushanStyle = GoogleFonts.cabinSketch(
      fontWeight: FontWeight.w600,
    );
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: RichText(
        text: TextSpan(
          children: [
            TextSpan(
              text: text.isNotEmpty ? text[0] : '', // Safe check for empty string
              style: kaushanStyle.copyWith(
                fontSize: 25,
                color: firstColor,
              ),
            ),
            if (text.length > 1)
              TextSpan(
                text: text.substring(1),
                style: kaushanStyle.copyWith(
                  fontSize: 20,
                  color: Colors.white,
                ),
              ),
          ],
        ),
      ),
    );
  }

  Widget artistCard(String name, String photoUrl) {
    return Container(
      width: 100,
      margin: EdgeInsets.only(right: 12),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(10),
        child: Stack(
          children: [
            FadeInImage.assetNetwork(
              placeholder: 'assets/placeholder.png',
              image: photoUrl,
              fit: BoxFit.cover,
              width: 100,
              height: 140,
              imageErrorBuilder: (context, error, stackTrace) {
                return Image.asset(
                  'assets/placeholder.png',
                  fit: BoxFit.cover,
                  width: 100,
                  height: 140,
                );
              },
            ),
            Container(
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.bottomCenter,
                  end: Alignment.topCenter,
                  colors: [
                    Colors.black.withOpacity(1),
                    Colors.black.withOpacity(0.0),
                  ],
                ),
              ),
            ),
            Positioned(
              bottom: 8,
              left: 8,
              right: 8,
              child: Text(
                name,
                style: GoogleFonts.cabinSketch(
                  color: Colors.white,
                  fontWeight: FontWeight.bold,
                  shadows: [
                    Shadow(
                      blurRadius: 4.0,
                      color: Colors.black,
                      offset: Offset(2.0, 2.0),
                    ),
                  ],
                ),
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget songAlbumCard(String title, String? coverUrl) {
    return Container(
      width: 120,
      margin: EdgeInsets.only(right: 12),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(10),
        child: Stack(
          children: [
            FadeInImage.assetNetwork(
              placeholder: 'assets/placeholder.png',
              image: coverUrl ?? '',
              fit: BoxFit.cover,
              width: 120,
              height: 130,
              imageErrorBuilder: (context, error, stackTrace) {
                return Image.asset(
                  'assets/placeholder.png',
                  fit: BoxFit.cover,
                  width: 120,
                  height: 130,
                );
              },
            ),
            Container(
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.bottomCenter,
                  end: Alignment.topCenter,
                  colors: [
                    Colors.black.withOpacity(1),
                    Colors.black.withOpacity(0.0),
                  ],
                ),
              ),
            ),
            Positioned(
              bottom: 8,
              left: 8,
              right: 8,
              child: Text(
                title,
                style: GoogleFonts.cabinSketch(
                  color: Colors.white,
                  fontWeight: FontWeight.bold,
                  shadows: [
                    Shadow(
                      blurRadius: 4.0,
                      color: Colors.black,
                      offset: Offset(2.0, 2.0),
                    ),
                  ],
                ),
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
              ),
            ),
          ],
        ),
      ),
    );
  }

  // --- WIDGET FOR THE GRADIENT LOGIN BUTTON ---
  Widget _buildLoginGradientButton() {
    final loginStyle = GoogleFonts.cabinSketch(
      color: Colors.white,
      fontSize: 18,
      fontWeight: FontWeight.w700,
    );
    final pinkLetterStyle = loginStyle.copyWith(
      color: Colors.pinkAccent,
    );

    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16.0, vertical: 8.0),
      child: GestureDetector(
        onTap: () {
          Navigator.pop(context); // Close the drawer first
          Navigator.push(
            context,
            MaterialPageRoute(
              builder: (context) => const LoginPage(),
            ),
          ).then((_) => _loadUserData());
        },
        child: Container(
          width: double.infinity,
          padding: const EdgeInsets.symmetric(vertical: 10),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(18),
            gradient: const LinearGradient(
              colors: [Color(0xFF8E2DE2), Color(0xFF4A00E0), Color(0xFF00D1FF)],
              begin: Alignment.centerLeft,
              end: Alignment.centerRight,
            ),
            boxShadow: [
              BoxShadow(
                color: const Color(0xFF7F53FF).withOpacity(0.6),
                blurRadius: 24,
                spreadRadius: 1,
                offset: const Offset(0, 8),
              ),
            ],
          ),
          alignment: Alignment.center,
          child: RichText(
            text: TextSpan(children: [
              TextSpan(text: 'L', style: pinkLetterStyle),
              TextSpan(text: 'ogin', style: loginStyle),
            ]),
          ),
        ),
      ),
    );
  }
}

// --- SLIDER ---
class SliderWidget extends StatefulWidget {
  @override
  _SliderWidgetState createState() => _SliderWidgetState();
}

class _SliderWidgetState extends State<SliderWidget> {
  final PageController _pageController = PageController(initialPage: 0);
  Timer? _timer;
  int _currentPage = 0;
  List _sliderItems = [];
  bool _isSliderLoading = true;

  @override
  void initState() {
    super.initState();
    fetchSliderData();
  }

  Future<void> fetchSliderData() async {
    final url = Uri.parse("${AppConfig.baseUrl}slider.php");
    try {
      final response = await http.get(url);
      if (response.statusCode == 200) {
        final decodedResponse = jsonDecode(response.body);
        if (decodedResponse is List) {
          setState(() {
            _sliderItems = decodedResponse;
            _isSliderLoading = false;
          });
          _startTimer();
        } else {
          print(
              "Error: Invalid format for slider data. Response: ${response.body}");
          throw Exception("Invalid format for slider data");
        }
      } else {
        print(
            "Error: Failed to load slider data. Status code: ${response.statusCode}");
        throw Exception("Failed to load slider data");
      }
    } catch (e) {
      print("Error fetching slider data: $e");
      setState(() => _isSliderLoading = false);
    }
  }

  void _startTimer() {
    _timer?.cancel();
    if (_sliderItems.length < 2) return;
    _timer = Timer.periodic(const Duration(seconds: 4), (Timer timer) {
      if (_currentPage < _sliderItems.length - 1) {
        _currentPage++;
      } else {
        _currentPage = 0;
      }
      if (_pageController.hasClients) {
        _pageController.animateToPage(
          _currentPage,
          duration: const Duration(milliseconds: 350),
          curve: Curves.easeIn,
        );
      }
    });
  }

  @override
  void dispose() {
    _timer?.cancel();
    _pageController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final orientation = MediaQuery.of(context).orientation;
    final double containerHeight =
    (orientation == Orientation.landscape) ? 280.0 : 180.0;

    if (_isSliderLoading) {
      return Container(
        height: containerHeight,
        child: Center(
          child: CircularProgressIndicator(color: Colors.pinkAccent),
        ),
      );
    }

    if (_sliderItems.isEmpty) {
      return Container(
        height: containerHeight,
        child: Center(
          child: Text(
            "No featured songs",
            style: GoogleFonts.cabinSketch(color: Colors.white70),
          ),
        ),
      );
    }

    return Container(
      height: containerHeight,
      child: PageView.builder(
        controller: _pageController,
        itemCount: _sliderItems.length,
        itemBuilder: (context, index) {
          final item = _sliderItems[index] as Map<String, dynamic>;
          final songId = item['sid']?.toString();

          return GestureDetector(
            onTap: () {
              if (songId != null) {
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (context) => SongDetailPage(sid: songId),
                  ),
                );
              } else {
                print("Slider item has no sid: $item");
              }
            },
            child: _SliderCard(
              title: item["name"] ?? "Unknown Song",
              subtitle: item["singer_name"] ?? "Unknown Artist",
              imagePath: item["image"] ?? "",
            ),
          );
        },
      ),
    );
  }
}

// --- _SliderCard ---
class _SliderCard extends StatelessWidget {
  final String title;
  final String subtitle;
  final String imagePath;

  const _SliderCard({
    required this.title,
    required this.subtitle,
    required this.imagePath,
  });

  @override
  Widget build(BuildContext context) {
    final baseTitleStyle = GoogleFonts.cabinSketch(
      fontSize: 24,
      fontWeight: FontWeight.bold,
    );
    final pinkTitleStyle = baseTitleStyle.copyWith(color: Colors.pinkAccent);
    final whiteTitleStyle = baseTitleStyle.copyWith(color: Colors.white);

    return Container(
      width: 280,
      margin: EdgeInsets.only(right: 12),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(20),
        child: Stack(
          children: [
            FadeInImage.assetNetwork(
              placeholder: 'assets/placeholder.png',
              image: imagePath,
              fit: BoxFit.cover,
              width: double.infinity,
              height: double.infinity,
              imageErrorBuilder: (context, error, stackTrace) {
                return Image.asset(
                  'assets/placeholder.png',
                  fit: BoxFit.cover,
                  width: double.infinity,
                  height: double.infinity,
                );
              },
            ),
            Container(
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.centerLeft,
                  end: Alignment.centerRight,
                  colors: [
                    Colors.black.withOpacity(0.7),
                    Colors.black.withOpacity(0.3),
                    Colors.black.withOpacity(0.0),
                  ],
                  stops: [0.0, 0.6, 1.0],
                ),
              ),
            ),
            Padding(
              padding: const EdgeInsets.all(20.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisAlignment: MainAxisAlignment.end,
                children: [
                  RichText(
                    text: TextSpan(
                      children: [
                        if (title.isNotEmpty) ...[
                          TextSpan(
                            text: title[0],
                            style: pinkTitleStyle,
                          ),
                          if (title.length > 1)
                            TextSpan(
                              text: title.substring(1),
                              style: whiteTitleStyle,
                            ),
                        ] else ...[
                          TextSpan(
                              text: 'Unknown Song', style: whiteTitleStyle),
                        ]
                      ],
                    ),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                  SizedBox(height: 4),
                  Text(
                    subtitle,
                    style: GoogleFonts.cabinSketch(
                      fontSize: 14,
                      color: Colors.pinkAccent,
                      fontWeight: FontWeight.bold,
                    ),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}

// --- TodaysSpecialWidget ---
class TodaysSpecialWidget extends StatefulWidget {
  @override
  _TodaysSpecialWidgetState createState() => _TodaysSpecialWidgetState();
}

class _TodaysSpecialWidgetState extends State<TodaysSpecialWidget> {
  List<Map<String, dynamic>> _specialSongs = [];
  bool _isSpecialLoading = true;

  @override
  void initState() {
    super.initState();
    fetchTodaysSpecial();
  }

  @override
  void dispose() {
    super.dispose();
  }

  Future<void> fetchTodaysSpecial() async {
    final url = Uri.parse("${AppConfig.baseUrl}getSpecial.php");
    try {
      final response = await http.get(url);
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);

        if (data != null && data is List) {
          List<Map<String, dynamic>> correctlyTypedList =
          List<Map<String, dynamic>>.from(data);

          setState(() {
            _specialSongs = correctlyTypedList;
            _isSpecialLoading = false;
          });
        } else {
          print(
              "Error: Invalid format for today's special data. Response: ${response.body}");
          setState(() {
            _specialSongs = [];
            _isSpecialLoading = false;
          });
        }
      } else {
        print(
            "Error: Failed to load today's special. Status code: ${response.statusCode}");
        throw Exception("Failed to load today's special");
      }
    } catch (e) {
      print("Error fetching today's special: $e");
      if (mounted) {
        setState(() => _isSpecialLoading = false);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final orientation = MediaQuery.of(context).orientation;
    final double containerHeight =
    (orientation == Orientation.landscape) ? 280.0 : 180.0;

    if (_isSpecialLoading) {
      return Container(
        height: containerHeight,
        child: Center(
          child: CircularProgressIndicator(color: Colors.pinkAccent),
        ),
      );
    }

    if (_specialSongs.isEmpty) {
      return Container(
        height: containerHeight,
        child: Center(
          child: Text(
            "No special songs today",
            style: GoogleFonts.cabinSketch(color: Colors.white70),
          ),
        ),
      );
    }

    final item = _specialSongs[0];

    return Container(
      height: containerHeight,
      child: GestureDetector(
        onTap: () {
          final String? songId = item['sid']?.toString();
          if (songId != null) {
            Navigator.push(
              context,
              MaterialPageRoute(
                builder: (context) => SongDetailPage(sid: songId),
              ),
            );
          } else {
            print("Error: Today's Special song has no sid.");
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(content: Text('Cannot open song. ID missing.')),
            );
          }
        },
        child: _SpecialSongCard(
          title: item["name"] ?? "Unknown Song",
          subtitle: item["singer_name"] ?? "Unknown Artist",
          imagePath: item["image"] ?? "",
        ),
      ),
    );
  }
}

// --- _SpecialSongCard ---
class _SpecialSongCard extends StatelessWidget {
  final String title;
  final String subtitle;
  final String imagePath;

  const _SpecialSongCard({
    required this.title,
    required this.subtitle,
    required this.imagePath,
  });

  @override
  Widget build(BuildContext context) {
    final baseTitleStyle = GoogleFonts.cabinSketch(
      fontSize: 24,
      fontWeight: FontWeight.bold,
    );
    final pinkTitleStyle = baseTitleStyle.copyWith(color: Colors.pinkAccent);
    final whiteTitleStyle = baseTitleStyle.copyWith(color: Colors.white);

    return Container(
      child: ClipRRect(
        borderRadius: BorderRadius.circular(20),
        child: Stack(
          children: [
            FadeInImage.assetNetwork(
              placeholder: 'assets/placeholder.png',
              image: imagePath,
              fit: BoxFit.cover,
              width: double.infinity,
              height: double.infinity,
              imageErrorBuilder: (context, error, stackTrace) {
                return Image.asset(
                  'assets/placeholder.png',
                  fit: BoxFit.cover,
                  width: double.infinity,
                  height: double.infinity,
                );
              },
            ),
            Container(
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.centerLeft,
                  end: Alignment.centerRight,
                  colors: [
                    Colors.black.withOpacity(0.7),
                    Colors.black.withOpacity(0.3),
                    Colors.black.withOpacity(0.0),
                  ],
                  stops: [0.0, 0.6, 1.0],
                ),
              ),
            ),
            Padding(
              padding: const EdgeInsets.all(20.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisAlignment: MainAxisAlignment.end,
                children: [
                  RichText(
                    text: TextSpan(
                      children: [
                        if (title.isNotEmpty) ...[
                          TextSpan(
                            text: title[0],
                            style: pinkTitleStyle,
                          ),
                          if (title.length > 1)
                            TextSpan(
                              text: title.substring(1),
                              style: whiteTitleStyle,
                            ),
                        ] else ...[
                          TextSpan(
                              text: 'Unknown Song', style: whiteTitleStyle),
                        ]
                      ],
                    ),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                  SizedBox(height: 4),
                  Text(
                    subtitle,
                    style: GoogleFonts.cabinSketch(
                      fontSize: 14,
                      color: Colors.pinkAccent,
                      fontWeight: FontWeight.bold,
                    ),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}