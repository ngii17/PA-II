import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../services/api_services.dart';
import '../event/event_header.dart'; 
import '../../colors/login_constants.dart';
import '../../widgets/login_widgets.dart';

class OrderDetailScreen extends StatelessWidget {
  final Map<String, dynamic> order;

  const OrderDetailScreen({super.key, required this.order});

  // ──────────────────────────────────────────────────────────────────────────
  //  FUNGSI POP-UP ULASAN (MODERN & SOFT)
  // ──────────────────────────────────────────────────────────────────────────
  void _showRestoReviewDialog(BuildContext context, int menuId, String menuName) {
    final TextEditingController commentController = TextEditingController();
    int selectedRating = 5;
    bool isSending = false;

    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => StatefulBuilder(
        builder: (context, setStateDialog) => AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(25)),
          title: Text("Ulas $menuName", 
            textAlign: TextAlign.center, 
            style: const TextStyle(fontWeight: FontWeight.bold)
          ),
          content: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                const Text("Bagaimana rasa menu ini menurut Anda?", 
                  textAlign: TextAlign.center, 
                  style: TextStyle(color: Colors.grey, fontSize: 13)
                ),
                const SizedBox(height: 20),
                // Bintang Interaktif Emas
                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: List.generate(5, (index) {
                    return GestureDetector(
                      onTap: isSending ? null : () => setStateDialog(() => selectedRating = index + 1),
                      child: Icon(
                        index < selectedRating ? Icons.star_rounded : Icons.star_outline_rounded,
                        color: AppTheme.goldAccent, 
                        size: 40,
                      ),
                    );
                  }),
                ),
                const SizedBox(height: 20),
                // Input Komentar
                Container(
                  padding: const EdgeInsets.all(10),
                  decoration: BoxDecoration(
                    color: const Color(0xFFF3F4F6),
                    borderRadius: BorderRadius.circular(15),
                  ),
                  child: TextField(
                    controller: commentController,
                    maxLines: 3,
                    enabled: !isSending,
                    decoration: const InputDecoration(
                      hintText: "Tulis ulasan Anda (min. 5 huruf)...",
                      border: InputBorder.none,
                      hintStyle: TextStyle(fontSize: 13),
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

                final result = await ApiServices.storeRestoReview({
                  "user_id": userId,
                  "menu_id": menuId,
                  "rating": selectedRating,
                  "komentar": commentController.text,
                });

                setStateDialog(() => isSending = false);

                if (context.mounted) {
                  Navigator.pop(context);
                  ModernNotify.show(context, result['message'], isError: !result['success']);
                }
              },
              style: ElevatedButton.styleFrom(
                backgroundColor: AppTheme.primaryBlue,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
              ),
              child: isSending 
                ? const SizedBox(height: 20, width: 20, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2)) 
                : const Text("Kirim", style: TextStyle(color: Colors.white)),
            ),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final List<dynamic> details = order['details'] ?? [];
    
