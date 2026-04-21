import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../services/api_services.dart';
import '../user/login_screen.dart';
import '../hotel/room_list_screen.dart';
import '../hotel/reservation_history_screen.dart';
import '../restoran/menu_list_screen.dart';
import '../restoran/order_history_screen.dart';
import '../user/profile_screen.dart'; // Import halaman profil

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  String _name = "...";
  String _photoUrl = ""; // Simpan link foto profil

  @override
  void initState() {
    super.initState();
    _loadUserData();
  }

  // Fungsi untuk mengambil data user terbaru dari server port 8000
  void _loadUserData() async {
    try {
      // 1. Ambil data profil lengkap dari API
      final result = await ApiServices.getUserProfile();
      
      if (result['success'] == true) {
        setState(() {
          _name = result['data']['full_name'];
          _photoUrl = result['data']['profile_photo'];
        });
      } else {
        // Jika gagal API, ambil dari memori lokal (SharedPreferences)
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
    return Scaffold(
      appBar: AppBar(
        title: const Text("Purnama Hotel & Resto"),
        backgroundColor: Colors.blueAccent,
        foregroundColor: Colors.white,
        elevation: 0,
        actions: [
          // --- IKON PROFIL BERKELAS (DENGAN FOTO/AVATAR) ---
          GestureDetector(
            onTap: () {
              Navigator.push(
                context,
                MaterialPageRoute(builder: (context) => const ProfileScreen()),
              ).then((_) => _loadUserData()); // AKAN REFRESH SAAT KEMBALI
            },
            child: Padding(
              padding: const EdgeInsets.only(right: 10),
              child: CircleAvatar(
                radius: 18,
                backgroundImage: _photoUrl.isNotEmpty ? NetworkImage(_photoUrl) : null,
                child: _photoUrl.isEmpty ? const Icon(Icons.person) : null,
              ),
            ),
          ),
          IconButton(
            onPressed: _handleLogout,
            icon: const Icon(Icons.logout),
            tooltip: "Keluar",
          )
        ],
      ),
      body: SingleChildScrollView(
        child: Center(
          child: Padding(
            padding: const EdgeInsets.symmetric(vertical: 40),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                // Header Selamat Datang dengan Avatar Besar
                CircleAvatar(
                  radius: 50,
                  backgroundColor: Colors.blueAccent.withOpacity(0.1),
                  backgroundImage: _photoUrl.isNotEmpty ? NetworkImage(_photoUrl) : null,
                  child: _photoUrl.isEmpty 
                      ? const Icon(Icons.account_circle, size: 100, color: Colors.blueAccent) 
                      : null,
                ),
                const SizedBox(height: 15),
                const Text("Selamat Datang,", style: TextStyle(fontSize: 16)),
                Text(_name, style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold)),
                const SizedBox(height: 40),

                // ================= MODUL HOTEL (BIRU) =================
                const Text("Layanan Hotel", style: TextStyle(fontWeight: FontWeight.bold, color: Colors.grey)),
                const SizedBox(height: 10),
                _buildMenuButton(
                  context,
                  label: "LIHAT KATALOG KAMAR",
                  icon: Icons.meeting_room,
                  color: Colors.blueAccent,
                  onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (context) => const RoomListScreen())),
                ),
                const SizedBox(height: 10),
                _buildMenuButton(
                  context,
                  label: "RIWAYAT RESERVASI",
                  icon: Icons.receipt_long,
                  color: Colors.blueAccent,
                  isOutlined: true,
                  onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (context) => const ReservationHistoryScreen())),
                ),

                const SizedBox(height: 30),
                const Divider(indent: 50, endIndent: 50),
                const SizedBox(height: 20),

                // ================= MODUL RESTORAN (ORANYE) =================
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