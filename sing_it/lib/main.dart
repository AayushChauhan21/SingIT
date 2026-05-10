import 'package:flutter/material.dart';
import 'registration.dart';   // Registration page (currently not used)
import 'home.dart';

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      title: 'Music App',
      themeMode: ThemeMode.dark,
      theme: ThemeData(brightness: Brightness.dark),

      // Run HomePage by default (remove const if HomePage is not const)
      // home: HomePage(),

      // If you want to switch back to RegistrationPage, just uncomment this:
      home: TopSection(),
    );
  }
}
