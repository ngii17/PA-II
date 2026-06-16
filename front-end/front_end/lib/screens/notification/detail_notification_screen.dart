// screens/notification/detail_notification_screen.dart

import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/event_provider.dart';
import '../../services/api_services.dart';
import '../home/unified_history_screen.dart';
import '../restoran/menu_list_screen.dart';
import '../hotel/room_list_screen.dart';

class DetailNotificationScreen extends StatefulWidget {
  final int notifId;
  final int userId;

  const DetailNotificationScreen({
    super.key, 
    required this.notifId, 
    required this.userId
  });

  @override
  State<DetailNotificationScreen> createState() => _DetailNotificationScreenState();
}

class _DetailNotificationScreenState extends State<DetailNotificationScreen> {
  late Future<Map<String, dynamic>> _detailFuture;

  @override
  void initState() {
    super.initState();
    _detailFuture = ApiServices.getNotificationDetail(widget.notifId, widget.userId);
    _markAsRead();
  }

  void _markAsRead() async {
    await ApiServices.markNotifAsRead(widget.notifId, widget.userId);
  }

  void _handleDelete() async {
    final result = await ApiServices.deleteNotification(widget.notifId, widget.userId);
    if (result['status'] == 'success' && mounted) {
      Navigator.pop(context, true);
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text("Notifikasi berhasil dihapus")),
      );
    }
  }

  void _showDeleteDialog() {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
        title: const Text("Hapus Notifikasi", style: TextStyle(fontWeight: FontWeight.bold)),
        content: const Text("Yakin ingin menghapus pesan ini dari riwayat? Tindakan ini tidak dapat dibatalkan."),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context), 
            child: const Text("BATAL", style: TextStyle(color: Colors.grey))
          ),
          ElevatedButton(
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.red,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
            ),
            onPressed: () {
              Navigator.pop(context);
              _handleDelete();
            }, 
            child: const Text("HAPUS", style: TextStyle(color: Colors.white))
          ),
        ],
      ),
    );
  }

  // ============================================================
  // FUNGSI NAVIGASI
  // ============================================================
  
  void _navigateToHistory() {
    Navigator.pop(context);
    Navigator.pushReplacement(
      context,
      MaterialPageRoute(
        builder: (context) => const UnifiedHistoryScreen(),
      ),
    );
  }

  void _navigateToHotelReservation() {
    Navigator.pop(context);
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (context) => const RoomListScreen(),
      ),
    );
  }

  void _navigateToRestaurant() {
    Navigator.pop(context);
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (context) => const MenuListScreen(),
      ),
    );
  }

  void _navigateBack() {
    Navigator.pop(context);
  }

  // ============================================================
  // WIDGET PURNAMA LOGO
  // ============================================================
  Widget _buildPurnamaLogo() {
    return Image.asset(
      'assets/icons/icon-purnama.png',
      width: 38,
      height: 38,
      errorBuilder: (_, __, ___) => Container(
        width: 38,
        height: 38,
        decoration: BoxDecoration(
          shape: BoxShape.circle,
          gradient: const LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [Color(0xFF1A4A9E), Color(0xFF0C2D6B)],
          ),
          border: Border.all(color: const Color(0xFFC9A227), width: 2),
        ),
        child: const Center(
          child: Text(
            "P",
            style: TextStyle(color: Color(0xFFC9A227), fontWeight: FontWeight.w900, fontSize: 18),
          ),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final ep = context.watch<EventProvider>();
    final Color primary = ep.eventCode != 'default' ? ep.primaryColor : const Color(0xFF0C2D6B);
    final Color secondary = ep.eventCode != 'default' ? ep.secondaryColor : const Color(0xFFC9A227);
    final topPadding = MediaQuery.of(context).padding.top;

    return Scaffold(
      backgroundColor: Colors.white,
      body: Column(
        children: [
          // ── HEADER MODERN DENGAN TOMBOL BACK ──
          Container(
            width: double.infinity,
            padding: EdgeInsets.only(top: topPadding + 16, left: 20, right: 20, bottom: 20),
            decoration: BoxDecoration(
              gradient: LinearGradient(
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
                colors: [
                  primary,
                  primary.withOpacity(0.85),
                  secondary.withOpacity(0.7),
                ],
              ),
              borderRadius: const BorderRadius.only(
                bottomLeft: Radius.circular(36),
                bottomRight: Radius.circular(36),
              ),
              boxShadow: [
                BoxShadow(
                  color: primary.withOpacity(0.35),
                  blurRadius: 16,
                  offset: const Offset(0, 6),
                ),
              ],
            ),
            child: Column(
              children: [
                Row(
                  children: [
                    // ── TOMBOL BACK ──
                    GestureDetector(
                      onTap: () => Navigator.pop(context),
                      child: Container(
                        width: 34,
                        height: 34,
                        decoration: BoxDecoration(
                          color: Colors.white.withOpacity(0.15),
                          shape: BoxShape.circle,
                        ),
                        child: const Icon(
                          Icons.arrow_back_ios_new_rounded,
                          color: Colors.white70,
                          size: 16,
                        ),
                      ),
                    ),
                    const SizedBox(width: 10),
                    _buildPurnamaLogo(),
                    const SizedBox(width: 10),
                    const Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text("Hotel & Restoran",
                            style: TextStyle(color: Colors.white60, fontSize: 9, letterSpacing: 1.2)),
                        Text("PURNAMA BALIGE",
                            style: TextStyle(color: Colors.white, fontSize: 13, fontWeight: FontWeight.w800, letterSpacing: 0.8)),
                      ],
                    ),
                    const Spacer(),
                    GestureDetector(
                      onTap: _showDeleteDialog,
                      child: Container(
                        width: 34,
                        height: 34,
                        decoration: BoxDecoration(
                          color: Colors.white.withOpacity(0.12),
                          shape: BoxShape.circle,
                        ),
                        child: const Icon(Icons.delete_sweep_outlined, color: Colors.white70, size: 18),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 14),
                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Icon(Icons.notifications_active_rounded, color: secondary, size: 20),
                    const SizedBox(width: 8),
                    const Text(
                      "Detail Pemberitahuan",
                      style: TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.w800, letterSpacing: 0.5),
                    ),
                  ],
                ),
              ],
            ),
          ),
          // ── BODY ──
          Expanded(
            child: FutureBuilder<Map<String, dynamic>>(
              future: _detailFuture,
              builder: (context, snapshot) {
                if (snapshot.connectionState == ConnectionState.waiting) {
                  return Center(child: CircularProgressIndicator(color: primary));
                }

                if (snapshot.hasError || snapshot.data?['status'] == 'error' || snapshot.data?['data'] == null) {
                  return _buildErrorState();
                }

                final data = snapshot.data!['data'];
                
                String typeLabel = "INFORMASI";
                IconData headerIcon = Icons.notifications_none_rounded;
                
                final String type = data['type'] ?? "";

                if (type == 'order_confirmed') {
                  typeLabel = "PEMBAYARAN SUKSES";
                  headerIcon = Icons.check_circle_rounded;
                } else if (type == 'order_ready') {
                  typeLabel = "PESANAN SIAP";
                  headerIcon = Icons.restaurant_rounded;
                } else if (type.contains('hotel_checkin') || type.contains('checkin_reminder')) {
                  typeLabel = "CHECK-IN BERHASIL";
                  headerIcon = Icons.vpn_key_rounded;
                } else if (type.contains('hotel_checkout') || type.contains('checkout_reminder')) {
                  typeLabel = "CHECK-OUT SELESAI";
                  headerIcon = Icons.exit_to_app_rounded;
                } else if (type == 'broadcast_admin' || type == 'broadcast') {
                  typeLabel = "PENGUMUMAN RESMI";
                  headerIcon = Icons.campaign_rounded;
                }

                return SingleChildScrollView(
                  physics: const BouncingScrollPhysics(),
                  padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 24),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                        decoration: BoxDecoration(
                          color: primary.withOpacity(0.08),
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(color: primary.withOpacity(0.2)),
                        ),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Icon(headerIcon, size: 18, color: primary),
                            const SizedBox(width: 8),
                            Text(
                              typeLabel,
                              style: TextStyle(color: primary, fontWeight: FontWeight.w900, fontSize: 11, letterSpacing: 0.5),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 24),

                      Text(
                        data['title'] ?? "Tanpa Judul",
                        style: const TextStyle(fontSize: 24, fontWeight: FontWeight.w900, height: 1.3, letterSpacing: -0.5),
                      ),
                      const SizedBox(height: 12),

                      Row(
                        children: [
                          const Icon(Icons.access_time_rounded, size: 14, color: Colors.grey),
                          const SizedBox(width: 6),
                          Text(
                            _formatDate(data['sent_at']),
                            style: const TextStyle(color: Colors.grey, fontSize: 13, fontWeight: FontWeight.w500),
                          ),
                        ],
                      ),
                      
                      const Padding(
                        padding: EdgeInsets.symmetric(vertical: 24),
                        child: Divider(thickness: 1, color: Color(0xFFF1F1F1)),
                      ),

                      Text(
                        data['body'] ?? "Tidak ada isi pesan.",
                        style: TextStyle(
                          fontSize: 16, 
                          height: 1.7, 
                          color: Colors.black.withOpacity(0.8),
                          fontWeight: FontWeight.w400
                        ),
                      ),

                      const SizedBox(height: 50),

                      // ── TOMBOL ACTION BERDASARKAN KONTEKS ──
                      if (type.contains('order'))
                        _buildActionButton(
                          context, 
                          primary, 
                          Icons.history_rounded, 
                          "LIHAT RIWAYAT MAKAN",
                          onPressed: _navigateToHistory,
                        )
                      else if (type.contains('hotel'))
                        _buildActionButton(
                          context, 
                          primary, 
                          Icons.book_online_rounded, 
                          "LIHAT DETAIL RESERVASI",
                          onPressed: _navigateToHotelReservation,
                        )
                      else if (type.contains('broadcast'))
                        _buildActionButton(
                          context, 
                          Colors.deepPurple, 
                          Icons.menu_rounded, 
                          "LIHAT MENU RESTORAN",
                          onPressed: _navigateToRestaurant,
                        )
                      else
                        _buildActionButton(
                          context, 
                          Colors.grey[700]!, 
                          Icons.arrow_back_rounded, 
                          "KEMBALI KE DAFTAR",
                          onPressed: _navigateBack,
                        ),
                      
                      const SizedBox(height: 40),
                    ],
                  ),
                );
              },
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildErrorState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(Icons.info_outline_rounded, size: 64, color: Colors.grey[300]),
          const SizedBox(height: 16),
          const Text("Notifikasi tidak ditemukan", style: TextStyle(color: Colors.grey, fontWeight: FontWeight.bold)),
        ],
      ),
    );
  }

  String _formatDate(dynamic dateStr) {
    if (dateStr == null) return "-";
    try {
      final String raw = dateStr.toString();
      return raw.replaceAll('T', ' ').substring(0, 16);
    } catch (e) {
      return dateStr.toString();
    }
  }

  // ============================================================
  // WIDGET TOMBOL ACTION - DENGAN CALLBACK
  // ============================================================
  Widget _buildActionButton(
    BuildContext context, 
    Color color, 
    IconData icon, 
    String label, {
    required VoidCallback onPressed,
  }) {
    return Container(
      width: double.infinity,
      height: 54,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(14),
        boxShadow: [
          BoxShadow(
            color: color.withOpacity(0.2),
            blurRadius: 10,
            offset: const Offset(0, 4),
          )
        ]
      ),
      child: ElevatedButton.icon(
        icon: Icon(icon, size: 20, color: Colors.white),
        label: Text(label, style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w800, fontSize: 14)),
        style: ElevatedButton.styleFrom(
          backgroundColor: color,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
          elevation: 0,
        ),
        onPressed: onPressed,
      ),
    );
  }
}