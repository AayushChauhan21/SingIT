// lib/edit_profile.dart

import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart'; // From profile.dart
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';
import 'config.dart'; // Your AppConfig
import 'login.dart'; // For navigation
import 'dart:ui'; // For BackdropFilter

class EditProfilePage extends StatefulWidget {
  const EditProfilePage({Key? key}) : super(key: key);

  @override
  _EditProfilePageState createState() => _EditProfilePageState();
}

class _EditProfilePageState extends State<EditProfilePage>
    with SingleTickerProviderStateMixin {
  // --- Animation Controllers (from registration.dart) ---
  late final AnimationController _controller;
  late final Animation<double> _blobShift;
  late final Animation<double> _glowPulse;
  late final Animation<BorderRadius> _cardRadius;

  // --- Form & State ---
  final _formKey = GlobalKey<FormState>();
  final _nameController = TextEditingController();
  final _emailController = TextEditingController();
  final _passController = TextEditingController();
  final _confirmController = TextEditingController();

  final _nameFocus = FocusNode();
  final _emailFocus = FocusNode();
  final _passFocus = FocusNode();
  final _confirmFocus = FocusNode();

  bool _obscure = true;

  // --- Data & Loading ---
  String? _loggedInUserId;
  bool _isPageLoading = true; // For initial data fetch
  bool _isUpdating = false; // For update button press

  @override
  void initState() {
    super.initState();
    // Init animations
    _controller = AnimationController(
      vsync: this,
      duration: const Duration(seconds: 8),
    )..repeat(reverse: true);

    _blobShift = Tween<double>(begin: -40, end: 40)
        .chain(CurveTween(curve: Curves.easeInOut))
        .animate(_controller);

    _glowPulse = Tween<double>(begin: 0.35, end: 0.75)
        .chain(CurveTween(curve: Curves.easeInOut))
        .animate(_controller);

    _cardRadius = Tween<BorderRadius>(
      begin: BorderRadius.circular(26),
      end: BorderRadius.circular(40),
    ).animate(CurvedAnimation(parent: _controller, curve: Curves.easeInOut));

    // Focus listeners for glow effect
    _nameFocus.addListener(() => setState(() {}));
    _emailFocus.addListener(() => setState(() {}));
    _passFocus.addListener(() => setState(() {}));
    _confirmFocus.addListener(() => setState(() {}));

    // Start fetching data
    _loadAndFetchUserData();
  }

  @override
  void dispose() {
    _controller.dispose();
    _nameController.dispose();
    _emailController.dispose();
    _passController.dispose();
    _confirmController.dispose();
    _nameFocus.dispose();
    _emailFocus.dispose();
    _passFocus.dispose();
    _confirmFocus.dispose();
    super.dispose();
  }

  // --- 1. DATA FETCHING LOGIC ---
  Future<void> _loadAndFetchUserData() async {
    final prefs = await SharedPreferences.getInstance();
    final String? userId = prefs.getString('user_id');

    if (userId == null) {
      // User not logged in
      if (mounted) {
        Navigator.pushAndRemoveUntil(
          context,
          MaterialPageRoute(builder: (context) => const LoginPage()),
              (Route<dynamic> route) => false,
        );
      }
      return;
    }

    setState(() {
      _loggedInUserId = userId;
    });

    try {
      final url = Uri.parse(
          "${AppConfig.baseUrl}getUserData.php?uid=${Uri.encodeComponent(userId)}");
      final response = await http.get(url);

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);

        if (data['success'] == true && data['user'] != null) {
          // Prefill fields
          setState(() {
            _nameController.text = data['user']['name'] ?? '';
            _emailController.text = data['user']['email'] ?? '';
            // *** We intentionally do NOT pre-fill the password ***
          });
        } else {
          throw Exception(data['error'] ?? 'Failed to find user data');
        }
      } else {
        throw Exception('Server error: ${response.statusCode}');
      }
    } catch (e) {
      print("Error fetching user data: $e");
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Error: $e'),
            backgroundColor: Colors.redAccent,
          ),
        );
        Navigator.pop(context); // Can't load data, pop back
      }
    } finally {
      if (mounted) {
        setState(() {
          _isPageLoading = false;
        });
      }
    }
  }

  // --- 2. UPDATE PROFILE LOGIC (Simplified) ---
  Future<void> _handleUpdateProfile() async {
    if (!_formKey.currentState!.validate() || _loggedInUserId == null) {
      return;
    }

    setState(() {
      _isUpdating = true;
    });

    // Show loading dialog
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (BuildContext context) {
        return Dialog(
          backgroundColor: const Color(0xFF1E1E1E).withOpacity(0.8),
          child: Padding(
            padding: const EdgeInsets.all(20.0),
            child: Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                const CircularProgressIndicator(color: Colors.pinkAccent),
                const SizedBox(width: 20),
                Text(
                  "Updating...",
                  style: GoogleFonts.cabinSketch(
                      color: Colors.white, fontSize: 18),
                ),
              ],
            ),
          ),
        );
      },
    );

    try {
      final url = Uri.parse("${AppConfig.baseUrl}updateProfile.php");

      // Create the request body
      Map<String, String> body = {
        'uid': _loggedInUserId!, // CRITICAL
        'name': _nameController.text,
        'email': _emailController.text,
      };

      // Only add password if user entered a new one
      if (_passController.text.isNotEmpty) {
        body['password'] = _passController.text;
      }

      // Use standard http.post
      final response = await http.post(url, body: body);

      if (mounted) Navigator.pop(context); // Dismiss loading dialog

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['success'] == true) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Profile updated successfully!'),
              backgroundColor: Colors.green,
            ),
          );
          if (mounted) Navigator.pop(context); // Go back to profile page
        } else {
          throw Exception(data['error'] ?? 'Unknown error');
        }
      } else {
        throw Exception('Server error: ${response.statusCode}');
      }
    } catch (e) {
      print("Error updating profile: $e");
      if (mounted) Navigator.pop(context); // Dismiss loading dialog
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Error: $e'),
          backgroundColor: Colors.redAccent,
        ),
      );
    } finally {
      if (mounted) {
        setState(() {
          _isUpdating = false;
        });
      }
    }
  }

  // --- Glow Icon (from registration.dart) ---
  Widget _glowIcon(IconData icon, bool focused) {
    const Color pinkColor = Color(0xFFCA8CFF);
    const Color aquaColor = Color(0xFF00D1FF);

    return AnimatedContainer(
      duration: const Duration(milliseconds: 300),
      child: Icon(
        icon,
        color: pinkColor, // Always pink
      ),
      decoration: focused
          ? BoxDecoration(
        boxShadow: [
          BoxShadow(
            color: aquaColor.withOpacity(0.7), // Aqua glow
            blurRadius: 18,
            spreadRadius: 1,
          )
        ],
      )
          : null,
    );
  }

  // --- 3. BUILD METHOD ---
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      // Use AnimatedBuilder for the background
      body: AnimatedBuilder(
        animation: _controller,
        builder: (context, _) {
          return Stack(
            children: [
              // Animated Background
              Positioned.fill(
                child: CustomPaint(
                  painter: _AnimatedBackgroundPainter(progress: _controller.value),
                ),
              ),
              Positioned(
                left: 40 + _blobShift.value,
                top: 80,
                child: _glowBlob(
                    size: 160,
                    color: const Color(0xFF7F53FF).withOpacity(0.35)),
              ),
              Positioned(
                right: 24 - _blobShift.value,
                bottom: 90,
                child: _glowBlob(
                    size: 200,
                    color: const Color(0xFF00D1FF).withOpacity(0.30)),
              ),

              // AppBar (for back button)
              Positioned(
                top: 0,
                left: 0,
                right: 0,
                child: AppBar(
                  title: Text(
                    'Edit Profile',
                    style: GoogleFonts.cabinSketch(
                        color: Colors.white, fontWeight: FontWeight.bold),
                  ),
                  centerTitle: true,
                  backgroundColor: Colors.transparent,
                  elevation: 0,
                  iconTheme: const IconThemeData(color: Colors.white),
                ),
              ),

              // Main Content
              SafeArea(
                child: Center(
                  child: Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 22, vertical: 20),
                    child: ConstrainedBox(
                      constraints: const BoxConstraints(maxWidth: 520),
                      child: _GlassCard(
                        radius: _cardRadius.value,
                        blurSigma: 20 + 4 * (1 - _controller.value),
                        opacity: 0.10 + 0.04 * (1 - _controller.value),
                        child: _isPageLoading
                            ? const Center(
                          child: Padding(
                            padding: EdgeInsets.all(32.0),
                            child: CircularProgressIndicator(
                              color: Colors.pinkAccent,
                            ),
                          ),
                        )
                            : _buildEditForm(), // Use a helper for the form
                      ),
                    ),
                  ),
                ),
              ),
            ],
          );
        },
      ),
    );
  }

  // --- 4. FORM WIDGET ---
  Widget _buildEditForm() {
    return SingleChildScrollView(
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          // --- Title ---
          ShaderMask(
            shaderCallback: (rect) => const LinearGradient(
              colors: [
                Color(0xFFCA8CFF),
                Color(0xFF7B6DFF),
                Color(0xFF00D1FF)
              ],
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ).createShader(rect),
            child: Text(
              'Update Details',
              style: GoogleFonts.cabinSketch(
                fontSize: 32,
                fontWeight: FontWeight.w800,
                color: Colors.white,
              ),
            ),
          ),
          const SizedBox(height: 24), // Added space

          // --- Form ---
          Form(
            key: _formKey,
            child: Column(
              children: [
                TextFormField(
                  focusNode: _nameFocus,
                  controller: _nameController, // Pre-filled
                  textInputAction: TextInputAction.next,
                  style: const TextStyle(color: Colors.white),
                  decoration: InputDecoration(
                    hintText: 'Name',
                    hintStyle: const TextStyle(color: Colors.white54),
                    prefixIcon:
                    _glowIcon(Icons.person_outline, _nameFocus.hasFocus),
                  ),
                  validator: (v) =>
                  (v == null || v.trim().isEmpty) ? 'Enter your name' : null,
                ),
                const SizedBox(height: 14),
                TextFormField(
                  focusNode: _emailFocus,
                  controller: _emailController, // Pre-filled
                  textInputAction: TextInputAction.next,
                  keyboardType: TextInputType.emailAddress,
                  style: const TextStyle(color: Colors.white),
                  decoration: InputDecoration(
                    hintText: 'Email',
                    hintStyle: const TextStyle(color: Colors.white54),
                    prefixIcon:
                    _glowIcon(Icons.mail_outline, _emailFocus.hasFocus),
                  ),
                  validator: (v) {
                    if (v == null || v.trim().isEmpty) return 'Enter your email';
                    final emailReg = RegExp(r'^[^@\s]+@[^@\s]+\.[^@\s]+$');
                    return emailReg.hasMatch(v) ? null : 'Enter a valid email';
                  },
                ),
                const SizedBox(height: 14),
                TextFormField(
                  focusNode: _passFocus,
                  controller: _passController, // Intentionally blank
                  obscureText: _obscure,
                  style: const TextStyle(color: Colors.white),
                  decoration: InputDecoration(
                    hintText: 'New Password (Optional)',
                    hintStyle: const TextStyle(color: Colors.white54),
                    prefixIcon:
                    _glowIcon(Icons.lock_outline, _passFocus.hasFocus),
                    suffixIcon: IconButton(
                      onPressed: () => setState(() => _obscure = !_obscure),
                      icon: Icon(
                          _obscure ? Icons.visibility : Icons.visibility_off),
                      color: const Color(0xFFCA8CFF),
                    ),
                  ),
                  // Password is optional, but if present, must be valid
                  validator: (v) => (v != null && v.isNotEmpty && v.length < 2)
                      ? 'Min 2 characters'
                      : null,
                ),
                const SizedBox(height: 14),
                TextFormField(
                  focusNode: _confirmFocus,
                  controller: _confirmController, // Intentionally blank
                  obscureText: _obscure, // Hides confirm password too
                  style: const TextStyle(color: Colors.white),
                  decoration: InputDecoration(
                    hintText: 'Confirm New Password',
                    hintStyle: const TextStyle(color: Colors.white54),
                    prefixIcon: _glowIcon(
                        Icons.verified_user_outlined, _confirmFocus.hasFocus),
                  ),
                  // Only validate if new password is typed
                  validator: (v) => (v != _passController.text)
                      ? 'Passwords do not match'
                      : null,
                ),
                const SizedBox(height: 22),
                _GradientButton(
                  glow: _glowPulse.value,
                  onPressed: _isUpdating ? () {} : _handleUpdateProfile,
                  child: Text(
                    'Update Profile',
                    style: GoogleFonts.cabinSketch(
                        fontSize: 18, fontWeight: FontWeight.w700),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  // --- Glow Blob (from registration.dart) ---
  Widget _glowBlob({required double size, required Color color}) {
    return AnimatedScale(
      duration: const Duration(milliseconds: 900),
      curve: Curves.easeInOut,
      scale: 0.98 + 0.04 * (1 - _controller.value),
      child: Container(
        width: size,
        height: size,
        decoration: BoxDecoration(
          shape: BoxShape.circle,
          boxShadow: [
            BoxShadow(
              color: color,
              blurRadius: 80,
              spreadRadius: 20,
            ),
          ],
        ),
      ),
    );
  }
}

// ---
// COPIED WIDGETS FROM REGISTRATION.DART
// ---

class _GlassCard extends StatelessWidget {
  final Widget child;
  final BorderRadius radius;
  final double blurSigma;
  final double opacity;

  const _GlassCard({
    required this.child,
    required this.radius,
    required this.blurSigma,
    required this.opacity,
  });

  @override
  Widget build(BuildContext context) {
    return ClipRRect(
      borderRadius: radius,
      child: BackdropFilter(
        filter: ImageFilter.blur(sigmaX: blurSigma, sigmaY: blurSigma),
        child: AnimatedContainer(
          duration: const Duration(milliseconds: 600),
          curve: Curves.easeInOut,
          decoration: BoxDecoration(
            borderRadius: radius,
            color: Colors.white.withOpacity(opacity),
            border: Border.all(color: Colors.white.withOpacity(0.18), width: 1),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.35),
                blurRadius: 40,
                offset: const Offset(0, 24),
              )
            ],
          ),
          padding: const EdgeInsets.fromLTRB(24, 28, 24, 22),
          child: child,
        ),
      ),
    );
  }
}

class _GradientButton extends StatelessWidget {
  final VoidCallback onPressed;
  final Widget child;
  final double glow;

  const _GradientButton(
      {required this.onPressed, required this.child, required this.glow});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onPressed,
      child: Container(
        width: double.infinity,
        padding: const EdgeInsets.symmetric(vertical: 16),
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(18),
          gradient: const LinearGradient(
            colors: [Color(0xFF8E2DE2), Color(0xFF4A00E0), Color(0xFF00D1FF)],
            begin: Alignment.centerLeft,
            end: Alignment.centerRight,
          ),
          boxShadow: [
            BoxShadow(
              color: const Color(0xFF7F53FF).withOpacity(glow),
              blurRadius: 24,
              spreadRadius: 1,
              offset: const Offset(0, 8),
            ),
          ],
        ),
        alignment: Alignment.center,
        child: DefaultTextStyle.merge(
          style: const TextStyle(color: Colors.white),
          child: child,
        ),
      ),
    );
  }
}

