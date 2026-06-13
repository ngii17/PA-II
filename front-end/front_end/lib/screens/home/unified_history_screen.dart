import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../hotel/reservation_history_screen.dart';
import '../restoran/order_history_screen.dart';
import '../../providers/event_provider.dart';

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
    final eventProvider = context.watch<EventProvider>();
    final primaryColor = eventProvider.primaryColor;
    final secondaryColor = eventProvider.secondaryColor;

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      body: Column(
        children: [
          // Header gradien dinamis dengan logo gambar
          Container(
            width: double.infinity,
            padding: EdgeInsets.only(top: topPadding + 20, bottom: 20),
            decoration: BoxDecoration(
              gradient: LinearGradient(
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
                colors: [primaryColor, secondaryColor.withOpacity(0.85)],
              ),
              borderRadius: const BorderRadius.only(
                bottomLeft: Radius.circular(50),
                bottomRight: Radius.circular(50),
              ),
              boxShadow: [
                BoxShadow(color: primaryColor.withOpacity(0.3), blurRadius: 15, offset: const Offset(0, 8)),
              ],
            ),
            child: Column(
              children: [
                Image.asset(
                  'assets/icons/icon-purnama.png',
                  width: 45,
                  height: 45,
                  errorBuilder: (c, e, s) => const Icon(Icons.receipt_long_rounded, color: Colors.white, size: 35),
                ),
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
                // Tab Bar dengan gaya pill dinamis
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
                      color: secondaryColor,
                      borderRadius: BorderRadius.circular(16),
                    ),
                    labelColor: secondaryColor.computeLuminance() > 0.5 ? Colors.black : Colors.white,
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
          Expanded(
            child: TabBarView(
              controller: _tabController,
              children: const [
                ReservationHistoryScreen(),
                OrderHistoryScreen(),
              ],
            ),
          ),
          const SizedBox(height: 100), // Spasi agar tidak tertutup navbar floating
        ],
      ),
    );
  }
}