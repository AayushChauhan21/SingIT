// lib/login_page.dart
import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart'; // --- ✅ 1. ADDED IMPORT ---
import 'registration.dart';
import 'home.dart'; // --- ✅ 2. CORRECTED IMPORT (was 'home.dart') ---
import 'config.dart';

class LoginPage extends StatefulWidget {
  const LoginPage({super.key});

  @override
  State<LoginPage> createState() => _LoginPageState();
}

// --- ✅ 3. MODIFIED LOGIN FUNCTION ---
Future<void> login(
    BuildContext context,
    TextEditingController email,
    TextEditingController pass,
    ) async {
  try {
    var res = await http.post(
      Uri.parse("${AppConfig.baseUrl}login.php"),
      body: {
        "email": email.text.trim(),
        "pass": pass.text.trim(),
      },
    );

    if (res.statusCode == 200) {
      var data = jsonDecode(res.body);

      if (data["success"] == "true") {

        // --- START: SAVE SESSION LOGIC ---
        // IMPORTANT: This assumes your PHP script returns 'id' on success
        if (data['id'] != null) {
          // 1. Get SharedPreferences instance
          final prefs = await SharedPreferences.getInstance();

          // 2. Store the user ID (you requested "user_id" as the key)
          await prefs.setString('user_id', data['id'].toString());

          // 3. Navigate to the home page
          Navigator.pushReplacement(
            context,
            MaterialPageRoute(builder: (context) => TopSection()),
          );
        } else {
          // PHP said success, but didn't send a user ID.
          showDialog(
            context: context,
            builder: (BuildContext ctx) {
              return AlertDialog(
                title: const Text("Login Error"),
                content: const Text(
                  "Login successful, but no user ID was returned from the server.",
                  style: TextStyle(color: Colors.red, fontWeight: FontWeight.bold),
                ),
                actions: [
                  TextButton(
                    onPressed: () => Navigator.of(ctx).pop(),
                    child: const Text("OK"),
                  ),
                ],
              );
            },
          );
        }
        // --- END: SAVE SESSION LOGIC ---

      } else {
        // ❌ Invalid login
        showDialog(
          context: context,
          builder: (BuildContext ctx) {
            return AlertDialog(
              title: const Text("Login Failed"),
              content: Text(
                data["error"] ?? "Invalid email or password",
                style: const TextStyle(
                    color: Colors.red, fontWeight: FontWeight.bold),
              ),
              actions: [
                TextButton(
                  onPressed: () => Navigator.of(ctx).pop(),
                  child: const Text("OK"),
                ),
              ],
            );
          },
        );
      }
    }
  } catch (e) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text("Error"),
        content: const Text(
          "Something went wrong. Please try again.",
          style: TextStyle(color: Colors.red, fontWeight: FontWeight.bold),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(ctx).pop(),
            child: const Text("OK"),
          ),
        ],
      ),
    );
  }
}

