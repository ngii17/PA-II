import 'package:flutter/material.dart';
import '../../services/api_services.dart';
import 'room_type_screen.dart'; // Model kamu
import 'room_detail_screen.dart'; // Wajib di-import agar tidak error

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
    _roomData = ApiServices.getRoomTypes();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Katalog Kamar Purnama"),
        backgroundColor: Colors.blueAccent,
        foregroundColor: Colors.white,
      ),
      body: FutureBuilder<Map<String, dynamic>>(
        future: _roomData,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(child: CircularProgressIndicator());
          }

          if (snapshot.hasError || snapshot.data?['success'] == false) {
            return Center(
              child: Text(snapshot.data?['message'] ?? "Gagal memuat data"),
            );
          }

          List<dynamic> listJson = snapshot.data?['data'];
          List<RoomType> rooms = listJson.map((e) => RoomType.fromJson(e)).toList();

          return ListView.builder(
            padding: const EdgeInsets.all(15),
            itemCount: rooms.length,
            itemBuilder: (context, index) {
              final room = rooms[index];
              return Card(
                elevation: 3,
                margin: const EdgeInsets.only(bottom: 15),
                child: Padding(
                  padding: const EdgeInsets.all(15),
                  // INI PERBAIKANNYA: Harus ada 'child:' sebelum Column
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // 1. Label Promo (Muncul jika ada promo dari Laravel)
                      if (room.promoAktif != null)
                        Container(
                          margin: const EdgeInsets.only(bottom: 8),
                          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                          decoration: BoxDecoration(
                            color: Colors.redAccent,
                            borderRadius: BorderRadius.circular(5),
                          ),
                          child: Text(
                            room.promoAktif!,
                            style: const TextStyle(
                                color: Colors.white,
                                fontSize: 12,
                                fontWeight: FontWeight.bold),
                          ),
                        ),

                      Text(
                        room.namaTipe,
                        style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                      ),
                      const SizedBox(height: 5),

                      // 2. Tampilan Harga Coret
                      Row(
                        children: [
                          Text(
                            "Rp ${room.hargaAkhir.toStringAsFixed(0)}",
                            style: const TextStyle(
                              color: Colors.green,
                              fontSize: 18,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                          const SizedBox(width: 10),
                          if (room.promoAktif != null)
                            Text(
                              "Rp ${room.hargaAsli.toStringAsFixed(0)}",
                              style: const TextStyle(
                                color: Colors.grey,
                                fontSize: 14,
                                decoration: TextDecoration.lineThrough,
                              ),
                            ),
                        ],
                      ),

                      const SizedBox(height: 10),
                      Text("Kapasitas: ${room.kapasitas} Orang"),
                      const SizedBox(height: 15),

                      Align(
                        alignment: Alignment.centerRight,
                        child: ElevatedButton(
                          onPressed: () {
                            // Pindah ke Detail
                            Navigator.push(
                              context,
                              MaterialPageRoute(
                                builder: (context) => RoomDetailScreen(room: room),
                              ),
                            );
                          },
                          child: const Text("LIHAT DETAIL"),
                        ),
                      ),
                    ],
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