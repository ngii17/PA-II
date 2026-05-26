import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:vibration/vibration.dart';

// Services & Providers
import '../../services/api_services.dart';
import '../../providers/cart_provider.dart';

// Screens
import '../user/login_screen.dart';
import '../user/register_screen.dart'; 
import '../hotel/room_list_screen.dart';
import '../restoran/menu_list_screen.dart';
import '../restoran/cart_screen.dart';
import '../user/profile_screen.dart';
import '../notification/notification_screen.dart';
import 'unified_history_screen..dart'; // Pastikan nama file benar (hapus titik dua)

// Widgets & Theme
import '..//event/event_header.dart'; 
import '../../colors/login_constants.dart';
import '../../widgets/home_widgets.dart';
import '../../widgets/login_widgets.dart';


class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});
  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> with SingleTickerProviderStateMixin {
  int _currentIndex = 0; 
  String _name = "Tamu Purnama";
  String _photoUrl = ""; 
  bool _isGuest = true; 

  late AnimationController _shakeController;
  late Animation<double> _shakeAnimation;

  @override
  void initState() {
    super.initState();
    _checkAuth();

    _shakeController = AnimationController(
      duration: const Duration(milliseconds: 500),
      vsync: this,
    );

    _shakeAnimation = TweenSequence<double>([
      TweenSequenceItem(tween: Tween(begin: 0.0, end: 12.0), weight: 1),
      TweenSequenceItem(tween: Tween(begin: 12.0, end: -12.0), weight: 1),
      TweenSequenceItem(tween: Tween(begin: -12.0, end: 12.0), weight: 1),
      TweenSequenceItem(tween: Tween(begin: 12.0, end: 0.0), weight: 1),
    ]).animate(CurvedAnimation(parent: _shakeController, curve: Curves.easeInOut));
  }

  @override
  void dispose() {
    _shakeController.dispose();
    super.dispose();
  }

  void _checkAuth() async {
    final SharedPreferences prefs = await SharedPreferences.getInstance();
    String? token = prefs.getString('auth_token');
    if (mounted) setState(() => _isGuest = (token == null || token.isEmpty));
    if (!_isGuest) _loadUserData();
  }

  void _loadUserData() async {
    final result = await ApiServices.getUserProfile();
    if (result['success'] == true && mounted) {
      setState(() {
        _name = result['data']['full_name'];
        String? rawPhoto = result['data']['profile_photo'];
        _photoUrl = rawPhoto != null ? "$rawPhoto?t=${DateTime.now().millisecondsSinceEpoch}" : "";
      });
    }
  }

  void _handleLockedAction() async {
    Vibration.hasVibrator().then((has) { if (has == true) Vibration.vibrate(duration: 100); });
    _shakeController.forward(from: 0.0);
    
    final SharedPreferences prefs = await SharedPreferences.getInstance();
    bool hasReg = prefs.getBool('has_registered') ?? false;

    if (!mounted) return;
    showDialog(
      context: context,
      builder: (c) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(25)),
        title: const Row(
          children: [Icon(Icons.lock_person_rounded, color: AppTheme.goldAccent), SizedBox(width: 10), Text("Akses Terbatas")],
        ),
        content: const Text("Silakan masuk atau daftar terlebih dahulu untuk mengakses menu ini."),
        actions: [
          TextButton(onPressed: () => Navigator.pop(c), child: const Text("Nanti", style: TextStyle(color: Colors.grey))),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: AppTheme.primaryBlue, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12))),
            onPressed: () {
              Navigator.pop(c);
              Navigator.push(context, MaterialPageRoute(builder: (c) => const LoginScreen()));
            },
            child: Text(hasReg ? "Masuk" : "Daftar", style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
          ),
        ],
      ),
    );
  }

  void _handleLogout() async {
    bool? confirm = await showDialog(
      context: context, 
      builder: (c) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: const Text("Konfirmasi"),
        content: const Text("Yakin ingin keluar?"),
        actions: [
          TextButton(onPressed: () => Navigator.pop(c), child: const Text("Batal")),
          TextButton(onPressed: () => Navigator.pop(c, true), child: const Text("Logout", style: TextStyle(color: Colors.red, fontWeight: FontWeight.bold))),
        ],
      )
    );
    if (confirm == true) {
      await ApiServices.logout();
      final SharedPreferences prefs = await SharedPreferences.getInstance();
      await prefs.remove('auth_token');
      await prefs.remove('user_id');
      await prefs.remove('full_name');
      if (!mounted) return;
      Navigator.pushAndRemoveUntil(context, MaterialPageRoute(builder: (context) => const LoginScreen()), (route) => false);  
    }
  }

  @override
  Widget build(BuildContext context) {
    final topPadding = MediaQuery.of(context).padding.top;

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      body: AnimatedBuilder(
        animation: _shakeAnimation,
        builder: (context, child) => Transform.translate(offset: Offset(_shakeAnimation.value, 0), child: child),
        child: Stack(
          children: [
            AnimatedSwitcher(
              duration: const Duration(milliseconds: 500),
              child: _getPage(_currentIndex, topPadding),
            ),
            Positioned(
              bottom: 30, left: 20, right: 20,
              child: AnimatedNavBar(
                currentIndex: _currentIndex,
                onTap: (index) {
                  if (_isGuest && (index == 3 || index == 4)) {
                    _handleLockedAction();
                  } else {
                    setState(() => _currentIndex = index);
                  }
                },
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _getPage(int index, double top) {
    switch (index) {
      case 0: return _buildHomeDashboard(top);
      case 1: return const RoomListScreen();
      case 2: return const MenuListScreen();
      case 3: return const UnifiedHistoryScreen(); 
      case 4: return const ProfileScreen();
      default: return _buildHomeDashboard(top);
    }
  }

  Widget _buildHomeDashboard(double top) {
    final cartProvider = context.watch<CartProvider>();

    return SingleChildScrollView(
      key: const ValueKey(0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // --- HEADER NAVY PREMIUM ---
          Container(
            width: double.infinity,
            padding: EdgeInsets.only(top: top + 40, left: 25, right: 25, bottom: 50),
            decoration: const BoxDecoration(
              gradient: AppTheme.headerGradient,
              borderRadius: BorderRadius.only(bottomLeft: Radius.circular(60), bottomRight: Radius.circular(60)),
              boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 10, offset: Offset(0, 5))]
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(_isGuest ? "Selamat Datang di Purnama," : "Halo,", style: TextStyle(color: Colors.white.withOpacity(0.6), fontSize: 13)),
                      Text(_name, style: const TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold), overflow: TextOverflow.ellipsis),
                    ],
                  ),
                ),
                Row(
                  children: [
                    // KERANJANG
                    IconButton(
                      onPressed: _isGuest ? _handleLockedAction : () => Navigator.push(context, MaterialPageRoute(builder: (c) => const CartScreen())),
                      icon: const Icon(Icons.shopping_bag_outlined, color: Colors.white70, size: 24),
                    ),
                    // NOTIFIKASI
                    IconButton(
                      onPressed: _isGuest ? _handleLockedAction : () => Navigator.push(context, MaterialPageRoute(builder: (c) => const NotificationScreen())),
                      icon: const Icon(Icons.notifications_none_rounded, color: Colors.white70, size: 24),
                    ),

                    const SizedBox(width: 8),

                    // --- TOMBOL LOGIN / PROFIL (POSISI HEADER) ---
                    _isGuest 
                      ? ElevatedButton(
                          onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const LoginScreen())),
                          style: ElevatedButton.styleFrom(
                            backgroundColor: AppTheme.goldAccent,
                            foregroundColor: AppTheme.primaryBlue,
                            elevation: 0,
                            padding: const EdgeInsets.symmetric(horizontal: 15, vertical: 8),
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                          ),
                          child: const Text("MASUK", style: TextStyle(fontWeight: FontWeight.w900, fontSize: 11, letterSpacing: 1)),
                        )
                      : Row(
                          children: [
                            IconButton(onPressed: _handleLogout, icon: const Icon(Icons.power_settings_new_rounded, color: Colors.white70, size: 24)),
                            const SizedBox(width: 5),
                            GestureDetector(
                              onTap: () => setState(() => _currentIndex = 4),
                              child: Container(
                                decoration: BoxDecoration(shape: BoxShape.circle, border: Border.all(color: Colors.white30, width: 2)),
                                child: CircleAvatar(
                                  radius: 18, backgroundColor: Colors.white24,
                                  backgroundImage: _photoUrl.isNotEmpty ? NetworkImage(_photoUrl) : null,
                                  child: _photoUrl.isEmpty ? const Icon(Icons.person, color: Colors.white, size: 18) : null,
                                ),
                              ),
                            ),
                          ],
                        ),
                  ],
                )
              ],
            ),
          ),
          
          // SEARCH BAR
          Transform.translate(
            offset: const Offset(0, -28),
            child: HomeSearchBar(
              isLocked: false, 
              onLockedTap: () {}, 
              onSearch: (q) => debugPrint("Cari: $q"),
            ),
          ),

          const EventHeader(),

          const Padding(
            padding: EdgeInsets.fromLTRB(25, 10, 25, 15),
            child: Text("Promo Spesial", style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: AppTheme.primaryBlue)),
          ),
          const AutoPromoSlider(), 

          const SizedBox(height: 35),
          const DynamicReviewSection(), 
          
          // Pesan Kecil Jika Guest (Tanpa Tombol karena sudah di atas)
          if (_isGuest)
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 25, vertical: 20),
              child: Container(
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: AppTheme.goldAccent.withOpacity(0.05),
                  borderRadius: BorderRadius.circular(15),
                  border: Border.all(color: AppTheme.goldAccent.withOpacity(0.2)),
                ),
                child: const Row(
                  children: [
                    Icon(Icons.info_outline_rounded, color: AppTheme.goldAccent, size: 20),
                    SizedBox(width: 12),
                    Expanded(
                      child: Text(
                        "Gunakan tombol Masuk di atas untuk melakukan reservasi hotel atau memesan menu restoran.",
                        style: TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: Colors.black54),
                      ),
                    ),
                  ],
                ),
              ),
            ),
          const SizedBox(height: 130), 
        ],
      ),
    );
  }
}