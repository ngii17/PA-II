import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../services/api_services.dart';
import '../event/event_header.dart'; 
import '../../colors/login_constants.dart';
import '../../widgets/login_widgets.dart';

class ReservationDetailScreen extends StatelessWidget {
  final Map<String, dynamic> reservation;

  const ReservationDetailScreen({super.key, required this.reservation});

  // ──────────────────────────────────────────────────────────────────────────
  //  FUNGSI POP-UP ULASAN (MODERN & PREMIUM)
  // ──────────────────────────────────────────────────────────────────────────
  void _showReviewDialog(BuildContext context) {
    final TextEditingController commentController = TextEditingController();
    int selectedRating = 5;
    bool isSending = false;

    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => StatefulBuilder(
        builder: (context, setStateDialog) => AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(25)),
          title: const Text("Beri Ulasan Kamar", 
            textAlign: TextAlign.center, 
            style: TextStyle(fontWeight: FontWeight.bold)
          ),
          content: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                const Text("Bagaimana pengalaman menginap Anda?", 
                  textAlign: TextAlign.center, 
                  style: TextStyle(color: Colors.grey, fontSize: 13)
                ),
                const SizedBox(height: 20),
                // Bintang Interaktif Navy-Gold
                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: List.generate(5, (index) {
                    return GestureDetector(
                      onTap: isSending ? null : () => setStateDialog(() => selectedRating = index + 1),
                      child: Icon(
                        index < selectedRating ? Icons.star_rounded : Icons.star_outline_rounded,
                        color: AppTheme.goldAccent, 
                        size: 42,
                      ),
                    );
                  }),
                ),
                const SizedBox(height: 20),
                // Input Komentar Soft
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: const Color(0xFFF3F4F6),
                    borderRadius: BorderRadius.circular(18),
                  ),
                  child: TextField(
                    controller: commentController,
                    maxLines: 3,
                    enabled: !isSending,
                    style: const TextStyle(fontSize: 14),
                    decoration: const InputDecoration(
                      hintText: "Tulis kesan Anda di sini (min. 5 huruf)...",
                      border: InputBorder.none,
                      hintStyle: TextStyle(fontSize: 13, color: Colors.grey),
                    ),
                  ),
                ),
              ],
            ),
          ),
          actions: [
            TextButton(
              onPressed: isSending ? null : () => Navigator.pop(context), 
              child: const Text("Batal", style: TextStyle(color: Colors.grey))
            ),
            ElevatedButton(
              onPressed: isSending ? null : () async {
                if (commentController.text.trim().length < 5) {
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(content: Text("Komentar minimal 5 huruf"))
                  );
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
                  // Notifikasi Modern
                  ModernNotify.show(context, result['message'], isError: !result['success']);
                }
              },
              style: ElevatedButton.styleFrom(
                backgroundColor: AppTheme.primaryBlue,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
              ),
              child: isSending 
                ? const SizedBox(height: 18, width: 18, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2))
                : const Text("Kirim", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
            ),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    // Penanganan data tamu dari list details (Laravel Port 8000)
    final List<dynamic> details = (reservation['details'] != null && reservation['details'] is List) 
        ? reservation['details'] : [];
    final Map<String, dynamic> detailTamu = details.isNotEmpty 
        ? details[0] as Map<String, dynamic> 
        : {"nama_tamu": "-", "nik_identitas": "-", "jumlah_tamu": 0};

    // Logika Status Pembayaran (2 = Lunas)
    bool isPaid = reservation['status_reservasi_id'].toString() == '2';
    String statusText = isPaid ? "BERHASIL DIBAYAR" : "MENUNGGU PEMBAYARAN";

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      extendBodyBehindAppBar: true,
      appBar: AppBar(
        title: const Text("Detail Reservasi", 
          style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white, fontSize: 18)
        ),
        centerTitle: true,
        backgroundColor: Colors.transparent,
        elevation: 0,
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      body: SingleChildScrollView(
        child: Column(
          children: [
            // ── 1. HEADER GRADIEN NAVY (RADIUS 60) ───────────────────────────
            Container(
              width: double.infinity,
              padding: EdgeInsets.only(top: MediaQuery.of(context).padding.top + 60, bottom: 45),
              decoration: const BoxDecoration(
                gradient: AppTheme.headerGradient,
                borderRadius: BorderRadius.only(
                  bottomLeft: Radius.circular(60), 
                  bottomRight: Radius.circular(60)
                ),
                boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 15, offset: Offset(0, 8))],
              ),
              child: Column(
                children: [
                  Container(
                    padding: const EdgeInsets.all(15),
                    decoration: BoxDecoration(color: Colors.white.withOpacity(0.1), shape: BoxShape.circle),
                    child: const Icon(Icons.receipt_long_rounded, color: Colors.white, size: 45),
                  ),
                  const SizedBox(height: 15),
                  Text(
                    reservation['nama_tipe']?.toString() ?? "Kamar Purnama", 
                    style: const TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.bold, letterSpacing: 0.5)
                  ),
                  const SizedBox(height: 10),
                  // Badge Status Capsule
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
                    decoration: BoxDecoration(
                      color: Colors.white.withOpacity(0.2), 
                      borderRadius: BorderRadius.circular(20),
                      border: Border.all(color: Colors.white.withOpacity(0.1))
                    ),
                    child: Text(
                      statusText,
                      style: const TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.bold, letterSpacing: 1.2),
                    ),
                  ),
                ],
              ),
            ),

            const EventHeader(), // Banner Event Port 8001

            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 25, vertical: 20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // ── 2. CARD INFORMASI RESERVASI ──────────────────────────────
                  const Text("Rincian Jadwal", style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Colors.black87)),
                  const SizedBox(height: 15),
                  _buildDetailCard([
                    _itemRow(Icons.calendar_today_rounded, "Check-in", reservation['tgl_checkin'] ?? "-"),
                    _itemRow(Icons.calendar_month_rounded, "Check-out", reservation['tgl_checkout'] ?? "-"),
                    _itemRow(Icons.person_pin_rounded, "Nama Tamu", detailTamu['nama_tamu']),
                    _itemRow(Icons.fingerprint_rounded, "NIK Identitas", detailTamu['nik_identitas']),
                  ]),

                  const SizedBox(height: 25),

                  // ── 3. CARD RINCIAN PEMBAYARAN ─────────────────────────────
                  const Text("Informasi Transaksi", style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Colors.black87)),
                  const SizedBox(height: 15),
                  _buildDetailCard([
                    _itemRow(Icons.account_balance_wallet_rounded, "Metode Pembayaran", reservation['metode_pembayaran'] ?? "-"),
                    _itemRow(Icons.monetization_on_rounded, "Total Bayar", 
                      "Rp ${double.parse(reservation['total_harga'].toString()).toStringAsFixed(0)}", 
                      isPrice: true
                    ),
                  ]),

                  const SizedBox(height: 45),
                  
                  // ── 4. TOMBOL ULASAN (Hanya Aktif Jika Sudah Dibayar) ────────
                  if (isPaid)
                    SizedBox(
                      width: double.infinity,
                      height: 56,
                      child: ElevatedButton.icon(
                        onPressed: () => _showReviewDialog(context),
                        icon: const Icon(Icons.star_half_rounded, color: Colors.white),
                        label: const Text("BERI ULASAN PENGALAMAN", 
                          style: TextStyle(fontWeight: FontWeight.bold, letterSpacing: 1.2, fontSize: 14)
                        ),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: AppTheme.primaryBlue,
                          foregroundColor: Colors.white,
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(18)),
                          elevation: 8,
                          shadowColor: AppTheme.primaryBlue.withOpacity(0.4),
                        ),
                      ),
                    ),
                  const SizedBox(height: 60),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  // Widget Helper: Wadah Kartu Putih
  Widget _buildDetailCard(List<Widget> children) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(22),
        border: Border.all(color: Colors.grey.shade100),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.02), blurRadius: 15, offset: const Offset(0, 5))],
      ),
      child: Column(children: children),
    );
  }

  // Widget Helper: Baris Informasi dengan Ikon
  Widget _itemRow(IconData icon, String label, String value, {bool isPrice = false}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 12),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(
              color: AppTheme.primaryBlue.withOpacity(0.05),
              borderRadius: BorderRadius.circular(10),
            ),
            child: Icon(icon, size: 20, color: AppTheme.primaryBlue.withOpacity(0.7)),
          ),
          const SizedBox(width: 18),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(label, style: const TextStyle(color: Colors.grey, fontSize: 11, fontWeight: FontWeight.w600, letterSpacing: 0.3)),
                const SizedBox(height: 2),
                Text(value, style: TextStyle(
                  fontWeight: FontWeight.bold, 
                  fontSize: 15, 
                  color: isPrice ? AppTheme.goldAccent : Colors.black87
                )),
              ],
            ),
          ),
        ],
      ),
    );
  }
}