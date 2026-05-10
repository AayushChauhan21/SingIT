import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'dart:io';
import 'dart:ui'; // For blur effect
import 'package:image_picker/image_picker.dart';
import 'package:permission_handler/permission_handler.dart';
import 'package:cloudinary_public/cloudinary_public.dart';
import 'config.dart';
import 'login.dart';
import 'edit_profile.dart';

class ProfilePage extends StatefulWidget {
  const ProfilePage({Key? key}) : super(key: key);

  @override
  State<ProfilePage> createState() => _ProfilePageState();
}

class _ProfilePageState extends State<ProfilePage> {
  String? _loggedInUserId;
  String _userName = "Loading...";
  String _userEmail = "...";
  String? _userImage;
  bool _isUserDataLoading = true;

  final ImagePicker _picker = ImagePicker();
  File? _newImageFile;

  final cloudinary = CloudinaryPublic(
      'do3hihcwa',
      'ml_default',
      cache: false
  );

  @override
  void initState() {
    super.initState();
    _loadUserData();
  }

  /// 1. Checks SharedPreferences for a user ID
  Future<void> _loadUserData() async {
    if (mounted) {
      setState(() {
        _isUserDataLoading = true;
      });
    }

    final prefs = await SharedPreferences.getInstance();
    final String? userId = prefs.getString('user_id');

    if (userId != null) {
      if (mounted) {
        setState(() {
          _loggedInUserId = userId;
        });
      }
      await _fetchUserData(userId);
    } else {
      if (mounted) {
        setState(() {
          _isUserDataLoading = false;
          _loggedInUserId = null;
          _userName = "Guest User";
          _userEmail = "Please log in";
          _userImage = null;
        });
      }
    }
  }

