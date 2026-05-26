import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:shared_preferences/shared_preferences.dart'; // Tambahkan ini

// --- IMPORT PROVIDERS ---
import 'providers/event_provider.dart';
import 'providers/cart_provider.dart';

// --- IMPORT SERVICES & THEME ---
import 'notification/notification_service.dart';
import 'screens/event/app_theme.dart';
import 'screens/user/login_screen.dart';
import 'screens/home/home_screen.dart'; // Pastikan path home_screen benar
import 'screens/notification/notification_screen.dart';

// 1. GLOBAL NAVIGATOR KEY
final GlobalKey<NavigatorState> navigatorKey = GlobalKey<NavigatorState>();

void main() async {
  WidgetsFlutterBinding.ensureInitialized();

  try {
    await Firebase.initializeApp();
    await PushNotificationService.initialize();
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

void setupNotificationInteractions() async {
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

  // Fungsi untuk memperbarui waktu aktivitas terakhir ke SharedPreferences
  void _updateLastActivity() async {
    final SharedPreferences prefs = await SharedPreferences.getInstance();
    // Simpan timestamp milidetik saat ini
    await prefs.setInt('last_activity', DateTime.now().millisecondsSinceEpoch);
  }

  @override
  Widget build(BuildContext context) {
    final eventProvider = context.watch<EventProvider>();

    return Listener(
      // --- PELACAK AKTIVITAS OTOMATIS ---
      // Setiap kali ada sentuhan di layar manapun, waktu aktivitas diperbarui
      onPointerDown: (_) => _updateLastActivity(),
      child: MaterialApp(
        navigatorKey: navigatorKey,
        title: 'Purnama Hotel & Resto',
        debugShowCheckedModeBanner: false,
        theme: AppTheme.getTheme(eventProvider.activeTheme),
        home: const SplashScreenProxy(),
      ),
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
    
    // 1. Ambil tema dari database
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (mounted) {
        context.read<EventProvider>().fetchActiveTheme();
      }
    });
    
    // 2. Cek Sesi User
    _checkSession();
  }

  void _checkSession() async {
    final SharedPreferences prefs = await SharedPreferences.getInstance();
    
    // Ambil Token dan Waktu Aktivitas Terakhir
    String? token = prefs.getString('access_token');
    int? lastActivity = prefs.getInt('last_activity');

    // Beri jeda 2 detik untuk Splash Screen
    await Future.delayed(const Duration(seconds: 2));

    if (token == null) {
      // Jika belum login sama sekali
      _goToLogin();
      return;
    }

    if (lastActivity != null) {
      int now = DateTime.now().millisecondsSinceEpoch;
      int difference = now - lastActivity;
      
      // 30 Menit = 30 * 60 * 1000 milidetik = 1.800.000
      const int timeoutLimit = 30 * 60 * 1000;

      if (difference > timeoutLimit) {
        // --- SESI HABIS ---
        print("LOG_SESSION: Sesi Kadaluwarsa. Menghapus data login...");
        await prefs.clear(); // Hapus token agar harus login ulang
        _goToLogin();
        return;
      }
    }

    // --- SESI MASIH BERLAKU ---
    // Update waktu aktivitas sekarang agar 10 menit mulai dihitung dari saat ini
    await prefs.setInt('last_activity', DateTime.now().millisecondsSinceEpoch);
    _goToHome();
  }

  void _goToLogin() {
    if (mounted) {
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (context) => const LoginScreen()),
      );
    }
  }

  void _goToHome() {
    if (mounted) {
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (context) => const HomeScreen()),
      );
    }
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