import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../services/api_services.dart';
import 'order_detail_screen.dart';
import '../../colors/login_constants.dart';

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
    const Color restoColor = AppTheme.goldAccent;

    return FutureBuilder<Map<String, dynamic>>(
      future: _historyData,
      builder: (context, snapshot) {
        if (snapshot.connectionState == ConnectionState.waiting) {
          return const Center(child: CircularProgressIndicator(color: restoColor));
        }
        if (snapshot.hasError || snapshot.data?['success'] == false) {
          return _buildEmptyState();
        }

        List<dynamic> history = snapshot.data?['data'] ?? [];
        if (history.isEmpty) return _buildEmptyState();

        return RefreshIndicator(
          onRefresh: () async {
            setState(() { _historyData = _fetchHistory(); });
          },
          child: ListView.builder(
            padding: const EdgeInsets.fromLTRB(20, 20, 20, 100),
            itemCount: history.length,
            itemBuilder: (context, index) {
              final order = history[index];
              final List<dynamic> details = order['details'] ?? [];

              bool isPaid = order['status_pembayaran_id'].toString() == '2';
              Color statusColor = isPaid ? Colors.green.shade700 : Colors.orange.shade700;

              return Container(
                margin: const EdgeInsets.only(bottom: 20),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(25),
                  boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 15, offset: const Offset(0, 8))],
                  border: Border.all(color: Colors.grey.shade100),
                ),
                child: InkWell(
                  onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => OrderDetailScreen(order: order))),
                  borderRadius: BorderRadius.circular(25),
                  child: Padding(
                    padding: const EdgeInsets.all(20),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Text("ID #RS-${order['id']}", style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.grey, fontSize: 12)),
                            _statusBadge(isPaid ? "DIBAYAR" : "PENDING", statusColor),
                          ],
                        ),
                        const Padding(padding: EdgeInsets.symmetric(vertical: 12), child: Divider(height: 1)),
                        
                        // Ringkasan Menu
                        ...details.take(2).map((item) => Padding(
                          padding: const EdgeInsets.only(bottom: 4),
                          child: Text("${item['jumlah']}x ${item['menu']['nama_menu']}", style: const TextStyle(fontSize: 14, color: Colors.black87, fontWeight: FontWeight.w600)),
                        )),
                        if (details.length > 2)
                          Text("+ ${details.length - 2} menu lainnya...", style: const TextStyle(fontSize: 12, color: restoColor, fontStyle: FontStyle.italic)),

                        const Padding(padding: EdgeInsets.symmetric(vertical: 12), child: Divider(height: 1)),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Text("Rp ${double.parse(order['total_harga'].toString()).toStringAsFixed(0)}", style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w900, color: AppTheme.primaryBlue)),
                            const Icon(Icons.chevron_right_rounded, color: restoColor),
                          ],
                        ),
                      ],
                    ),
                  ),
                ),
              );
            },
          ),
        );
      },
    );
  }

  Widget _statusBadge(String label, Color color) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(color: color.withOpacity(0.1), borderRadius: BorderRadius.circular(8)),
      child: Text(label, style: TextStyle(color: color, fontWeight: FontWeight.bold, fontSize: 9)),
    );
  }

  Widget _buildEmptyState() {
    return Center(child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [Icon(Icons.no_meals_rounded, size: 70, color: Colors.grey[300]), const SizedBox(height: 10), const Text("Belum ada riwayat pesanan makan.", style: TextStyle(color: Colors.grey))]));
  }
}