  /// 2. Calls your PHP script
  Future<void> _fetchUserData(String userId) async {
    final url = Uri.parse(
        "${AppConfig.baseUrl}getUserData.php?uid=${Uri.encodeComponent(userId)}");

    try {
      final response = await http.get(url);
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);

        if (data['success'] == true) {
          if (mounted) {
            setState(() {
              _userName = data['user']['name'] ?? 'Unknown Name';
              _userEmail = data['user']['email'] ?? 'No Email';
              _userImage = data['user']['photo'];
            });
          }
        } else {
          print("API Error: ${data['error']}");
          _handleLogout(navigateBack: false);
        }
      } else {
        print("HTTP Error: ${response.statusCode}");
      }
    } catch (e) {
      print("Error fetching user data: $e");
    } finally {
      if (mounted) {
        setState(() {
          _isUserDataLoading = false;
        });
      }
    }
  }

  /// 3. Clears the session and resets the UI
  Future<void> _handleLogout({bool navigateBack = true}) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('user_id');

    if (mounted) {
      setState(() {
        _loggedInUserId = null;
        _userName = "Guest User";
        _userEmail = "Please log in";
        _userImage = null;
        _isUserDataLoading = false;
      });

      if (navigateBack) {
        Navigator.pushAndRemoveUntil(
          context,
          MaterialPageRoute(builder: (context) => const LoginPage()),
              (Route<dynamic> route) => false,
        );
      }
    }
  }

  /// 4. Navigation helper for "Edit Profile" list item
  void _navigateToEditProfile() {
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (context) => const EditProfilePage(),
      ),
    ).then((_) {
      _loadUserData();
    });
  }

  // --- 5. IMAGE PICKING FUNCTION ---
  Future<void> _pickAndConfirmImage() async {
    Map<Permission, PermissionStatus> statuses = await [
      Permission.photos,
      Permission.storage,
    ].request();

    bool isGranted = statuses[Permission.photos] == PermissionStatus.granted ||
        statuses[Permission.storage] == PermissionStatus.granted;

    bool isPermanentlyDenied =
        statuses[Permission.photos] == PermissionStatus.permanentlyDenied ||
            statuses[Permission.storage] == PermissionStatus.permanentlyDenied;

    if (isPermanentlyDenied) {
      print("Permission permanently denied. Opening settings.");
      openAppSettings();
      return;
    }

    if (isGranted) {
      print("Permission granted, opening gallery.");
      final XFile? pickedFile =
      await _picker.pickImage(source: ImageSource.gallery, imageQuality: 70);

      if (pickedFile != null) {
        setState(() {
          _newImageFile = File(pickedFile.path);
        });
        _showConfirmationDialog();
      }
    } else {
      print("Photo permission was denied.");
    }
  }

  // --- 6. CONFIRMATION DIALOG ---
  Future<void> _showConfirmationDialog() async {
    if (_newImageFile == null) return;

    showDialog(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          backgroundColor: const Color(0xFF282828),
          title: Text(
            'Update Photo?',
            style: GoogleFonts.cabinSketch(color: Colors.white),
          ),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Text(
                'Are you sure you want to use this photo?',
                style: GoogleFonts.cabinSketch(color: Colors.white70),
              ),
              const SizedBox(height: 20),
              CircleAvatar(
                radius: 60,
                backgroundImage: FileImage(_newImageFile!),
              ),
            ],
          ),
          actions: [
            TextButton(
              child: Text(
                'Cancel',
                style: GoogleFonts.cabinSketch(color: Colors.white70),
              ),
              onPressed: () {
                setState(() {
                  _newImageFile = null; // Clear selection
                });
                Navigator.of(context).pop();
              },
            ),
            TextButton(
              child: Text(
                'Confirm',
                style: GoogleFonts.cabinSketch(color: Colors.pinkAccent),
              ),
              onPressed: () {
                Navigator.of(context).pop(); // Close dialog
                _uploadImageToCloudinary();
              },
            ),
          ],
        );
      },
    );
  }

  // --- 8. CLOUDINARY UPLOAD FUNCTION ---
  Future<void> _uploadImageToCloudinary() async {
    if (_newImageFile == null || _loggedInUserId == null) return;

    // Show loading dialog
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (BuildContext context) {
        return Dialog(
          backgroundColor: const Color(0xFF282828),
          child: Padding(
            padding: const EdgeInsets.all(20.0),
            child: Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                const CircularProgressIndicator(color: Colors.pinkAccent),
                const SizedBox(width: 20),
                Text(
                  "Uploading...",
                  style:
                  GoogleFonts.cabinSketch(color: Colors.white, fontSize: 18),
                ),
              ],
            ),
          ),
        );
      },
    );

    try {
      // 1. Upload to Cloudinary
      CloudinaryResponse response = await cloudinary.uploadFile(
        CloudinaryFile.fromFile(_newImageFile!.path,
            resourceType: CloudinaryResourceType.Image),
      );

      // 2. Get the new URL
      final String secureUrl = response.secureUrl;

      // 3. Send the URL to YOUR server
      final url = Uri.parse("${AppConfig.baseUrl}updateProfilePic.php");
      final http.Response postResponse = await http.post(
        url,
        body: {
          'uid': _loggedInUserId!,
          'photo_url': secureUrl,
        },
      );

      if (mounted) Navigator.pop(context); // Close loading dialog

      if (postResponse.statusCode == 200) {
        final data = jsonDecode(postResponse.body);
        if (data['success'] == true) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Profile picture updated!'),
              backgroundColor: Colors.green,
            ),
          );
          _loadUserData(); // Refresh the page to show new image
        } else {
          throw Exception(data['error'] ?? 'Server error');
        }
      } else {
        throw Exception('Server error: ${postResponse.statusCode}');
      }
    } catch (e) {
      print("Error uploading image: $e");
      if (mounted) Navigator.pop(context); // Close loading dialog
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Error: $e'),
          backgroundColor: Colors.redAccent,
        ),
      );
    } finally {
      if (mounted) {
        setState(() {
          _newImageFile = null; // Clear selection
        });
      }
    }
  }

  // --- CLEAR HISTORY FUNCTIONS ---

  Future<void> _showClearHistoryDialog() async {
    return showDialog<void>(
      context: context,
      barrierDismissible: false, // User must tap button
      builder: (BuildContext context) {
        // Define text styles
        final titleStyle = GoogleFonts.cabinSketch(
            color: Colors.white, fontWeight: FontWeight.bold);
        final pinkTitleStyle = titleStyle.copyWith(color: Colors.pinkAccent);

        return AlertDialog(
          backgroundColor: Color(0xFF282828),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(16.0),
            side: BorderSide(color: Colors.pinkAccent, width: 2),
          ),
          title: RichText(
            text: TextSpan(
              style: titleStyle, // Default style
              children: <TextSpan>[
                TextSpan(text: 'Clear', style: pinkTitleStyle),
                TextSpan(text: ' All History?'),
              ],
            ),
          ),
          content: Text(
            'Are you sure you want to delete your entire play history? This action cannot be undone.',
            style: GoogleFonts.cabinSketch(color: Colors.white70),
          ),
          actions: <Widget>[
            TextButton(
              child: Text(
                'Cancel',
                style: GoogleFonts.cabinSketch(
                    color: Colors.white70, fontWeight: FontWeight.bold),
              ),
              onPressed: () {
                Navigator.of(context).pop();
              },
            ),
            TextButton(
              child: Text(
                'Yes, Delete All',
                style: GoogleFonts.cabinSketch(
                    color: Colors.redAccent, fontWeight: FontWeight.bold),
              ),
              onPressed: () {
                Navigator.of(context).pop(); // Close the dialog
                _clearAllHistory(); // Call the delete function
              },
            ),
          ],
        );
      },
    );
  }

  Future<void> _clearAllHistory() async {
    if (_loggedInUserId == null) {
      print("Cannot clear history, user ID is null");
      return;
    }

    try {
      final url = Uri.parse(
          "${AppConfig.baseUrl}clear_history.php?user_id=$_loggedInUserId");
      final response = await http.get(url);

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['status'] == 'success') {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text('History cleared',
                  style: GoogleFonts.cabinSketch(color: Colors.white)),
              backgroundColor: Colors.green,
            ),
          );
        } else {
          throw Exception(data['message'] ?? 'Failed to clear history');
        }
      } else {
        throw Exception('Server error: ${response.statusCode}');
      }
    } catch (e) {
      print("Error clearing history: $e");
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
    // Theme colors
    const Color primaryBg = Colors.black;
    const Color cardBg = Color(0xFF282828);
    const Color primaryAccent = Colors.pinkAccent;
    const Color secondaryAccent = Colors.blueAccent;
    const Color primaryText = Colors.white;
    const Color secondaryText = Colors.white70;

    // --- UPDATED IMAGE LOGIC ---
    ImageProvider profileImage;
    if (_newImageFile != null) {
      // 1. If we just picked a new file, show it
      profileImage = FileImage(_newImageFile!);
    } else if (_userImage != null && _userImage!.isNotEmpty) {
      // 2. Otherwise, show the one from the server
      profileImage = NetworkImage(_userImage!);
    } else {
      // 3. Fallback to the default asset
      profileImage = const AssetImage("assets/user.png");
    }
    // --- END ---

    return Scaffold(
      backgroundColor: primaryBg,
      appBar: AppBar(
        backgroundColor: primaryBg,
        elevation: 0,
        title: Text(
          'Profile',
          style: GoogleFonts.cabinSketch(
              color: primaryText, fontWeight: FontWeight.bold),
        ),
        centerTitle: true,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new, color: primaryText),
          onPressed: () {
            if (Navigator.canPop(context)) {
              Navigator.pop(context);
            }
          },
        ),
        actions: const [],
      ),
      body: _isUserDataLoading
          ? const Center(
        child: CircularProgressIndicator(color: primaryAccent),
      )
          : SingleChildScrollView(
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 24.0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.center,
            children: [
              const SizedBox(height: 20),
              // --- PROFILE PICTURE STACK ---
              Stack(
                alignment: Alignment.bottomRight,
                children: [
                  // --- WRAPPED AVATAR IN HERO & GESTUREDETECTOR ---
                  Hero(
                    tag: 'profilePic', // Unique tag for the animation
                    child: GestureDetector(
                      onTap: () {
                        // --- NAVIGATION TO FULL SCREEN ---
                        Navigator.push(
                          context,
                          // Use PageRouteBuilder for a clean fade transition
                          PageRouteBuilder(
                            opaque: false, // Makes background transparent
                            barrierColor: Colors.black.withOpacity(0.8),
                            pageBuilder: (context, animation, secondaryAnimation) {
                              return FadeTransition(
                                opacity: animation,
                                child: _FullScreenImageViewer(imageProvider: profileImage),
                              );
                            },
                          ),
                        );
                      },
                      child: CircleAvatar(
                        radius: 50,
                        backgroundColor: cardBg,
                        backgroundImage: profileImage,
                        onBackgroundImageError: (e, s) {
                          print("Failed to load profile image: $e");
                        },
                      ),
                    ),
                  ),
                  // The small edit button
                  GestureDetector(
                    onTap: _pickAndConfirmImage,
                    child: Container(
                      padding: const EdgeInsets.all(8),
                      decoration: const BoxDecoration(
                        color: primaryAccent,
                        shape: BoxShape.circle,
                        border: Border.fromBorderSide(
                          BorderSide(color: primaryBg, width: 2),
                        ),
                      ),
                      child: const Icon(
                        Icons.edit, // "Pen" icon
                        color: primaryText,
                        size: 18,
                      ),
                    ),
                  ),
                ],
              ),
              // --- END OF STACK ---
              const SizedBox(height: 16),
              // User Name
              Builder(builder: (context) {
                final String displayName = _userName;
                final nameStyle = GoogleFonts.cabinSketch(
                  fontSize: 28,
                  fontWeight: FontWeight.bold,
                  color: primaryText,
                );
                return RichText(
                  text: TextSpan(
                    children: [
                      TextSpan(
                        text: displayName.isNotEmpty ? displayName[0] : '?',
                        style: nameStyle.copyWith(color: primaryAccent),
                      ),
                      if (displayName.length > 1)
                        TextSpan(
                          text: displayName.substring(1),
                          style: nameStyle,
                        ),
                    ],
                  ),
                );
              }),
              const SizedBox(height: 4),
              // User Email
              Text(
                _userEmail,
                style: GoogleFonts.cabinSketch(
                  color: primaryAccent,
                  fontSize: 16,
                  fontWeight: FontWeight.w600,
                ),
              ),

              // --- REMOVED STATS ROW (Active, Pending, Complete) ---

              const SizedBox(height: 30),
              // Menu List
              Column(
                children: [
                  _MenuListItem(
                    icon: Icons.edit,
                    title: 'Edit Profile',
                    subtitle: 'Edit your Profile',
                    iconColor: secondaryAccent,
                    iconBgColor: primaryBg,
                    textColor: primaryText,
                    subtextColor: secondaryText,
                    onTap: _navigateToEditProfile,
                  ),
                  _MenuListItem(
                    icon: Icons.delete_sweep_outlined,
                    title: 'Clear History',
                    subtitle: 'Remove all saved history',
                    iconColor: Colors.redAccent,
                    iconBgColor: primaryBg,
                    textColor: primaryText,
                    subtextColor: secondaryText,
                    onTap: () {
                      if (_loggedInUserId != null) {
                        _showClearHistoryDialog();
                      } else {
                        ScaffoldMessenger.of(context).showSnackBar(
                          const SnackBar(content: Text('You are not logged in.')),
                        );
                      }
                    },
                  ),
                  _MenuListItem(
                    icon: Icons.logout,
                    title: 'Logout',
                    subtitle: 'Clear session and sign out',
                    iconColor: secondaryAccent,
                    iconBgColor: primaryBg,
                    textColor: primaryText,
                    subtextColor: secondaryText,
                    onTap: _handleLogout,
                  ),
                ],
              ),
              const SizedBox(height: 30),
            ],
          ),
        ),
      ),
    );
  }
}

