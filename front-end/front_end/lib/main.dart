import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart'; // Tambahkan ini

// --- IMPORT PROVIDERS ---
import 'providers/event_provider.dart';
import 'providers/cart_provider.dart';

// --- IMPORT SERVICES & THEME ---
import 'notification/notification_service.dart';
import 'screens/event/app_theme.dart';
import 'screens/user/login_screen.dart';
import 'screens/notification/notification_screen.dart'; // Import Screen Notifikasi

// 1. GLOBAL NAVIGATOR KEY
// Ini kunci agar kita bisa pindah halaman dari mana saja tanpa BuildContext
final GlobalKey<NavigatorState> navigatorKey = GlobalKey<NavigatorState>();

void main() async {
  WidgetsFlutterBinding.ensureInitialized();

  try {
    await Firebase.initializeApp();
    
    // Inisialisasi service dasar
    await PushNotificationService.initialize();

    // 2. LOGIKA KLIK NOTIFIKASI (Background & Terminated)
    setupNotificationInteractions();
    
    print("LOG_NOTIFICATION: Firebase & Interaction Handler Berhasil");
  } catch (e) {
    print("LOG_ERROR: Gagal inisialisasi Firebase: $e");
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

// 3. FUNGSI UNTUK MENANGANI KLIK NOTIFIKASI
void setupNotificationInteractions() async {
  // A. Jika aplikasi mati total (Terminated) lalu diklik
  RemoteMessage? initialMessage = await FirebaseMessaging.instance.getInitialMessage();
  if (initialMessage != null) {
    _navigateToNotificationScreen();
  }

  // B. Jika aplikasi ada di background (tidak mati total) lalu diklik
  FirebaseMessaging.onMessageOpenedApp.listen((RemoteMessage message) {
    _navigateToNotificationScreen();
  });
}

void _navigateToNotificationScreen() {
  // Menggunakan navigatorKey untuk pindah ke NotificationScreen
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
      navigatorKey: navigatorKey, // <--- 4. PASANG NAVIGATOR KEY DI SINI
      title: 'Purnama Hotel & Resto',
      debugShowCheckedModeBanner: false,
      theme: AppTheme.getTheme(eventProvider.activeTheme),
      
      // Definisikan rute jika diperlukan, tapi kita gunakan push manual di atas
      home: const SplashScreenProxy(),
    );
  }
}

class SplashScreenProxy extends StatefulWidget {
  const SplashScreenProxy({super.key});

  @override
  State<SplashScreenProxy> createState() => _SplashScreenProxyState();
}

class _SplashScreenProxyState extends State<SplashScreenProxy> {
  @override
  void initState() {
    super.initState();
    
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (mounted) {
        context.read<EventProvider>().fetchActiveTheme();
      }
    });
    
    Future.delayed(const Duration(seconds: 2), () {
      if (mounted) {
        // Gunakan pushReplacement agar splash screen hilang dari stack
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(builder: (context) => const LoginScreen()),
        );
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return const Scaffold(
      body: Center(
        child: CircularProgressIndicator(),
      ),
    );
  }
}