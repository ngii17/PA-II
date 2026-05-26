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
      // --- TAMBAHAN: REFRESH INDICATOR AGAR BISA TARIK KE BAWAH ---
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
              return ListView( // Gunakan ListView agar tetap bisa di-refresh saat error
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
                if (history.isEmpty)
                  const Expanded(
                    child: Center(child: Text("Anda belum memiliki riwayat reservasi.")),
                  )
                else
                  Expanded(
                    child: ListView.builder(
                      padding: const EdgeInsets.all(15),
                      itemCount: history.length,
                      itemBuilder: (context, index) {
                        final item = history[index];
                        
                        // ==========================================
                        // --- LOGIKA PEMBAHARUAN STATUS (SINKRON DB) ---
                        // ==========================================
                        Color statusColor;
                        String statusLabel;
                        
                        switch (int.parse(item['status_reservasi_id'].toString())) {
                          case 1:
                            statusColor = Colors.orange;
                            statusLabel = "MENUNGGU PEMBAYARAN";
                            break;
                          case 2:
                            statusColor = Colors.blue;
                            statusLabel = "TERBAYAR";
                            break;
                          case 3:
                            statusColor = Colors.green;
                            statusLabel = "SUDAH CHECK-IN";
                            break;
                          case 4:
                            statusColor = Colors.grey;
                            statusLabel = "SELESAI (CHECK-OUT)";
                            break;
                          case 5:
                            statusColor = Colors.red;
                            statusLabel = "DIBATALKAN";
                            break;
                          default:
                            statusColor = Colors.black45;
                            statusLabel = "STATUS TIDAK DIKENAL";
                        }
                        // ==========================================

                        return InkWell(
                          onTap: () {
                            Navigator.push(
                              context,
                              MaterialPageRoute(
                                builder: (context) => ReservationDetailScreen(reservation: item),
                              ),
                            ).then((_) => _refreshHistory()); // Refresh saat balik dari detail
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