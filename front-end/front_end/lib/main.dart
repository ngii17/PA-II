import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';

// --- IMPORT PROVIDERS ---
import 'providers/event_provider.dart';
import 'providers/cart_provider.dart';

// --- IMPORT SERVICES ---
import 'notification/notification_service.dart';
import 'screens/event/app_theme.dart';

// --- IMPORT SCREENS ---
import 'screens/user/premium_splash_screen.dart'; // Memakai Opening Sinematik Anda
import 'screens/notification/notification_screen.dart';

// 1. GLOBAL NAVIGATOR KEY
// Digunakan agar sistem notifikasi bisa melakukan navigasi tanpa context
final GlobalKey<NavigatorState> navigatorKey = GlobalKey<NavigatorState>();

void main() async {
  // Pastikan binding Flutter siap
  WidgetsFlutterBinding.ensureInitialized();

  try {
    // Inisialisasi Firebase
    await Firebase.initializeApp();
    
    // Inisialisasi Service Notifikasi (Lokal & FCM)
    await PushNotificationService.initialize();

    // 2. SETUP HANDLING KLIK NOTIFIKASI
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

// 3. LOGIKA INTERAKSI NOTIFIKASI (Background & Terminated)
void _setupNotificationInteractions() async {
  // Case A: Jika aplikasi mati total (Terminated) lalu diklik
  RemoteMessage? initialMessage = await FirebaseMessaging.instance.getInitialMessage();
  if (initialMessage != null) {
    _navigateToNotificationScreen();
  }

  // Case B: Jika aplikasi ada di background (tidak mati) lalu diklik
  FirebaseMessaging.onMessageOpenedApp.listen((RemoteMessage message) {
    _navigateToNotificationScreen();
  });
}

void _navigateToNotificationScreen() {
  // Pindah ke Inbox Notifikasi menggunakan navigatorKey
  navigatorKey.currentState?.push(
    MaterialPageRoute(builder: (context) => const NotificationScreen()),
  );
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    // Memantau perubahan tema dari EventProvider (Port 8001)
    final eventProvider = context.watch<EventProvider>();

    return MaterialApp(
      // 4. PASANG NAVIGATOR KEY
      navigatorKey: navigatorKey, 
      title: 'Purnama',

      debugShowCheckedModeBanner: false,
      
      // Tema dinamis yang bisa berubah otomatis sesuai Event aktif
      theme: AppTheme.getTheme(eventProvider.activeTheme),
      
      // 5. HALAMAN PEMBUKA (Premium SplashScreen Sinematik)
      // Logika pindah ke Login/Home ada di dalam file PremiumSplashScreen setelah animasi selesai
      // Langsung hanya premium splash (tanpa splash Flutter default lain)
      home: const PremiumSplashScreen(), 

    );
  }
}