import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../services/api_services.dart';
import '../event/event_header.dart'; // <--- IMPORT HEADER

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
          content: SingleChildScrollView( // Agar tidak overflow saat keyboard muncul
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
                if (commentController.text.length < 5) {
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
              child: const Text("Kirim"),
            ),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    // KUNCI: Ambil warna tema aktif (Valentine = Pink, HUT RI = Merah)
    final primaryColor = Theme.of(context).primaryColor;

    final List<dynamic> details = (reservation['details'] != null && reservation['details'] is List) ? reservation['details'] : [];
    final Map<String, dynamic> detailTamu = details.isNotEmpty ? details[0] as Map<String, dynamic> : {"nama_tamu": "-", "nik_identitas": "-", "jumlah_tamu": 0};

    bool isPaid = reservation['status_reservasi_id'].toString() == '2';
    Color statusColor = isPaid ? Colors.green : Colors.orange;

    return Scaffold(
      appBar: AppBar(
        title: const Text("Detail Reservasi"), 
        backgroundColor: primaryColor, // Ikuti Tema (Pink/Merah/Biru)
        foregroundColor: Colors.white,
      ),
      body: SingleChildScrollView(
        child: Column(
          children: [
            // --- 1. BANNER EVENT (Agar sinkron dengan Home) ---
            const EventHeader(),

            Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(
                      color: statusColor.withOpacity(0.1), 
                      borderRadius: BorderRadius.circular(12), 
                      border: Border.all(color: statusColor)
                    ),
                    child: Column(
                      children: [
                        Text(reservation['nama_tipe']?.toString() ?? "Kamar", style: const TextStyle(fontSize: 22, fontWeight: FontWeight.bold)),
                        Text(isPaid ? "SUDAH DIBAYAR" : "MENUNGGU PEMBAYARAN", style: TextStyle(color: statusColor, fontWeight: FontWeight.bold)),
                      ],
                    ),
                  ),
                  const SizedBox(height: 25),
                  _itemRow("Check-in", reservation['tgl_checkin'] ?? "-"),
                  _itemRow("Check-out", reservation['tgl_checkout'] ?? "-"),
                  _itemRow("Nama Tamu", detailTamu['nama_tamu']),
                  const Divider(height: 40),
                  
                  if (isPaid)
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton.icon(
                        onPressed: () => _showReviewDialog(context),
                        icon: const Icon(Icons.rate_review, color: Colors.white),
                        label: const Text("BERI ULASAN KAMAR", style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white)),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: primaryColor, // Tombol ikuti tema
                          padding: const EdgeInsets.all(15),
                        ),
                      ),
                    ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _itemRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label, style: const TextStyle(color: Colors.grey, fontSize: 16)),
          Text(value, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
        ],
      ),
    );
  }
}