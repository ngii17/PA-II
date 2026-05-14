import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../services/api_services.dart';
import 'detail_notification_screen.dart'; // Pastikan import ini ada

class NotificationScreen extends StatefulWidget {
  const NotificationScreen({super.key});

  @override
  State<NotificationScreen> createState() => _NotificationScreenState();
}

class _NotificationScreenState extends State<NotificationScreen> {
  late Future<Map<String, dynamic>> _inboxData;
  int _currentUserId = 0; // Simpan userId di sini

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
    _currentUserId = prefs.getInt('user_id') ?? 0; // Update userId global
    return ApiServices.getNotificationInbox(_currentUserId.toString());
  }

  // Fungsi menentukan ikon berdasarkan tipe (Hotel & Restoran)
  IconData _getIcon(String type) {
    switch (type) {
      // Hotel
      case 'booking_confirmed': return Icons.check_circle_outline;
      case 'booking_cancelled': return Icons.cancel_outlined;
      case 'checkin_reminder': return Icons.hotel;
      case 'checkout_reminder': return Icons.exit_to_app;
      case 'payment_failed': return Icons.error_outline;
      // Restoran
            // Restoran
      case 'order_confirmed': 
        return Icons.verified_user_outlined; // Ikon lunas/terverifikasi
      case 'order_ready': 
        return Icons.restaurant_menu;
      case 'broadcast': 
        return Icons.campaign_outlined; // Ikon promo admin
      default: 
        return Icons.notifications_active_outlined;
    }
  }

  @override
  Widget build(BuildContext context) {
    final primaryColor = Theme.of(context).primaryColor;

    return Scaffold(
      appBar: AppBar(
        title: const Text("Kotak Masuk"),
        backgroundColor: primaryColor,
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: RefreshIndicator(
        onRefresh: () async => _refreshInbox(),
        child: FutureBuilder<Map<String, dynamic>>(
          future: _inboxData,
          builder: (context, snapshot) {
            if (snapshot.connectionState == ConnectionState.waiting) {
              return const Center(child: CircularProgressIndicator());
            }

            if (snapshot.hasError) {
              return const Center(child: Text("Gangguan koneksi ke server."));
            }

            final List<dynamic> notifications = snapshot.data?['data'] ?? [];

            if (notifications.isEmpty) {
              return Center(
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Icon(Icons.mail_outline, size: 80, color: Colors.grey[300]),
                    const SizedBox(height: 10),
                    const Text("Belum ada pemberitahuan.", style: TextStyle(color: Colors.grey)),
                  ],
                ),
              );
            }

            return ListView.builder(
              padding: const EdgeInsets.all(15),
              itemCount: notifications.length,
              itemBuilder: (context, index) {
                final notif = notifications[index];
                // Cek status is_read (jika ada di database) untuk menebalkan teks
                bool isUnread = notif['is_read'] == false;
                
                return Card(
                  elevation: 0,
                  margin: const EdgeInsets.only(bottom: 12),
                  color: isUnread ? Colors.white : Colors.grey[50], // Bedakan warna jika belum baca
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(12),
                    side: BorderSide(
                      color: isUnread ? primaryColor.withOpacity(0.3) : Colors.grey[200]!,
                      width: isUnread ? 1.5 : 1,
                    ),
                  ),
                  child: ListTile(
                    contentPadding: const EdgeInsets.all(15),
                    onTap: () async {
                      // --- NAVIGASI KE DETAIL ---
                      final shouldRefresh = await Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (context) => DetailNotificationScreen(
                            notifId: notif['id'],
                            userId: _currentUserId,
                          ),
                        ),
                      );

                      // Jika kembali dari detail dan ada penghapusan, refresh list
                      if (shouldRefresh == true) {
                        _refreshInbox();
                      } else {
                        // Tetap refresh jika hanya ingin mengupdate status "is_read"
                        _refreshInbox();
                      }
                    },
                    leading: CircleAvatar(
                      backgroundColor: primaryColor.withOpacity(0.1),
                      child: Icon(_getIcon(notif['type']), color: primaryColor),
                    ),
                    title: Text(
                      notif['title'] ?? "Info Terbaru",
                      style: TextStyle(
                        fontWeight: isUnread ? FontWeight.bold : FontWeight.normal, 
                        fontSize: 16,
                      ),
                    ),
                    subtitle: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const SizedBox(height: 5),
                        Text(
                          notif['body'] ?? "", 
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                          style: const TextStyle(color: Colors.black87),
                        ),
                        const SizedBox(height: 10),
                        Text(
                          notif['sent_at'] != null 
                              ? notif['sent_at'].toString().substring(0, 16).replaceAll('T', ' ')
                              : "",
                          style: const TextStyle(fontSize: 10, color: Colors.grey),
                        ),
                      ],
                    ),
                    trailing: isUnread 
                        ? Icon(Icons.circle, size: 10, color: primaryColor) 
                        : null,
                  ),
                );
              },
            );
          },
        ),
      ),
    );
  }
}