import 'package:flutter/material.dart';
import '../home/home_screen.dart'; 
import '../event/event_header.dart'; // <--- IMPORT HEADER EVENT

class PaymentStatusScreen extends StatelessWidget {
  const PaymentStatusScreen({super.key});

  @override
  Widget build(BuildContext context) {
    // KUNCI: Mengambil warna tema aktif
    final primaryColor = Theme.of(context).primaryColor;

    return Scaffold(
      backgroundColor: Colors.white,
      body: Column(
        children: [
          // --- 1. HIASAN BANNER EVENT (Paling Atas) ---
          const EventHeader(),

          Expanded(
            child: Container(
              padding: const EdgeInsets.all(25),
              width: double.infinity,
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  // Icon Berhasil (Tetap Hijau karena simbol universal sukses)
                  const Icon(Icons.check_circle_rounded, color: Colors.green, size: 100),
                  
                  const SizedBox(height: 25),
                  const Text(
                    "Pembayaran Berhasil!",
                    style: TextStyle(fontSize: 22, fontWeight: FontWeight.bold),
                  ),
                  
                  const SizedBox(height: 10),
                  const Text(
                    "Transaksi Anda telah kami terima. Silahkan cek menu Riwayat Pemesanan untuk melihat detail pesanan Anda.",
                    textAlign: TextAlign.center,
                    style: TextStyle(fontSize: 16, color: Colors.grey, height: 1.5),
                  ),
                  
                  const SizedBox(height: 40),

                  // --- 2. TOMBOL KEMBALI KE HOME (IKUTI TEMA) ---
                  SizedBox(
                    width: double.infinity,
                    height: 55,
                    child: ElevatedButton(
                      style: ElevatedButton.styleFrom(
                        backgroundColor: primaryColor, // Otomatis Merah/Biru/dll
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                        elevation: 2,
                      ),
                      onPressed: () {
                        // Bersihkan semua halaman dan kembali ke Home
                        Navigator.pushAndRemoveUntil(
                          context,
                          MaterialPageRoute(builder: (context) => const HomeScreen()),
                          (route) => false,
                        );
                      },
                      child: const Text(
                        "KEMBALI KE BERANDA", 
                        style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}