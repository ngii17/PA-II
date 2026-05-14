import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../services/api_services.dart';
import '../event/event_header.dart';

class ReservationDetailScreen extends StatelessWidget {
  final Map<String, dynamic> reservation;

  const ReservationDetailScreen({super.key, required this.reservation});

  void _showReviewDialog(BuildContext context) {
    final TextEditingController commentController = TextEditingController();
    int selectedRating = 5;
    bool isSending = false;

    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => StatefulBuilder(
        builder: (context, setStateDialog) => AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
          title: const Text("Beri Ulasan Kamar"),
          content: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                const Text("Bagaimana pengalaman menginap Anda?", textAlign: TextAlign.center),
                const SizedBox(height: 15),
                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: List.generate(5, (index) {
                    return IconButton(
                      icon: Icon(
                        index < selectedRating ? Icons.star : Icons.star_border,
                        color: Colors.amber, size: 30,
                      ),
                      onPressed: isSending ? null : () => setStateDialog(() => selectedRating = index + 1),
                    );
                  }),
                ),
                TextField(
                  controller: commentController,
                  maxLines: 3,
                  enabled: !isSending,
                  decoration: const InputDecoration(hintText: "Tulis komentar (min. 5 huruf)...", border: OutlineInputBorder()),
                ),
              ],
            ),
          ),
          actions: [
            TextButton(onPressed: isSending ? null : () => Navigator.pop(context), child: const Text("Batal")),
            ElevatedButton(
              onPressed: isSending ? null : () async {
                if (commentController.text.trim().length < 5) {
                  ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text("Komentar minimal 5 huruf")));
                  return;
                }
                setStateDialog(() => isSending = true);
                final SharedPreferences prefs = await SharedPreferences.getInstance();
                int userId = prefs.getInt('user_id') ?? 0;

                final result = await ApiServices.storeHotelReview({
                  "user_id": userId,
                  "tipe_kamar_id": reservation['tipe_kamar_id'], 
                  "rating": selectedRating,
                  "komentar": commentController.text,
                });

                setStateDialog(() => isSending = false);
                if (context.mounted) {
                  Navigator.pop(context);
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(content: Text(result['message']), backgroundColor: result['success'] ? Colors.green : Colors.red),
                  );
                }
              },
              child: isSending 
                ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white)) 
                : const Text("Kirim"),
            ),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final primaryColor = Theme.of(context).primaryColor;

    // Parsing data detail tamu
    final List<dynamic> details = (reservation['details'] != null && reservation['details'] is List) ? reservation['details'] : [];
    final Map<String, dynamic> detailTamu = details.isNotEmpty 
        ? details[0] as Map<String, dynamic> 
        : {"nama_tamu": "-", "nik_identitas": "-", "jumlah_tamu": 0};

    // ==========================================
    // --- LOGIKA PEMBAHARUAN STATUS (SINKRON DB) ---
    // ==========================================
    int statusId = int.parse(reservation['status_reservasi_id'].toString());
    Color statusColor;
    String statusText;

    switch (statusId) {
      case 1:
        statusColor = Colors.orange;
        statusText = "MENUNGGU PEMBAYARAN";
        break;
      case 2:
        statusColor = Colors.blue;
        statusText = "TERBAYAR (LUNAS)";
        break;
      case 3:
        statusColor = Colors.green;
        statusText = "SUDAH CHECK-IN";
        break;
      case 4:
        statusColor = Colors.grey;
        statusText = "SELESAI / CHECK-OUT";
        break;
      case 5:
        statusColor = Colors.red;
        statusText = "DIBATALKAN";
        break;
      default:
        statusColor = Colors.black45;
        statusText = "STATUS TIDAK DIKENAL";
    }

    // Ulasan diperbolehkan jika status adalah Terbayar, Check-in, atau Selesai
    bool canReview = statusId >= 2 && statusId <= 4;

    return Scaffold(
      appBar: AppBar(
        title: const Text("Detail Reservasi"), 
        backgroundColor: primaryColor,
        foregroundColor: Colors.white,
      ),
      body: SingleChildScrollView(
        child: Column(
          children: [
            const EventHeader(),
            Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // --- KOTAK STATUS ---
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(
                      color: statusColor.withOpacity(0.05), 
                      borderRadius: BorderRadius.circular(12), 
                      border: Border.all(color: statusColor.withOpacity(0.5))
                    ),
                    child: Column(
                      children: [
                        Text(
                          reservation['nama_tipe']?.toString() ?? "Kamar", 
                          style: const TextStyle(fontSize: 22, fontWeight: FontWeight.bold)
                        ),
                        const SizedBox(height: 8),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                          decoration: BoxDecoration(
                            color: statusColor,
                            borderRadius: BorderRadius.circular(20),
                          ),
                          child: Text(
                            statusText, 
                            style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 12)
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 25),

                  _buildSectionTitle("Informasi Tamu"),
                  _itemRow("Nama Tamu", detailTamu['nama_tamu']),
                  _itemRow("NIK / KTP", detailTamu['nik_identitas']),
                  _itemRow("Jumlah Tamu", "${detailTamu['jumlah_tamu']} Orang"),
                  
                  const Divider(height: 40),
                  
                  _buildSectionTitle("Informasi Menginap"),
                  _itemRow("Check-in", reservation['tgl_checkin'] ?? "-"),
                  _itemRow("Check-out", reservation['tgl_checkout'] ?? "-"),
                  _itemRow("Total Malam", "${reservation['total_malam'] ?? 0} Malam"),
                  _itemRow("Metode Bayar", reservation['metode_pembayaran'] ?? "-"),
                  
                  const Divider(height: 40),

                  _itemRow(
                    "Total Bayar", 
                    "Rp ${double.parse(reservation['total_harga'].toString()).toStringAsFixed(0)}",
                    isBold: true,
                    textColor: primaryColor,
                  ),

                  const SizedBox(height: 30),
                  
                  // --- TOMBOL ULASAN ---
                  if (canReview)
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton.icon(
                        onPressed: () => _showReviewDialog(context),
                        icon: const Icon(Icons.rate_review, color: Colors.white),
                        label: const Text("BERI ULASAN PENGALAMAN", style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white)),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: primaryColor,
                          padding: const EdgeInsets.all(15),
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                        ),
                      ),
                    ),
                  const SizedBox(height: 50),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSectionTitle(String title) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 10),
      child: Text(title, style: const TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: Colors.grey)),
    );
  }

  Widget _itemRow(String label, String value, {bool isBold = false, Color? textColor}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 6),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label, style: const TextStyle(color: Colors.black54, fontSize: 15)),
          Text(
            value, 
            style: TextStyle(
              fontWeight: isBold ? FontWeight.bold : FontWeight.w600, 
              fontSize: isBold ? 18 : 15,
              color: textColor ?? Colors.black,
            )
          ),
        ],
      ),
    );
  }
}