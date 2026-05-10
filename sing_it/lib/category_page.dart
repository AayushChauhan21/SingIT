import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'config.dart'; // Assuming you have this config file
import 'category_result.dart';

class CategoryPage extends StatefulWidget {
  const CategoryPage({Key? key}) : super(key: key);

  @override
  _CategoryPageState createState() => _CategoryPageState();
}

class _CategoryPageState extends State<CategoryPage> {
  List<Map<String, dynamic>> _categories = [];
  bool _isLoading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _fetchCategories();
  }

  Future<void> _fetchCategories() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    final url = Uri.parse("${AppConfig.baseUrl}getCategories.php");

    try {
      final response = await http.get(url);
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);

        if (data is Map && data.containsKey('error')) {
          throw Exception(data['error']);
        }

        if (data['categories'] != null) {
          setState(() {
            _categories = List<Map<String, dynamic>>.from(data['categories']);
            _isLoading = false;
          });
        } else {
          throw Exception('Could not find categories in response.');
        }
      } else {
        throw Exception(
            'Failed to load categories (Status code: ${response.statusCode})');
      }
    } catch (e) {
      print("Error fetching categories: $e");
      setState(() {
        _error = e.toString();
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.black,
      appBar: AppBar(
        iconTheme: IconThemeData(color: Colors.white),
        title: Text(
          'Categories',
          // --- ✅ MODIFICATION: Changed font to CabinSketch ---
          style: GoogleFonts.cabinSketch(
            color: Colors.white,
            fontWeight: FontWeight.bold,
            fontSize: 20,
          ),
          // --- END OF MODIFICATION ---
        ),
        centerTitle: true,
        backgroundColor: Colors.black,
        elevation: 0,
      ),
      body: _buildBody(),
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
            style: TextStyle(color: Colors.redAccent),
            textAlign: TextAlign.center,
          ),
        ),
      );
    }

    if (_categories.isEmpty) {
      return Center(
        child: Text(
          'No categories found.',
          style: TextStyle(color: Colors.white70),
        ),
      );
    }

    return GridView.builder(
      padding: const EdgeInsets.all(8.0),
      gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: 2,
        crossAxisSpacing: 16,
        mainAxisSpacing: 16,
        childAspectRatio: 1.25,
      ),
      itemCount: _categories.length,
      itemBuilder: (context, index) {
        final category = _categories[index];
        return _buildCategoryTile(category);
      },
    );
  }

  Widget _buildCategoryTile(Map<String, dynamic> category) {
    final imageUrl = category['image']?.toString() ?? '';
    final name = category['name']?.toString() ?? 'Unknown';
    // --- ADDED: Get the category ID ---
    final categoryId = category['id']?.toString() ?? '0';

    final songCount = category['song_count']?.toString() ?? '0';
    final songCountText = (songCount == '1') ? '1 Song' : '$songCount Songs';

    final nameStyle = GoogleFonts.cabinSketch(
      color: Colors.white,
      fontSize: 20,
      fontWeight: FontWeight.bold,
    );

    return GestureDetector(
      // --- MODIFIED: Updated onTap functionality ---
      onTap: () {
        print("Tapped on category: $name (ID: $categoryId)");
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (context) => CategoryResultPage(
              categoryId: categoryId, // Pass the ID
              categoryName: name,      // Pass the name
            ),
          ),
        );
      },
      // --- END MODIFIED ---
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
                  // Background Image
                  FadeInImage.assetNetwork(
                    placeholder: 'assets/placeholder.png',
                    image: imageUrl,
                    fit: BoxFit.cover,
                    imageErrorBuilder: (c, e, s) => Image.asset(
                      'assets/placeholder.png',
                      fit: BoxFit.cover,
                    ),
                  ),

                  // Gradient overlay
                  Container(
                    decoration: BoxDecoration(
                      gradient: LinearGradient(
                        colors: [
                          Colors.black.withOpacity(0.8), // Darker on the left
                          Colors.transparent, // Fades out to the right
                        ],
                        begin: Alignment.centerLeft, // Start from the left
                        end: Alignment.centerRight, // End on the right
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
                        // RichText for category name
                        RichText(
                          text: TextSpan(
                            children: [
                              if (name.isNotEmpty) ...[
                                TextSpan(
                                  text: name[0], // First letter
                                  style: nameStyle.copyWith(color: Colors.pinkAccent), // Pink color
                                ),
                                if (name.length > 1)
                                  TextSpan(
                                    text: name.substring(1), // Rest of the name
                                    style: nameStyle, // Default white color
                                  ),
                              ] else ...[
                                TextSpan(text: 'Unknown', style: nameStyle), // Fallback
                              ]
                            ],
                          ),
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                        ),

                        // Song Count Text
                        SizedBox(height: 4),
                        Text(
                          songCountText,
                          style: TextStyle(
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