import 'dart:convert';
import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'login.dart';
import 'config.dart';

class RegistrationPage extends StatefulWidget {
  const RegistrationPage({super.key});

  @override
  State<RegistrationPage> createState() => _RegistrationPageState();
}

final TextEditingController name = TextEditingController();
final TextEditingController email = TextEditingController();
final TextEditingController pass = TextEditingController();

Future<void> register(BuildContext context) async {
  var res = await http.post(
    Uri.parse("${AppConfig.baseUrl}registration.php"),
    body: {
      "name": name.text,
      "email": email.text,
      "pass": pass.text,
    },
  );

  if (res.statusCode == 200) {
    var data = jsonDecode(res.body);

    if (data["success"] == "true") {
      // ✅ Success Popup (Green)
      showDialog(
        context: context,
        builder: (_) => AlertDialog(
          title: const Text("Success"),
          content: const Text(
            "Registration successful! Please login.",
            style: TextStyle(color: Colors.green),
          ),
          actions: [
            TextButton(
              onPressed: () {
                Navigator.of(context).pop();
                Navigator.pushReplacement(
                  context,
                  MaterialPageRoute(builder: (_) => const LoginPage()),
                );
              },
              child: const Text("OK"),
            ),
          ],
        ),
      );
    } else {
      // ❌ Failure Popup (Red)
      showDialog(
        context: context,
        builder: (_) => AlertDialog(
          title: const Text("Registration Failed"),
          content: Text(
            data["error"] ?? "Registration failed",
            style: const TextStyle(color: Colors.red),
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.of(context).pop(),
              child: const Text("Try Again"),
            ),
          ],
        ),
      );
    }
  }
}




