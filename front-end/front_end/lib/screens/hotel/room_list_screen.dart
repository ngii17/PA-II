// screens/hotel/room_list_screen.dart

import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../services/api_services.dart';
import '../../providers/event_provider.dart';
import 'room_type_screen.dart';
import 'room_detail_screen.dart';
import 'booking_screen.dart';
import '../event/event_header.dart';
import '../notification/notification_screen.dart';
import 'package:shared_preferences/shared_preferences.dart';

// ─────────────────────────────────────────────
//  WARNA (konsisten dengan home_screen.dart)
// ─────────────────────────────────────────────
class _AppColors {
  static const Color navyDark  = Color(0xFF0C2D6B);
  static const Color gold      = Color(0xFFC9A227);
  static const Color bgPage    = Color(0xFFF2F4F8);
  static const Color bgCard    = Color(0xFFFFFFFF);
  static const Color textDark  = Color(0xFF1F2937);
  static const Color textMuted = Color(0xFF6B7280);
  static const Color textHint  = Color(0xFF9CA3AF);
  static const Color divider   = Color(0xFFE5E7EB);
}

// ─────────────────────────────────────────────
//  ROOM LIST SCREEN
// ─────────────────────────────────────────────
class RoomListScreen extends StatefulWidget {
  // ✅ CHANGE 1: tambahkan onBack opsional.
  // Kalau null (dibuka via Navigator.push), fallback ke Navigator.pop biasa.
  // Kalau diisi (dibuka sebagai tab di IndexedStack), dipakai untuk pindah tab balik ke Home.
  final VoidCallback? onBack;

  const RoomListScreen({super.key, this.onBack});

  @override
  State<RoomListScreen> createState() => _RoomListScreenState();
}

class _RoomListScreenState extends State<RoomListScreen> {
  late Future<Map<String, dynamic>> _roomData;
  String _searchQuery      = '';
  String _selectedFilter   = 'Semua';
  final List<String> _filters = ['Semua', 'Tersedia', 'Promo'];

  @override
  void initState() {
    super.initState();
    _refreshRooms();
  }

  void _refreshRooms() {
    setState(() {
      _roomData = ApiServices.getRoomTypes();
    });
  }

  List<RoomType> _filterRooms(List<RoomType> rooms) {
    return rooms.where((room) {
      final matchSearch = _searchQuery.isEmpty ||
          room.namaTipe
              .toLowerCase()
              .contains(_searchQuery.toLowerCase()) ||
          room.fasilitas
              .toLowerCase()
              .contains(_searchQuery.toLowerCase());

      final matchFilter = _selectedFilter == 'Semua' ||
          (_selectedFilter == 'Promo' && room.promoAktif != null) ||
          (_selectedFilter == 'Tersedia');

      return matchSearch && matchFilter;
    }).toList();
  }

  String _formatPrice(double price) {
    final parts = price.toStringAsFixed(0).split('');
    final buffer = StringBuffer();
    for (int i = 0; i < parts.length; i++) {
      if (i > 0 && (parts.length - i) % 3 == 0) buffer.write('.');
      buffer.write(parts[i]);
    }
    return buffer.toString();
  }

