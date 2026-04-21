import 'package:flutter/material.dart';
import '../../services/api_services.dart';
import 'edit_profile_screen.dart'; // Import halaman edit

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({super.key});

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  late Future<Map<String, dynamic>> _profileData;

  @override
  void initState() {
    super.initState();
    _loadProfile();
  }

  // Fungsi untuk memuat data profil
  void _loadProfile() {
    setState(() {
      _profileData = ApiServices.getUserProfile();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Profil Saya"),
        backgroundColor: Colors.blueAccent,
        foregroundColor: Colors.white,
        elevation: 0,
        actions: [
          // --- TOMBOL EDIT DI APPBAR ---
          FutureBuilder<Map<String, dynamic>>(
            future: _profileData,
            builder: (context, snapshot) {
              if (snapshot.hasData && snapshot.data?['success'] == true) {
                return IconButton(
                  icon: const Icon(Icons.edit_note_rounded, size: 28),
                  onPressed: () {
                    // Pindah ke halaman Edit sambil membawa data user saat ini
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (context) => EditProfileScreen(userData: snapshot.data?['data']),
                      ),
                    ).then((value) {
                      // JIKA KEMBALI DARI EDIT: Cek apakah ada perubahan (value == true)
                      if (value == true) {
                        _loadProfile(); // Segarkan data profil di layar
                      }
                    });
                  },
                );
              }
              return const SizedBox();
            },
          ),
        ],
      ),
      body: FutureBuilder<Map<String, dynamic>>(
        future: _profileData,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(child: CircularProgressIndicator());
          }

          if (snapshot.hasError || snapshot.data?['success'] == false) {
            return const Center(child: Text("Gagal memuat profil."));
          }

          final user = snapshot.data?['data'];

          return SingleChildScrollView(
            child: Column(
              children: [
                // 1. Header Profil (Avatar & Nama)
                Container(
                  width: double.infinity,
                  padding: const EdgeInsets.symmetric(vertical: 40),
                  decoration: const BoxDecoration(
                    color: Colors.blueAccent,
                    borderRadius: BorderRadius.vertical(bottom: Radius.circular(30)),
                  ),
                  child: Column(
                    children: [
                      CircleAvatar(
                        radius: 60,
                        backgroundColor: Colors.white,
                        backgroundImage: NetworkImage(user['profile_photo']),
                      ),
                      const SizedBox(height: 15),
                      Text(
                        user['full_name'] ?? "-",
                        style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold, color: Colors.white),
                      ),
                      Text(
                        "@${user['username']}",
                        style: const TextStyle(fontSize: 16, color: Colors.white70),
                      ),
                    ],
                  ),
                ),

                const SizedBox(height: 30),

                // 2. Daftar Informasi (Detail)
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 20),
                  child: Column(
                    children: [
                      _buildProfileItem(Icons.email_outlined, "Email", user['email']),
                      _buildProfileItem(Icons.phone_android_outlined, "Nomor HP", user['phone']),
                      _buildProfileItem(Icons.location_on_outlined, "Alamat", user['address']),
                      _buildProfileItem(Icons.calendar_today_outlined, "Member Sejak", user['created_at']),
                    ],
                  ),
                ),
              ],
            ),
          );
        },
      ),
    );
  }

  Widget _buildProfileItem(IconData icon, String label, String value) {
    return Card(
      elevation: 0,
      margin: const EdgeInsets.only(bottom: 15),
      color: Colors.grey[100],
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
      child: ListTile(
        leading: Icon(icon, color: Colors.blueAccent),
        title: Text(label, style: const TextStyle(fontSize: 12, color: Colors.grey)),
        subtitle: Text(value, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
      ),
    );
  }
}