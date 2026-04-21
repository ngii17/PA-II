import 'package:flutter/material.dart';
import '../home/home_screen.dart'; // Pastikan path import benar

class PaymentStatusScreen extends StatelessWidget {
  const PaymentStatusScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: Container(
        padding: const EdgeInsets.all(25),
        width: double.infinity,
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            // Icon Berhasil
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
            // Tombol Kembali ke Home
            SizedBox(
              width: double.infinity,
              height: 50,
              child: ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.blueAccent,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                ),
                onPressed: () {
                  // Bersihkan semua halaman dan kembali ke Home
                  Navigator.pushAndRemoveUntil(
                    context,
                    MaterialPageRoute(builder: (context) => const HomeScreen()),
                    (route) => false,
                  );
                },
                child: const Text("KEMBALI KE HOME", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
              ),
            ),
          ],
        ),
      ),
    );
  }
}