  // ============================================================
  // WIDGET PURNAMA LOGO
  // ============================================================
  Widget _buildPurnamaLogo() {
    return Image.asset(
      'assets/icons/icon-purnama.png',
      width: 38,
      height: 38,
      errorBuilder: (_, __, ___) => Container(
        width: 38,
        height: 38,
        decoration: BoxDecoration(
          shape: BoxShape.circle,
          gradient: const LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [Color(0xFF1A4A9E), Color(0xFF0C2D6B)],
          ),
          border: Border.all(color: _AppColors.gold, width: 2),
          boxShadow: [BoxShadow(color: _AppColors.gold.withOpacity(0.3), blurRadius: 6, offset: const Offset(0, 2))],
        ),
        child: const Center(
          child: Text("P", style: TextStyle(color: _AppColors.gold, fontWeight: FontWeight.w900, fontSize: 18)),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final topPadding = MediaQuery.of(context).padding.top;
    final ep         = context.watch<EventProvider>();
    final Color primaryColor =
        ep.eventCode != 'default' ? ep.primaryColor : _AppColors.navyDark;
    final Color accentColor =
        ep.eventCode != 'default' ? ep.secondaryColor : _AppColors.gold;

    return Scaffold(
      backgroundColor: _AppColors.bgPage,
      body: Column(
        children: [
          // ── HEADER TETAP (STICKY) ──
          _RoomHeader(
            topPadding: topPadding,
            primaryColor: primaryColor,
            accentColor: accentColor,
            searchQuery: _searchQuery,
            onSearchChanged: (v) => setState(() => _searchQuery = v),
            filters: _filters,
            selectedFilter: _selectedFilter,
            onFilterSelect: (f) => setState(() => _selectedFilter = f),
            buildPurnamaLogo: _buildPurnamaLogo,
            // ✅ CHANGE 2: pakai widget.onBack kalau ada, kalau tidak fallback pop biasa
            onBack: widget.onBack ??
                () {
                  if (Navigator.canPop(context)) Navigator.pop(context);
                },
          ),

          // ── BODY (SCROLLABLE) ──
          Expanded(
            child: RefreshIndicator(
              onRefresh: () async => _refreshRooms(),
              color: primaryColor,
              child: FutureBuilder<Map<String, dynamic>>(
                future: _roomData,
                builder: (context, snapshot) {
                  if (snapshot.connectionState == ConnectionState.waiting) {
                    return Center(child: CircularProgressIndicator(color: primaryColor));
                  }

                  if (snapshot.hasError) {
                    return Center(
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          const Icon(Icons.error_outline, size: 52, color: Colors.grey),
                          const SizedBox(height: 14),
                          const Text("Gagal memuat data kamar.", style: TextStyle(color: _AppColors.textMuted)),
                          const SizedBox(height: 16),
                          ElevatedButton(
                            onPressed: _refreshRooms,
                            style: ElevatedButton.styleFrom(
                              backgroundColor: primaryColor,
                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                            ),
                            child: const Text("Coba Lagi", style: TextStyle(color: Colors.white)),
                          ),
                        ],
                      ),
                    );
                  }

                  final List<dynamic> listJson = snapshot.data?['data'] ?? [];
                  final List<RoomType> allRooms = listJson.map((e) => RoomType.fromJson(e)).toList();
                  final List<RoomType> filtered = _filterRooms(allRooms);

                  return CustomScrollView(
                    physics: const BouncingScrollPhysics(),
                    slivers: [
                      // ── EVENT HEADER ──
                      const SliverToBoxAdapter(child: EventHeader()),

                      // ── JUMLAH HASIL ──
                      SliverToBoxAdapter(
                        child: Padding(
                          padding: const EdgeInsets.fromLTRB(16, 12, 16, 4),
                          child: Text(
                            "${filtered.length} tipe kamar tersedia",
                            style: const TextStyle(fontSize: 12, color: _AppColors.textMuted),
                          ),
                        ),
                      ),

                      // ── LIST KAMAR ──
                      filtered.isEmpty
                          ? const SliverFillRemaining(
                              child: Center(
                                child: Column(
                                  mainAxisAlignment: MainAxisAlignment.center,
                                  children: [
                                    Icon(Icons.hotel_outlined, size: 60, color: _AppColors.textHint),
                                    SizedBox(height: 12),
                                    Text("Kamar tidak ditemukan", style: TextStyle(color: _AppColors.textMuted, fontSize: 14)),
                                  ],
                                ),
                              ),
                            )
                          : SliverPadding(
                              padding: const EdgeInsets.fromLTRB(16, 4, 16, 100),
                              sliver: SliverList(
                                delegate: SliverChildBuilderDelegate(
                                  (context, index) => Padding(
                                    padding: const EdgeInsets.only(bottom: 14),
                                    child: _RoomCard(
                                      room: filtered[index],
                                      primaryColor: primaryColor,
                                      accentColor: accentColor,
                                      formatPrice: _formatPrice,
                                    ),
                                  ),
                                  childCount: filtered.length,
                                ),
                              ),
                            ),
                    ],
                  );
                },
              ),
            ),
          ),
        ],
      ),
    );
  }
}