    // Logika Status Pembayaran (ID 2 = Lunas sesuai Database Port 8000)
    bool isPaid = order['status_pembayaran_id'].toString() == '2';
    String statusText = isPaid ? "BERHASIL DIBAYAR" : "MENUNGGU PEMBAYARAN";

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      extendBodyBehindAppBar: true,
      appBar: AppBar(
        title: const Text("Detail Pesanan", style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white, fontSize: 18)),
        centerTitle: true,
        backgroundColor: Colors.transparent,
        elevation: 0,
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      body: SingleChildScrollView(
        child: Column(
          children: [
            // ── 1. HEADER GRADIEN GOLD (Radius 60) ───────────────────────────
            Container(
              width: double.infinity,
              padding: EdgeInsets.only(top: MediaQuery.of(context).padding.top + 60, bottom: 40),
              decoration: const BoxDecoration(
                gradient: AppTheme.restoGradient, // Sesuai identitas Restoran
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
                    decoration: BoxDecoration(color: Colors.white.withOpacity(0.15), shape: BoxShape.circle),
                    child: const Icon(Icons.receipt_long_rounded, color: Colors.white, size: 45),
                  ),
                  const SizedBox(height: 15),
                  Text(
                    "Nota #RS-${order['id']}", 
                    style: const TextStyle(color: Colors.white, fontSize: 20, fontWeight: FontWeight.bold)
                  ),
                  const SizedBox(height: 8),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
                    decoration: BoxDecoration(color: Colors.white.withOpacity(0.2), borderRadius: BorderRadius.circular(20)),
                    child: Text(
                      statusText,
                      style: const TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.bold, letterSpacing: 1.2),
                    ),
                  ),
                ],
              ),
            ),

            const EventHeader(), // Integrasi Banner Event Port 8001

            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 25, vertical: 20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // ── 2. DAFTAR MENU PESANAN ──────────────────────────────────
                  const Text("Daftar Menu", style: TextStyle(fontSize: 17, fontWeight: FontWeight.bold, color: Colors.black87)),
                  const SizedBox(height: 15),
                  Container(
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(22),
                      border: Border.all(color: Colors.grey.shade100),
                      boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.02), blurRadius: 10, offset: const Offset(0, 5))],
                    ),
                    child: ListView.separated(
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
                      itemCount: details.length,
                      separatorBuilder: (context, index) => const Divider(height: 1, indent: 20, endIndent: 20),
                      itemBuilder: (context, index) {
                        final item = details[index];
                        String menuName = item['menu']['nama_menu'] ?? "Menu";
                        int menuId = item['menu_id'];

                        return ListTile(
                          contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
                          leading: Container(
                            padding: const EdgeInsets.all(8),
                            decoration: BoxDecoration(color: AppTheme.goldAccent.withOpacity(0.1), borderRadius: BorderRadius.circular(10)),
                            child: const Icon(Icons.restaurant_menu_rounded, color: AppTheme.goldAccent, size: 24),
                          ),
                          title: Text(menuName, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
                          subtitle: Text(
                            "${item['jumlah']} porsi x Rp ${double.parse(item['harga_at_porsi'].toString()).toStringAsFixed(0)}", 
                            style: const TextStyle(fontSize: 12)
                          ),
                          trailing: isPaid 
                            ? TextButton(
                                onPressed: () => _showRestoReviewDialog(context, menuId, menuName),
                                style: TextButton.styleFrom(
                                  backgroundColor: AppTheme.primaryBlue.withOpacity(0.1),
                                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                                ),
                                child: const Text("ULAS", 
                                  style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: AppTheme.primaryBlue)
                                ),
                              )
                            : Text(
                                "Rp ${(item['jumlah'] * double.parse(item['harga_at_porsi'].toString())).toStringAsFixed(0)}",
                                style: const TextStyle(fontWeight: FontWeight.bold, color: AppTheme.goldAccent),
                              ),
                        );
                      },
                    ),
                  ),

                  const SizedBox(height: 30),

                  // ── 3. INFORMASI TRANSAKSI ──────────────────────────────────
                  const Text("Informasi Transaksi", style: TextStyle(fontSize: 17, fontWeight: FontWeight.bold, color: Colors.black87)),
                  const SizedBox(height: 15),
                  Container(
                    padding: const EdgeInsets.all(22),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(22),
                      border: Border.all(color: Colors.grey.shade100),
                    ),
                    child: Column(
                      children: [
                        _buildInfoRow(Icons.payment_rounded, "Metode", order['metode_pembayaran'] ?? "-"),
                        _buildInfoRow(Icons.access_time_filled_rounded, "Waktu", order['created_at'].toString().substring(0, 16)),
                        const Padding(padding: EdgeInsets.symmetric(vertical: 12), child: Divider()),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            const Text("Total Bayar", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
                            Text(
                              "Rp ${double.parse(order['total_harga'].toString()).toStringAsFixed(0)}",
                              style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 20, color: AppTheme.primaryBlue),
                            ),
                          ],
                        ),
                      ],
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

  Widget _buildInfoRow(IconData icon, String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8),
      child: Row(
        children: [
          Icon(icon, size: 18, color: Colors.grey.shade400),
          const SizedBox(width: 12),
          Text(label, style: const TextStyle(color: Colors.grey, fontSize: 13, fontWeight: FontWeight.w500)),
          const Spacer(),
          Text(value, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: Colors.black87)),
        ],
      ),
    );
  }
}