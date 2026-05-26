import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../services/api_services.dart';
import 'reservation_detail_screen.dart';
import '../../colors/login_constants.dart';

class ReservationHistoryScreen extends StatefulWidget {
  const ReservationHistoryScreen({super.key});
  @override
  State<ReservationHistoryScreen> createState() => _ReservationHistoryScreenState();
}

class _ReservationHistoryScreenState extends State<ReservationHistoryScreen> {
  late Future<Map<String, dynamic>> _historyData;

  @override
  void initState() {
    super.initState();
    _historyData = _fetchHistory();
  }

  Future<Map<String, dynamic>> _fetchHistory() async {
    final SharedPreferences prefs = await SharedPreferences.getInstance();
    int? userId = prefs.getInt('user_id');
    return ApiServices.getReservationHistory(userId.toString());
  }

  @override
  Widget build(BuildContext context) {
    return FutureBuilder<Map<String, dynamic>>(
      future: _historyData,
      builder: (context, snapshot) {
        if (snapshot.connectionState == ConnectionState.waiting) {
          return const Center(child: CircularProgressIndicator(color: AppTheme.primaryBlue));
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
            padding: const EdgeInsets.fromLTRB(20, 20, 20, 100), // Padding bawah agar tidak tertutup navbar
            itemCount: history.length,
            itemBuilder: (context, index) {
              final item = history[index];
              
              // Logika Mapping Status
              Color statusColor;
              String statusLabel;
              switch (item['status_reservasi_id'].toString()) {
                case '1': statusColor = Colors.orange.shade700; statusLabel = "MENUNGGU BAYAR"; break;
                case '2': statusColor = Colors.green.shade700; statusLabel = "BERHASIL"; break;
                default: statusColor = Colors.red.shade700; statusLabel = "BATAL";
              }

              return Container(
                margin: const EdgeInsets.only(bottom: 20),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(25),
                  boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 15, offset: const Offset(0, 8))],
                  border: Border.all(color: Colors.grey.shade100),
                ),
                child: InkWell(
                  onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => ReservationDetailScreen(reservation: item))),
                  borderRadius: BorderRadius.circular(25),
                  child: Padding(
                    padding: const EdgeInsets.all(20),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Expanded(child: Text(item['nama_tipe'] ?? "Kamar Purnama", style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w900, color: AppTheme.primaryBlue))),
                            _statusBadge(statusLabel, statusColor),
                          ],
                        ),
                        const Padding(padding: EdgeInsets.symmetric(vertical: 15), child: Divider(height: 1)),
                        Row(
                          children: [
                            const Icon(Icons.calendar_month_rounded, size: 16, color: Colors.grey),
                            const SizedBox(width: 8),
                            Text("Check-in: ${item['tgl_checkin']}", style: const TextStyle(fontSize: 13, color: Colors.black54, fontWeight: FontWeight.w600)),
                          ],
                        ),
                        const SizedBox(height: 15),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Text("Rp ${double.parse(item['total_harga'].toString()).toStringAsFixed(0)}", style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w900, color: AppTheme.primaryBlue)),
                            const Icon(Icons.arrow_forward_ios_rounded, size: 14, color: AppTheme.goldAccent),
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
    return Center(child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [Icon(Icons.event_busy_rounded, size: 70, color: Colors.grey[300]), const SizedBox(height: 10), const Text("Belum ada reservasi hotel.", style: TextStyle(color: Colors.grey))]));
  }
}