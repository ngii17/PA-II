import 'package:flutter/material.dart';
import 'package:provider/provider.dart'; 
import 'package:shared_preferences/shared_preferences.dart';
import '../../services/api_services.dart';
import '../../providers/cart_provider.dart'; 
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

  @override
  void initState() {
    super.initState();
    _loadUserData();
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
          // --- 2. TOMBOL LONCENG NOTIFIKASI ---
          IconButton(
            icon: const Icon(Icons.notifications_none_rounded),
            tooltip: "Notifikasi",
            onPressed: () {
              Navigator.push(
                context,
                MaterialPageRoute(builder: (context) => const NotificationScreen()),
              );
            },
          ),

          // --- TOMBOL KERANJANG ---
          Stack(
            alignment: Alignment.center,
            children: [
              IconButton(
                icon: const Icon(Icons.shopping_cart_outlined),
                onPressed: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(builder: (context) => const CartScreen()),
                  );
                },
              ),
              if (cartProvider.totalItems > 0)
                Positioned(
                  right: 8,
                  top: 8,
                  child: Container(
                    padding: const EdgeInsets.all(2),
                    decoration: BoxDecoration(color: Colors.red, borderRadius: BorderRadius.circular(10)),
                    constraints: const BoxConstraints(minWidth: 16, minHeight: 16),
                    child: Text(
                      '${cartProvider.totalItems}',
                      style: const TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.bold),
                      textAlign: TextAlign.center,
                    ),
                  ),
                ),
            ],
          ),
          
          // --- FOTO PROFIL ---
          GestureDetector(
            onTap: () {
              Navigator.push(context, MaterialPageRoute(builder: (context) => const ProfileScreen()))
                  .then((_) => _loadUserData()); 
            },
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

          // --- LOGOUT ---
          IconButton(
            onPressed: _handleLogout,
            icon: const Icon(Icons.logout, size: 20),
            tooltip: "Keluar",
          )
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
                    child: _photoUrl.isEmpty 
                        ? Icon(Icons.account_circle, size: 100, color: primaryColor) 
                        : null,
                  ),
                  const SizedBox(height: 15),
                  const Text("Selamat Datang,", style: TextStyle(fontSize: 16)),
                  Text(_name, style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold)),
                  const SizedBox(height: 40),

                  // ================= MODUL HOTEL =================
                  const Text("Layanan Hotel", style: TextStyle(fontWeight: FontWeight.bold, color: Colors.grey)),
                  const SizedBox(height: 10),
                  _buildMenuButton(
                    context,
                    label: "LIHAT KATALOG KAMAR",
                    icon: Icons.meeting_room,
                    color: primaryColor,
                    onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (context) => const RoomListScreen())),
                  ),
                  const SizedBox(height: 10),
                  _buildMenuButton(
                    context,
                    label: "RIWAYAT RESERVASI",
                    icon: Icons.receipt_long,
                    color: primaryColor,
                    isOutlined: true,
                    onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (context) => const ReservationHistoryScreen())),
                  ),

                  const SizedBox(height: 30),
                  const Divider(indent: 50, endIndent: 50),
                  const SizedBox(height: 20),

                  // ================= MODUL RESTORAN =================
                  const Text("Layanan Restoran", style: TextStyle(fontWeight: FontWeight.bold, color: Colors.grey)),
                  const SizedBox(height: 10),
                  _buildMenuButton(
                    context,
                    label: "MENU RESTORAN",
                    icon: Icons.restaurant_menu,
                    color: Colors.orangeAccent,
                    onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (context) => const MenuListScreen())),
                  ),
                  const SizedBox(height: 10),
                  _buildMenuButton(
                    context,
                    label: "RIWAYAT MAKAN",
                    icon: Icons.history,
                    color: Colors.orangeAccent,
                    isOutlined: true,
                    onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (context) => const OrderHistoryScreen())),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildMenuButton(
    BuildContext context, {
    required String label,
    required IconData icon,
    required Color color,
    required VoidCallback onPressed,
    bool isOutlined = false,
  }) {
    return SizedBox(
      width: 280,
      child: isOutlined
          ? OutlinedButton.icon(
              onPressed: onPressed,
              icon: Icon(icon),
              label: Text(label),
              style: OutlinedButton.styleFrom(
                padding: const EdgeInsets.all(15),
                foregroundColor: color,
                side: BorderSide(color: color),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
              ),
            )
          : ElevatedButton.icon(
              onPressed: onPressed,
              icon: Icon(icon),
              label: Text(label),
              style: ElevatedButton.styleFrom(
                padding: const EdgeInsets.all(15),
                backgroundColor: color,
                foregroundColor: Colors.white,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                elevation: 2,
              ),
            ),
    );
  }
}