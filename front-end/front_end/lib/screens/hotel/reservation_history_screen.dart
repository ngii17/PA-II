import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../services/api_services.dart';
import 'reservation_detail_screen.dart';
import '../event/event_header.dart';

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
    _refreshHistory();
  }

  // Fungsi untuk memicu muat ulang data (untuk Pull-to-Refresh)
  Future<void> _refreshHistory() async {
    setState(() {
      _historyData = _fetchHistory();
    });
  }

  Future<Map<String, dynamic>> _fetchHistory() async {
    final SharedPreferences prefs = await SharedPreferences.getInstance();
    int? userId = prefs.getInt('user_id');
    return ApiServices.getReservationHistory(userId.toString());
  }

  @override
  Widget build(BuildContext context) {
    final primaryColor = Theme.of(context).primaryColor;

    return Scaffold(
      appBar: AppBar(
        title: const Text("Riwayat Reservasi"),
        backgroundColor: primaryColor,
        foregroundColor: Colors.white,
      ),
      body: RefreshIndicator(
        onRefresh: _refreshHistory,
        color: primaryColor,
        child: FutureBuilder<Map<String, dynamic>>(
          future: _historyData,
          builder: (context, snapshot) {
            if (snapshot.connectionState == ConnectionState.waiting) {
              return Center(child: CircularProgressIndicator(color: primaryColor));
            }

            if (snapshot.hasError || snapshot.data?['success'] == false) {
              return ListView(
                children: const [
                  SizedBox(height: 100),
                  Center(child: Text("Gagal mengambil riwayat.")),
                ],
              );
            }

            List<dynamic> history = snapshot.data?['data'] ?? [];

            return Column(
              children: [
                const EventHeader(),
                Expanded(
                  child: ListView.builder(
                    padding: const EdgeInsets.all(15),
                    itemCount: history.length,
                    itemBuilder: (context, index) {
                      final item = history[index];

                      // ==========================================
                      // --- FIX LOGIKA STATUS: SINKRON DENGAN DB ---
                      // ==========================================
                      int statusId = int.tryParse(item['status_reservasi_id'].toString()) ?? 1;
                      
                      String statusLabel;
                      Color statusColor;

                      switch (statusId) {
                        case 1:
                          statusLabel = "PENDING";
                          statusColor = Colors.orange;
                          break;
                        case 2:
                          statusLabel = "TERBAYAR";
                          statusColor = Colors.blue;
                          break;
                        case 3:
                          statusLabel = "SUDAH CHECK-IN";
                          statusColor = Colors.green;
                          break;
                        case 4:
                          statusLabel = "SELESAI";
                          statusColor = Colors.grey;
                          break;
                        case 5:
                          statusLabel = "DIBATALKAN";
                          statusColor = Colors.red;
                          break;
                        default:
                          statusLabel = "UNKNOWN";
                          statusColor = Colors.black;
                      }
                      // ==========================================

                      return InkWell(
                        onTap: () {
                          Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (context) => ReservationDetailScreen(reservation: item),
                            ),
                          ).then((_) => _refreshHistory());
                        },
                        child: Card(
                          elevation: 4,
                          margin: const EdgeInsets.only(bottom: 15),
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                          child: Padding(
                            padding: const EdgeInsets.all(15),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Row(
                                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                  children: [
                                    Text(
                                      item['nama_tipe'] ?? "Kamar",
                                      style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                                    ),
                                    Container(
                                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                      decoration: BoxDecoration(
                                        color: statusColor.withOpacity(0.15),
                                        borderRadius: BorderRadius.circular(5),
                                        border: Border.all(color: statusColor.withOpacity(0.5)),
                                      ),
                                      child: Text(
                                        statusLabel,
                                        style: TextStyle(color: statusColor, fontWeight: FontWeight.bold, fontSize: 10),
                                      ),
                                    ),
                                  ],
                                ),
                                const Divider(height: 25),
                                Row(
                                  children: [
                                    const Icon(Icons.calendar_today, size: 14, color: Colors.grey),
                                    const SizedBox(width: 5),
                                    Text("Check-in: ${item['tgl_checkin']}"),
                                  ],
                                ),
                                const SizedBox(height: 10),
                                Row(
                                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                  children: [
                                    Text(
                                      "Total: Rp ${double.parse(item['total_harga'].toString()).toStringAsFixed(0)}",
                                      style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: primaryColor),
                                    ),
                                    const Icon(Icons.arrow_forward_ios, size: 12, color: Colors.grey),
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
      ),
    );
  }
}