// ─────────────────────────────────────────────
//  HEADER (dengan tombol back)
// ─────────────────────────────────────────────
class _RoomHeader extends StatelessWidget {
  final double topPadding;
  final Color primaryColor;
  final Color accentColor;
  final String searchQuery;
  final ValueChanged<String> onSearchChanged;
  final List<String> filters;
  final String selectedFilter;
  final ValueChanged<String> onFilterSelect;
  final Widget Function() buildPurnamaLogo;
  final VoidCallback onBack; // ✅ CHANGE 3: terima onBack dari parent

  const _RoomHeader({
    required this.topPadding,
    required this.primaryColor,
    required this.accentColor,
    required this.searchQuery,
    required this.onSearchChanged,
    required this.filters,
    required this.selectedFilter,
    required this.onFilterSelect,
    required this.buildPurnamaLogo,
    required this.onBack, // ✅ CHANGE 3
  });

  @override
  Widget build(BuildContext context) {
    return Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        // ── GRADIENT HEADER ──
        Container(
          width: double.infinity,
          padding: EdgeInsets.only(
              top: topPadding + 16,
              left: 20,
              right: 20,
              bottom: 24),
          decoration: BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
              colors: [
                primaryColor,
                primaryColor.withOpacity(0.85),
                accentColor.withOpacity(0.7),
              ],
            ),
            borderRadius: const BorderRadius.only(
              bottomLeft: Radius.circular(36),
              bottomRight: Radius.circular(36),
            ),
            boxShadow: [
              BoxShadow(
                color: primaryColor.withOpacity(0.35),
                blurRadius: 16,
                offset: const Offset(0, 6),
              ),
            ],
          ),
          child: Column(
            children: [
              Row(
                children: [
                  // ── TOMBOL BACK ──
                  Material(
                    color: Colors.transparent,
                    child: InkWell(
                      onTap: onBack, // ✅ pakai callback dari parent
                      borderRadius: BorderRadius.circular(17),
                      child: Container(
                        width: 34,
                        height: 34,
                        decoration: BoxDecoration(
                          color: Colors.white.withOpacity(0.15),
                          shape: BoxShape.circle,
                        ),
                        child: const Icon(
                          Icons.arrow_back_ios_new_rounded,
                          color: Colors.white70,
                          size: 16,
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(width: 10),
                  buildPurnamaLogo(),
                  const SizedBox(width: 10),
                  const Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text("Hotel & Restoran",
                          style: TextStyle(
                              color: Colors.white60,
                              fontSize: 9,
                              letterSpacing: 1.2)),
                      Text("PURNAMA BALIGE",
                          style: TextStyle(
                              color: Colors.white,
                              fontSize: 13,
                              fontWeight: FontWeight.w800,
                              letterSpacing: 0.8)),
                    ],
                  ),
                  const Spacer(),
                  _HeaderIconButton(
                    icon: Icons.notifications_none_rounded,
                    onTap: () => Navigator.push(context,
                        MaterialPageRoute(
                            builder: (_) =>
                                const NotificationScreen())),
                  ),
                ],
              ),
              const SizedBox(height: 18),
              Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.hotel_rounded, color: accentColor, size: 20),
                  const SizedBox(width: 8),
                  const Text(
                    "Reservasi Kamar",
                    style: TextStyle(
                      color: Colors.white,
                      fontSize: 20,
                      fontWeight: FontWeight.w800,
                      letterSpacing: 0.5,
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),

        // ── SEARCH BAR ──
        Padding(
          padding: const EdgeInsets.fromLTRB(16, 0, 16, 0),
          child: Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(16),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withOpacity(0.08),
                  blurRadius: 14,
                  offset: const Offset(0, 4),
                ),
              ],
            ),
            child: Row(
              children: [
                Icon(Icons.search_rounded, color: accentColor, size: 20),
                const SizedBox(width: 10),
                Expanded(
                  child: TextField(
                    onChanged: onSearchChanged,
                    style: const TextStyle(fontSize: 13, color: _AppColors.textDark),
                    decoration: const InputDecoration(
                      hintText: "Cari tipe kamar atau fasilitas...",
                      hintStyle: TextStyle(color: _AppColors.textHint, fontSize: 13),
                      border: InputBorder.none,
                      isDense: true,
                      contentPadding: EdgeInsets.zero,
                    ),
                  ),
                ),
                Container(
                  padding: const EdgeInsets.all(6),
                  decoration: BoxDecoration(
                    color: _AppColors.navyDark.withOpacity(0.08),
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: const Icon(Icons.tune_rounded, color: _AppColors.navyDark, size: 16),
                ),
              ],
            ),
          ),
        ),

        // ── FILTER CHIPS ──
        SizedBox(
          height: 44,
          child: ListView.builder(
            scrollDirection: Axis.horizontal,
            padding: const EdgeInsets.symmetric(horizontal: 16),
            itemCount: filters.length,
            itemBuilder: (_, i) {
              final f = filters[i];
              final isSelected = selectedFilter == f;
              return GestureDetector(
                onTap: () => onFilterSelect(f),
                child: AnimatedContainer(
                  duration: const Duration(milliseconds: 200),
                  margin: const EdgeInsets.only(right: 10),
                  padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 8),
                  decoration: BoxDecoration(
                    color: isSelected ? primaryColor : Colors.white,
                    borderRadius: BorderRadius.circular(30),
                    border: Border.all(color: isSelected ? primaryColor : Colors.grey.shade300),
                    boxShadow: isSelected ? [BoxShadow(color: primaryColor.withOpacity(0.3), blurRadius: 8, offset: const Offset(0, 3))] : [],
                  ),
                  child: Center(
                    child: Text(
                      f,
                      style: TextStyle(
                        color: isSelected ? Colors.white : Colors.grey.shade600,
                        fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
                        fontSize: 13,
                      ),
                    ),
                  ),
                ),
              );
            },
          ),
        ),
        const SizedBox(height: 8),
      ],
    );
  }
}

