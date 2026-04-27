import 'package:flutter/material.dart';
import '../../services/api_services.dart';
import 'edit_profile_screen.dart';
import '../event/event_header.dart'; // <--- IMPORT HEADER EVENT

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

  void _loadProfile() {
    setState(() {
      _profileData = ApiServices.getUserProfile();
    });
  }

  @override
  Widget build(BuildContext context) {
    // KUNCI: Mengambil warna tema aktif dari database lewat Provider
    final primaryColor = Theme.of(context).primaryColor;

    return Scaffold(
      appBar: AppBar(
        title: const Text("Profil Saya"),
        backgroundColor: primaryColor, // Ikuti tema
        foregroundColor: Colors.white,
        elevation: 0,
        actions: [
          FutureBuilder<Map<String, dynamic>>(
            future: _profileData,
            builder: (context, snapshot) {
              if (snapshot.hasData && snapshot.data?['success'] == true) {
                return IconButton(
                  icon: const Icon(Icons.edit_note_rounded, size: 28),
                  onPressed: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (context) => EditProfileScreen(userData: snapshot.data?['data']),
                      ),
                    ).then((value) {
                      if (value == true) {
                        _loadProfile(); 
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
            return Center(child: CircularProgressIndicator(color: primaryColor));
          }

          if (snapshot.hasError || snapshot.data?['success'] == false) {
            return const Center(child: Text("Gagal memuat profil."));
          }

          final user = snapshot.data?['data'];

          return SingleChildScrollView(
            child: Column(
              children: [
                // --- 1. BANNER EVENT (Paling Atas) ---
                const EventHeader(),

                // 2. Header Profil (Avatar & Nama)
                Container(
                  width: double.infinity,
                  padding: const EdgeInsets.symmetric(vertical: 40),
                  decoration: BoxDecoration(
                    color: primaryColor, // Ikuti tema
                    borderRadius: const BorderRadius.vertical(bottom: Radius.circular(30)),
                  ),
                  child: Column(
                    children: [
                      CircleAvatar(
                        radius: 60,
                        backgroundColor: Colors.white,
                        backgroundImage: user['profile_photo'] != null 
                            ? NetworkImage(user['profile_photo']) 
                            : null,
                        child: user['profile_photo'] == null 
                            ? Icon(Icons.person, size: 60, color: primaryColor) 
                            : null,
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

                const SizedBox(height: 25),

                // 3. Daftar Informasi
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 20),
                  child: Column(
                    children: [
                      _buildProfileItem(Icons.email_outlined, "Email", user['email'], primaryColor),
                      _buildProfileItem(Icons.phone_android_outlined, "Nomor HP", user['phone'], primaryColor),
                      _buildProfileItem(Icons.location_on_outlined, "Alamat", user['address'], primaryColor),
                      _buildProfileItem(Icons.calendar_today_outlined, "Member Sejak", user['created_at'], primaryColor),
                    ],
                  ),
                ),
                const SizedBox(height: 50),
              ],
            ),
          );
        },
      ),
    );
  }

  Widget _buildProfileItem(IconData icon, String label, String value, Color color) {
    return Card(
      elevation: 0,
      margin: const EdgeInsets.only(bottom: 15),
      color: Colors.grey[100],
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
      child: ListTile(
        leading: Icon(icon, color: color), // Icon ikuti tema
        title: Text(label, style: const TextStyle(fontSize: 12, color: Colors.grey)),
        subtitle: Text(value, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
      ),
    );
  }
}