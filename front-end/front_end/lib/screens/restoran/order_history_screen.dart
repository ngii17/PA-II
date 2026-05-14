import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../services/api_services.dart';
import 'order_detail_screen.dart';
import '../event/event_header.dart';

class OrderHistoryScreen extends StatefulWidget {
  const OrderHistoryScreen({super.key});

  @override
  State<OrderHistoryScreen> createState() => _OrderHistoryScreenState();
}

class _OrderHistoryScreenState extends State<OrderHistoryScreen> {
  late Future<Map<String, dynamic>> _historyData;

  @override
  void initState() {
    super.initState();
    _historyData = _fetchHistory();
  }

  Future<Map<String, dynamic>> _fetchHistory() async {
    final SharedPreferences prefs = await SharedPreferences.getInstance();
    int? userId = prefs.getInt('user_id');
    return ApiServices.getRestaurantOrderHistory(userId.toString());
  }

  @override
  Widget build(BuildContext context) {
    final primaryColor = Theme.of(context).primaryColor;

    return Scaffold(
      appBar: AppBar(
        title: const Text("Riwayat Makan"),
        backgroundColor: primaryColor,
        foregroundColor: Colors.white,
      ),
      body: FutureBuilder<Map<String, dynamic>>(
        future: _historyData,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return Center(child: CircularProgressIndicator(color: primaryColor));
          }

          if (snapshot.hasError || snapshot.data?['success'] == false) {
            return const Center(child: Text("Belum ada riwayat pesanan resto."));
          }

          List<dynamic> history = snapshot.data?['data'] ?? [];

          return Column(
            children: [
              const EventHeader(),
              if (history.isEmpty)
                const Expanded(
                  child: Center(child: Text("Anda belum pernah memesan makanan.")),
                )
              else
                Expanded(
                  child: ListView.builder(
                    padding: const EdgeInsets.all(15),
                    itemCount: history.length,
                    itemBuilder: (context, index) {
                      final order = history[index];
                      final List<dynamic> details = order['details'] ?? [];

                      // --- LOGIKA STATUS ---
                      Color statusColor = order['status_pembayaran_id'] == 2
                          ? Colors.green
                          : Colors.orange;
                      String statusText = order['status_pembayaran_id'] == 2
                          ? "LUNAS"
                          : "PENDING";

                      // --- LOGIKA LOKASI PENGANTARAN ---
                      String deliveryType = order['tipe_pengantaran'] ?? "Meja";
                      String locationNum = order['nomor_lokasi'] ?? "-";
                      IconData locationIcon = deliveryType == "Kamar" 
                          ? Icons.bed 
                          : Icons.table_restaurant;

                      return InkWell(
                        onTap: () {
                          Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (context) => OrderDetailScreen(order: order),
                            ),
                          );
                        },
                        child: Card(
                          elevation: 3,
                          margin: const EdgeInsets.only(bottom: 15),
                          shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(12)),
                          child: Padding(
                            padding: const EdgeInsets.all(15),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Row(
                                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                  children: [
                                    Text(
                                      "Nota #RS-${order['id']}",
                                      style: const TextStyle(
                                          fontWeight: FontWeight.bold,
                                          color: Colors.grey),
                                    ),
                                    Container(
                                      padding: const EdgeInsets.symmetric(
                                          horizontal: 8, vertical: 4),
                                      decoration: BoxDecoration(
                                        color: statusColor.withOpacity(0.1),
                                        borderRadius: BorderRadius.circular(5),
                                        border: Border.all(color: statusColor),
                                      ),
                                      child: Text(
                                        statusText,
                                        style: TextStyle(
                                            color: statusColor,
                                            fontWeight: FontWeight.bold,
                                            fontSize: 10),
                                      ),
                                    ),
                                  ],
                                ),
                                const SizedBox(height: 10),
                                
                                // --- INFO LOKASI PENGANTARAN (BARU) ---
                                Row(
                                  children: [
                                    Icon(locationIcon, size: 16, color: primaryColor),
                                    const SizedBox(width: 5),
                                    Text(
                                      "Diantar ke: $deliveryType $locationNum",
                                      style: TextStyle(
                                        fontSize: 13, 
                                        fontWeight: FontWeight.w500,
                                        color: primaryColor
                                      ),
                                    ),
                                  ],
                                ),
                                
                                const Divider(height: 25),

                                // Ringkasan item makanan
                                Column(
                                  children: details.map((item) {
                                    return Padding(
                                      padding: const EdgeInsets.symmetric(
                                          vertical: 2),
                                      child: Row(
                                        mainAxisAlignment:
                                            MainAxisAlignment.spaceBetween,
                                        children: [
                                          Text(
                                              "${item['jumlah']}x ${item['menu']['nama_menu']}"),
                                          Text(
                                              "Rp ${double.parse(item['harga_at_porsi'].toString()).toStringAsFixed(0)}"),
                                        ],
                                      ),
                                    );
                                  }).toList(),
                                ),

                                const Divider(height: 25),
                                Row(
                                  mainAxisAlignment:
                                      MainAxisAlignment.spaceBetween,
                                  children: [
                                    const Text("Total Bayar:",
                                        style: TextStyle(
                                            fontWeight: FontWeight.bold)),
                                    Text(
                                      "Rp ${double.parse(order['total_harga'].toString()).toStringAsFixed(0)}",
                                      style: TextStyle(
                                          fontSize: 18,
                                          fontWeight: FontWeight.bold,
                                          color: primaryColor),
                                    ),
                                  ],
                                ),
                                const SizedBox(height: 10),
                                Row(
                                  mainAxisAlignment:
                                      MainAxisAlignment.spaceBetween,
                                  children: [
                                    Text(
                                      "Dipesan pada: ${order['created_at'].toString().substring(0, 10)}",
                                      style: const TextStyle(
                                          fontSize: 10, color: Colors.grey),
                                    ),
                                    Text(
                                      "Lihat Detail >",
                                      style: TextStyle(
                                          fontSize: 10,
                                          color: primaryColor,
                                          fontWeight: FontWeight.bold),
                                    ),
                                  ],
                                ),
                              ],
                            ),
                          ),
                        ),
                      );
                    },
                  ),
                ),
            ],
          );
        },
      ),
    );
  }
}