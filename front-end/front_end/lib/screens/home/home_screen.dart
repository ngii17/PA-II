import 'package:flutter/material.dart';
import 'package:provider/provider.dart'; 
import 'package:shared_preferences/shared_preferences.dart';
import '../../services/api_services.dart';
import '../../providers/cart_provider.dart'; 
import '../../providers/event_provider.dart'; // Tambahkan import EventProvider
import '../user/login_screen.dart';
import '../hotel/room_list_screen.dart';
import '../hotel/reservation_history_screen.dart';
import '../restoran/menu_list_screen.dart';
import '../restoran/order_history_screen.dart';
import '../restoran/cart_screen.dart'; 
import '../user/profile_screen.dart';
import '../event/event_header.dart';
import '../notification/notification_screen.dart';


class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  String _name = "...";
  String _photoUrl = ""; 
  bool _hasShownPromo = false; // Flag agar promo muncul sekali saja

  @override
  void initState() {
    super.initState();
    _loadUserData();
    
    // --- PENGECEKAN PROMO POP-UP ---
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _checkAndShowPromo();
    });
  }

  void _loadUserData() async {
    try {
      final result = await ApiServices.getUserProfile();
      if (result['success'] == true) {
        setState(() {
          _name = result['data']['full_name'];
          _photoUrl = result['data']['profile_photo'];
        });
      } else {
        final SharedPreferences prefs = await SharedPreferences.getInstance();
        setState(() {
          _name = prefs.getString('full_name') ?? "User";
        });
      }
    } catch (e) {
      print("LOG_ERROR: Gagal sinkronisasi data profil: $e");
    }
  }

  // --- LOGIKA POP-UP PROMO SINKRON DENGAN EVENT ---
  void _checkAndShowPromo() async {
    if (_hasShownPromo) return;

    final result = await ApiServices.getActivePromo();

    if (result['success'] == true && mounted) {
      final promoData = result['data'];
      final eventProvider = context.read<EventProvider>();

      // Ambil warna langsung dari Provider yang sudah sinkron dengan database
      final Color primaryTheme = eventProvider.primaryColor;
      final Color secondaryTheme = eventProvider.secondaryColor;

      _showPromoDialog(promoData, primaryTheme, secondaryTheme);
      setState(() => _hasShownPromo = true);
    }
  }

  void _showPromoDialog(Map<String, dynamic> promo, Color primary, Color secondary) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        contentPadding: EdgeInsets.zero,
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            // Header Pop-up (Warna Primary Event)
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: primary,
                borderRadius: const BorderRadius.only(
                  topLeft: Radius.circular(20),
                  topRight: Radius.circular(20),
                ),
              ),
              child: Column(
                children: [
                  Icon(Icons.local_offer, color: secondary, size: 50), // Warna Sekunder Event
                  const SizedBox(height: 10),
                  Text(
                    promo['nama_promo'],
                    textAlign: TextAlign.center,
                    style: const TextStyle(
                      color: Colors.white,
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ],
              ),
            ),
            
            // Body Pop-up
            Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                children: [
                  const Text("Kabar Gembira!", style: TextStyle(fontSize: 14, color: Colors.grey)),
                  const SizedBox(height: 8),
                  Text(
  promo['tipe_diskon'] == 'persen' 
      ? "DISKON ${promo['nominal'].toStringAsFixed(0)}%" 
      : "POTONGAN Rp ${promo['nominal'].toStringAsFixed(0)}",
  style: TextStyle(
    fontSize: 22, 
    fontWeight: FontWeight.w900, // <--- UBAH DARI .black MENJADI .w900
    color: primary
  ),
),
                  const SizedBox(height: 8),
                  const Text(
                    "Berlaku otomatis untuk semua menu restoran tanpa kode promo!",
                    textAlign: TextAlign.center,
                    style: TextStyle(fontSize: 13),
                  ),
                ],
              ),
            ),

            // Tombol Navigasi
            Padding(
              padding: const EdgeInsets.only(bottom: 20, left: 20, right: 20),
              child: SizedBox(
                width: double.infinity,
                height: 45,
                child: ElevatedButton(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: primary,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                  ),
                  onPressed: () {
                    Navigator.pop(context);
                    Navigator.push(context, MaterialPageRoute(builder: (context) => const MenuListScreen()));
                  },
                  child: const Text("PESAN SEKARANG", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  void _handleLogout() async {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => const Center(child: CircularProgressIndicator()),
    );
    await ApiServices.logout();
    final SharedPreferences prefs = await SharedPreferences.getInstance();
    await prefs.clear();
    if (mounted) {
      Navigator.pop(context);
      Navigator.pushAndRemoveUntil(
        context,
        MaterialPageRoute(builder: (context) => const LoginScreen()),
        (route) => false,
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final cartProvider = context.watch<CartProvider>();
    final primaryColor = Theme.of(context).primaryColor;

    return Scaffold(
      appBar: AppBar(
        title: const Text("Purnama Hotel"),
        backgroundColor: primaryColor,
        foregroundColor: Colors.white,
        elevation: 0,
        actions: [
          IconButton(
            icon: const Icon(Icons.notifications_none_rounded),
            onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (context) => const NotificationScreen())),
          ),
          Stack(
            alignment: Alignment.center,
            children: [
              IconButton(
                icon: const Icon(Icons.shopping_cart_outlined),
                onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (context) => const CartScreen())),
              ),
              if (cartProvider.totalItems > 0)
                Positioned(
                  right: 8,
                  top: 8,
                  child: Container(
                    padding: const EdgeInsets.all(2),
                    decoration: BoxDecoration(color: Colors.red, borderRadius: BorderRadius.circular(10)),
                    constraints: const BoxConstraints(minWidth: 16, minHeight: 16),
                    child: Text('${cartProvider.totalItems}', style: const TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.bold), textAlign: TextAlign.center),
                  ),
                ),
            ],
          ),
          GestureDetector(
            onTap: () => Navigator.push(context, MaterialPageRoute(builder: (context) => const ProfileScreen())).then((_) => _loadUserData()),
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 10),
              child: CircleAvatar(
                radius: 16,
                backgroundColor: Colors.white24,
                backgroundImage: _photoUrl.isNotEmpty ? NetworkImage(_photoUrl) : null,
                child: _photoUrl.isEmpty ? const Icon(Icons.person, color: Colors.white, size: 20) : null,
              ),
            ),
          ),
          IconButton(onPressed: _handleLogout, icon: const Icon(Icons.logout, size: 20))
        ],
      ),
      body: SingleChildScrollView(
        child: Column(
          children: [
            const EventHeader(),
            Padding(
              padding: const EdgeInsets.symmetric(vertical: 30),
              child: Column(
                children: [
                  CircleAvatar(
                    radius: 50,
                    backgroundColor: primaryColor.withOpacity(0.1),
                    backgroundImage: _photoUrl.isNotEmpty ? NetworkImage(_photoUrl) : null,
                    child: _photoUrl.isEmpty ? Icon(Icons.account_circle, size: 100, color: primaryColor) : null,
                  ),
                  const SizedBox(height: 15),
                  const Text("Selamat Datang,", style: TextStyle(fontSize: 16)),
                  Text(_name, style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold)),
                  const SizedBox(height: 40),

                  const Text("Layanan Hotel", style: TextStyle(fontWeight: FontWeight.bold, color: Colors.grey)),
                  const SizedBox(height: 10),
                  _buildMenuButton(context, label: "LIHAT KATALOG KAMAR", icon: Icons.meeting_room, color: primaryColor, onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (context) => const RoomListScreen()))),
                  const SizedBox(height: 10),
                  _buildMenuButton(context, label: "RIWAYAT RESERVASI", icon: Icons.receipt_long, color: primaryColor, isOutlined: true, onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (context) => const ReservationHistoryScreen()))),

                  const SizedBox(height: 30),
                  const Divider(indent: 50, endIndent: 50),
                  const SizedBox(height: 20),

                  const Text("Layanan Restoran", style: TextStyle(fontWeight: FontWeight.bold, color: Colors.grey)),
                  const SizedBox(height: 10),
                  _buildMenuButton(context, label: "MENU RESTORAN", icon: Icons.restaurant_menu, color: Colors.orangeAccent, onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (context) => const MenuListScreen()))),
                  const SizedBox(height: 10),
                  _buildMenuButton(context, label: "RIWAYAT MAKAN", icon: Icons.history, color: Colors.orangeAccent, isOutlined: true, onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (context) => const OrderHistoryScreen()))),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildMenuButton(BuildContext context, {required String label, required IconData icon, required Color color, required VoidCallback onPressed, bool isOutlined = false}) {
    return SizedBox(
      width: 280,
      child: isOutlined
          ? OutlinedButton.icon(onPressed: onPressed, icon: Icon(icon), label: Text(label), style: OutlinedButton.styleFrom(padding: const EdgeInsets.all(15), foregroundColor: color, side: BorderSide(color: color), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10))))
          : ElevatedButton.icon(onPressed: onPressed, icon: Icon(icon), label: Text(label), style: ElevatedButton.styleFrom(padding: const EdgeInsets.all(15), backgroundColor: color, foregroundColor: Colors.white, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)), elevation: 2)),
    );
  }
}