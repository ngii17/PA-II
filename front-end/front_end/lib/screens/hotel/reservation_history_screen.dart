import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../services/api_services.dart';
import '../../providers/event_provider.dart';
import 'reservation_detail_screen.dart';
import '../hotel/room_list_screen.dart'; // Import RoomListScreen

class ReservationHistoryScreen extends StatefulWidget {
  const ReservationHistoryScreen({super.key});

  @override
  State<ReservationHistoryScreen> createState() => _ReservationHistoryScreenState();
}

class _ReservationHistoryScreenState extends State<ReservationHistoryScreen> {
  late Future<Map<String, dynamic>> _historyData;

  Future<void> _refreshHistory() async {
    setState(() {
      _historyData = _fetchHistory();
    });
  }

  Future<Map<String, dynamic>> _fetchHistory() async {
    final prefs = await SharedPreferences.getInstance();
    int? userId = prefs.getInt('user_id');
    return ApiServices.getReservationHistory(userId.toString());
  }

  @override
  void initState() {
    super.initState();
    _refreshHistory();
  }

  String _getStatusLabel(int statusId) {
    switch (statusId) {
      case 1: return "PENDING";
      case 2: return "TERBAYAR";
      case 3: return "SUDAH CHECK-IN";
      case 4: return "SELESAI";
      case 5: return "DIBATALKAN";
      default: return "UNKNOWN";
    }
  }

  Color _getStatusColor(int statusId) {
    switch (statusId) {
      case 1: return Colors.orange;
      case 2: return Colors.blue;
      case 3: return Colors.green;
      case 4: return Colors.grey;
      case 5: return Colors.red;
      default: return Colors.black;
    }
  }

  @override
  Widget build(BuildContext context) {
    final eventProvider = context.watch<EventProvider>();
    final primaryColor = eventProvider.primaryColor;

    return RefreshIndicator(
      onRefresh: _refreshHistory,
      color: primaryColor,
      child: FutureBuilder<Map<String, dynamic>>(
        future: _historyData,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return Center(child: CircularProgressIndicator(color: primaryColor));
          }
          if (snapshot.hasError || snapshot.data?['success'] == false) {
            return _buildEmptyState(primaryColor);
          }

          final List<dynamic> history = snapshot.data?['data'] ?? [];
          if (history.isEmpty) {
            return _buildEmptyState(primaryColor);
          }

          return ListView.builder(
            padding: const EdgeInsets.all(16),
            itemCount: history.length,
            itemBuilder: (context, index) {
              final item = history[index];
              final statusId = int.tryParse(item['status_reservasi_id'].toString()) ?? 1;
              final statusLabel = _getStatusLabel(statusId);
              final statusColor = _getStatusColor(statusId);
              final total = double.parse(item['total_harga'].toString());

              return Card(
                margin: const EdgeInsets.only(bottom: 16),
                elevation: 2,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                child: InkWell(
                  onTap: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (context) => ReservationDetailScreen(reservation: item),
                      ),
                    ).then((_) => _refreshHistory());
                  },
                  borderRadius: BorderRadius.circular(16),
                  child: Padding(
                    padding: const EdgeInsets.all(16),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Expanded(
                              child: Text(
                                item['nama_tipe'] ?? "Kamar",
                                style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                                overflow: TextOverflow.ellipsis,
                              ),
                            ),
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                              decoration: BoxDecoration(
                                color: statusColor.withOpacity(0.1),
                                borderRadius: BorderRadius.circular(20),
                                border: Border.all(color: statusColor),
                              ),
                              child: Text(
                                statusLabel,
                                style: TextStyle(color: statusColor, fontWeight: FontWeight.bold, fontSize: 10),
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 12),
                        Row(
                          children: [
                            const Icon(Icons.calendar_today, size: 14, color: Colors.grey),
                            const SizedBox(width: 6),
                            Text("Check-in: ${item['tgl_checkin']}", style: const TextStyle(fontSize: 13)),
                          ],
                        ),
                        const SizedBox(height: 10),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Text(
                              "Total: Rp ${total.toStringAsFixed(0)}",
                              style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: primaryColor),
                            ),
                            const Icon(Icons.chevron_right, color: Colors.grey),
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

  Widget _buildEmptyState(Color primaryColor) {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(Icons.hotel_outlined, size: 70, color: Colors.grey.shade300),
          const SizedBox(height: 12),
          const Text(
            "Belum ada reservasi hotel",
            style: TextStyle(color: Colors.grey, fontSize: 14, fontWeight: FontWeight.w600),
          ),
          const SizedBox(height: 6),
          const Text(
            "Silakan lakukan reservasi kamar terlebih dahulu",
            style: TextStyle(color: Colors.grey, fontSize: 12),
          ),
          const SizedBox(height: 20),
          OutlinedButton.icon(
            onPressed: () {
              Navigator.push(
                context,
                MaterialPageRoute(builder: (context) => const RoomListScreen()),
              );
            },
            icon: const Icon(Icons.add_circle_outline, size: 18),
            label: const Text("PESAN KAMAR SEKARANG"),
            style: OutlinedButton.styleFrom(
              foregroundColor: primaryColor,
              side: BorderSide(color: primaryColor, width: 1.5),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
              padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
            ),
          ),
        ],
      ),
    );
  }
}