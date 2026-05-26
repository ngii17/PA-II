import 'dart:async';
import 'package:flutter/material.dart';
import '../../services/api_services.dart';
import '../../colors/login_constants.dart';
import '../../widgets/login_widgets.dart';

class WaitingPaymentScreen extends StatefulWidget {
  final int orderId;
  const WaitingPaymentScreen({super.key, required this.orderId});

  @override
  State<WaitingPaymentScreen> createState() => _WaitingPaymentScreenState();
}

class _WaitingPaymentScreenState extends State<WaitingPaymentScreen> with SingleTickerProviderStateMixin {
  Timer? _timer;
  late AnimationController _animationController;

  @override
  void initState() {
    super.initState();
    
    // 1. Inisialisasi Animasi Denyut pada Icon Header
    _animationController = AnimationController(
      vsync: this,
      duration: const Duration(seconds: 2),
    )..repeat(reverse: true);

    // 2. Mulai Polling Status ke Server Port 8000 (Tiap 3 Detik)
    _timer = Timer.periodic(const Duration(seconds: 3), (timer) {
      _checkStatus();
    });
  }

  // Logika Cek Status Database
  void _checkStatus() async {
    final result = await ApiServices.checkRestoOrderStatus(widget.orderId);

    if (result['success'] == true) {
      // Jika status_bayar_id sudah berubah jadi 2 (Lunas di Laravel)
      if (result['status_bayar_id'] == 2) {
        _timer?.cancel(); 
        _showSuccessFlow(); // Pindah ke tampilan sukses
      }
    }
  }

  // --- TAMPILAN DIALOG SUKSES MODERN ---
  void _showSuccessFlow() {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(25)),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            const SizedBox(height: 10),
            Container(
              padding: const EdgeInsets.all(18),
              decoration: BoxDecoration(
                color: Colors.green.shade50,
                shape: BoxShape.circle,
              ),
              child: Icon(Icons.check_circle_rounded, color: Colors.green.shade600, size: 70),
            ),
            const SizedBox(height: 20),
            const Text(
              "Pembayaran Berhasil!",
              style: TextStyle(fontWeight: FontWeight.bold, fontSize: 20, color: AppTheme.primaryBlue),
            ),
            const SizedBox(height: 10),
            const Text(
              "Pesanan kuliner Anda telah kami teruskan ke dapur. Silahkan duduk manis menunggu hidangan Anda.",
              textAlign: TextAlign.center,
              style: TextStyle(color: Colors.grey, fontSize: 13, height: 1.5),
            ),
            const SizedBox(height: 30),
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: () {
                  Navigator.pop(context); // Tutup Dialog
                  Navigator.popUntil(context, (route) => route.isFirst); // Kembali ke Home/Dashboard Utama
                },
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppTheme.primaryBlue,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
                  padding: const EdgeInsets.symmetric(vertical: 15),
                ),
                child: const Text("KEMBALI KE BERANDA", 
                  style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, letterSpacing: 1)),
              ),
            ),
          ],
        ),
      ),
    );
  }

  @override
  void dispose() {
    _timer?.cancel(); // Sangat Penting: Matikan polling saat user keluar agar tidak boros memory
    _animationController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final restoColor = AppTheme.goldAccent;

    return Scaffold(
      backgroundColor: Colors.white,
      extendBodyBehindAppBar: true,
      appBar: AppBar(
        title: const Text("Status Transaksi", 
          style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white, fontSize: 18)),
        centerTitle: true,
        backgroundColor: Colors.transparent,
        elevation: 0,
        automaticallyImplyLeading: false, // User tidak boleh back sembarangan saat proses bayar
      ),
      body: Column(
        children: [
          // ── 1. HEADER RESTORAN GRADIEN GOLD (Radius 60) ───────────────────
          Container(
            width: double.infinity,
            padding: EdgeInsets.only(
              top: MediaQuery.of(context).padding.top + 60,
              bottom: 45,
            ),
            decoration: const BoxDecoration(
              gradient: AppTheme.restoGradient, // Gradien Emas Premium
              borderRadius: BorderRadius.only(
                bottomLeft: Radius.circular(60),
                bottomRight: Radius.circular(60),
              ),
              boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 15, offset: Offset(0, 8))],
            ),
            child: Column(
              children: [
                // Animasi Denyut Emas pada Ikon
                ScaleTransition(
                  scale: Tween(begin: 0.95, end: 1.1).animate(
                    CurvedAnimation(parent: _animationController, curve: Curves.easeInOut),
                  ),
                  child: Container(
                    padding: const EdgeInsets.all(22),
                    decoration: BoxDecoration(
                      color: Colors.white.withOpacity(0.15),
                      shape: BoxShape.circle,
                      border: Border.all(color: Colors.white30, width: 2),
                    ),
                    child: const Icon(Icons.restaurant_menu_rounded, color: Colors.white, size: 55),
                  ),
                ),
                const SizedBox(height: 25),
                const Text("MENUNGGU KONFIRMASI", 
                  style: TextStyle(color: Colors.white, fontWeight: FontWeight.w900, letterSpacing: 2, fontSize: 14)),
                const SizedBox(height: 8),
                Text("Nota Digital #RS-${widget.orderId}", 
                  style: const TextStyle(color: Colors.white70, fontSize: 12, fontWeight: FontWeight.w500)),
              ],
            ),
          ),

          // ── 2. KONTEN LOADING TENGAH ───────────────────────────────────────
          Expanded(
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 40),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  // Loading Indicator Tebal (Gold)
                  SizedBox(
                    width: 70,
                    height: 70,
                    child: CircularProgressIndicator(
                      color: restoColor,
                      strokeWidth: 8,
                      backgroundColor: restoColor.withOpacity(0.1),
                    ),
                  ),
                  const SizedBox(height: 45),
                  Text(
                    "Memverifikasi Pembayaran",
                    style: TextStyle(fontSize: 22, fontWeight: FontWeight.w900, color: AppTheme.primaryBlue),
                  ),
                  const SizedBox(height: 15),
                  const Text(
                    "Silahkan selesaikan pembayaran pada jendela browser Anda. Sistem akan mendeteksi transaksi secara otomatis dalam beberapa saat.",
                    textAlign: TextAlign.center,
                    style: TextStyle(color: Colors.grey, fontSize: 14, height: 1.6),
                  ),
                  const SizedBox(height: 60),

                  // ── 3. TOMBOL KEMBALI OPSIONAL ─────────────────────────────
                  SizedBox(
                    width: double.infinity,
                    height: 55,
                    child: OutlinedButton.icon(
                      style: OutlinedButton.styleFrom(
                        foregroundColor: AppTheme.primaryBlue,
                        side: const BorderSide(color: AppTheme.primaryBlue, width: 1.8),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(18)),
                      ),
                      onPressed: () => Navigator.pop(context), // Kembali ke halaman sebelumnya jika ingin ganti metode
                      icon: const Icon(Icons.keyboard_arrow_left_rounded, size: 24),
                      label: const Text("KEMBALI KE PESANAN", 
                        style: TextStyle(fontWeight: FontWeight.w900, letterSpacing: 1)),
                    ),
                  ),
                ],
              ),
            ),
          ),
          const SizedBox(height: 30),
        ],
      ),
    );
  }
}