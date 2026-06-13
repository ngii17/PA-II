import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../services/api_services.dart';
import '../../providers/event_provider.dart';
import 'room_type_screen.dart';
import 'booking_screen.dart';
import '../event/event_header.dart';
import '../notification/notification_screen.dart';

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
//  ROOM DETAIL SCREEN
// ─────────────────────────────────────────────
class RoomDetailScreen extends StatefulWidget {
  final RoomType room;
  const RoomDetailScreen({super.key, required this.room});

  @override
  State<RoomDetailScreen> createState() => _RoomDetailScreenState();
}

class _RoomDetailScreenState extends State<RoomDetailScreen> {
  late Future<Map<String, dynamic>> _reviewData;

  @override
  void initState() {
    super.initState();
    _refreshReviews();
  }

  void _refreshReviews() {
    setState(() {
      _reviewData = ApiServices.getHotelReviews(widget.room.id);
    });
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

  @override
  Widget build(BuildContext context) {
    final topPadding  = MediaQuery.of(context).padding.top;
    final ep          = context.watch<EventProvider>();
    final Color primaryColor =
        ep.eventCode != 'default' ? ep.primaryColor : _AppColors.navyDark;
    final Color accentColor =
        ep.eventCode != 'default' ? ep.secondaryColor : _AppColors.gold;
    final bool hasPromo = widget.room.promoAktif != null &&
        widget.room.hargaAkhir < widget.room.hargaAsli;

    return Scaffold(
      backgroundColor: _AppColors.bgPage,
      body: Stack(
        children: [
          // Menggunakan Column agar Header tetap di atas (fixed)
          Column(
            children: [
              // ── HEADER (FIXED) ──
              _RoomDetailHeader(
                topPadding: topPadding,
                roomName: widget.room.namaTipe,
                primaryColor: primaryColor,
                accentColor: accentColor,
                onBack: () => Navigator.pop(context),
              ),

              // ── SCROLLABLE CONTENT ──
              Expanded(
                child: SingleChildScrollView(
                  physics: const BouncingScrollPhysics(),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // ── EVENT HEADER ──
                      const EventHeader(),

                      // ── FOTO KAMAR ──
                      Padding(
                        padding: const EdgeInsets.fromLTRB(16, 14, 16, 0),
                        child: ClipRRect(
                          borderRadius: BorderRadius.circular(20),
                          child: Stack(
                            children: [
                              Image.network(
                                "https://plus.unsplash.com/premium_photo-1675745329954-9639d3b74bbf?q=80&w=2000&auto=format&fit=crop",
                                height: 220,
                                width: double.infinity,
                                fit: BoxFit.cover,
                                errorBuilder: (_, __, ___) => Container(
                                  height: 220,
                                  color: const Color(0xFFE5E7EB),
                                  child: const Icon(Icons.hotel_rounded,
                                      size: 60, color: Colors.grey),
                                ),
                                loadingBuilder: (_, child, progress) {
                                  if (progress == null) return child;
                                  return Container(
                                    height: 220,
                                    color: const Color(0xFFF3F4F6),
                                    child: Center(
                                      child: CircularProgressIndicator(
                                        strokeWidth: 2,
                                        color: primaryColor,
                                        value: progress.expectedTotalBytes != null
                                            ? progress.cumulativeBytesLoaded /
                                                progress.expectedTotalBytes!
                                            : null,
                                      ),
                                    ),
                                  );
                                },
                              ),
                              // Promo badge di atas foto
                              if (hasPromo)
                                Positioned(
                                  top: 12,
                                  left: 12,
                                  child: Container(
                                    padding: const EdgeInsets.symmetric(
                                        horizontal: 10, vertical: 5),
                                    decoration: BoxDecoration(
                                      color: Colors.red.shade600,
                                      borderRadius: BorderRadius.circular(8),
                                    ),
                                    child: Text(
                                      widget.room.promoAktif!,
                                      style: const TextStyle(
                                          color: Colors.white,
                                          fontSize: 11,
                                          fontWeight: FontWeight.bold),
                                    ),
                                  ),
                                ),
                              // Kapasitas badge
                              Positioned(
                                bottom: 12,
                                right: 12,
                                child: Container(
                                  padding: const EdgeInsets.symmetric(
                                      horizontal: 10, vertical: 5),
                                  decoration: BoxDecoration(
                                    color: Colors.black.withOpacity(0.55),
                                    borderRadius: BorderRadius.circular(8),
                                  ),
                                  child: Row(
                                    mainAxisSize: MainAxisSize.min,
                                    children: [
                                      const Icon(Icons.people_rounded,
                                          color: Colors.white, size: 13),
                                      const SizedBox(width: 5),
                                      Text(
                                        "${widget.room.kapasitas} tamu",
                                        style: const TextStyle(
                                            color: Colors.white,
                                            fontSize: 11,
                                            fontWeight: FontWeight.w600),
                                      ),
                                    ],
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),

                      // ── INFO KAMAR ──
                      Padding(
                        padding: const EdgeInsets.fromLTRB(16, 16, 16, 0),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            // Nama kamar
                            Text(
                              widget.room.namaTipe,
                              style: const TextStyle(
                                fontSize: 22,
                                fontWeight: FontWeight.w800,
                                color: _AppColors.textDark,
                              ),
                            ),

                            const SizedBox(height: 8),

                            // Harga
                            Row(
                              crossAxisAlignment: CrossAxisAlignment.end,
                              children: [
                                Text(
                                  "Rp ${_formatPrice(widget.room.hargaAkhir)}",
                                  style: TextStyle(
                                    fontSize: 22,
                                    fontWeight: FontWeight.w800,
                                    color: primaryColor,
                                  ),
                                ),
                                const Text(
                                  " / malam",
                                  style: TextStyle(
                                      fontSize: 13,
                                      color: _AppColors.textMuted),
                                ),
                                if (hasPromo) ...[
                                  const SizedBox(width: 8),
                                  Text(
                                    "Rp ${_formatPrice(widget.room.hargaAsli)}",
                                    style: const TextStyle(
                                      fontSize: 13,
                                      color: _AppColors.textMuted,
                                      decoration: TextDecoration.lineThrough,
                                    ),
                                  ),
                                ],
                              ],
                            ),

                            const SizedBox(height: 14),

                            // ── QUICK STATS ──
                            Row(
                              children: [
                                _StatChip(
                                  icon: Icons.star_rounded,
                                  label: "4.9 Rating",
                                  iconColor: const Color(0xFFF59E0B),
                                  bgColor: const Color(0xFFFFFBEB),
                                ),
                                const SizedBox(width: 8),
                                _StatChip(
                                  icon: Icons.people_rounded,
                                  label: "${widget.room.kapasitas} Tamu",
                                  iconColor: primaryColor,
                                  bgColor: primaryColor.withOpacity(0.08),
                                ),
                                const SizedBox(width: 8),
                                _StatChip(
                                  icon: Icons.check_circle_rounded,
                                  label: "Tersedia",
                                  iconColor: Colors.green.shade600,
                                  bgColor: Colors.green.shade50,
                                ),
                              ],
                            ),

                            const SizedBox(height: 20),
                            Container(height: 1, color: _AppColors.divider),
                            const SizedBox(height: 20),

                            // ── FASILITAS ──
                            _SectionTitle(
                                title: "Fasilitas Kamar",
                                icon: Icons.room_service_rounded,
                                color: primaryColor),
                            const SizedBox(height: 12),
                            _FasilitasChips(
                                fasilitas: widget.room.fasilitas,
                                primaryColor: primaryColor),

                            const SizedBox(height: 20),

                            // ── DESKRIPSI ──
                            _SectionTitle(
                                title: "Deskripsi",
                                icon: Icons.description_rounded,
                                color: primaryColor),
                            const SizedBox(height: 10),
                            Container(
                              padding: const EdgeInsets.all(14),
                              decoration: BoxDecoration(
                                color: _AppColors.bgCard,
                                borderRadius: BorderRadius.circular(14),
                                boxShadow: [
                                  BoxShadow(
                                    color: Colors.black.withOpacity(0.04),
                                    blurRadius: 8,
                                    offset: const Offset(0, 2),
                                  ),
                                ],
                              ),
                              child: Text(
                                widget.room.deskripsi,
                                style: const TextStyle(
                                  fontSize: 14,
                                  color: _AppColors.textMuted,
                                  height: 1.65,
                                ),
                                textAlign: TextAlign.justify,
                              ),
                            ),

                            const SizedBox(height: 20),
                            Container(height: 1, color: _AppColors.divider),
                            const SizedBox(height: 20),

                            // ── ULASAN ──
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                _SectionTitle(
                                    title: "Ulasan Tamu",
                                    icon: Icons.rate_review_rounded,
                                    color: primaryColor),
                                GestureDetector(
                                  onTap: _refreshReviews,
                                  child: Container(
                                    padding: const EdgeInsets.all(8),
                                    decoration: BoxDecoration(
                                      color: primaryColor.withOpacity(0.08),
                                      shape: BoxShape.circle,
                                    ),
                                    child: Icon(Icons.refresh_rounded,
                                        color: primaryColor, size: 18),
                                  ),
                                ),
                              ],
                            ),

                            const SizedBox(height: 12),

                            FutureBuilder<Map<String, dynamic>>(
                              future: _reviewData,
                              builder: (context, snapshot) {
                                if (snapshot.connectionState ==
                                    ConnectionState.waiting) {
                                  return Center(
                                    child: Padding(
                                      padding: const EdgeInsets.all(20),
                                      child: CircularProgressIndicator(
                                          color: primaryColor),
                                    ),
                                  );
                                }

                                final List<dynamic> reviews =
                                    snapshot.data?['data'] ?? [];

                                if (reviews.isEmpty) {
                                  return Container(
                                    padding: const EdgeInsets.all(20),
                                    decoration: BoxDecoration(
                                      color: _AppColors.bgCard,
                                      borderRadius: BorderRadius.circular(14),
                                    ),
                                    child: const Row(
                                      mainAxisAlignment: MainAxisAlignment.center,
                                      children: [
                                        Icon(Icons.rate_review_outlined,
                                            color: _AppColors.textHint, size: 20),
                                        SizedBox(width: 8),
                                        Text(
                                          "Belum ada ulasan untuk kamar ini.",
                                          style: TextStyle(
                                              color: _AppColors.textMuted,
                                              fontStyle: FontStyle.italic,
                                              fontSize: 13),
                                        ),
                                      ],
                                    ),
                                  );
                                }

                                return ListView.separated(
                                  shrinkWrap: true,
                                  physics: const NeverScrollableScrollPhysics(),
                                  itemCount: reviews.length,
                                  separatorBuilder: (_, __) =>
                                      const SizedBox(height: 10),
                                  itemBuilder: (context, index) {
                                    final rev = reviews[index];
                                    final int rating =
                                        (rev['rating'] ?? 0) as int;
                                    return _ReviewCard(
                                      namaUser: rev['nama_user'] ??
                                          "Verified Guest",
                                      komentar: rev['komentar'] ?? "",
                                      rating: rating,
                                      primaryColor: primaryColor,
                                      accentColor: accentColor,
                                    );
                                  },
                                );
                              },
                            ),

                            // Ruang untuk bottom bar agar tidak tertutup
                            const SizedBox(height: 110),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ],
          ),

          // ── BOTTOM BAR ──
          Positioned(
            bottom: 0,
            left: 0,
            right: 0,
            child: _BottomBookingBar(
              room: widget.room,
              primaryColor: primaryColor,
              accentColor: accentColor,
              formatPrice: _formatPrice,
            ),
          ),
        ],
      ),
    );
  }
}

// ─────────────────────────────────────────────
//  HEADER (style home_screen + back button)
// ─────────────────────────────────────────────
class _RoomDetailHeader extends StatelessWidget {
  final double       topPadding;
  final String       roomName;
  final Color        primaryColor;
  final Color        accentColor;
  final VoidCallback onBack;

  const _RoomDetailHeader({
    required this.topPadding,
    required this.roomName,
    required this.primaryColor,
    required this.accentColor,
    required this.onBack,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      padding: EdgeInsets.only(
          top: topPadding + 16, left: 20, right: 20, bottom: 24),
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
          bottomLeft:  Radius.circular(36),
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
          // TOP ROW
          Row(
            children: [
              // Back button
              GestureDetector(
                onTap: onBack,
                child: Container(
                  width: 34,
                  height: 34,
                  decoration: BoxDecoration(
                    color: Colors.white.withOpacity(0.15),
                    shape: BoxShape.circle,
                  ),
                  child: const Icon(Icons.arrow_back_ios_new_rounded,
                      color: Colors.white70, size: 16),
                ),
              ),
              const SizedBox(width: 10),
              // Logo
              _PurnamaLogo(),
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
            ],
          ),

          const SizedBox(height: 18),

          // Judul
          Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(Icons.hotel_rounded, color: accentColor, size: 20),
              const SizedBox(width: 8),
              Flexible(
                child: Text(
                  roomName,
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 20,
                    fontWeight: FontWeight.w800,
                    letterSpacing: 0.5,
                  ),
                  textAlign: TextAlign.center,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}

// ─────────────────────────────────────────────
//  LOGO PURNAMA
// ─────────────────────────────────────────────
class _PurnamaLogo extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
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
          boxShadow: [
            BoxShadow(
              color: _AppColors.gold.withOpacity(0.3),
              blurRadius: 6,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: const Center(
          child: Text(
            "P",
            style: TextStyle(
              color: _AppColors.gold,
              fontWeight: FontWeight.w900,
              fontSize: 18,
            ),
          ),
        ),
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
        decoration: BoxDecoration(
          color: Colors.white.withOpacity(0.12),
          shape: BoxShape.circle,
        ),
        child: Icon(icon, color: Colors.white70, size: 18),
      ),
    );
  }
}

// ─────────────────────────────────────────────
//  SECTION TITLE
// ─────────────────────────────────────────────
class _SectionTitle extends StatelessWidget {
  final String   title;
  final IconData icon;
  final Color    color;
  const _SectionTitle(
      {required this.title, required this.icon, required this.color});

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        Container(
          padding: const EdgeInsets.all(6),
          decoration: BoxDecoration(
            color: color.withOpacity(0.1),
            borderRadius: BorderRadius.circular(8),
          ),
          child: Icon(icon, color: color, size: 16),
        ),
        const SizedBox(width: 8),
        Text(
          title,
          style: TextStyle(
            fontSize: 16,
            fontWeight: FontWeight.bold,
            color: color,
          ),
        ),
      ],
    );
  }
}

// ─────────────────────────────────────────────
//  STAT CHIP
// ─────────────────────────────────────────────
class _StatChip extends StatelessWidget {
  final IconData icon;
  final String   label;
  final Color    iconColor;
  final Color    bgColor;
  const _StatChip({
    required this.icon,
    required this.label,
    required this.iconColor,
    required this.bgColor,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
      decoration: BoxDecoration(
        color: bgColor,
        borderRadius: BorderRadius.circular(20),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 13, color: iconColor),
          const SizedBox(width: 4),
          Text(
            label,
            style: TextStyle(
                fontSize: 11,
                fontWeight: FontWeight.w600,
                color: iconColor),
          ),
        ],
      ),
    );
  }
}

// ─────────────────────────────────────────────
//  FASILITAS CHIPS
// ─────────────────────────────────────────────
class _FasilitasChips extends StatelessWidget {
  final String fasilitas;
  final Color  primaryColor;
  const _FasilitasChips(
      {required this.fasilitas, required this.primaryColor});

  @override
  Widget build(BuildContext context) {
    final List<String> items = fasilitas
        .split(RegExp(r'[,;]'))
        .map((e) => e.trim())
        .where((e) => e.isNotEmpty)
        .toList();

    if (items.isEmpty) {
      return Text(fasilitas,
          style: const TextStyle(
              color: _AppColors.textMuted, fontSize: 14));
    }

    return Wrap(
      spacing: 8,
      runSpacing: 8,
      children: items.map((item) {
        return Container(
          padding:
              const EdgeInsets.symmetric(horizontal: 12, vertical: 7),
          decoration: BoxDecoration(
            color: primaryColor.withOpacity(0.08),
            borderRadius: BorderRadius.circular(20),
            border: Border.all(color: primaryColor.withOpacity(0.2)),
          ),
          child: Row(
            mainAxisSize: MainAxisSize.min,
            children: [
              Icon(Icons.check_circle_rounded,
                  size: 12, color: primaryColor),
              const SizedBox(width: 5),
              Text(
                item,
                style: TextStyle(
                    fontSize: 12,
                    color: primaryColor,
                    fontWeight: FontWeight.w500),
              ),
            ],
          ),
        );
      }).toList(),
    );
  }
}

// ─────────────────────────────────────────────
//  REVIEW CARD
// ─────────────────────────────────────────────
class _ReviewCard extends StatelessWidget {
  final String namaUser;
  final String komentar;
  final int    rating;
  final Color  primaryColor;
  final Color  accentColor;

  const _ReviewCard({
    required this.namaUser,
    required this.komentar,
    required this.rating,
    required this.primaryColor,
    required this.accentColor,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: _AppColors.bgCard,
        borderRadius: BorderRadius.circular(14),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.05),
            blurRadius: 8,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              // Avatar inisial
              Container(
                width: 38,
                height: 38,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  gradient: LinearGradient(
                    colors: [
                      primaryColor,
                      primaryColor.withOpacity(0.7),
                    ],
                  ),
                ),
                child: Center(
                  child: Text(
                    namaUser.isNotEmpty
                        ? namaUser[0].toUpperCase()
                        : "G",
                    style: const TextStyle(
                        color: Colors.white,
                        fontWeight: FontWeight.bold,
                        fontSize: 16),
                  ),
                ),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      namaUser,
                      style: const TextStyle(
                          fontWeight: FontWeight.bold,
                          fontSize: 13,
                          color: _AppColors.textDark),
                    ),
                    const SizedBox(height: 3),
                    Row(
                      children: List.generate(5, (i) {
                        return Icon(
                          i < rating
                              ? Icons.star_rounded
                              : Icons.star_border_rounded,
                          color: const Color(0xFFF59E0B),
                          size: 14,
                        );
                      }),
                    ),
                  ],
                ),
              ),
              // Badge rating
              Container(
                padding: const EdgeInsets.symmetric(
                    horizontal: 8, vertical: 4),
                decoration: BoxDecoration(
                  color: accentColor.withOpacity(0.15),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Text(
                  "$rating/5",
                  style: TextStyle(
                      fontSize: 11,
                      fontWeight: FontWeight.bold,
                      color: accentColor),
                ),
              ),
            ],
          ),
          const SizedBox(height: 10),
          Text(
            komentar,
            style: const TextStyle(
              fontSize: 13,
              color: _AppColors.textMuted,
              height: 1.5,
            ),
          ),
        ],
      ),
    );
  }
}

