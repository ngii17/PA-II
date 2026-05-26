import 'package:flutter/material.dart';
import '../home/home_screen.dart'; 
import '../event/event_header.dart'; 
import '../../colors/login_constants.dart'; // Import tema warna brand Anda

class PaymentStatusScreen extends StatelessWidget {
  const PaymentStatusScreen({super.key});

  @override
  Widget build(BuildContext context) {
    // Mengambil warna primer jika ingin tetap dinamis sesuai sistem theme
    final primaryColor = Theme.of(context).primaryColor;

    return Scaffold(
      backgroundColor: Colors.white,
      body: Column(
        children: [
          // ── 1. HEADER SUKSES MEWAH (GRADIENT NAVY + RADIUS 60) ────────────────
          Container(
            width: double.infinity,
            padding: EdgeInsets.only(
              top: MediaQuery.of(context).padding.top + 50,
              bottom: 40,
            ),
            decoration: const BoxDecoration(
              gradient: AppTheme.headerGradient, // Warna Navy Mewah
              borderRadius: BorderRadius.only(
                bottomLeft: Radius.circular(60),
                bottomRight: Radius.circular(60),
              ),
              boxShadow: [
                BoxShadow(color: Colors.black26, blurRadius: 20, offset: Offset(0, 10))
              ],
            ),
            child: Column(
              children: [
                // Efek Lingkaran Bercahaya untuk Checkmark
                Stack(
                  alignment: Alignment.center,
                  children: [
                    Container(
                      width: 130,
                      height: 130,
                      decoration: BoxDecoration(
                        color: Colors.white.withOpacity(0.1),
                        shape: BoxShape.circle,
                      ),
                    ),
                    Container(
                      width: 100,
                      height: 100,
                      decoration: const BoxDecoration(
                        color: Colors.white,
                        shape: BoxShape.circle,
                        boxShadow: [
                          BoxShadow(color: Colors.greenAccent, blurRadius: 30, spreadRadius: 2)
                        ],
                      ),
                      child: const Icon(
                        Icons.check_circle_rounded, 
                        color: Color(0xFF2ECC71), // Hijau Sukses Solid
                        size: 85
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 25),
                const Text(
                  "PEMBAYARAN BERHASIL", 
                  style: TextStyle(
                    color: Colors.white, 
                    fontSize: 22, 
                    fontWeight: FontWeight.w900, 
                    letterSpacing: 1.5
                  )
                ),
                const SizedBox(height: 5),
                Text(
                  "Reservasi Anda telah dikonfirmasi", 
                  style: TextStyle(
                    color: Colors.white.withOpacity(0.7), 
                    fontSize: 14, 
                    letterSpacing: 1,
                    fontWeight: FontWeight.w500
                  )
                ),
              ],
            ),
          ),

          // Banner Event Dinamis (Port 8001)
          const EventHeader(),

          // ── 2. KONTEN PESAN INFORMASI ──────────────────────────────────────
          Expanded(
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 40),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  // Icon Struk Pembayaran Mungil
                  Container(
                    padding: const EdgeInsets.all(22),
                    decoration: BoxDecoration(
                      color: AppTheme.primaryBlue.withOpacity(0.05),
                      borderRadius: BorderRadius.circular(25),
                      border: Border.all(color: AppTheme.primaryBlue.withOpacity(0.1)),
                    ),
                    child: const Icon(
                      Icons.receipt_long_rounded, 
                      color: AppTheme.primaryBlue, 
                      size: 45
                    ),
                  ),
                  const SizedBox(height: 30),
                  const Text(
                    "Terima Kasih!",
                    style: TextStyle(
                      fontSize: 26, 
                      fontWeight: FontWeight.bold, 
                      color: AppTheme.primaryBlue,
                      letterSpacing: 0.5
                    ),
                  ),
                  const SizedBox(height: 15),
                  const Text(
                    "Konfirmasi pembayaran Anda telah kami terima secara otomatis. Silahkan cek menu 'Riwayat Reservasi' untuk melihat detail pesanan dan nomor kamar Anda.",
                    textAlign: TextAlign.center,
                    style: TextStyle(
                      fontSize: 14, 
                      color: Colors.black54, 
                      height: 1.7,
                      fontWeight: FontWeight.w500
                    ),
                  ),
                  const SizedBox(height: 60),

                  // ── 3. TOMBOL AKSI UTAMA (NAVY PREMIUM) ────────────────────────
                  SizedBox(
                    width: double.infinity,
                    height: 58,
                    child: ElevatedButton(
                      style: ElevatedButton.styleFrom(
                        backgroundColor: AppTheme.primaryBlue,
                        foregroundColor: Colors.white,
                        elevation: 8,
                        shadowColor: AppTheme.primaryBlue.withOpacity(0.5),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(18)
                        ),
                      ),
                      onPressed: () {
                        // Navigasi Aman: Bersihkan stack dan kembali ke Home
                        Navigator.pushAndRemoveUntil(
                          context,
                          MaterialPageRoute(builder: (context) => const HomeScreen()),
                          (route) => false,
                        );
                      },
                      child: const Text(
                        "KEMBALI KE BERANDA", 
                        style: TextStyle(
                          fontWeight: FontWeight.w900, 
                          fontSize: 16, 
                          letterSpacing: 1.2
                        )
                      ),
                    ),
                  ),
                  const SizedBox(height: 20),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}