import 'package:flutter/material.dart';
import '../../services/api_services.dart';
import 'room_type_screen.dart'; 
import 'room_detail_screen.dart';
import '../event/event_header.dart'; 
import '../../colors/login_constants.dart';

class RoomListScreen extends StatefulWidget {
  const RoomListScreen({super.key});

  @override
  State<RoomListScreen> createState() => _RoomListScreenState();
}

class _RoomListScreenState extends State<RoomListScreen> {
  late Future<Map<String, dynamic>> _roomData;

  // Link Gambar Premium yang sama dengan Detail Room
  final String _premiumFallbackImage = "https://plus.unsplash.com/premium_photo-1675745329954-9639d3b74bbf?q=80&w=2000";

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
    final Color navy = AppTheme.primaryBlue;
    final Color gold = AppTheme.goldAccent;
    final double topPadding = MediaQuery.of(context).padding.top;

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      extendBodyBehindAppBar: true, 
      appBar: AppBar(
        title: const Text("Katalog Kamar", 
          style: TextStyle(fontWeight: FontWeight.w900, color: Colors.white, fontSize: 18, letterSpacing: 1)),
        centerTitle: true,
        backgroundColor: Colors.transparent,
        elevation: 0,
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      body: FutureBuilder<Map<String, dynamic>>(
        future: _roomData,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return Center(child: CircularProgressIndicator(color: navy));
          }

          if (snapshot.hasError || snapshot.data?['success'] == false) {
            return _buildEmptyState();
          }

          List<dynamic> listJson = snapshot.data?['data'] ?? [];
          List<RoomType> rooms = listJson.map((e) => RoomType.fromJson(e)).toList();

          return SingleChildScrollView(
            child: Column(
              children: [
                // ── 1. PREMIUM HEADER (Radius 60) ─────────────────────────────
                Container(
                  width: double.infinity,
                  padding: EdgeInsets.only(top: topPadding + 60, bottom: 50),
                  decoration: BoxDecoration(
                    gradient: AppTheme.headerGradient, 
                    borderRadius: const BorderRadius.only(
                      bottomLeft: Radius.circular(60),
                      bottomRight: Radius.circular(60),
                    ),
                    boxShadow: [
                      BoxShadow(color: Colors.black.withOpacity(0.2), blurRadius: 15, offset: const Offset(0, 8))
                    ],
                  ),
                  child: Column(
                    children: [
                      Container(
                        padding: const EdgeInsets.all(15),
                        decoration: BoxDecoration(
                          color: Colors.white.withOpacity(0.1), 
                          shape: BoxShape.circle,
                          border: Border.all(color: Colors.white.withOpacity(0.2), width: 1.5)
                        ),
                        child: const Icon(Icons.hotel_class_rounded, color: Colors.white, size: 40),
                      ),
                      const SizedBox(height: 15),
                      Text("PURNAMA BALIGE HOTEL", 
                        style: TextStyle(color: gold, fontSize: 10, letterSpacing: 4, fontWeight: FontWeight.w900)),
                      const SizedBox(height: 5),
                      const Text("Kenyamanan Eksklusif di Tepi Danau Toba", 
                        style: TextStyle(color: Colors.white70, fontSize: 13, fontWeight: FontWeight.w500)),
                    ],
                  ),
                ),

                const EventHeader(),

                // ── 2. DAFTAR KATALOG KAMAR ───────────────────────────────────
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
                  child: ListView.builder(
                    shrinkWrap: true,
                    physics: const NeverScrollableScrollPhysics(),
                    padding: const EdgeInsets.only(bottom: 120),
                    itemCount: rooms.length,
                    itemBuilder: (context, index) {
                      final room = rooms[index];
                      
                      return Container(
                        margin: const EdgeInsets.only(bottom: 25),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(30),
                          boxShadow: [
                            BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 20, offset: const Offset(0, 10))
                          ],
                          border: Border.all(color: Colors.grey.shade100),
                        ),
                        child: InkWell(
                          onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => RoomDetailScreen(room: room))),
                          borderRadius: BorderRadius.circular(30),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              // Area Gambar + Badge Promo (Link dari Detail Room)
                              Stack(
                                children: [
                                  ClipRRect(
                                    borderRadius: const BorderRadius.vertical(top: Radius.circular(30)),
                                    child: Image.network(
                                      // Cek foto dari DB, jika kosong pakai link premium tadi
                                      (room.fotoTipe != null && room.fotoTipe!.isNotEmpty) 
                                          ? room.fotoTipe! 
                                          : _premiumFallbackImage, 
                                      height: 220, 
                                      width: double.infinity, 
                                      fit: BoxFit.cover,
                                      loadingBuilder: (context, child, loadingProgress) {
                                        if (loadingProgress == null) return child;
                                        return Container(height: 220, color: Colors.grey[100], child: const Center(child: CircularProgressIndicator()));
                                      },
                                      errorBuilder: (c,e,s) => Image.network(_premiumFallbackImage, height: 220, width: double.infinity, fit: BoxFit.cover),
                                    ),
                                  ),
                                  if (room.promoAktif != null)
                                    Positioned(
                                      top: 15, left: 15,
                                      child: Container(
                                        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                                        decoration: BoxDecoration(
                                          color: gold,
                                          borderRadius: BorderRadius.circular(12),
                                          boxShadow: const [BoxShadow(color: Colors.black26, blurRadius: 8)],
                                        ),
                                        child: Text(room.promoAktif!, 
                                          style: TextStyle(color: navy, fontWeight: FontWeight.w900, fontSize: 11)),
                                      ),
                                    ),
                                ],
                              ),
                              
                              // Detail Informasi
                              Padding(
                                padding: const EdgeInsets.all(22),
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Row(
                                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                      children: [
                                        Expanded(
                                          child: Text(room.namaTipe, 
                                            style: TextStyle(fontSize: 20, fontWeight: FontWeight.w900, color: navy, letterSpacing: 0.3)),
                                        ),
                                        Row(
                                          children: [
                                            Icon(Icons.star_rounded, color: gold, size: 20),
                                            const SizedBox(width: 4),
                                            const Text("4.9", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
                                          ],
                                        ),
                                      ],
                                    ),
                                    const SizedBox(height: 12),
                                    Row(
                                      children: [
                                        _buildSpecIcon(Icons.people_alt_rounded, "${room.kapasitas} Orang", navy),
                                        const SizedBox(width: 20),
                                        _buildSpecIcon(Icons.aspect_ratio_rounded, "32 m²", navy),
                                        const SizedBox(width: 20),
                                        _buildSpecIcon(Icons.wifi_rounded, "Free WiFi", navy),
                                      ],
                                    ),
                                    const Padding(padding: EdgeInsets.symmetric(vertical: 20), child: Divider(height: 1)),
                                    Row(
                                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                      children: [
                                        Column(
                                          crossAxisAlignment: CrossAxisAlignment.start,
                                          children: [
                                            if (room.promoAktif != null)
                                              Text("Rp ${room.hargaAsli.toStringAsFixed(0)}", 
                                                style: const TextStyle(color: Colors.grey, fontSize: 13, decoration: TextDecoration.lineThrough, fontWeight: FontWeight.w600)),
                                            Row(
                                              crossAxisAlignment: CrossAxisAlignment.end,
                                              children: [
                                                Text("Rp ${room.hargaAkhir.toStringAsFixed(0)}", 
                                                  style: TextStyle(color: navy, fontWeight: FontWeight.w900, fontSize: 24)),
                                                const Padding(
                                                  padding: EdgeInsets.only(bottom: 4, left: 4),
                                                  child: Text("/ malam", style: TextStyle(color: Colors.grey, fontSize: 11, fontWeight: FontWeight.bold)),
                                                ),
                                              ],
                                            ),
                                          ],
                                        ),
                                        // Tombol Aksi Bulat Modern
                                        Container(
                                          padding: const EdgeInsets.all(12),
                                          decoration: BoxDecoration(
                                            color: navy, 
                                            borderRadius: BorderRadius.circular(16),
                                            boxShadow: [BoxShadow(color: navy.withOpacity(0.3), blurRadius: 10, offset: const Offset(0, 4))]
                                          ),
                                          child: const Icon(Icons.arrow_forward_ios_rounded, color: Colors.white, size: 18),
                                        ),
                                      ],
                                    ),
                                  ],
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
            ),
          );
        },
      ),
    );
  }

  Widget _buildSpecIcon(IconData icon, String label, Color color) {
    return Row(
      children: [
        Icon(icon, size: 16, color: color.withOpacity(0.4)),
        const SizedBox(width: 6),
        Text(label, style: const TextStyle(color: Colors.black54, fontSize: 12, fontWeight: FontWeight.w700)),
      ],
    );
  }

  Widget _buildEmptyState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Container(
            padding: const EdgeInsets.all(35),
            decoration: BoxDecoration(color: Colors.grey[100], shape: BoxShape.circle),
            child: Icon(Icons.bed_rounded, size: 70, color: Colors.grey[300]),
          ),
          const SizedBox(height: 25),
          const Text("Kamar Tidak Tersedia", 
            style: TextStyle(color: AppTheme.primaryBlue, fontWeight: FontWeight.w900, fontSize: 18)),
          const SizedBox(height: 8),
          const Text("Mohon cek kembali beberapa saat lagi.", 
            style: TextStyle(color: Colors.grey, fontSize: 13)),
        ],
      ),
    );
  }
}