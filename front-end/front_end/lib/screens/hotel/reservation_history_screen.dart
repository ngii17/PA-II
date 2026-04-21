import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../services/api_services.dart';
import 'package:intl/intl.dart';
import 'reservation_detail_screen.dart'; // Import sudah benar karena satu folder

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
    return Scaffold(
      appBar: AppBar(
        title: const Text("Riwayat Reservasi"),
        backgroundColor: Colors.blueAccent,
        foregroundColor: Colors.white,
      ),
      body: FutureBuilder<Map<String, dynamic>>(
        future: _historyData,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(child: CircularProgressIndicator());
          }

          if (snapshot.hasError || snapshot.data?['success'] == false) {
            return const Center(
              child: Text("Gagal mengambil riwayat atau data kosong."),
            );
          }

          List<dynamic> history = snapshot.data?['data'] ?? [];

          if (history.isEmpty) {
            return const Center(
              child: Text("Anda belum melakukan pemesanan apapun."),
            );
          }

          return ListView.builder(
            padding: const EdgeInsets.all(15),
            itemCount: history.length,
            itemBuilder: (context, index) {
              final item = history[index];

              Color statusColor;
              String statusLabel;

              switch (item['status_reservasi_id']) {
                case 1:
                  statusColor = Colors.orange;
                  statusLabel = "MENUNGGU PEMBAYARAN";
                  break;
                case 2:
                  statusColor = Colors.green;
                  statusLabel = "SUDAH DIBAYAR";
                  break;
                default:
                  statusColor = Colors.red;
                  statusLabel = "BATAL / GAGAL";
              }

              // --- TAMBAHAN: InkWell agar kartu bisa diklik ---
              return InkWell(
                onTap: () {
                  // PINDAH KE HALAMAN DETAIL
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (context) => ReservationDetailScreen(reservation: item),
                    ),
                  );
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
                            const Icon(Icons.calendar_month, size: 16, color: Colors.grey),
                            const SizedBox(width: 8),
                            Text("Check-in: ${item['tgl_checkin']}"),
                          ],
                        ),
                        const SizedBox(height: 5),
                        
                        Row(
                          children: [
                            const Icon(Icons.nightlight_round, size: 16, color: Colors.grey),
                            const SizedBox(width: 8),
                            Text("Durasi: ${item['total_malam']} Malam"),
                          ],
                        ),
                        const SizedBox(height: 15),
                        
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            const Text("Total Pembayaran:"),
                            Text(
                              "Rp ${double.parse(item['total_harga'].toString()).toStringAsFixed(0)}",
                              style: const TextStyle(
                                fontSize: 16, 
                                fontWeight: FontWeight.bold, 
                                color: Colors.blueAccent
                              ),
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
}