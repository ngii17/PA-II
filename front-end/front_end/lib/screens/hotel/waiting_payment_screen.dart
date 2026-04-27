import 'dart:async';
import 'package:flutter/material.dart';
import '../../services/api_services.dart';
import 'payment_status_screen.dart';
import '../event/event_header.dart'; // <--- IMPORT HEADER EVENT

class WaitingPaymentScreen extends StatefulWidget {
  final int reservasiId;
  const WaitingPaymentScreen({super.key, required this.reservasiId});

  @override
  State<WaitingPaymentScreen> createState() => _WaitingPaymentScreenState();
}

class _WaitingPaymentScreenState extends State<WaitingPaymentScreen> {
  Timer? _timer;
  bool _isChecking = false;

  @override
  void initState() {
    super.initState();
    // MULAI CEK STATUS SETIAP 3 DETIK (POLLING)
    _timer = Timer.periodic(const Duration(seconds: 3), (timer) {
      _checkPaymentStatus();
    });
  }

  void _checkPaymentStatus() async {
    if (_isChecking) return; 
    _isChecking = true;

    final response = await ApiServices.checkPaymentStatus(widget.reservasiId);

    if (response['success'] == true) {
      int statusId = response['status_id'];

      if (statusId == 2) {
        _timer?.cancel(); 
        if (mounted) {
          Navigator.pushAndRemoveUntil(
            context,
            MaterialPageRoute(builder: (context) => const PaymentStatusScreen()),
            (route) => false,
          );
        }
      }
    }
    _isChecking = false;
  }

  @override
  void dispose() {
    _timer?.cancel(); 
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    // KUNCI: Mengambil warna tema aktif
    final primaryColor = Theme.of(context).primaryColor;

    return Scaffold(
      appBar: AppBar(
        title: const Text("Menunggu Pembayaran"), 
        backgroundColor: primaryColor, // Ikuti Tema
        foregroundColor: Colors.white,
        automaticallyImplyLeading: false, // User tidak boleh back manual sembarangan
      ),
      body: Column(
        children: [
          // --- 1. HIASAN BANNER EVENT ---
          const EventHeader(),

          Expanded(
            child: Center(
              child: Padding(
                padding: const EdgeInsets.all(25.0),
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    // Loading indicator mengikuti warna tema
                    CircularProgressIndicator(color: primaryColor),
                    
                    const SizedBox(height: 35),
                    const Text(
                      "Selesaikan Pembayaran",
                      style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
                    ),
                    const SizedBox(height: 12),
                    const Text(
                      "Silahkan selesaikan transaksi Anda pada browser. Halaman ini akan otomatis berpindah setelah pembayaran kami terima.",
                      textAlign: TextAlign.center,
                      style: TextStyle(color: Colors.grey, height: 1.5),
                    ),
                    const SizedBox(height: 45),

                    // --- 2. TOMBOL KEMBALI (IKUTI TEMA) ---
                    OutlinedButton(
                      style: OutlinedButton.styleFrom(
                        foregroundColor: primaryColor,
                        side: BorderSide(color: primaryColor),
                        padding: const EdgeInsets.symmetric(horizontal: 30, vertical: 12),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                      ),
                      onPressed: () => Navigator.pop(context),
                      child: const Text("KEMBALI KE FORM", style: TextStyle(fontWeight: FontWeight.bold)),
                    )
                  ],
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}