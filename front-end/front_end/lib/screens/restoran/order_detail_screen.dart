import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../services/api_services.dart';
import '../event/event_header.dart'; // Import Header Event

class OrderDetailScreen extends StatelessWidget {
  final Map<String, dynamic> order;

  const OrderDetailScreen({super.key, required this.order});

  // --- FUNGSI POP-UP FORM ULASAN MAKANAN ---
  void _showRestoReviewDialog(BuildContext context, int menuId, String menuName) {
    final TextEditingController commentController = TextEditingController();
    int selectedRating = 5;
    bool isSending = false;

    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => StatefulBuilder(
        builder: (context, setStateDialog) => AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
          title: Text("Ulas $menuName"),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              const Text("Bagaimana rasa makanan ini?", textAlign: TextAlign.center),
              const SizedBox(height: 10),
              Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: List.generate(5, (index) {
                  return IconButton(
                    icon: Icon(
                      index < selectedRating ? Icons.star : Icons.star_border,
                      color: Colors.amber,
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
                  hintText: "Tulis ulasan rasa (min. 5 huruf)...",
                  border: OutlineInputBorder(),
                ),
              ),
            ],
          ),
          actions: [
            TextButton(
              onPressed: isSending ? null : () => Navigator.pop(context), 
              child: const Text("Batal")
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
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(
                      content: Text(result['message']),
                      backgroundColor: result['success'] ? Colors.green : Colors.red,
                    ),
                  );
                }
              },
              child: isSending ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2)) : const Text("Kirim"),
            ),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final primaryColor = Theme.of(context).primaryColor;
    final List<dynamic> details = order['details'] ?? [];

    // Logika Status Pembayaran (ID 2 = Lunas)
    bool isPaid = order['status_pembayaran_id'].toString() == '2';
    Color statusColor = isPaid ? Colors.green : Colors.orange;
    String statusText = isPaid ? "SUDAH DIBAYAR" : "MENUNGGU PEMBAYARAN";

    return Scaffold(
      appBar: AppBar(
        title: const Text("Detail Pesanan Resto"),
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
                  // Header Status
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(
                      color: primaryColor.withOpacity(0.05),
                      borderRadius: BorderRadius.circular(12),
                      border: Border.all(color: primaryColor.withOpacity(0.3)),
                    ),
                    child: Column(
                      children: [
                        Icon(Icons.restaurant, size: 40, color: primaryColor),
                        const SizedBox(height: 10),
                        Text(
                          "Nota #RS-${order['id']}",
                          style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                        ),
                        const SizedBox(height: 5),
                        Text(
                          statusText,
                          style: TextStyle(color: statusColor, fontWeight: FontWeight.bold),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 25),

                  const Text("Pesanan Anda:", style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                  const SizedBox(height: 10),

                  // --- DAFTAR MENU DENGAN TOMBOL ULASAN ---
                  Card(
                    elevation: 2,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                    child: ListView.separated(
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
                      itemCount: details.length,
                      separatorBuilder: (context, index) => const Divider(indent: 15, endIndent: 15),
                      itemBuilder: (context, index) {
                        final item = details[index];
                        String menuName = item['menu']['nama_menu'] ?? "Menu";
                        int menuId = item['menu_id'];

                        return ListTile(
                          title: Text(menuName, style: const TextStyle(fontWeight: FontWeight.w600)),
                          subtitle: Text("${item['jumlah']} porsi x Rp ${double.parse(item['harga_at_porsi'].toString()).toStringAsFixed(0)}"),
                          
                          // --- PERBAIKAN: Tombol Ulas ditaruh di sini ---
                          trailing: isPaid 
                            ? ElevatedButton(
                                onPressed: () => _showRestoReviewDialog(context, menuId, menuName),
                                style: ElevatedButton.styleFrom(
                                  backgroundColor: primaryColor,
                                  padding: const EdgeInsets.symmetric(horizontal: 10),
                                  minimumSize: const Size(60, 30),
                                ),
                                child: const Text("ULAS", style: TextStyle(fontSize: 10, color: Colors.white)),
                              )
                            : Text(
                                "Rp ${(item['jumlah'] * double.parse(item['harga_at_porsi'].toString())).toStringAsFixed(0)}",
                                style: TextStyle(fontWeight: FontWeight.bold, color: primaryColor),
                              ),
                        );
                      },
                    ),
                  ),

                  const SizedBox(height: 25),

                  const Text("Informasi Pembayaran:", style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                  const SizedBox(height: 10),
                  Container(
                    padding: const EdgeInsets.all(15),
                    decoration: BoxDecoration(
                      color: Colors.grey[50],
                      borderRadius: BorderRadius.circular(10),
                      border: Border.all(color: Colors.grey[200]!),
                    ),
                    child: Column(
                      children: [
                        _buildInfoRow("Metode Pembayaran", order['metode_pembayaran'] ?? "-"),
                        _buildInfoRow("Waktu Pesan", order['created_at'].toString().substring(0, 16)),
                        const Divider(),
                        _buildInfoRow(
                          "Total Bayar", 
                          "Rp ${double.parse(order['total_harga'].toString()).toStringAsFixed(0)}",
                          isBold: true,
                          color: primaryColor,
                        ),
                      ],
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

  Widget _buildInfoRow(String label, String value, {bool isBold = false, Color color = Colors.black}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 5),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label, style: const TextStyle(color: Colors.grey)),
          Text(
            value, 
            style: TextStyle(
              fontWeight: isBold ? FontWeight.bold : FontWeight.w600,
              fontSize: isBold ? 18 : 14,
              color: color,
            )
          ),
        ],
      ),
    );
  }
}