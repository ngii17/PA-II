import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../services/api_services.dart';
import '../../providers/event_provider.dart';
import 'detail_notification_screen.dart';

class NotificationScreen extends StatefulWidget {
  const NotificationScreen({super.key});

  @override
  State<NotificationScreen> createState() => _NotificationScreenState();
}

class _NotificationScreenState extends State<NotificationScreen> {
  late Future<Map<String, dynamic>> _inboxData;
  int _currentUserId = 0;

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
    _currentUserId = prefs.getInt('user_id') ?? 0;
    return ApiServices.getNotificationInbox(_currentUserId.toString());
  }

  // Menentukan Ikon dan Warna berdasarkan tipe (Gabungan Hotel & Restoran)
  Map<String, dynamic> _getNotifStyle(String type, Color primary) {
    switch (type) {
      // Hotel
      case 'booking_confirmed':
        return {'icon': Icons.check_circle_rounded, 'color': Colors.green};
      case 'booking_cancelled':
        return {'icon': Icons.cancel_rounded, 'color': Colors.redAccent};
      case 'checkin_reminder':
      case 'hotel_checkin':
        return {'icon': Icons.vpn_key_rounded, 'color': const Color(0xFFC9A227)};
      case 'checkout_reminder':
      case 'hotel_checkout':
        return {'icon': Icons.exit_to_app_rounded, 'color': Colors.blueGrey};
      
      // Restoran
      case 'order_confirmed':
        return {'icon': Icons.verified_user_rounded, 'color': Colors.blue};
      case 'order_ready':
        return {'icon': Icons.restaurant_menu_rounded, 'color': Colors.orangeAccent};
      
      // Umum / Admin
      case 'broadcast_admin':
      case 'broadcast':
        return {'icon': Icons.campaign_rounded, 'color': primary};
      
      default:
        return {'icon': Icons.notifications_active_rounded, 'color': primary};
    }
  }

  @override
  Widget build(BuildContext context) {
    final ep = context.watch<EventProvider>();
    final Color primary = ep.eventCode != 'default' ? ep.primaryColor : const Color(0xFF0C2D6B);

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        title: const Text("Kotak Masuk", style: TextStyle(fontWeight: FontWeight.w900, fontSize: 18)),
        centerTitle: true,
        backgroundColor: primary,
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
        color: primary,
        onRefresh: () async => _refreshInbox(),
        child: FutureBuilder<Map<String, dynamic>>(
          future: _inboxData,
          builder: (context, snapshot) {
            if (snapshot.connectionState == ConnectionState.waiting) {
              return Center(child: CircularProgressIndicator(color: primary));
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
                final style = _getNotifStyle(notif['type'] ?? "", primary);
                final bool isUnread = notif['is_read'] == false || notif['is_read'] == 0;
                
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
                        border: Border(
                          left: BorderSide(
                            color: isUnread ? style['color'] : Colors.grey[300]!, 
                            width: 6
                          )
                        ),
                      ),
                      child: ListTile(
                        contentPadding: const EdgeInsets.all(18),
                        onTap: () async {
                          // Navigasi ke Detail
                          final shouldRefresh = await Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (context) => DetailNotificationScreen(
                                notifId: notif['id'],
                                userId: _currentUserId,
                              ),
                            ),
                          );

                          // Refresh list saat kembali (untuk update status baca atau jika ada yang dihapus)
                          _refreshInbox();
                        },
                        leading: Container(
                          padding: const EdgeInsets.all(12),
                          decoration: BoxDecoration(
                            color: (style['color'] as Color).withOpacity(0.1),
                            shape: BoxShape.circle,
                          ),
                          child: Icon(style['icon'], color: style['color'], size: 24),
                        ),
                        title: Row(
                          children: [
                            Expanded(
                              child: Text(
                                notif['title'] ?? "Pemberitahuan",
                                style: TextStyle(
                                  fontWeight: isUnread ? FontWeight.w900 : FontWeight.w600, 
                                  fontSize: 15, 
                                  color: isUnread ? Colors.black87 : Colors.grey[600]
                                ),
                                overflow: TextOverflow.ellipsis,
                              ),
                            ),
                            if (isUnread)
                              Container(
                                width: 8,
                                height: 8,
                                decoration: BoxDecoration(color: primary, shape: BoxShape.circle),
                              ),
                          ],
                        ),
                        subtitle: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const SizedBox(height: 6),
                            Text(
                              notif['body'] ?? "Anda mendapatkan info baru.",
                              maxLines: 2,
                              overflow: TextOverflow.ellipsis,
                              style: TextStyle(
                                color: isUnread ? Colors.black54 : Colors.grey, 
                                fontSize: 13, 
                                height: 1.4
                              ),
                            ),
                            const SizedBox(height: 12),
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
            decoration: BoxDecoration(color: Colors.white, shape: BoxShape.circle, boxShadow: [
              BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 20)
            ]),
            child: Icon(Icons.mail_lock_rounded, size: 60, color: Colors.grey[300]),
          ),
          const SizedBox(height: 25),
          const Text("Kotak Masuk Kosong", style: TextStyle(fontWeight: FontWeight.w900, fontSize: 18, color: Colors.black87)),
          const SizedBox(height: 10),
          const Padding(
            padding: EdgeInsets.symmetric(horizontal: 50),
            child: Text(
              "Semua pemberitahuan tentang reservasi hotel dan pesanan restoran akan muncul di sini.",
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
          const Text("Gagal memuat data", style: TextStyle(fontWeight: FontWeight.bold)),
          const SizedBox(height: 5),
          TextButton(
            onPressed: _refreshInbox, 
            child: const Text("Coba Lagi", style: TextStyle(fontWeight: FontWeight.bold))
          ),
        ],
      ),
    );
  }

  // Helper format tanggal sederhana
  String _formatDate(dynamic date) {
    if (date == null) return "-";
    String d = date.toString();
    try {
      if (d.contains('T')) {
        return d.substring(0, 16).replaceAll('T', ' ');
      }
      return d.substring(0, 16);
    } catch (e) {
      return d;
    }
  }
}