// ─────────────────────────────────────────────
//  BOTTOM BOOKING BAR
// ─────────────────────────────────────────────
class _BottomBookingBar extends StatelessWidget {
  final RoomType room;
  final Color    primaryColor;
  final Color    accentColor;
  final String Function(double) formatPrice;

  const _BottomBookingBar({
    required this.room,
    required this.primaryColor,
    required this.accentColor,
    required this.formatPrice,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.fromLTRB(20, 14, 20, 24),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: const BorderRadius.only(
          topLeft:  Radius.circular(24),
          topRight: Radius.circular(24),
        ),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.10),
            blurRadius: 20,
            offset: const Offset(0, -4),
          ),
        ],
      ),
      child: Row(
        children: [
          // Harga ringkas di kiri
          Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text("Mulai dari",
                  style: TextStyle(
                      fontSize: 11, color: _AppColors.textMuted)),
              Text(
                "Rp ${formatPrice(room.hargaAkhir)}",
                style: TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.w800,
                  color: primaryColor,
                ),
              ),
              const Text("/malam",
                  style: TextStyle(
                      fontSize: 10, color: _AppColors.textMuted)),
            ],
          ),
          const SizedBox(width: 16),
          // Tombol Book Now — sisa lebar
          Expanded(
            child: GestureDetector(
              onTap: () => Navigator.push(
                context,
                MaterialPageRoute(
                    builder: (_) => BookingScreen(room: room)),
              ),
              child: Container(
                padding: const EdgeInsets.symmetric(vertical: 14),
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    colors: [
                      primaryColor,
                      primaryColor.withOpacity(0.85),
                    ],
                  ),
                  borderRadius: BorderRadius.circular(14),
                  boxShadow: [
                    BoxShadow(
                      color: primaryColor.withOpacity(0.4),
                      blurRadius: 10,
                      offset: const Offset(0, 4),
                    ),
                  ],
                ),
                child: const Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Icon(Icons.calendar_month_rounded,
                        color: Colors.white, size: 18),
                    SizedBox(width: 8),
                    Text(
                      "PESAN SEKARANG",
                      style: TextStyle(
                        color: Colors.white,
                        fontWeight: FontWeight.w800,
                        fontSize: 15,
                        letterSpacing: 0.5,
                      ),
                    ),
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