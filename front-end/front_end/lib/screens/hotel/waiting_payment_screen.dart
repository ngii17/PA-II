import 'dart:async';
import 'package:flutter/material.dart';
import '../../services/api_services.dart';
import 'payment_status_screen.dart';

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
    if (_isChecking) return; // Jangan cek jika proses sebelumnya belum selesai
    _isChecking = true;

    final response = await ApiServices.checkPaymentStatus(widget.reservasiId);

    if (response['success'] == true) {
      int statusId = response['status_id'];

      // Jika status_id == 2 (Terbayar)
      if (statusId == 2) {
        _timer?.cancel(); // Berhenti bertanya ke server
        if (mounted) {
          // PINDAH KE HALAMAN BERHASIL
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
    _timer?.cancel(); // Hentikan timer jika user menekan tombol back
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text("Menunggu Pembayaran"), automaticallyImplyLeading: false),
      body: Center(
        child: Padding(
          padding: const EdgeInsets.all(20.0),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const CircularProgressIndicator(color: Colors.blueAccent),
              const SizedBox(height: 30),
              const Text(
                "Selesaikan Pembayaran Anda",
                style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 10),
              const Text(
                "Silahkan lakukan pembayaran di browser yang baru saja terbuka. Aplikasi akan otomatis pindah jika pembayaran sukses.",
                textAlign: TextAlign.center,
                style: TextStyle(color: Colors.grey),
              ),
              const SizedBox(height: 40),
              OutlinedButton(
                onPressed: () => Navigator.pop(context),
                child: const Text("KEMBALI KE FORM"),
              )
            ],
          ),
        ),
      ),
    );
  }
}