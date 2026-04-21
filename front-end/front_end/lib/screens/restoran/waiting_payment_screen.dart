import 'dart:async';
import 'package:flutter/material.dart';
import '../../services/api_services.dart';

class WaitingPaymentScreen extends StatefulWidget {
  final int orderId;
  const WaitingPaymentScreen({super.key, required this.orderId});

  @override
  State<WaitingPaymentScreen> createState() => _WaitingPaymentScreenState();
}

class _WaitingPaymentScreenState extends State<WaitingPaymentScreen> {
  Timer? _timer;

  @override
  void initState() {
    super.initState();
    // Mulai bertanya ke server setiap 3 detik
    _timer = Timer.periodic(const Duration(seconds: 3), (timer) {
      _checkStatus();
    });
  }

  void _checkStatus() async {
    final result = await ApiServices.checkRestoOrderStatus(widget.orderId);

    if (result['success'] == true) {
      // Jika status_bayar_id sudah berubah jadi 2 (Lunas)
      if (result['status_bayar_id'] == 2) {
        _timer?.cancel(); // Berhenti bertanya
        _showSuccessDialog();
      }
    }
  }

  void _showSuccessDialog() {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => AlertDialog(
        title: const Icon(Icons.check_circle, color: Colors.green, size: 60),
        content: const Text("Pembayaran Berhasil Dikonfirmasi!", textAlign: TextAlign.center),
        actions: [
          TextButton(
            onPressed: () {
              Navigator.pop(context); // Tutup Dialog
              Navigator.popUntil(context, (route) => route.isFirst); // Kembali ke Home
            },
            child: const Text("Selesai"),
          )
        ],
      ),
    );
  }

  @override
  void dispose() {
    _timer?.cancel(); // Pastikan timer mati saat halaman ditutup
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const CircularProgressIndicator(color: Colors.orangeAccent),
            const SizedBox(height: 30),
            const Text(
              "Menunggu Pembayaran...",
              style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
            ),
            const Padding(
              padding: EdgeInsets.symmetric(horizontal: 40, vertical: 10),
              child: Text(
                "Silakan selesaikan pembayaran Anda di browser. Halaman ini akan otomatis berubah setelah Anda membayar.",
                textAlign: TextAlign.center,
                style: TextStyle(color: Colors.grey),
              ),
            ),
            const SizedBox(height: 20),
            ElevatedButton(
              onPressed: () => Navigator.pop(context), 
              child: const Text("Kembali"),
            )
          ],
        ),
      ),
    );
  }
}