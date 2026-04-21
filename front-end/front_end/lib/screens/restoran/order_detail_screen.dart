import 'package:flutter/material.dart';

class OrderDetailScreen extends StatelessWidget {
  final Map<String, dynamic> order; // Menangkap data pesanan dari halaman riwayat

  const OrderDetailScreen({super.key, required this.order});

  @override
  Widget build(BuildContext context) {
    // Ambil daftar makanan dari data detail
    final List<dynamic> details = order['details'] ?? [];

    // Logika Status Pembayaran
    Color statusColor = order['status_pembayaran_id'] == 2 ? Colors.green : Colors.orange;
    String statusText = order['status_pembayaran_id'] == 2 ? "SUDAH DIBAYAR" : "MENUNGGU PEMBAYARAN";

    return Scaffold(
      appBar: AppBar(
        title: const Text("Detail Pesanan Resto"),
        backgroundColor: Colors.orangeAccent,
        foregroundColor: Colors.white,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // 1. HEADER STATUS
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: statusColor.withOpacity(0.1),
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: statusColor),
              ),
              child: Column(
                children: [
                  const Icon(Icons.restaurant, size: 40, color: Colors.orangeAccent),
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

            // 2. RINCIAN MENU YANG DIPESAN
            const Text(
              "Pesanan Anda:",
              style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 10),
            Card(
              elevation: 2,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
              child: ListView.separated(
                shrinkWrap: true, // Penting agar bisa masuk ke dalam Column
                physics: const NeverScrollableScrollPhysics(),
                itemCount: details.length,
                separatorBuilder: (context, index) => const Divider(indent: 15, endIndent: 15),
                itemBuilder: (context, index) {
                  final item = details[index];
                  return ListTile(
                    title: Text(item['menu']['nama_menu'] ?? "Menu"),
                    subtitle: Text("${item['jumlah']} porsi x Rp ${double.parse(item['harga_at_porsi'].toString()).toStringAsFixed(0)}"),
                    trailing: Text(
                      "Rp ${(item['jumlah'] * double.parse(item['harga_at_porsi'].toString())).toStringAsFixed(0)}",
                      style: const TextStyle(fontWeight: FontWeight.bold),
                    ),
                  );
                },
              ),
            ),

            const SizedBox(height: 25),

            // 3. INFORMASI PEMBAYARAN
            const Text(
              "Informasi Pembayaran:",
              style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 10),
            Container(
              padding: const EdgeInsets.all(15),
              decoration: BoxDecoration(
                color: Colors.grey[100],
                borderRadius: BorderRadius.circular(10),
              ),
              child: Column(
                children: [
                  _buildInfoRow("Metode Pembayaran", order['metode_pembayaran'] ?? "-"),
                  _buildInfoRow("Waktu Pesan", order['created_at'].toString().substring(0, 16)),
                  const Divider(),
                  _buildInfoRow(
                    "Total Bayar", 
                    "Rp ${double.parse(order['total_harga'].toString()).toStringAsFixed(0)}",
                    isBold: true
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildInfoRow(String label, String value, {bool isBold = false}) {
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
              color: isBold ? Colors.orangeAccent : Colors.black,
            )
          ),
        ],
      ),
    );
  }
}