class _AnimatedBackgroundPainter extends CustomPainter {
  final double progress;
  const _AnimatedBackgroundPainter({required this.progress});

  @override
  void paint(Canvas canvas, Size size) {
    final rect = Offset.zero & size;

    final background = Paint()..color = const Color(0xFF0B0B12);
    canvas.drawRect(rect, background);

    final g1 = RadialGradient(
      colors: const [Color(0xFF1B1037), Color(0xFF0B0B12)],
      radius: 0.9,
      center: Alignment(-0.8 + 0.4 * progress, -1.0 + 0.6 * progress),
    );
    final g2 = RadialGradient(
      colors: const [Color(0xFF0C1C2B), Color(0xFF0B0B12)],
      radius: 1.1,
      center: Alignment(1.0 - 0.4 * progress, 1.0 - 0.6 * progress),
    );

    final paint1 = Paint()..shader = g1.createShader(rect);
    final paint2 = Paint()..shader = g2.createShader(rect);

    canvas.drawRect(rect, paint1);
    canvas.drawRect(rect, paint2);

    final vignette = Paint()
      ..shader = RadialGradient(
        colors: [Colors.transparent, Colors.black.withOpacity(0.55)],
        stops: const [0.6, 1.0],
        center: Alignment.center,
        radius: 1.2,
      ).createShader(rect);
    canvas.drawRect(rect, vignette);
  }

  @override
  bool shouldRepaint(covariant _AnimatedBackgroundPainter oldDelegate) {
    return oldDelegate.progress != progress;
  }
}