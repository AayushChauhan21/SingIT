// lib/config.dart

class AppConfig {
  // For emulator / Chrome (localhost works only on PC, not real device)
  static const String localhostUrl = "http://172.16.200.203/flutter_crud/";

  // For real device (replace with your PC LAN IP)
  static const String lanUrl = "http://172.16.200.203/flutter_crud/";

  // Switch manually depending on testing
  static const bool useLocalhost = true;

  // Final URL used in app
  static String get baseUrl => useLocalhost ? localhostUrl : lanUrl;
}
