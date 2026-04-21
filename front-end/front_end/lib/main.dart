import 'package:flutter/material.dart';
import 'screens/user/login_screen.dart'; // Import login_screen

// 1. TAMBAHKAN BARIS INI (Kunci Navigasi Global)
final GlobalKey<NavigatorState> navigatorKey = GlobalKey<NavigatorState>();
void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      // 2. DAFTARKAN KUNCI DI SINI
      navigatorKey: navigatorKey, 
      title: 'Purnama Hotel',
      theme: ThemeData(primarySwatch: Colors.blue),
      home: const LoginScreen(), 
    );
  }
}