// --- MENU LIST ITEM WIDGET ---
class _MenuListItem extends StatelessWidget {
  final IconData icon;
  final String title;
  final String subtitle;
  final Color iconColor;
  final Color iconBgColor;
  final Color textColor;
  final Color subtextColor;
  final VoidCallback? onTap;

  const _MenuListItem({
    Key? key,
    required this.icon,
    required this.title,
    required this.subtitle,
    required this.iconColor,
    required this.iconBgColor,
    required this.textColor,
    required this.subtextColor,
    this.onTap,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.symmetric(vertical: 4),
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
            color: const Color(0xFF282828),
            child: Material(
              color: Colors.transparent,
              child: InkWell(
                onTap: onTap, // Wired up to InkWell
                child: Padding(
                  padding: const EdgeInsets.symmetric(
                      horizontal: 16.0, vertical: 12.0),
                  child: Row(
                    children: [
                      CircleAvatar(
                        backgroundColor: iconBgColor,
                        radius: 24,
                        child: Icon(
                          icon,
                          color: iconColor,
                          size: 24,
                        ),
                      ),
                      const SizedBox(width: 16),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              title,
                              style: GoogleFonts.cabinSketch(
                                color: textColor,
                                fontSize: 17,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            const SizedBox(height: 2),
                            Text(
                              subtitle,
                              style: GoogleFonts.cabinSketch(
                                color: subtextColor,
                                fontSize: 14,
                                fontWeight: FontWeight.w600,
                              ),
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                            ),
                          ],
                        ),
                      ),
                      Icon(
                        Icons.arrow_forward_ios,
                        color: Colors.white54,
                        size: 16,
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }
}

// --- FULL SCREEN IMAGE VIEWER ---
class _FullScreenImageViewer extends StatelessWidget {
  final ImageProvider imageProvider;

  const _FullScreenImageViewer({Key? key, required this.imageProvider})
      : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.transparent, // Make scaffold bg transparent
      body: SafeArea(
        child: Stack(
          children: [
            // The image viewer
            Center(
              child: InteractiveViewer(
                panEnabled: false, // To prevent panning
                boundaryMargin: const EdgeInsets.all(20),
                minScale: 1.0,
                maxScale: 4.0,
                child: Hero(
                  tag: 'profilePic', // Must match the tag on the CircleAvatar
                  child: Image(
                    image: imageProvider,
                    fit: BoxFit.contain,
                  ),
                ),
              ),
            ),
            // The close button
            Positioned(
              top: 10,
              right: 10,
              child: IconButton(
                icon: const Icon(Icons.close, color: Colors.white, size: 30),
                onPressed: () {
                  Navigator.pop(context);
                },
              ),
            ),
          ],
        ),
      ),
    );
  }
}