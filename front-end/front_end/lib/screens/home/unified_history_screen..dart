import 'package:flutter/material.dart';
import '../hotel/reservation_history_screen.dart';
import '../restoran/order_history_screen.dart';
import '../../colors/login_constants.dart';

class UnifiedHistoryScreen extends StatefulWidget {
  const UnifiedHistoryScreen({super.key});

  @override
  State<UnifiedHistoryScreen> createState() => _UnifiedHistoryScreenState();
}

class _UnifiedHistoryScreenState extends State<UnifiedHistoryScreen>
    with SingleTickerProviderStateMixin {
  late TabController _tabController;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this);
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final topPadding = MediaQuery.of(context).padding.top;

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      body: Column(
        children: [
          // ── HEADER UNIFIED (Satu-satunya Header) ────────────────────────
          Container(
            width: double.infinity,
            padding: EdgeInsets.only(top: topPadding + 20, bottom: 20),
            decoration: const BoxDecoration(
              gradient: AppTheme.headerGradient,
              borderRadius: BorderRadius.only(
                bottomLeft: Radius.circular(50),
                bottomRight: Radius.circular(50),
              ),
              boxShadow: [
                BoxShadow(color: Colors.black26, blurRadius: 15, offset: Offset(0, 8))
              ],
            ),
            child: Column(
              children: [
                const Icon(Icons.receipt_long_rounded, color: AppTheme.goldAccent, size: 35),
                const SizedBox(height: 8),
                const Text(
                  "RIWAYAT TRANSAKSI",
                  style: TextStyle(
                    color: Colors.white,
                    fontSize: 16,
                    fontWeight: FontWeight.w900,
                    letterSpacing: 2,
                  ),
                ),
                const SizedBox(height: 20),

                // ── CUSTOM TAB BAR PILL STYLE ─────────────────────
                Container(
                  margin: const EdgeInsets.symmetric(horizontal: 40),
                  height: 48,
                  decoration: BoxDecoration(
                    color: Colors.white.withOpacity(0.1),
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: TabBar(
                    controller: _tabController,
                    indicatorSize: TabBarIndicatorSize.tab,
                    dividerColor: Colors.transparent,
                    indicatorPadding: const EdgeInsets.all(4),
                    indicator: BoxDecoration(
                      color: AppTheme.goldAccent,
                      borderRadius: BorderRadius.circular(16),
                    ),
                    labelColor: AppTheme.primaryBlue,
                    unselectedLabelColor: Colors.white70,
                    labelStyle: const TextStyle(fontWeight: FontWeight.w900, fontSize: 12),
                    tabs: const [
                      Tab(text: "HOTEL"),
                      Tab(text: "RESTO"),
                    ],
                  ),
                ),
              ],
            ),
          ),

          // ── ISI LIST (Hanya List, Tidak Boleh Ada Header Lagi) ───────────
          Expanded(
            child: TabBarView(
              controller: _tabController,
              children: const [
                // Pastikan di dalam file ini tidak ada Container Header lagi!
                ReservationHistoryScreen(),
                OrderHistoryScreen(),
              ],
            ),
          ),
          
          // KUNCI: Berikan spasi kosong yang transparan di paling bawah 
          // agar list item terakhir tidak tertutup oleh Floating Navbar Home
          const SizedBox(height: 100),
        ],
      ),
    );
  }
}