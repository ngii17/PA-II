import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../services/api_services.dart';
import '../event/event_header.dart'; // <--- IMPORT HEADER

class ReservationDetailScreen extends StatelessWidget {
  final Map<String, dynamic> reservation;

  const ReservationDetailScreen({super.key, required this.reservation});

  // ... (kode import tetap sama)

  void _showReviewDialog(BuildContext context) {
    final TextEditingController commentController = TextEditingController();
    int selectedRating = 5;
    bool isAnonymous = false; // 1. TAMBAHKAN VARIABEL INI
    bool isSending = false;
    final primaryColor = Theme.of(context).primaryColor;

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
                  decoration: const InputDecoration(
                    hintText: "Tulis komentar (min. 5 huruf)...", 
                    border: OutlineInputBorder()
                  ),
                ),
                
                // 2. TAMBAHKAN UI SWITCH DI SINI
                const SizedBox(height: 10),
                SwitchListTile(
                  contentPadding: EdgeInsets.zero,
                  title: const Text("Ulasan Anonim", style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold)),
                  subtitle: const Text("Nama Anda akan disensor di halaman publik", style: TextStyle(fontSize: 11)),
                  value: isAnonymous,
                  activeColor: primaryColor,
                  onChanged: isSending ? null : (val) {
                    setStateDialog(() => isAnonymous = val);
                  },
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

                // 3. KIRIM DATA KE API (TAMBAHKAN is_anonymous)
                final result = await ApiServices.storeHotelReview({
                  "user_id": userId,
                  "tipe_kamar_id": reservation['tipe_kamar_id'], 
                  "rating": selectedRating,
                  "komentar": commentController.text,
                  "is_anonymous": isAnonymous, // <--- DATA INI AKAN TERKIRIM KE LARAVEL
                });

                setStateDialog(() => isSending = false);
                if (context.mounted) {
                  Navigator.pop(context);
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(
                      content: Text(result['message']), 
                      backgroundColor: result['success'] ? Colors.green : Colors.red
                    ),
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

    // =========================================================================
    // --- PERBAIKAN LOGIKA PENGAMBILAN DATA IDENTITAS (FIX NAMA & NIK) ---
    // =========================================================================
    // Kita ambil langsung dari 'reservation' karena Backend sudah kita "Flatten"
    // Fallback: Jika di depan kosong, baru cari di dalam list details
    final List<dynamic> details = (reservation['details'] != null && reservation['details'] is List) ? reservation['details'] : [];
    
    final String namaTamu = reservation['nama_tamu'] ?? 
                           (details.isNotEmpty ? details[0]['nama_tamu'] : "-");
    
    final String nikIdentitas = reservation['nik_identitas'] ?? 
                               (details.isNotEmpty ? details[0]['nik_identitas'] : "-");
                               
    final String jumlahOrang = (reservation['jumlah_tamu'] ?? 
                               (details.isNotEmpty ? details[0]['jumlah_tamu'] : 0)).toString();
    // =========================================================================

    // --- LOGIKA STATUS ---
    int statusId = int.parse(reservation['status_reservasi_id']?.toString() ?? "1");
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

    bool canReview = statusId >= 2 && statusId <= 4;

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
                        Text(statusText, style: TextStyle(color: statusColor, fontWeight: FontWeight.bold)),
                      ],
                    ),
                  ),
                  const SizedBox(height: 25),

                  _buildSectionTitle("Informasi Tamu"),
                  // --- MENGGUNAKAN VARIABEL YANG SUDAH DIPERBAIKI ---
                  _itemRow("Nama Tamu", namaTamu),
                  _itemRow("NIK / KTP", nikIdentitas),
                  _itemRow("Jumlah Tamu", "$jumlahOrang Orang"),
                  
                  const Divider(height: 40),
                  
                  _buildSectionTitle("Informasi Menginap"),
                  _itemRow("Check-in", reservation['tgl_checkin'] ?? "-"),
                  _itemRow("Check-out", reservation['tgl_checkout'] ?? "-"),
                  _itemRow("Durasi Menginap", "${(DateTime.parse(reservation['tgl_checkout'] ?? DateTime.now().toString()).difference(DateTime.parse(reservation['tgl_checkin'] ?? DateTime.now().toString())).inDays)} Malam"),
                  const Divider(height: 40),

                  _itemRow(
                    "Total Bayar", 
                    "Rp ${double.parse(reservation['total_harga']?.toString() ?? "0").toStringAsFixed(0)}",
                  ),

                  const SizedBox(height: 30),
                  
                  if (canReview)
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

  Widget _buildSectionTitle(String title) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: Text(
        title,
        style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
      ),
    );
  }
}