import 'package:flutter/material.dart';
import '../../services/api_services.dart';
import 'room_type_screen.dart'; 
import 'room_detail_screen.dart';
import '../event/event_header.dart'; 

class RoomListScreen extends StatefulWidget {
  const RoomListScreen({super.key});

  @override
  State<RoomListScreen> createState() => _RoomListScreenState();
}

class _RoomListScreenState extends State<RoomListScreen> {
  late Future<Map<String, dynamic>> _roomData;

  @override
  void initState() {
    super.initState();
    _loadData(); // Panggil fungsi muat data
  }

  // Fungsi untuk memuat ulang data
  void _loadData() {
    setState(() {
      _roomData = ApiServices.getRoomTypes();
    });
  }

  @override
  Widget build(BuildContext context) {
    final primaryColor = Theme.of(context).primaryColor;

    return Scaffold(
      appBar: AppBar(
        title: const Text("Katalog Kamar Purnama"),
        backgroundColor: primaryColor, 
        foregroundColor: Colors.white,
      ),
      // --- TAMBAHAN: RefreshIndicator agar data selalu segar ---
      body: RefreshIndicator(
        onRefresh: () async {
          _loadData();
        },
        child: FutureBuilder<Map<String, dynamic>>(
          future: _roomData,
          builder: (context, snapshot) {
            if (snapshot.connectionState == ConnectionState.waiting) {
              return Center(child: CircularProgressIndicator(color: primaryColor));
            }

            if (snapshot.hasError || snapshot.data?['success'] == false) {
              return const Center(child: Text("Gagal memuat data"));
            }

            List<dynamic> listJson = snapshot.data?['data'];
            List<RoomType> rooms = listJson.map((e) => RoomType.fromJson(e)).toList();

            return Column(
              children: [
                const EventHeader(),
                Expanded(
                  child: ListView.builder(
                    padding: const EdgeInsets.all(15),
                    itemCount: rooms.length,
                    itemBuilder: (context, index) {
                      final room = rooms[index];

                      // LOGIKA KRUSIAL: Promo hanya muncul jika teksnya ada DAN harga_akhir < harga_asli
                      bool showPromo = room.promoAktif != null && (room.hargaAkhir < room.hargaAsli);

                      return Card(
                        elevation: 3,
                        margin: const EdgeInsets.only(bottom: 15),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                        child: Padding(
                          padding: const EdgeInsets.all(15),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              // Label Promo
                              if (showPromo)
                                Container(
                                  margin: const EdgeInsets.only(bottom: 8),
                                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                  decoration: BoxDecoration(
                                    color: Colors.redAccent,
                                    borderRadius: BorderRadius.circular(5),
                                  ),
                                  child: Text(
                                    room.promoAktif!,
                                    style: const TextStyle(color: Colors.white, fontSize: 12, fontWeight: FontWeight.bold),
                                  ),
                                ),

                              Text(
                                room.namaTipe,
                                style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                              ),
                              const SizedBox(height: 5),

                              // Harga
                              Row(
                                children: [
                                  Text(
                                    "Rp ${room.hargaAkhir.toStringAsFixed(0)}",
                                    style: TextStyle(
                                      color: primaryColor,
                                      fontSize: 18,
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                  const SizedBox(width: 10),
                                  // HANYA CORET JIKA MEMANG ADA PROMO AKTIF
                                  if (showPromo)
                                    Text(
                                      "Rp ${room.hargaAsli.toStringAsFixed(0)}",
                                      style: const TextStyle(
                                        color: Colors.grey, 
                                        decoration: TextDecoration.lineThrough,
                                        fontSize: 14
                                      ),
                                    ),
                                ],
                              ),

                              const SizedBox(height: 10),
                              Text("Kapasitas: ${room.kapasitas} Orang", style: const TextStyle(color: Colors.grey)),
                              const SizedBox(height: 15),

                              Align(
                                alignment: Alignment.centerRight,
                                child: ElevatedButton(
                                  style: ElevatedButton.styleFrom(backgroundColor: primaryColor),
                                  onPressed: () {
                                    Navigator.push(
                                      context,
                                      MaterialPageRoute(builder: (context) => RoomDetailScreen(room: room)),
                                    );
                                  },
                                  child: const Text("LIHAT DETAIL", style: TextStyle(color: Colors.white)),
                                ),
                              ),
                            ],
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