// ─────────────────────────────────────────────
//  ROOM CARD  — horizontal layout
// ─────────────────────────────────────────────
class _RoomCard extends StatelessWidget {
  final RoomType room;
  final Color    primaryColor;
  final Color    accentColor;
  final String Function(double) formatPrice;

  const _RoomCard({
    required this.room,
    required this.primaryColor,
    required this.accentColor,
    required this.formatPrice,
  });

  @override
  Widget build(BuildContext context) {
    final bool hasPromo =
        room.promoAktif != null && room.hargaAkhir < room.hargaAsli;

    final List<String> fasilitasList = room.fasilitas
        .split(RegExp(r'[,;]'))
        .map((e) => e.trim())
        .where((e) => e.isNotEmpty)
        .take(3)
        .toList();

    return Container(
      decoration: BoxDecoration(
        color: _AppColors.bgCard,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.07),
            blurRadius: 12,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      clipBehavior: Clip.antiAlias,
      child: Column(
        children: [
          IntrinsicHeight(
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                SizedBox(
                  width: 130,
                  child: Stack(
                    fit: StackFit.expand,
                    children: [
                      Image.network(
                        room.foto ?? '',
                        fit: BoxFit.cover,
                        errorBuilder: (_, __, ___) => Container(
                          color: const Color(0xFFF3F4F6),
                          child: const Icon(Icons.hotel_rounded, size: 40, color: Colors.grey),
                        ),
                        loadingBuilder: (_, child, progress) {
                          if (progress == null) return child;
                          return Container(
                            color: const Color(0xFFF3F4F6),
                            child: Center(child: CircularProgressIndicator(strokeWidth: 2, color: primaryColor)),
                          );
                        },
                      ),
                      if (hasPromo)
                        Positioned(
                          top: 8,
                          left: 8,
                          child: Container(
                            padding: const EdgeInsets.symmetric(horizontal: 7, vertical: 3),
                            decoration: BoxDecoration(color: Colors.red.shade600, borderRadius: BorderRadius.circular(6)),
                            child: const Text("PROMO", style: TextStyle(color: Colors.white, fontSize: 9, fontWeight: FontWeight.bold, letterSpacing: 0.5)),
                          ),
                        ),
                      Positioned(
                        bottom: 8,
                        left: 8,
                        child: Container(
                          padding: const EdgeInsets.symmetric(horizontal: 7, vertical: 3),
                          decoration: BoxDecoration(color: Colors.black.withOpacity(0.5), borderRadius: BorderRadius.circular(6)),
                          child: Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              const Icon(Icons.people_rounded, color: Colors.white, size: 10),
                              const SizedBox(width: 3),
                              Text("${room.kapasitas} tamu", style: const TextStyle(color: Colors.white, fontSize: 9, fontWeight: FontWeight.w600)),
                            ],
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
                Expanded(
                  child: Padding(
                    padding: const EdgeInsets.fromLTRB(12, 12, 12, 12),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(room.namaTipe, style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w800, color: _AppColors.textDark), maxLines: 1, overflow: TextOverflow.ellipsis),
                        const SizedBox(height: 6),
                        Wrap(
                          spacing: 5,
                          runSpacing: 4,
                          children: fasilitasList.map((f) => Container(
                            padding: const EdgeInsets.symmetric(horizontal: 7, vertical: 3),
                            decoration: BoxDecoration(color: primaryColor.withOpacity(0.08), borderRadius: BorderRadius.circular(20)),
                            child: Text(f, style: TextStyle(fontSize: 9, color: primaryColor, fontWeight: FontWeight.w600)),
                          )).toList(),
                        ),
                        const SizedBox(height: 8),
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text("Rp ${formatPrice(room.hargaAkhir)}", style: TextStyle(fontSize: 15, fontWeight: FontWeight.w800, color: primaryColor)),
                            Row(children: [if (hasPromo) Text("Rp ${formatPrice(room.hargaAsli)}", style: const TextStyle(fontSize: 10, color: _AppColors.textMuted, decoration: TextDecoration.lineThrough)), const Text(" /malam", style: TextStyle(fontSize: 10, color: _AppColors.textMuted))]),
                          ],
                        ),
                      ],
                    ),
                  ),
                ),
              ],
            ),
          ),
          Container(height: 1, color: _AppColors.divider.withOpacity(0.6)),
          Padding(
            padding: const EdgeInsets.fromLTRB(12, 10, 12, 12),
            child: Row(
              children: [
                Expanded(
                  child: GestureDetector(
                    onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => RoomDetailScreen(room: room))),
                    child: Container(
                      padding: const EdgeInsets.symmetric(vertical: 10),
                      decoration: BoxDecoration(color: primaryColor.withOpacity(0.08), borderRadius: BorderRadius.circular(12), border: Border.all(color: primaryColor.withOpacity(0.25))),
                      child: Row(mainAxisAlignment: MainAxisAlignment.center, children: [Icon(Icons.info_outline_rounded, size: 14, color: primaryColor), const SizedBox(width: 5), Text("Detail", style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: primaryColor))]),
                    ),
                  ),
                ),
                const SizedBox(width: 10),
                Expanded(
                  flex: 2,
                  child: GestureDetector(
                    onTap: () async {
                      final prefs = await SharedPreferences.getInstance();
                      int? userId = prefs.getInt('user_id');
                      if (userId == null || userId == 0) {
                        if (context.mounted) {
                          ScaffoldMessenger.of(context).showSnackBar(
                            const SnackBar(
                              content: Text("Silakan login terlebih dahulu untuk melakukan reservasi kamar."),
                              backgroundColor: Colors.red,
                              behavior: SnackBarBehavior.floating,
                            ),
                          );
                        }
                        return;
                      }
                      if (context.mounted) {
                        Navigator.push(context, MaterialPageRoute(builder: (_) => BookingScreen(room: room)));
                      }
                    },
                    child: Container(
                      padding: const EdgeInsets.symmetric(vertical: 10),
                      decoration: BoxDecoration(gradient: LinearGradient(colors: [primaryColor, primaryColor.withOpacity(0.85)]), borderRadius: BorderRadius.circular(12), boxShadow: [BoxShadow(color: primaryColor.withOpacity(0.35), blurRadius: 8, offset: const Offset(0, 3))]),
                      child: const Row(mainAxisAlignment: MainAxisAlignment.center, children: [Icon(Icons.calendar_month_rounded, size: 14, color: Colors.white), SizedBox(width: 5), Text("Book Now", style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: Colors.white))]),
                    ),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

// ─────────────────────────────────────────────
//  HEADER ICON BUTTON
// ─────────────────────────────────────────────
class _HeaderIconButton extends StatelessWidget {
  final IconData     icon;
  final VoidCallback onTap;
  const _HeaderIconButton({required this.icon, required this.onTap});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        width: 34,
        height: 34,
        decoration: BoxDecoration(color: Colors.white.withOpacity(0.12), shape: BoxShape.circle),
        child: Icon(icon, color: Colors.white70, size: 18),
      ),
    );
  }
}