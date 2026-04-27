import 'package:flutter/material.dart';
import '../../services/api_services.dart';
import 'room_type_screen.dart'; // Model RoomType kamu
import 'booking_screen.dart';
import '../event/event_header.dart'; // Import Header Event

class RoomDetailScreen extends StatefulWidget {
  final RoomType room;

  const RoomDetailScreen({super.key, required this.room});

  @override
  State<RoomDetailScreen> createState() => _RoomDetailScreenState();
}

class _RoomDetailScreenState extends State<RoomDetailScreen> {
  late Future<Map<String, dynamic>> _reviewData;

  @override
  void initState() {
    super.initState();
    _refreshReviews();
  }

  void _refreshReviews() {
    setState(() {
      _reviewData = ApiServices.getHotelReviews(widget.room.id);
    });
  }

  @override
  Widget build(BuildContext context) {
    // KUNCI: Mengambil warna tema aktif
    final primaryColor = Theme.of(context).primaryColor;

    return Scaffold(
      appBar: AppBar(
        title: Text(widget.room.namaTipe),
        backgroundColor: primaryColor, // Otomatis Biru/Merah/dll
        foregroundColor: Colors.white,
      ),
      body: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // --- TAMBAHAN: BANNER EVENT DI ATAS GAMBAR KAMAR ---
            const EventHeader(),

            // 1. Gambar Utama
            Image.network(
              "https://plus.unsplash.com/premium_photo-1675745329954-9639d3b74bbf?q=80&w=2000&auto=format&fit=crop",
              height: 250, 
              width: double.infinity, 
              fit: BoxFit.cover,
              errorBuilder: (context, error, stackTrace) => Container(
                height: 250,
                color: Colors.grey[300],
                child: const Icon(Icons.broken_image, size: 50),
              ),
            ),

            Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(widget.room.namaTipe, style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold)),
                  const SizedBox(height: 10),
                  Text(
                    "Rp ${widget.room.hargaAkhir.toStringAsFixed(0)} / malam", 
                    style: TextStyle(fontSize: 20, color: primaryColor, fontWeight: FontWeight.bold) // Harga sewarna tema
                  ),
                  
                  if (widget.room.promoAktif != null)
                    Container(
                      margin: const EdgeInsets.only(top: 10),
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                      decoration: BoxDecoration(color: Colors.redAccent, borderRadius: BorderRadius.circular(5)),
                      child: Text(widget.room.promoAktif!, style: const TextStyle(color: Colors.white, fontSize: 12, fontWeight: FontWeight.bold)),
                    ),

                  const Divider(height: 40),
                  
                  const Text("Fasilitas Kamar:", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                  Text(widget.room.fasilitas),
                  
                  const SizedBox(height: 20),
                  const Text("Deskripsi:", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                  Text(widget.room.deskripsi, textAlign: TextAlign.justify),
                  
                  const Divider(height: 40),

                  // --- BAGIAN ULASAN ---
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text("Ulasan Tamu", style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: primaryColor)),
                      IconButton(
                        onPressed: _refreshReviews,
                        icon: Icon(Icons.refresh, color: primaryColor, size: 20),
                      )
                    ],
                  ),
                  const SizedBox(height: 10),

                  FutureBuilder<Map<String, dynamic>>(
                    future: _reviewData,
                    builder: (context, snapshot) {
                      if (snapshot.connectionState == ConnectionState.waiting) {
                        return const Center(child: CircularProgressIndicator());
                      }

                      List<dynamic> reviews = snapshot.data?['data'] ?? [];

                      if (reviews.isEmpty) {
                        return const Text("Belum ada ulasan.", style: TextStyle(color: Colors.grey, fontStyle: FontStyle.italic));
                      }

                      return ListView.builder(
                        shrinkWrap: true,
                        physics: const NeverScrollableScrollPhysics(),
                        itemCount: reviews.length,
                        itemBuilder: (context, index) {
                          final rev = reviews[index];
                          return Container(
                            margin: const EdgeInsets.only(bottom: 15),
                            padding: const EdgeInsets.all(15),
                            decoration: BoxDecoration(
                              color: primaryColor.withOpacity(0.05), // Latar ulasan tipis sesuai tema
                              borderRadius: BorderRadius.circular(12),
                            ),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Row(
                                  children: [
                                    Row(
                                      children: List.generate(5, (starIndex) {
                                        return Icon(
                                          starIndex < (rev['rating'] ?? 0) ? Icons.star : Icons.star_border,
                                          color: Colors.amber, size: 16,
                                        );
                                      }),
                                    ),
                                    const SizedBox(width: 10),
                                    const Text("Verified Guest", style: TextStyle(fontSize: 11, color: Colors.grey, fontWeight: FontWeight.bold)),
                                  ],
                                ),
                                const SizedBox(height: 8),
                                Text("${rev['komentar']}", style: const TextStyle(fontSize: 14)),
                              ],
                            ),
                          );
                        },
                      );
                    },
                  ),
                  const SizedBox(height: 100), 
                ],
              ),
            ),
          ],
        ),
      ),
      
      // Tombol Pesan Ikuti Tema
      bottomNavigationBar: Container(
        padding: const EdgeInsets.all(20),
        decoration: BoxDecoration(
          color: Colors.white,
          boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 10)],
        ),
        child: ElevatedButton(
          style: ElevatedButton.styleFrom(
            backgroundColor: primaryColor, // Tombol Merah/Biru/dll
            padding: const EdgeInsets.symmetric(vertical: 15),
          ),
          onPressed: () {
            Navigator.push(context, MaterialPageRoute(builder: (context) => BookingScreen(room: widget.room)));
          },
          child: const Text("PESAN SEKARANG", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
        ),
      ),
    );
  }
}