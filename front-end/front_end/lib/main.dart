import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:shared_preferences/shared_preferences.dart';

import 'providers/event_provider.dart';
import 'providers/cart_provider.dart';
import 'notification/notification_service.dart';
import 'screens/event/app_theme.dart';
import 'screens/user/premium_splash_screen.dart';
import 'screens/user/login_screen.dart';
import 'screens/home/home_screen.dart';
import 'screens/notification/notification_screen.dart';

final GlobalKey<NavigatorState> navigatorKey = GlobalKey<NavigatorState>();

void main() async {
  WidgetsFlutterBinding.ensureInitialized();

  try {
    await Firebase.initializeApp();
    await PushNotificationService.initialize();
    _setupNotificationInteractions();
    debugPrint("LOG_SYSTEM: Firebase & Notification System Ready");
  } catch (e) {
    debugPrint("LOG_ERROR: Gagal inisialisasi Firebase: $e");
  }

  runApp(
    MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => EventProvider()),
        ChangeNotifierProvider(create: (_) => CartProvider()),
      ],
      child: const MyApp(),
    ),
  );
}

void _setupNotificationInteractions() async {
  RemoteMessage? initialMessage = await FirebaseMessaging.instance.getInitialMessage();
  if (initialMessage != null) {
    _navigateToNotificationScreen();
  }
  FirebaseMessaging.onMessageOpenedApp.listen((RemoteMessage message) {
    _navigateToNotificationScreen();
  });
}

void _navigateToNotificationScreen() {
  navigatorKey.currentState?.push(
    MaterialPageRoute(builder: (context) => const NotificationScreen()),
  );
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    final eventProvider = context.watch<EventProvider>();
    return MaterialApp(
      navigatorKey: navigatorKey,
      title: 'Purnama',
      debugShowCheckedModeBanner: false,
      theme: AppTheme.getTheme(eventProvider.activeTheme),
      home: const SplashScreenWrapper(),
    );
  }
}

class SplashScreenWrapper extends StatefulWidget {
  const SplashScreenWrapper({super.key});

  @override
  State<SplashScreenWrapper> createState() => _SplashScreenWrapperState();
}

class _SplashScreenWrapperState extends State<SplashScreenWrapper> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (mounted) {
        context.read<EventProvider>().fetchActiveTheme();
      }
    });
  }

  void _handleSplashFinished() {
    _checkSessionAndNavigate();
  }

  void _checkSessionAndNavigate() async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('access_token');
    final lastActivity = prefs.getInt('last_activity');

    // Jika tidak ada token (guest), langsung ke home
    if (token == null) {
      _goToHome(); // <-- PERUBAHAN UTAMA
      return;
    }

    // Cek session timeout (30 menit)
    if (lastActivity != null) {
      final now = DateTime.now().millisecondsSinceEpoch;
      final diff = now - lastActivity;
      const timeout = 30 * 60 * 1000; // 30 menit
      if (diff > timeout) {
        debugPrint("LOG_SESSION: Sesi kadaluwarsa. Hapus token.");
        await prefs.clear();
        _goToLogin();
        return;
      }
    }

    // Session masih valid, update last_activity dan lanjut ke home
    await prefs.setInt('last_activity', DateTime.now().millisecondsSinceEpoch);
    _goToHome();
  }

  void _goToLogin() {
    if (mounted) {
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (_) => const LoginScreen()),
      );
    }
  }

  void _goToHome() {
    if (mounted) {
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (_) => const HomeScreen()),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return PremiumSplashScreen(
      onFinished: _handleSplashFinished,
    );
  }
}