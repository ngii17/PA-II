import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../services/api_services.dart';
import '../../colors/login_constants.dart'; // Menyesuaikan dengan identitas Navy-Gold

class NotificationScreen extends StatefulWidget {
  const NotificationScreen({super.key});

  @override
  State<NotificationScreen> createState() => _NotificationScreenState();
}

class _NotificationScreenState extends State<NotificationScreen> {
  late Future<Map<String, dynamic>> _inboxData;

  @override
  void initState() {
    super.initState();
    _refreshInbox();
  }

  void _refreshInbox() {
    setState(() {
      _inboxData = _loadData();
    });
  }

  Future<Map<String, dynamic>> _loadData() async {
    final SharedPreferences prefs = await SharedPreferences.getInstance();
    int userId = prefs.getInt('user_id') ?? 0;
    // Tetap memanggil API asli Anda
    return ApiServices.getNotificationInbox(userId.toString());
  }

  // Menentukan Ikon dan Warna berdasarkan tipe notifikasi
  Map<String, dynamic> _getNotifStyle(String type) {
    switch (type) {
      case 'booking_confirmed':
        return {'icon': Icons.verified_user_rounded, 'color': Colors.green};
      case 'booking_cancelled':
        return {'icon': Icons.cancel_rounded, 'color': Colors.redAccent};
      case 'checkin_reminder':
        return {'icon': Icons.vpn_key_rounded, 'color': AppTheme.goldAccent};
      case 'checkout_reminder':
        return {'icon': Icons.exit_to_app_rounded, 'color': Colors.blueGrey};
      case 'payment_failed':
        return {'icon': Icons.error_rounded, 'color': Colors.orangeAccent};
      default:
        return {'icon': Icons.notifications_active_rounded, 'color': AppTheme.primaryBlue};
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        title: const Text("Kotak Masuk", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18)),
        centerTitle: true,
        backgroundColor: AppTheme.primaryBlue,
        foregroundColor: Colors.white,
        elevation: 0,
        actions: [
          IconButton(
            onPressed: _refreshInbox,
            icon: const Icon(Icons.refresh_rounded),
          )
        ],
      ),
      body: RefreshIndicator(
        color: AppTheme.goldAccent,
        onRefresh: () async => _refreshInbox(),
        child: FutureBuilder<Map<String, dynamic>>(
          future: _inboxData,
          builder: (context, snapshot) {
            if (snapshot.connectionState == ConnectionState.waiting) {
              return const Center(child: CircularProgressIndicator(color: AppTheme.primaryBlue));
            }

            if (snapshot.hasError) {
              return _buildErrorState();
            }

            final List<dynamic> notifications = snapshot.data?['data'] ?? [];

            if (notifications.isEmpty) {
              return _buildEmptyState();
            }

            return ListView.builder(
              padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 15),
              itemCount: notifications.length,
              itemBuilder: (context, index) {
                final notif = notifications[index];
                final style = _getNotifStyle(notif['type'] ?? "");
                
                return Container(
                  margin: const EdgeInsets.only(bottom: 15),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(22),
                    boxShadow: [
                      BoxShadow(
                        color: Colors.black.withOpacity(0.04),
                        blurRadius: 15,
                        offset: const Offset(0, 5),
                      )
                    ],
                  ),
                  child: ClipRRect(
                    borderRadius: BorderRadius.circular(22),
                    child: Container(
                      decoration: BoxDecoration(
                        border: Border(left: BorderSide(color: style['color'], width: 6)),
                      ),
                      child: ListTile(
                        contentPadding: const EdgeInsets.all(20),
                        leading: Container(
                          padding: const EdgeInsets.all(12),
                          decoration: BoxDecoration(
                            color: (style['color'] as Color).withOpacity(0.1),
                            shape: BoxShape.circle,
                          ),
                          child: Icon(style['icon'], color: style['color'], size: 26),
                        ),
                        title: Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Expanded(
                              child: Text(
                                notif['title'] ?? "Pemberitahuan",
                                style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 16, color: AppTheme.primaryBlue),
                                overflow: TextOverflow.ellipsis,
                              ),
                            ),
                          ],
                        ),
                        subtitle: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const SizedBox(height: 8),
                            Text(
                              notif['body'] ?? "Anda mendapatkan info baru.",
                              style: TextStyle(color: Colors.grey[700], fontSize: 13, height: 1.4),
                            ),
                            const SizedBox(height: 15),
                            Row(
                              children: [
                                const Icon(Icons.access_time_rounded, size: 12, color: Colors.grey),
                                const SizedBox(width: 5),
                                Text(
                                  _formatDate(notif['sent_at']),
                                  style: const TextStyle(fontSize: 10, color: Colors.grey, fontWeight: FontWeight.bold),
                                ),
                              ],
                            ),
                          ],
                        ),
                      ),
                    ),
                  ),
                );
              },
            );
          },
        ),
      ),
    );
  }

  // --- WIDGET TAMPILAN KOSONG ---
  Widget _buildEmptyState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Container(
            padding: const EdgeInsets.all(30),
            decoration: BoxDecoration(color: Colors.grey[100], shape: BoxShape.circle),
            child: Icon(Icons.mail_lock_rounded, size: 80, color: Colors.grey[300]),
          ),
          const SizedBox(height: 25),
          const Text("Kotak Masuk Kosong", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18, color: Colors.grey)),
          const SizedBox(height: 10),
          const Padding(
            padding: EdgeInsets.symmetric(horizontal: 50),
            child: Text(
              "Semua pemberitahuan tentang reservasi dan promo akan muncul di sini.",
              textAlign: TextAlign.center,
              style: TextStyle(color: Colors.grey, fontSize: 13),
            ),
          ),
        ],
      ),
    );
  }

  // --- WIDGET TAMPILAN ERROR ---
  Widget _buildErrorState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          const Icon(Icons.wifi_off_rounded, size: 60, color: Colors.redAccent),
          const SizedBox(height: 15),
          const Text("Koneksi Bermasalah", style: TextStyle(fontWeight: FontWeight.bold)),
          TextButton(onPressed: _refreshInbox, child: const Text("Coba Lagi")),
        ],
      ),
    );
  }

  // Helper format tanggal sederhana
  String _formatDate(dynamic date) {
    if (date == null) return "-";
    String d = date.toString();
    if (d.length >= 16) {
      return d.substring(0, 16).replaceAll('T', ' ');
    }
    return d;
  }
}