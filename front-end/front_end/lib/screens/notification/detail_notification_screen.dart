import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/event_provider.dart';
import '../../services/api_services.dart';

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
    // 1. Ambil data detail dari Port 8002
    _detailFuture = ApiServices.getNotificationDetail(widget.notifId, widget.userId);
    
    // 2. Tandai sebagai sudah dibaca secara otomatis
    ApiServices.markNotifAsRead(widget.notifId, widget.userId);
  }

  // Fungsi hapus data
  void _handleDelete() async {
    final result = await ApiServices.deleteNotification(widget.notifId, widget.userId);
    if (result['status'] == 'success' && mounted) {
      Navigator.pop(context, true);
    }
  }

  // Dialog Konfirmasi Hapus
  void _showDeleteDialog() {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text("Hapus Notifikasi"),
        content: const Text("Yakin ingin menghapus pesan ini dari riwayat?"),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context), 
            child: const Text("BATAL")
          ),
          TextButton(
            onPressed: () {
              Navigator.pop(context);
              _handleDelete();
            }, 
            child: const Text("HAPUS", style: TextStyle(color: Colors.red))
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final eventProvider = context.watch<EventProvider>();
    final Color primary = eventProvider.primaryColor;

    return Scaffold(
      appBar: AppBar(
        title: const Text("Detail Pemberitahuan"),
        backgroundColor: primary,
        foregroundColor: Colors.white,
        actions: [
          IconButton(
            icon: const Icon(Icons.delete_outline),
            onPressed: _showDeleteDialog,
          )
        ],
      ),
      body: FutureBuilder<Map<String, dynamic>>(
        future: _detailFuture,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(child: CircularProgressIndicator());
          }

          if (snapshot.hasError || snapshot.data?['status'] == 'error') {
            return const Center(child: Text("Notifikasi tidak ditemukan"));
          }

          final data = snapshot.data!['data'];
          
          // ==========================================
          // --- LOGIKA JUDUL & IKON BERDASARKAN TIPE ---
          // ==========================================
          String typeLabel = "INFORMASI";
          IconData headerIcon = Icons.notifications_none;
          
          // Tipe Restoran
          if (data['type'] == 'order_confirmed') {
            typeLabel = "PEMBAYARAN SUKSES";
            headerIcon = Icons.check_circle;
          } else if (data['type'] == 'order_ready') {
            typeLabel = "PESANAN SIAP";
            headerIcon = Icons.restaurant;
          } 
          // Tipe Hotel (BARU)
          else if (data['type'] == 'hotel_checkin' || data['type'] == 'checkin_reminder') {
            typeLabel = "CHECK-IN BERHASIL";
            headerIcon = Icons.vpn_key;
          } else if (data['type'] == 'hotel_checkout' || data['type'] == 'checkout_reminder') {
            typeLabel = "CHECK-OUT SELESAI";
            headerIcon = Icons.exit_to_app;
          }
          // Tipe Umum
          else if (data['type'] == 'broadcast') {
            typeLabel = "PROMO TERBARU";
            headerIcon = Icons.star;
          }

          return SingleChildScrollView(
            padding: const EdgeInsets.all(25),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Container Badge Tipe
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                  decoration: BoxDecoration(
                    color: primary.withOpacity(0.1),
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Icon(headerIcon, size: 16, color: primary),
                      const SizedBox(width: 8),
                      Text(
                        typeLabel,
                        style: TextStyle(color: primary, fontWeight: FontWeight.bold, fontSize: 10),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 20),

                // Judul
                Text(
                  data['title'] ?? "",
                  style: const TextStyle(fontSize: 22, fontWeight: FontWeight.bold),
                ),
                const SizedBox(height: 10),

                // Waktu
                Text(
                  "Diterima pada: ${data['sent_at'] != null ? data['sent_at'].toString().replaceAll('T', ' ').substring(0, 16) : '-'}",
                  style: const TextStyle(color: Colors.grey, fontSize: 12),
                ),
                
                const Padding(
                  padding: EdgeInsets.symmetric(vertical: 20),
                  child: Divider(),
                ),

                // Pesan (Body)
                Text(
                  data['body'] ?? "",
                  style: const TextStyle(fontSize: 16, height: 1.6, color: Colors.black87),
                ),

                const SizedBox(height: 40),

                // --- TOMBOL EXPLORE BERDASARKAN KONTEKS ---
                if (data['type'] == 'order_confirmed' || data['type'] == 'order_ready')
                  _buildActionButton(context, primary, "LIHAT RIWAYAT MAKAN")
                else if (data['type'] == 'hotel_checkin' || data['type'] == 'hotel_checkout')
                  _buildActionButton(context, primary, "LIHAT DETAIL RESERVASI"),
              ],
            ),
          );
        },
      ),
    );
  }

  // Widget tombol aksi yang seragam
  Widget _buildActionButton(BuildContext context, Color color, String label) {
    return SizedBox(
      width: double.infinity,
      height: 50,
      child: OutlinedButton(
        style: OutlinedButton.styleFrom(
          side: BorderSide(color: color),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
        ),
        onPressed: () => Navigator.pop(context), // Kembali untuk melihat daftar
        child: Text(label, style: TextStyle(color: color, fontWeight: FontWeight.bold)),
      ),
    );
  }
}