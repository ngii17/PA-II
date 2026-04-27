import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

// --- IMPORT PROVIDERS ---
import 'providers/event_provider.dart';
import 'providers/cart_provider.dart'; // <--- Import baru

// --- IMPORT SCREENS & THEME ---
import 'screens/event/app_theme.dart';
import 'screens/user/login_screen.dart';

void main() {
  runApp(
    // 1. Mendaftarkan semua Provider di level tertinggi
    MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => EventProvider()),
        ChangeNotifierProvider(create: (_) => CartProvider()), // <--- Mendaftarkan Keranjang
      ],
      child: const MyApp(),
    ),
  );
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    // 2. Memantau perubahan tema secara global
    final eventProvider = context.watch<EventProvider>();

    return MaterialApp(
      title: 'Purnama Hotel & Resto',
      debugShowCheckedModeBanner: false,
      
      // 3. Menggunakan Tema Dinamis yang sinkron dengan database
      theme: AppTheme.getTheme(eventProvider.activeTheme),
      
      // Mengarahkan ke Splash Screen untuk pengecekan tema saat startup
      home: const SplashScreenProxy(),
    );
  }
}

// Widget pembantu untuk memicu pengambilan data tema saat aplikasi baru dibuka
class SplashScreenProxy extends StatefulWidget {
  const SplashScreenProxy({super.key});

  @override
  State<SplashScreenProxy> createState() => _SplashScreenProxyState();
}

class _SplashScreenProxyState extends State<SplashScreenProxy> {
  @override
  void initState() {
    super.initState();
    
    // Memanggil API Tema dari Laravel Port 8001 tepat setelah aplikasi render
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (mounted) {
        context.read<EventProvider>().fetchActiveTheme();
      }
    });
    
    // Simulasi jeda Splash Screen selama 2 detik
    Future.delayed(const Duration(seconds: 2), () {
      if (mounted) {
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
        // Loading indicator akan berwarna sesuai tema default sebelum tema baru dimuat
        child: CircularProgressIndicator(),
      ),
    );
  }
}