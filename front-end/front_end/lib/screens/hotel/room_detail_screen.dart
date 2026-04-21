import 'package:flutter/material.dart';
import 'room_type_screen.dart'; // Import model RoomType kamu
import 'booking_screen.dart'; // Import halaman booking untuk navigasi
import 'package:intl/intl.dart';
class RoomDetailScreen extends StatelessWidget {
  final RoomType room; // Menangkap data dari halaman katalog

  const RoomDetailScreen({super.key, required this.room});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(room.namaTipe),
        backgroundColor: Colors.blueAccent,
        foregroundColor: Colors.white,
      ),
      body: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // 1. Gambar Utama (Gunakan NetworkImage sementara)
            Container(
              height: 250,
              width: double.infinity,
              decoration: const BoxDecoration(
                image: DecorationImage(
                  image: NetworkImage("https://plus.unsplash.com/premium_photo-1675745329954-9639d3b74bbf?q=80&w=2000&auto=format&fit=crop"),
                  fit: BoxFit.cover,
                ),
              ),
            ),

            Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // 2. Badge Promo (Jika ada promo aktif)
                  if (room.promoAktif != null)
                    Container(
                      margin: const EdgeInsets.only(bottom: 10),
                      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                      decoration: BoxDecoration(
                        color: Colors.redAccent,
                        borderRadius: BorderRadius.circular(5),
                      ),
                      child: Text(
                        room.promoAktif!,
                        style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
                      ),
                    ),

                  // 3. Nama Kamar
                  Text(
                    room.namaTipe,
                    style: const TextStyle(fontSize: 26, fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 10),

                  // 4. Harga dengan Logika Harga Coret (Sinkron dengan Katalog)
                  Row(
                    children: [
                      Text(
                        "Rp ${room.hargaAkhir.toStringAsFixed(0)}",
                        style: const TextStyle(
                          fontSize: 22, 
                          color: Colors.green, 
                          fontWeight: FontWeight.bold
                        ),
                      ),
                      const SizedBox(width: 12),
                      if (room.promoAktif != null)
                        Text(
                          "Rp ${room.hargaAsli.toStringAsFixed(0)}",
                          style: const TextStyle(
                            fontSize: 16, 
                            color: Colors.grey, 
                            decoration: TextDecoration.lineThrough
                          ),
                        ),
                      const Text(" / malam", style: TextStyle(color: Colors.grey)),
                    ],
                  ),

                  const Divider(height: 40),

                  // 5. Kapasitas & Fasilitas
                  const Text("Kapasitas:", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                  Text("${room.kapasitas} Tamu Dewasa", style: const TextStyle(fontSize: 15)),
                  const SizedBox(height: 20),

                  const Text("Fasilitas Kamar:", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                  Text(room.fasilitas, style: const TextStyle(fontSize: 15, height: 1.5)),
                  const SizedBox(height: 20),

                  // 6. Deskripsi Panjang
                  const Text("Deskripsi:", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                  const SizedBox(height: 5),
                  Text(
                    room.deskripsi,
                    textAlign: TextAlign.justify,
                    style: const TextStyle(fontSize: 15, color: Colors.black87, height: 1.5),
                  ),
                  const SizedBox(height: 100), // Ruang kosong agar tidak tertutup tombol bawah
                ],
              ),
            ),
          ],
        ),
      ),
      
      // 7. Tombol Pesan Permanen di Bawah
      bottomNavigationBar: Container(
        padding: const EdgeInsets.all(20),
        decoration: BoxDecoration(
          color: Colors.white,
          boxShadow: [
            BoxShadow(color: Colors.black12, blurRadius: 10, spreadRadius: 1)
          ],
        ),
        child: ElevatedButton(
          onPressed: () {
              Navigator.push(
              context,
              MaterialPageRoute(
              builder: (context) => BookingScreen(room: room),
            ),
          );// Nanti di Langkah 8: Ke Halaman Form Reservasi
          },
          style: ElevatedButton.styleFrom(
            backgroundColor: Colors.blueAccent,
            padding: const EdgeInsets.symmetric(vertical: 15),
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
          ),
          child: const Text(
            "PESAN SEKARANG", 
            style: TextStyle(fontSize: 18, color: Colors.white, fontWeight: FontWeight.bold)
          ),
        ),
      ),
    );
  }
}