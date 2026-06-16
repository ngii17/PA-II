import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../services/api_services.dart';
import '../../providers/event_provider.dart';
import 'order_detail_screen.dart';
import '../restoran/menu_list_screen.dart'; // Import MenuListScreen

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

  void _refreshHistory() {
    setState(() {
      _historyData = _fetchHistory();
    });
  }

  @override
  Widget build(BuildContext context) {
    final eventProvider = context.watch<EventProvider>();
    final primaryColor = eventProvider.primaryColor;

    return RefreshIndicator(
      onRefresh: () async => _refreshHistory(),
      color: primaryColor,
      child: FutureBuilder<Map<String, dynamic>>(
        future: _historyData,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(child: CircularProgressIndicator());
          }
          if (snapshot.hasError || snapshot.data?['success'] == false) {
            return _buildEmptyState(primaryColor);
          }

          final List<dynamic> history = snapshot.data?['data'] ?? [];
          if (history.isEmpty) return _buildEmptyState(primaryColor);

          return ListView.builder(
            physics: const AlwaysScrollableScrollPhysics(), // ← tambah ini
            padding: const EdgeInsets.fromLTRB(16, 16, 16, 100),
            itemCount: history.length,
            itemBuilder: (context, index) {
              final order = history[index];
              final List<dynamic> details = order['details'] ?? [];
              final bool isPaid = order['status_pembayaran_id'].toString() == '2';
              final Color statusColor = isPaid ? Colors.green.shade700 : Colors.orange.shade700;
              final String statusText = isPaid ? "LUNAS" : "PENDING";

              final String deliveryType = order['tipe_pengantaran'] ?? "Meja";
              final String locationNum = order['nomor_lokasi'] ?? "-";
              final IconData locationIcon = deliveryType == "Kamar" ? Icons.bed : Icons.table_restaurant;

              return Container(
                margin: const EdgeInsets.only(bottom: 20),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(24),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withOpacity(0.04),
                      blurRadius: 15,
                      offset: const Offset(0, 8),
                    ),
                  ],
                  border: Border.all(color: Colors.grey.shade100),
                ),
                child: InkWell(
                  onTap: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (_) => OrderDetailScreen(order: order),
                      ),
                    );
                  },
                  borderRadius: BorderRadius.circular(24),
                  child: Padding(
                    padding: const EdgeInsets.all(20),
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
                                color: Colors.grey,
                                fontSize: 12,
                              ),
                            ),
                            _statusBadge(statusText, statusColor),
                          ],
                        ),
                        const SizedBox(height: 8),
                        Row(
                          children: [
                            Icon(locationIcon, size: 14, color: primaryColor),
                            const SizedBox(width: 5),
                            Text(
                              "Diantar ke: $deliveryType $locationNum",
                              style: TextStyle(
                                fontSize: 12,
                                fontWeight: FontWeight.w500,
                                color: primaryColor,
                              ),
                            ),
                          ],
                        ),
                        const Padding(
                          padding: EdgeInsets.symmetric(vertical: 12),
                          child: Divider(height: 1),
                        ),
                        ...details.take(2).map((item) => Padding(
                          padding: const EdgeInsets.only(bottom: 4),
                          child: Text(
                            "${item['jumlah']}x ${item['menu']?['nama_menu'] ?? 'Menu tidak tersedia'}",
                            style: const TextStyle(
                              fontSize: 14,
                              color: Colors.black87,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                        )),
                        if (details.length > 2)
                          Padding(
                            padding: const EdgeInsets.only(top: 4),
                            child: Text(
                              "+ ${details.length - 2} menu lainnya...",
                              style: TextStyle(
                                fontSize: 12,
                                color: primaryColor,
                                fontStyle: FontStyle.italic,
                              ),
                            ),
                          ),
                        const Padding(
                          padding: EdgeInsets.symmetric(vertical: 12),
                          child: Divider(height: 1),
                        ),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Text(
                              "Rp ${double.parse(order['total_harga'].toString()).toStringAsFixed(0)}",
                              style: TextStyle(
                                fontSize: 18,
                                fontWeight: FontWeight.w900,
                                color: primaryColor,
                              ),
                            ),
                            Row(
                              children: [
                                Text(
                                  order['created_at'].toString().substring(0, 10),
                                  style: const TextStyle(
                                    fontSize: 10,
                                    color: Colors.grey,
                                  ),
                                ),
                                const SizedBox(width: 8),
                                Icon(
                                  Icons.chevron_right_rounded,
                                  color: primaryColor,
                                ),
                              ],
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                ),
              );
            },
          );
        },
      ),
    );
  }

  Widget _statusBadge(String label, Color color) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(
        color: color.withOpacity(0.1),
        borderRadius: BorderRadius.circular(8),
      ),
      child: Text(
        label,
        style: TextStyle(
          color: color,
          fontWeight: FontWeight.bold,
          fontSize: 10,
        ),
      ),
    );
  }

Widget _buildEmptyState(Color primaryColor) {
  return ListView(
    physics: const AlwaysScrollableScrollPhysics(),
    children: [
      SizedBox(
        height: 400,
        child: Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(Icons.no_meals_rounded, size: 70, color: Colors.grey.shade300),
              const SizedBox(height: 12),
              const Text(
                "Belum ada riwayat pesanan",
                style: TextStyle(color: Colors.grey, fontSize: 14, fontWeight: FontWeight.w600),
              ),
              const SizedBox(height: 6),
              const Text(
                "Silakan pesan makanan terlebih dahulu",
                style: TextStyle(color: Colors.grey, fontSize: 12),
              ),
              const SizedBox(height: 20),
              OutlinedButton.icon(
                onPressed: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(builder: (context) => const MenuListScreen()),
                  );
                },
                icon: const Icon(Icons.add_circle_outline, size: 18),
                label: const Text("PESAN MAKANAN SEKARANG"),
                style: OutlinedButton.styleFrom(
                  foregroundColor: primaryColor,
                  side: BorderSide(color: primaryColor, width: 1.5),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
                ),
              ),
            ],
          ),
        ),
      ),
    ],
  );
  }
}