class _LoginPageState extends State<LoginPage>
    with SingleTickerProviderStateMixin {
  late final AnimationController _controller;
  late final Animation<double> _blobShift;
  late final Animation<double> _glowPulse;
  late final Animation<BorderRadius> _cardRadius;

  final _formKey = GlobalKey<FormState>();
  final _email = TextEditingController();
  final _pass = TextEditingController();
  final _emailFocus = FocusNode();
  final _passFocus = FocusNode();
  bool _obscure = true;

  final Color _pink = const Color(0xFFCA8CFF);
  final Color _aqua = const Color(0xFF00D1FF);

  @override
  void initState() {
    super.initState();

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

    for (final node in [_emailFocus, _passFocus]) {
      node.addListener(() => setState(() {}));
    }
  }

  @override
  void dispose() {
    _controller.dispose();
    _email.dispose();
    _pass.dispose();
    _emailFocus.dispose();
    _passFocus.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: AnimatedBuilder(
        animation: _controller,
        builder: (context, _) {
          return Stack(
            children: [
              Positioned.fill(
                child: CustomPaint(
                  painter:
                  _AnimatedBackgroundPainter(progress: _controller.value),
                ),
              ),
              Positioned(
                left: 40 + _blobShift.value,
                top: 80,
                child: _glowBlob(
                    size: 160, color: const Color(0xFF7F53FF).withOpacity(0.35)),
              ),
              Positioned(
                right: 24 - _blobShift.value,
                bottom: 90,
                child: _glowBlob(size: 200, color: _aqua.withOpacity(0.30)),
              ),
              SafeArea(
                child: Center(
                  child: Padding(
                    padding:
                    const EdgeInsets.symmetric(horizontal: 22, vertical: 20),
                    child: ConstrainedBox(
                      constraints: const BoxConstraints(maxWidth: 520),
                      child: _GlassCard(
                        radius: _cardRadius.value,
                        blurSigma: 20 + 4 * (1 - _controller.value),
                        opacity: 0.10 + 0.04 * (1 - _controller.value),
                        child: _LoginForm(
                          formKey: _formKey,
                          email: _email,
                          pass: _pass,
                          emailFocus: _emailFocus,
                          passFocus: _passFocus,
                          pink: _pink,
                          aqua: _aqua,
                          obscure: _obscure,
                          onToggleObscure: () =>
                              setState(() => _obscure = !_obscure),
                          buttonGlow: _glowPulse.value,
                        ),
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

  const _GradientButton({
    required this.onPressed,
    required this.child,
    required this.glow,
  });

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

class _LoginForm extends StatelessWidget {
  final GlobalKey<FormState> formKey;
  final TextEditingController email;
  final TextEditingController pass;
  final FocusNode emailFocus;
  final FocusNode passFocus;
  final Color pink;
  final Color aqua;
  final bool obscure;
  final VoidCallback onToggleObscure;
  final double buttonGlow;

  const _LoginForm({
    required this.formKey,
    required this.email,
    required this.pass,
    required this.emailFocus,
    required this.passFocus,
    required this.pink,
    required this.aqua,
    required this.obscure,
    required this.onToggleObscure,
    required this.buttonGlow,
  });

  @override
  Widget build(BuildContext context) {
    return SingleChildScrollView(
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          // 👇 Logo added here
          Center(
            child: SizedBox(
              height: 80,
              child: Image.asset(
                "assets/logo2.png", // make sure added in pubspec.yaml
                fit: BoxFit.contain,
              ),
            ),
          ),
          const SizedBox(height: 16),

          Center(
            child: ShaderMask(
              shaderCallback: (rect) => const LinearGradient(
                colors: [Color(0xFFCA8CFF), Color(0xFF7B6DFF), Color(0xFF00D1FF)],
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              ).createShader(rect),
              child: const Text(
                'Login',
                style: TextStyle(
                  fontSize: 38,
                  fontWeight: FontWeight.w800,
                  color: Colors.white,
                ),
              ),
            ),
          ),
          const SizedBox(height: 18),
          Form(
            key: formKey,
            child: Column(
              children: [
                _glowTextField(
                  controller: email,
                  focusNode: emailFocus,
                  hint: 'Email',
                  icon: Icons.mail_outline,
                  keyboardType: TextInputType.emailAddress,
                  validator: (v) {
                    if (v == null || v.trim().isEmpty) return 'Enter your email';
                    final emailReg =
                    RegExp(r'^[^@\s]+@[^@\s]+\.[^@\s]+$');
                    return emailReg.hasMatch(v) ? null : 'Enter a valid email';
                  },
                  pink: pink,
                  aqua: aqua,
                ),
                const SizedBox(height: 14),
                _glowTextField(
                  controller: pass,
                  focusNode: passFocus,
                  hint: 'Password',
                  icon: Icons.lock_outline,
                  obscure: obscure,
                  suffix: IconButton(
                    onPressed: onToggleObscure,
                    icon: Icon(obscure ? Icons.visibility : Icons.visibility_off),
                    color: pink,
                  ),
                  // --- ✅ 4. CHANGED VALIDATOR ---
                  validator: (v) =>
                  (v != null && v.length >= 2) ? null : 'Min 2 characters',
                  pink: pink,
                  aqua: aqua,
                ),
                const SizedBox(height: 22),
                _GradientButton(
                  glow: buttonGlow,
                  onPressed: () {
                    if (formKey.currentState!.validate()) {
                      login(context, email, pass);
                    }
                  },
                  child: const Text(
                    'Login',
                    style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700),
                  ),
                ),
                const SizedBox(height: 12),
                GestureDetector(
                  onTap: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (context) => RegistrationPage(),
                      ),
                    );
                  },
                  child: RichText(
                    text: TextSpan(
                      children: [
                        const TextSpan(
                          text: "Don't have an account? ",
                          style: TextStyle(color: Colors.white70),
                        ),
                        TextSpan(
                          text: "Sign up",
                          style: TextStyle(
                            color: pink,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                      ],
                    ),
                  ),
                )
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _glowTextField({
    required TextEditingController controller,
    required FocusNode focusNode,
    required String hint,
    required IconData icon,
    required Color pink,
    required Color aqua,
    TextInputType? keyboardType,
    String? Function(String?)? validator,
    bool obscure = false,
    Widget? suffix,
  }) {
    final focused = focusNode.hasFocus;
    return TextFormField(
      controller: controller,
      focusNode: focusNode,
      keyboardType: keyboardType,
      obscureText: obscure,
      style: const TextStyle(color: Colors.white),
      decoration: InputDecoration(
        hintText: hint,
        prefixIcon: AnimatedContainer(
          duration: const Duration(milliseconds: 220),
          child: Icon(icon, color: pink),
          decoration: focused
              ? BoxDecoration(boxShadow: [
            BoxShadow(
              color: aqua.withOpacity(0.7),
              blurRadius: 18,
              spreadRadius: 1,
            )
          ])
              : null,
        ),
        suffixIcon: suffix,
      ),
      validator: validator,
      textInputAction: TextInputAction.next,
    );
  }
}