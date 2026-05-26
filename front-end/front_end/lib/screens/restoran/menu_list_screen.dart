import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:vibration/vibration.dart';

import '../../services/api_services.dart';
import '../../providers/event_provider.dart';
import '../../providers/cart_provider.dart'; 
import 'menu_resto.dart';
import 'menu_detail_screen.dart';
import 'cart_screen.dart';
import '../user/login_screen.dart';
import '../user/register_screen.dart';
import '../event/event_header.dart';
import '../../colors/login_constants.dart';
import '../../widgets/login_widgets.dart';

class MenuListScreen extends StatefulWidget {
  const MenuListScreen({super.key});

  @override
  State<MenuListScreen> createState() => _MenuListScreenState();
}

class _MenuListScreenState extends State<MenuListScreen> with SingleTickerProviderStateMixin {
  late Future<Map<String, dynamic>> _menuData;
  bool _isLoggedIn = false;
  
  late AnimationController _shakeController;
  late Animation<double> _shakeAnimation;

  @override
  void initState() {
    super.initState();
    _menuData = ApiServices.getRestaurantMenus();
    _checkAuthStatus();

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

  void _checkAuthStatus() async {
    final SharedPreferences prefs = await SharedPreferences.getInstance();
    String? token = prefs.getString('auth_token');
    if (mounted) setState(() => _isLoggedIn = (token != null && token.isNotEmpty));
  }

  @override
  void dispose() {
    _shakeController.dispose();
    super.dispose();
  }

  void _handleRestrictedAction(VoidCallback action) async {
    if (_isLoggedIn) {
      action();
    } else {
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
            children: [
              Icon(Icons.lock_person_rounded, color: AppTheme.goldAccent),
              SizedBox(width: 10),
              Text("Akses Terbatas", style: TextStyle(fontWeight: FontWeight.bold)),
            ],
          ),
          content: const Text("Silakan masuk atau daftar terlebih dahulu untuk memesan menu lezat kami."),
          actions: [
            TextButton(onPressed: () => Navigator.pop(c), child: const Text("Batal", style: TextStyle(color: Colors.grey))),
            ElevatedButton(
              style: ElevatedButton.styleFrom(backgroundColor: AppTheme.primaryBlue, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10))),
              onPressed: () {
                Navigator.pop(c);
                Navigator.push(context, MaterialPageRoute(builder: (c) => hasReg ? const LoginScreen() : const RegisterScreen()));
              },
              child: const Text("Masuk", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
            ),
          ],
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final cartProvider = context.watch<CartProvider>();
    final eventProvider = context.watch<EventProvider>();
    final eventName = eventProvider.activeTheme['nama_event'];
    const Color restoColor = AppTheme.goldAccent;
    const Color navyColor = AppTheme.primaryBlue;

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      body: AnimatedBuilder(
        animation: _shakeAnimation,
        builder: (context, child) => Transform.translate(offset: Offset(_shakeAnimation.value, 0), child: child),
        child: FutureBuilder<Map<String, dynamic>>(
          future: _menuData,
          builder: (context, snapshot) {
            if (snapshot.connectionState == ConnectionState.waiting) {
              return const Center(child: CircularProgressIndicator(color: restoColor));
            }
            if (snapshot.hasError) return const Center(child: Text("Gagal memuat menu."));

            List<dynamic> listJson = snapshot.data?['data'] ?? [];
            List<MenuResto> menus = listJson.map((e) => MenuResto.fromJson(e)).toList();

            return Stack(
              children: [
                SingleChildScrollView(
                  physics: const ClampingScrollPhysics(),
                    child: Column(
                    children: [
                      // --- HEADER GOLD ---
                      Container(
                        width: double.infinity,
                        padding: const EdgeInsets.only(top: 80, bottom: 40),
                        decoration: const BoxDecoration(
                          gradient: AppTheme.restoGradient, 
                          borderRadius: BorderRadius.only(bottomLeft: Radius.circular(60), bottomRight: Radius.circular(60)),
                        ),
                        child: const Column(
                          children: [
                            Icon(Icons.restaurant_rounded, color: Colors.white, size: 45),
                            SizedBox(height: 10),
                            Text("PURNAMA RESTO", style: TextStyle(color: Colors.white, fontSize: 20, fontWeight: FontWeight.bold, letterSpacing: 2)),
                          ],
                        ),
                      ),

                      const EventHeader(),

                      // DAFTAR MENU
                      ListView.builder(
                        shrinkWrap: true,
                        physics: const NeverScrollableScrollPhysics(),
                        // Padding bawah ekstra besar (220) agar menu terakhir tidak tertutup 2 bar
                        padding: const EdgeInsets.fromLTRB(20, 10, 20, 220), 
                        itemCount: menus.length,
                        itemBuilder: (context, index) {
                          final menu = menus[index];
                          int quantity = cartProvider.items[menu.id] ?? 0;

                          return Container(
                            margin: const EdgeInsets.only(bottom: 18),
                            decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(22), boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 15, offset: const Offset(0, 8))]),
                            child: InkWell(
                              onTap: () => Navigator.push(context, MaterialPageRoute(builder: (context) => MenuDetailScreen(menu: menu))),
                              borderRadius: BorderRadius.circular(22),
                              child: Padding(
                                padding: const EdgeInsets.all(12),
                                child: Row(
                                  children: [
                                    ClipRRect(
                                      borderRadius: BorderRadius.circular(15),
                                      child: Image.network(menu.fotoMenu ?? "", width: 85, height: 85, fit: BoxFit.cover, errorBuilder: (c,e,s) => Container(width: 85, height: 85, color: restoColor.withOpacity(0.1), child: const Icon(Icons.fastfood, color: Colors.grey))),
                                    ),
                                    const SizedBox(width: 15),
                                    Expanded(
                                      child: Column(
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: [
                                          Text(menu.namaMenu, maxLines: 1, overflow: TextOverflow.ellipsis, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                                          Text("Rp ${menu.harga.toStringAsFixed(0)}", style: const TextStyle(color: restoColor, fontWeight: FontWeight.w900, fontSize: 15)),
                                        ],
                                      ),
                                    ),
                                    // KONTROL QUANTITY
                                    Row(
                                      children: [
                                        if (quantity > 0)
                                          _buildCircleBtn(Icons.remove, () {
                                            _handleRestrictedAction(() => cartProvider.removeFromCart(menu.id));
                                          }, Colors.grey[100]!, Colors.black87),
                                        if (quantity > 0) 
                                          Padding(padding: const EdgeInsets.symmetric(horizontal: 10), child: Text("$quantity", style: const TextStyle(fontWeight: FontWeight.bold))),
                                        _buildCircleBtn(Icons.add, () {
                                          _handleRestrictedAction(() => cartProvider.addToCart(menu));
                                        }, navyColor, Colors.white),
                                      ],
                                    ),
                                  ],
                                ),
                              ),
                            ),
                          );
                        },
                      ),
                    ],
                  ),
                ),

                // --- FIX: FLOATING CART BAR (POSISI DI ATAS NAVBAR HOME) ---
                if (cartProvider.totalItems > 0)
                  Positioned(
                    bottom: 130, // <--- DINAIKKAN AGAR DI ATAS NAV DOCK HOME
                    left: 20, right: 20,
                    child: Container(
                      height: 75, padding: const EdgeInsets.symmetric(horizontal: 20),
                      decoration: BoxDecoration(
                        color: navyColor, 
                        borderRadius: BorderRadius.circular(20), 
                        boxShadow: [BoxShadow(color: Colors.black45, blurRadius: 20, offset: const Offset(0, 10))]
                      ),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Column(mainAxisAlignment: MainAxisAlignment.center, crossAxisAlignment: CrossAxisAlignment.start, children: [
                            Text("${cartProvider.totalItems} Item dipilih", style: const TextStyle(color: Colors.white70, fontSize: 11)),
                            Text("Rp ${cartProvider.getTotalPrice().toStringAsFixed(0)}", style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Colors.white)),
                          ]),
                          ElevatedButton(
                            onPressed: () {
                              _handleRestrictedAction(() => Navigator.push(context, MaterialPageRoute(builder: (context) => const CartScreen())));
                            },
                            style: ElevatedButton.styleFrom(backgroundColor: restoColor, foregroundColor: navyColor, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12))),
                            child: const Text("KERANJANG", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12)),
                          )
                        ],
                      ),
                    ),
                  ),
              ],
            );
          },
        ),
      ),
    );
  }

  Widget _buildCircleBtn(IconData icon, VoidCallback onTap, Color bg, Color ic) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(6),
        decoration: BoxDecoration(color: bg, shape: BoxShape.circle),
        child: Icon(icon, color: ic, size: 18),
      ),
    );
  }
}