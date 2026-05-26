import 'dart:async';
import 'package:flutter/material.dart';
import '../../services/api_services.dart';
import 'payment_status_screen.dart';
import '../event/event_header.dart'; 
import '../../colors/login_constants.dart';

class WaitingPaymentScreen extends StatefulWidget {
  final int reservasiId;
  const WaitingPaymentScreen({super.key, required this.reservasiId});

  @override
  State<WaitingPaymentScreen> createState() => _WaitingPaymentScreenState();
}

class _WaitingPaymentScreenState extends State<WaitingPaymentScreen> with SingleTickerProviderStateMixin {
  Timer? _timer;
  bool _isChecking = false;
  late AnimationController _animationController;

  @override
  void initState() {
    super.initState();
    
    // 1. Inisialisasi Animasi Denyut pada Ikon Header
    _animationController = AnimationController(
      vsync: this,
      duration: const Duration(seconds: 2),
    )..repeat(reverse: true);

    // 2. Mulai Polling Status ke Server Port 8000 (Tiap 3 Detik)
    _timer = Timer.periodic(const Duration(seconds: 3), (timer) {
      _checkPaymentStatus();
    });
  }

  // Logika Cek Status Database
  void _checkPaymentStatus() async {
    if (_isChecking) return; 
    _isChecking = true;

    final response = await ApiServices.checkPaymentStatus(widget.reservasiId);

    if (response['success'] == true) {
      int statusId = response['status_id'];

      // Jika status_reservasi_id sudah berubah jadi 2 (Berhasil di Laravel)
      if (statusId == 2) {
        _timer?.cancel(); 
        if (mounted) {
          // Navigasi ke Layar Sukses (Membersihkan stack)
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
    _timer?.cancel(); // Sangat Penting: Matikan polling agar tidak boros memory
    _animationController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      extendBodyBehindAppBar: true,
      appBar: AppBar(
        title: const Text("Konfirmasi Pembayaran", 
          style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white, fontSize: 18)),
        centerTitle: true,
        backgroundColor: Colors.transparent,
        elevation: 0,
        automaticallyImplyLeading: false, // Terkunci sesuai alur reservasi hotel
      ),
      body: Column(
        children: [
          // ── 1. HEADER MODERN (NAVY GRADIEN + RADIUS 60) ──────────────────
          Container(
            width: double.infinity,
            padding: EdgeInsets.only(
              top: MediaQuery.of(context).padding.top + 60,
              bottom: 45,
            ),
            decoration: const BoxDecoration(
              gradient: AppTheme.headerGradient, // Warna Navy Mewah
              borderRadius: BorderRadius.only(
                bottomLeft: Radius.circular(60),
                bottomRight: Radius.circular(60),
              ),
              boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 15, offset: Offset(0, 8))],
            ),
            child: Column(
              children: [
                // Visual Animasi Ikon Dompet Emas
                ScaleTransition(
                  scale: Tween(begin: 0.9, end: 1.1).animate(
                    CurvedAnimation(parent: _animationController, curve: Curves.easeInOut),
                  ),
                  child: Container(
                    padding: const EdgeInsets.all(22),
                    decoration: BoxDecoration(
                      color: Colors.white.withOpacity(0.15),
                      shape: BoxShape.circle,
                      border: Border.all(color: AppTheme.goldAccent.withOpacity(0.5), width: 2),
                    ),
                    child: const Icon(Icons.account_balance_wallet_rounded, 
                      color: AppTheme.goldAccent, size: 55),
                  ),
                ),
                const SizedBox(height: 25),
                const Text("MENUNGGU PEMBAYARAN", 
                  style: TextStyle(color: Colors.white, fontWeight: FontWeight.w900, letterSpacing: 2, fontSize: 14)),
                const SizedBox(height: 8),
                Text("ID Reservasi #${widget.reservasiId}", 
                  style: const TextStyle(color: Colors.white70, fontSize: 12, fontWeight: FontWeight.w500)),
              ],
            ),
          ),

          const EventHeader(), // Banner Promo Dinamis Port 8001

          // ── 2. KONTEN LOADING TENGAH ─────────────────────────────────────
          Expanded(
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 40),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  // Progress Indicator Navy Tebal
                  SizedBox(
                    width: 65,
                    height: 65,
                    child: CircularProgressIndicator(
                      color: AppTheme.primaryBlue,
                      strokeWidth: 6,
                      backgroundColor: AppTheme.primaryBlue.withOpacity(0.1),
                    ),
                  ),
                  const SizedBox(height: 45),
                  const Text(
                    "Verifikasi Transaksi",
                    style: TextStyle(fontSize: 22, fontWeight: FontWeight.w900, color: AppTheme.primaryBlue),
                  ),
                  const SizedBox(height: 15),
                  const Text(
                    "Silahkan selesaikan pembayaran pada browser Anda. Sistem akan mendeteksi transaksi secara otomatis dan mengalihkan Anda ke halaman nota dalam beberapa saat.",
                    textAlign: TextAlign.center,
                    style: TextStyle(color: Colors.black54, fontSize: 14, height: 1.7, fontWeight: FontWeight.w500),
                  ),
                  const SizedBox(height: 60),

                  // ── 3. TOMBOL KEMBALI (OUTLINED MODERN) ───────────────────
                  SizedBox(
                    width: double.infinity,
                    height: 55,
                    child: OutlinedButton.icon(
                      style: OutlinedButton.styleFrom(
                        foregroundColor: AppTheme.primaryBlue,
                        side: const BorderSide(color: AppTheme.primaryBlue, width: 1.8),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(18)),
                      ),
                      onPressed: () => Navigator.pop(context),
                      icon: const Icon(Icons.arrow_back_ios_new_rounded, size: 18),
                      label: const Text("KEMBALI KE DETAIL", 
                        style: TextStyle(fontWeight: FontWeight.w900, letterSpacing: 1.2)),
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