import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../services/api_services.dart';
import '../../providers/event_provider.dart';
import 'edit_profile_screen.dart';
import '../event/event_header.dart';

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
      _profileData = ApiServices.getUserProfile().then((result) {
        if (result['success'] == true && result['data']['profile_photo'] != null) {
          String originalUrl = result['data']['profile_photo'];
          final separator = originalUrl.contains('?') ? '&' : '?';
          result['data']['profile_photo'] =
              "$originalUrl${separator}t=${DateTime.now().millisecondsSinceEpoch}";
        }
        return result;
      });
    });
  }

  @override
  Widget build(BuildContext context) {
    final eventProvider = context.watch<EventProvider>();
    final bool hasEvent = eventProvider.eventCode != 'default';
    final Color primaryColor = hasEvent ? eventProvider.primaryColor : const Color(0xFF00197D);
    final Color secondaryColor = hasEvent ? eventProvider.secondaryColor : const Color(0xFFD4AF37);
    final Color onPrimary = primaryColor.computeLuminance() > 0.5 ? Colors.black87 : Colors.white;

    final LinearGradient headerGradient = LinearGradient(
      begin: Alignment.topLeft,
      end: Alignment.bottomRight,
      colors: [primaryColor, secondaryColor.withOpacity(0.85)],
    );

    return Scaffold(
      backgroundColor: Colors.white,
      extendBodyBehindAppBar: true,
      appBar: AppBar(
        title: const Text("Profil Saya",
            style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white)),
        centerTitle: true,
        backgroundColor: Colors.transparent,
        elevation: 0,
        iconTheme: const IconThemeData(color: Colors.white),
        actions: [
          FutureBuilder<Map<String, dynamic>>(
            future: _profileData,
            builder: (context, snapshot) {
              if (snapshot.hasData && snapshot.data?['success'] == true) {
                return IconButton(
                  icon: const Icon(Icons.edit_square, color: Colors.white),
                  onPressed: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (context) => EditProfileScreen(
                          userData: snapshot.data?['data'],
                        ),
                      ),
                    ).then((value) {
                      if (value == true) _loadProfile();
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
            return const Center(
              child: Text("Gagal memuat profil. Periksa koneksi Anda."),
            );
          }

          final user = snapshot.data?['data'];

          return SingleChildScrollView(
            physics: const ClampingScrollPhysics(),
            child: Column(
              children: [
                // Header gradien dengan avatar
                Container(
                  width: double.infinity,
                  padding: EdgeInsets.only(
                    top: MediaQuery.of(context).padding.top + 60,
                    bottom: 45,
                  ),
                  decoration: BoxDecoration(
                    gradient: headerGradient,
                    borderRadius: const BorderRadius.only(
                      bottomLeft: Radius.circular(60),
                      bottomRight: Radius.circular(60),
                    ),
                    boxShadow: [
                      BoxShadow(
                          color: primaryColor.withOpacity(0.3),
                          blurRadius: 15,
                          offset: const Offset(0, 8)),
                    ],
                  ),
                  child: Column(
                    children: [
                      Container(
                        decoration: BoxDecoration(
                          shape: BoxShape.circle,
                          border: Border.all(
                              color: onPrimary.withOpacity(0.3), width: 4),
                        ),
                        child: CircleAvatar(
                          radius: 65,
                          backgroundColor: Colors.white,
                          backgroundImage: user['profile_photo'] != null
                              ? NetworkImage(user['profile_photo'])
                              : null,
                          onBackgroundImageError: user['profile_photo'] != null
                              ? (exception, stackTrace) {}
                              : null,
                          child: user['profile_photo'] == null
                              ? Text(
                                  user['full_name']
                                          ?.substring(0, 1)
                                          .toUpperCase() ??
                                      "P",
                                  style: TextStyle(
                                      fontSize: 45,
                                      fontWeight: FontWeight.bold,
                                      color: primaryColor),
                                )
                              : null,
                        ),
                      ),
                      const SizedBox(height: 20),
                      Text(
                        user['full_name'] ?? "User Purnama",
                        style: TextStyle(
                            fontSize: 24,
                            fontWeight: FontWeight.bold,
                            color: onPrimary,
                            letterSpacing: 0.5),
                      ),
                      const SizedBox(height: 4),
                      Text(
                        "@${user['username']}",
                        style: TextStyle(
                            fontSize: 15,
                            color: onPrimary.withOpacity(0.7),
                            letterSpacing: 1.5),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 20),
                const Padding(
                  padding: EdgeInsets.symmetric(horizontal: 20),
                  child: EventHeader(),
                ),
                const SizedBox(height: 10),
                // Detail informasi
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 25, vertical: 10),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        children: [
                          Icon(Icons.badge_outlined,
                              color: secondaryColor, size: 20),
                          const SizedBox(width: 10),
                          Text(
                            "Informasi Pribadi",
                            style: TextStyle(
                                fontSize: 18,
                                fontWeight: FontWeight.bold,
                                color: primaryColor),
                          ),
                        ],
                      ),
                      const SizedBox(height: 20),
                      _buildProfileItem(
                          Icons.email_outlined, "Email Terdaftar", user['email'],
                          primaryColor: primaryColor),
                      _buildProfileItem(
                          Icons.phone_android_rounded, "Nomor WhatsApp",
                          user['phone'], primaryColor: primaryColor),
                      _buildProfileItem(Icons.location_on_outlined,
                          "Alamat Domisili", user['address'],
                          primaryColor: primaryColor),
                      _buildProfileItem(
                          Icons.calendar_month_outlined, "Member Sejak",
                          user['created_at'], primaryColor: primaryColor),
                    ],
                  ),
                ),
                const SizedBox(height: 130),
              ],
            ),
          );
        },
      ),
    );
  }

  Widget _buildProfileItem(IconData icon, String label, String? value,
      {required Color primaryColor}) {
    return Container(
      margin: const EdgeInsets.only(bottom: 15),
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: const Color(0xFFF8F9FA),
        borderRadius: BorderRadius.circular(22),
        border: Border.all(color: Colors.grey.shade100),
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(15),
              boxShadow: [
                BoxShadow(
                    color: Colors.black.withOpacity(0.04), blurRadius: 8)
              ],
            ),
            child: Icon(icon, color: primaryColor, size: 22),
          ),
          const SizedBox(width: 18),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(label,
                    style: const TextStyle(
                        fontSize: 11,
                        color: Colors.grey,
                        fontWeight: FontWeight.w600,
                        letterSpacing: 0.5)),
                const SizedBox(height: 4),
                Text(
                  value ?? "-",
                  style: TextStyle(
                      fontSize: 15,
                      fontWeight: FontWeight.bold,
                      color: primaryColor),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}