class _RegistrationPageState extends State<RegistrationPage>
    with SingleTickerProviderStateMixin {
  late final AnimationController _controller;
  late final Animation<double> _blobShift;
  late final Animation<double> _glowPulse;
  late final Animation<BorderRadius> _cardRadius;

  final _formKey = GlobalKey<FormState>();
  final _name = TextEditingController();
  final _email = TextEditingController();
  final _pass = TextEditingController();
  final _confirm = TextEditingController();

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
  }

  @override
  void dispose() {
    _controller.dispose();
    _name.dispose();
    _email.dispose();
    _pass.dispose();
    _confirm.dispose();
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
                  painter: _AnimatedBackgroundPainter(progress: _controller.value),
                ),
              ),
              Positioned(
                left: 40 + _blobShift.value,
                top: 80,
                child: _glowBlob(size: 160, color: const Color(0xFF7F53FF).withOpacity(0.35)),
              ),
              Positioned(
                right: 24 - _blobShift.value,
                bottom: 90,
                child: _glowBlob(size: 200, color: const Color(0xFF00D1FF).withOpacity(0.30)),
              ),
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
                        child: _RegisterForm(
                          glowPulse: _glowPulse,
                          formKey: _formKey,
                          name: _name,
                          email: _email,
                          pass: _pass,
                          confirm: _confirm,
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

class _RegisterForm extends StatefulWidget {
  final GlobalKey<FormState> formKey;
  final TextEditingController name;
  final TextEditingController email;
  final TextEditingController pass;
  final TextEditingController confirm;
  final Animation<double> glowPulse;

  const _RegisterForm({
    required this.formKey,
    required this.name,
    required this.email,
    required this.pass,
    required this.confirm,
    required this.glowPulse,
  });

  @override
  State<_RegisterForm> createState() => _RegisterFormState();
}

class _RegisterFormState extends State<_RegisterForm> {
  bool _obscure = true;

  final _nameFocus = FocusNode();
  final _emailFocus = FocusNode();
  final _passFocus = FocusNode();
  final _confirmFocus = FocusNode();

  final Color _pinkColor = const Color(0xFFCA8CFF); // Pink from gradient

  @override
  void dispose() {
    _nameFocus.dispose();
    _emailFocus.dispose();
    _passFocus.dispose();
    _confirmFocus.dispose();
    super.dispose();
  }

  final Color _aquaColor = const Color(0xFF00D1FF); // Aqua from gradient

  Widget _glowIcon(IconData icon, bool focused) {
    return AnimatedContainer(
      duration: const Duration(milliseconds: 300),
      child: Icon(
        icon,
        color: _pinkColor, // Always pink
      ),
      decoration: focused
          ? BoxDecoration(
        boxShadow: [
          BoxShadow(
            color: _aquaColor.withOpacity(0.7), // Aqua glow
            blurRadius: 18,
            spreadRadius: 1,
          )
        ],
      )
          : null,
    );
  }


  @override
  Widget build(BuildContext context) {
    return SingleChildScrollView(
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          // 👇 Logo + Title
          Center(
            child: Column(
              children: [
                SizedBox(
                  height: 80, // adjust this value to control logo size
                  child: Image.asset(
                    "assets/logo2.png", // make sure added in pubspec.yaml
                    fit: BoxFit.contain,
                  ),
                ),
                const SizedBox(height: 10),

                // Gradient Title
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
                  child: const Text(
                    'Registration',
                    style: TextStyle(
                      fontSize: 38,
                      fontWeight: FontWeight.w800,
                      color: Colors.white,
                    ),
                  ),
                ),
              ],
            ),
          ),

          const SizedBox(height: 18),

          // 👇 Your Form
          Form(
            key: widget.formKey,
            child: Column(
              children: [
                TextFormField(
                  focusNode: _nameFocus,
                  controller: name,
                  textInputAction: TextInputAction.next,
                  decoration: InputDecoration(
                    hintText: 'Name',
                    prefixIcon:
                    _glowIcon(Icons.person_outline, _nameFocus.hasFocus),
                  ),
                  onTap: () => setState(() {}),
                  validator: (v) =>
                  (v == null || v.trim().isEmpty) ? 'Enter your name' : null,
                ),
                const SizedBox(height: 14),
                TextFormField(
                  focusNode: _emailFocus,
                  controller: email,
                  textInputAction: TextInputAction.next,
                  keyboardType: TextInputType.emailAddress,
                  decoration: InputDecoration(
                    hintText: 'Email',
                    prefixIcon:
                    _glowIcon(Icons.mail_outline, _emailFocus.hasFocus),
                  ),
                  onTap: () => setState(() {}),
                  validator: (v) {
                    if (v == null || v.trim().isEmpty) return 'Enter your email';
                    final emailReg =
                    RegExp(r'^[^@\s]+@[^@\s]+\.[^@\s]+$');
                    return emailReg.hasMatch(v)
                        ? null
                        : 'Enter a valid email';
                  },
                ),
                const SizedBox(height: 14),
                TextFormField(
                  focusNode: _passFocus,
                  controller: pass,
                  obscureText: _obscure,
                  decoration: InputDecoration(
                    hintText: 'Password',
                    prefixIcon:
                    _glowIcon(Icons.lock_outline, _passFocus.hasFocus),
                    suffixIcon: IconButton(
                      onPressed: () =>
                          setState(() => _obscure = !_obscure),
                      icon: Icon(
                          _obscure ? Icons.visibility : Icons.visibility_off),
                      color: _pinkColor,
                    ),
                  ),
                  onTap: () => setState(() {}),
                  validator: (v) =>
                  (v != null && v.length >= 2) ? null : 'Min 2 characters',
                ),
                const SizedBox(height: 14),
                TextFormField(
                  focusNode: _confirmFocus,
                  controller: widget.confirm,
                  obscureText: true,
                  decoration: InputDecoration(
                    hintText: 'Confirm Password',
                    prefixIcon: _glowIcon(Icons.verified_user_outlined,
                        _confirmFocus.hasFocus),
                  ),
                  onTap: () => setState(() {}),
                  validator: (v) =>
                  (v == pass.text) ? null : 'Passwords do not match',
                ),
                const SizedBox(height: 22),
                _GradientButton(
                  glow: widget.glowPulse.value,
                  onPressed: () {
                    if (widget.formKey.currentState!.validate()) {
                      // ✅ Call register with context
                      register(context);
                    }
                  },
                  child: const Text(
                    'Sign Up',
                    style: TextStyle(
                        fontSize: 16, fontWeight: FontWeight.w700),
                  ),
                ),
                const SizedBox(height: 16),
                GestureDetector(
                  onTap: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                          builder: (context) => const LoginPage()),
                    );
                  },
                  child: RichText(
                    text: TextSpan(
                      children: [
                        const TextSpan(
                          text: "Already have an account? ",
                          style: TextStyle(
                              color: Colors.white, fontSize: 14),
                        ),
                        TextSpan(
                          text: "Log in",
                          style: TextStyle(
                            color: _pinkColor,
                            fontSize: 14,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}



class _GradientButton extends StatelessWidget {
  final VoidCallback onPressed;
  final Widget child;
  final double glow;

  const _GradientButton({required this.onPressed, required this.child, required this.glow});

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
