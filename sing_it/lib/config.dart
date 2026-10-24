// lib/config.dart

class AppConfig {
  // Android emulator address for reaching the host machine.
  static const String localhostUrl = "http://10.0.2.2/flutter_crud/";

  // Use this address when running on a physical device on the same network.
  static const String lanUrl = "http://10.241.54.203/flutter_crud/";

  // Default to the current PC LAN address.
  static const bool useLocalhost = false;

  // Final URL used in app
  static String get baseUrl => useLocalhost ? localhostUrl : lanUrl;
}
