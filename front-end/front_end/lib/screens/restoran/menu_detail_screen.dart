import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/cart_provider.dart';
import '../../providers/event_provider.dart';
import '../../services/api_services.dart';
import 'menu_resto.dart';
import 'checkout_screen.dart';
import 'cart_screen.dart';
import '../notification/notification_screen.dart';
import '../event/event_header.dart';
import 'package:shared_preferences/shared_preferences.dart';

class MenuDetailScreen extends StatefulWidget {
  final MenuResto menu;
  const MenuDetailScreen({super.key, required this.menu});

  @override
  State<MenuDetailScreen> createState() => _MenuDetailScreenState();
}

class _MenuDetailScreenState extends State<MenuDetailScreen> {
  int _localQuantity = 1;
  late Future<Map<String, dynamic>> _reviewData;

  @override
  void initState() {
    super.initState();
    final cartProvider = Provider.of<CartProvider>(context, listen: false);
    if (cartProvider.itemQuantities.containsKey(widget.menu.id)) {
      _localQuantity = cartProvider.itemQuantities[widget.menu.id]!;
    }
    _reviewData = ApiServices.getRestoReviews(widget.menu.id);
  }

  void _addToCart() async {
    final prefs = await SharedPreferences.getInstance();
    int? userId = prefs.getInt('user_id');

    if (userId == null || userId == 0) {
        if (mounted) {
            showDialog(
                context: context,
                builder: (context) => AlertDialog(
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                    contentPadding: const EdgeInsets.fromLTRB(24, 24, 24, 0),
                    content: Column(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                            Container(
                                padding: const EdgeInsets.all(16),
                                decoration: BoxDecoration(
                                    color: Colors.red.shade50,
                                    shape: BoxShape.circle,
                                ),
                                child: Icon(Icons.lock_outline_rounded, color: Colors.red.shade400, size: 40),
                            ),
                            const SizedBox(height: 16),
                            const Text(
                                "Login Diperlukan",
                                style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
                                textAlign: TextAlign.center,
                            ),
                            const SizedBox(height: 8),
                            const Text(
                                "Silakan login terlebih dahulu untuk memesan menu.",
                                style: TextStyle(color: Colors.grey, fontSize: 13),
                                textAlign: TextAlign.center,
                            ),
                            const SizedBox(height: 24),
                        ],
                    ),
                    actions: [
                        SizedBox(
                            width: double.infinity,
                            child: ElevatedButton(
                                style: ElevatedButton.styleFrom(
                                    backgroundColor: const Color(0xFF00197D),
                                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                                ),
                                onPressed: () => Navigator.pop(context),
                                child: const Text("Mengerti", style: TextStyle(color: Colors.white)),
                            ),
                        ),
                    ],
                ),
            );
        }
        return;
    }

    final cartProvider = Provider.of<CartProvider>(context, listen: false);
    cartProvider.setQuantity(widget.menu, _localQuantity);
    ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
            content: Text("${widget.menu.namaMenu} diperbarui di keranjang"),
            backgroundColor: Colors.green,
            behavior: SnackBarBehavior.floating,
            duration: const Duration(seconds: 1),
        ),
    );
}

  @override
  Widget build(BuildContext context) {
    final cartProvider = context.watch<CartProvider>();
    final eventProvider = context.watch<EventProvider>();
    final primaryColor = eventProvider.primaryColor;
    final secondaryColor = eventProvider.secondaryColor;
    final topPadding = MediaQuery.of(context).padding.top;

    final bool hasPromo =
        widget.menu.promoAktif != null &&
        (widget.menu.hargaAkhir < widget.menu.hargaAsli);

    return Scaffold(
      backgroundColor: Colors.grey.shade50,
      body: Column(
        children: [
          // ── HEADER ──
          _buildHeader(
            context: context,
            primaryColor: primaryColor,
            secondaryColor: secondaryColor,
            topPadding: topPadding,
            cartProvider: cartProvider,
          ),

          const EventHeader(),

          // ── KONTEN DETAIL ──
          Expanded(
            child: SingleChildScrollView(
              padding: const EdgeInsets.only(bottom: 110),
              child: Column(
                children: [
                  _buildMenuImage(),
                  const SizedBox(height: 4),
                  _buildInfoCard(
                    primaryColor: primaryColor,
                    hasPromo: hasPromo,
                  ),
                  const SizedBox(height: 4),
                  _buildReviewSection(primaryColor),
                  const SizedBox(height: 8),
                ],
              ),
            ),
          ),
        ],
      ),
      bottomSheet: _buildBottomAction(primaryColor, cartProvider),
    );
  }

  // ── HEADER ──────────────────────────────────────────────────────────────────

  Widget _buildHeader({
    required BuildContext context,
    required Color primaryColor,
    required Color secondaryColor,
    required double topPadding,
    required CartProvider cartProvider,
  }) {
    return Container(
      width: double.infinity,
      padding: EdgeInsets.only(
        top: topPadding + 12,
        left: 16,
        right: 16,
        bottom: 22,
      ),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [
            primaryColor,
            primaryColor.withOpacity(0.88),
            secondaryColor.withOpacity(0.72),
          ],
        ),
        borderRadius: const BorderRadius.only(
          bottomLeft: Radius.circular(32),
          bottomRight: Radius.circular(32),
        ),
        boxShadow: [
          BoxShadow(
            color: primaryColor.withOpacity(0.28),
            blurRadius: 14,
            offset: const Offset(0, 6),
          ),
        ],
      ),
      child: Column(
        children: [
          Row(
            children: [
              // Tombol back
              _CircleIconButton(
                icon: Icons.arrow_back_ios_new_rounded,
                onTap: () => Navigator.pop(context),
              ),
              const SizedBox(width: 10),

              // Logo
              _PurnamaLogo(
                primaryColor: primaryColor,
                secondaryColor: secondaryColor,
              ),
              const SizedBox(width: 10),

              // Brand text
              const Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      "Hotel & Restoran",
                      style: TextStyle(
                        color: Colors.white60,
                        fontSize: 9,
                        letterSpacing: 1.4,
                      ),
                    ),
                    Text(
                      "PURNAMA BALIGE",
                      style: TextStyle(
                        color: Colors.white,
                        fontSize: 13,
                        fontWeight: FontWeight.w800,
                        letterSpacing: 0.4,
                      ),
                    ),
                  ],
                ),
              ),

              // Keranjang
              Stack(
                clipBehavior: Clip.none,
                children: [
                  _CircleIconButton(
                    icon: Icons.shopping_bag_outlined,
                    onTap: () => Navigator.push(
                      context,
                      MaterialPageRoute(builder: (_) => const CartScreen()),
                    ),
                  ),
                  if (cartProvider.totalItems > 0)
                    Positioned(
                      top: -4,
                      right: -4,
                      child: Container(
                        width: 18,
                        height: 18,
                        decoration: const BoxDecoration(
                          color: Colors.red,
                          shape: BoxShape.circle,
                        ),
                        child: Center(
                          child: Text(
                            "${cartProvider.totalItems}",
                            style: const TextStyle(
                              color: Colors.white,
                              fontSize: 9,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ),
                      ),
                    ),
                ],
              ),
              const SizedBox(width: 8),

              // Notifikasi
              _CircleIconButton(
                icon: Icons.notifications_none_rounded,
                onTap: () => Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (_) => const NotificationScreen(),
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 14),
          const Text(
            "Detail Menu",
            style: TextStyle(
              color: Colors.white,
              fontSize: 17,
              fontWeight: FontWeight.bold,
              letterSpacing: 0.3,
            ),
          ),
        ],
      ),
    );
  }

  // ── GAMBAR MENU ─────────────────────────────────────────────────────────────

  // ── GAMBAR MENU ─────────────────────────────────────────────────────────────

  Widget _buildMenuImage() {
final String storageUrl = "https://purnama-hotel.duckdns.org/storage";

final String? finalImageUrl = widget.menu.fotoMenu != null
    ? (widget.menu.fotoMenu!.startsWith('http')
        ? widget.menu.fotoMenu!                        // ✅ URL internet — pakai langsung
        : "$storageUrl/${widget.menu.fotoMenu}")        // ✅ Path lokal — tambah storageUrl
    : null;



    return Container(
      height: 240,
      width: double.infinity,
      margin: const EdgeInsets.fromLTRB(16, 16, 16, 0),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(24),
        color: Colors.grey.shade200,
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.08),
            blurRadius: 12,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(24),
        child: finalImageUrl != null
            ? Image.network(
                finalImageUrl,
                fit: BoxFit.cover,
                errorBuilder: (_, __, ___) => Container(
                  color: const Color(0xFFF3F4F6),
                  child: const Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Icon(Icons.fastfood, size: 40, color: Colors.grey),
                      SizedBox(height: 4),
                      Text(
                        "No Image",
                        style: TextStyle(fontSize: 8, color: Colors.grey),
                      ),
                    ],
                  ),
                ),
                loadingBuilder: (_, child, progress) {
                  if (progress == null) return child;
                  return Container(
                    color: const Color(0xFFF3F4F6),
                    child: Center(
                      child: CircularProgressIndicator(
                        strokeWidth: 2,
                        value: progress.expectedTotalBytes != null
                            ? progress.cumulativeBytesLoaded /
                                progress.expectedTotalBytes!
                            : null,
                      ),
                    ),
                  );
                },
              )
            : const Center(
                child: Icon(
                  Icons.fastfood_rounded,
                  size: 72,
                  color: Colors.grey,
                ),
              ),
      ),
    );
  }
  // ── KARTU INFO & DESKRIPSI ──────────────────────────────────────────────────

  Widget _buildInfoCard({
    required Color primaryColor,
    required bool hasPromo,
  }) {
    return Container(
      margin: const EdgeInsets.fromLTRB(16, 12, 16, 0),
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 8,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Nama + badge promo
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Expanded(
                child: Text(
                  widget.menu.namaMenu,
                  style: const TextStyle(
                    fontSize: 20,
                    fontWeight: FontWeight.bold,
                    height: 1.3,
                  ),
                ),
              ),
              if (hasPromo) ...[
                const SizedBox(width: 10),
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 10,
                    vertical: 5,
                  ),
                  decoration: BoxDecoration(
                    color: Colors.red.shade600,
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: Text(
                    widget.menu.promoAktif!,
                    style: const TextStyle(
                      color: Colors.white,
                      fontSize: 10,
                      fontWeight: FontWeight.bold,
                      letterSpacing: 0.3,
                    ),
                  ),
                ),
              ],
            ],
          ),
          const SizedBox(height: 10),

          // Harga
          Row(
            crossAxisAlignment: CrossAxisAlignment.end,
            children: [
              Text(
                "Rp ${_formatNumber(widget.menu.hargaAkhir)}",
                style: TextStyle(
                  color: primaryColor,
                  fontSize: 22,
                  fontWeight: FontWeight.bold,
                ),
              ),
              if (hasPromo) ...[
                const SizedBox(width: 10),
                Padding(
                  padding: const EdgeInsets.only(bottom: 2),
                  child: Text(
                    "Rp ${_formatNumber(widget.menu.hargaAsli)}",
                    style: TextStyle(
                      color: Colors.grey.shade400,
                      decoration: TextDecoration.lineThrough,
                      fontSize: 13,
                    ),
                  ),
                ),
              ],
            ],
          ),

          const Padding(
            padding: EdgeInsets.symmetric(vertical: 16),
            child: Divider(height: 1),
          ),

          // Deskripsi
          const Text(
            "Deskripsi",
            style: TextStyle(fontWeight: FontWeight.w700, fontSize: 15),
          ),
          const SizedBox(height: 8),
          Text(
            widget.menu.deskripsi,
            style: TextStyle(
              color: Colors.grey.shade600,
              fontSize: 13,
              height: 1.6,
            ),
          ),
        ],
      ),
    );
  }

  // ── SECTION ULASAN ──────────────────────────────────────────────────────────

  Widget _buildReviewSection(Color primaryColor) {
    return FutureBuilder<Map<String, dynamic>>(
      future: _reviewData,
      builder: (context, snapshot) {
        if (!snapshot.hasData) return const SizedBox.shrink();
        final reviews = snapshot.data?['data'] as List? ?? [];

        return Container(
          margin: const EdgeInsets.fromLTRB(16, 12, 16, 0),
          padding: const EdgeInsets.all(20),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(24),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.04),
                blurRadius: 8,
                offset: const Offset(0, 2),
              ),
            ],
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  const Icon(Icons.star_rounded, color: Colors.amber, size: 18),
                  const SizedBox(width: 6),
                  Text(
                    "Ulasan Pelanggan (${reviews.length})",
                    style: const TextStyle(
                      fontWeight: FontWeight.w700,
                      fontSize: 15,
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 12),
              if (reviews.isEmpty)
                Center(
                  child: Padding(
                    padding: const EdgeInsets.symmetric(vertical: 16),
                    child: Column(
                      children: [
                        Icon(
                          Icons.chat_bubble_outline_rounded,
                          size: 40,
                          color: Colors.grey.shade300,
                        ),
                        const SizedBox(height: 8),
                        Text(
                          "Belum ada ulasan.",
                          style: TextStyle(
                            color: Colors.grey.shade400,
                            fontSize: 13,
                          ),
                        ),
                      ],
                    ),
                  ),
                )
              else
                ...reviews.asMap().entries.map((entry) {
                  final rev = entry.value;
                  final isLast = entry.key == reviews.length - 1;
                  return Column(
                    children: [
                      _ReviewTile(
                        namaUser: rev['nama_user'] ?? '',
                        komentar: rev['komentar'] ?? '',
                        rating: (rev['rating'] ?? 0).toDouble(),
                        primaryColor: primaryColor,
                      ),
                      if (!isLast)
                        Divider(
                          height: 20,
                          color: Colors.grey.shade100,
                        ),
                    ],
                  );
                }),
            ],
          ),
        );
      },
    );
  }

  // ── BOTTOM ACTION ───────────────────────────────────────────────────────────

  Widget _buildBottomAction(Color primaryColor, CartProvider cartProvider) {
    return Container(
      padding: const EdgeInsets.fromLTRB(16, 14, 16, 20),
      decoration: BoxDecoration(
        color: Colors.white,
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.08),
            blurRadius: 12,
            offset: const Offset(0, -4),
          ),
        ],
      ),
      child: Row(
        children: [
          // Quantity selector
          Container(
            decoration: BoxDecoration(
              color: Colors.grey.shade100,
              borderRadius: BorderRadius.circular(14),
            ),
            child: Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                _QtyButton(
                  icon: Icons.remove_rounded,
                  onTap: () => setState(() {
                    if (_localQuantity > 1) _localQuantity--;
                  }),
                ),
                SizedBox(
                  width: 32,
                  child: Text(
                    "$_localQuantity",
                    textAlign: TextAlign.center,
                    style: const TextStyle(
                      fontWeight: FontWeight.bold,
                      fontSize: 15,
                    ),
                  ),
                ),
                _QtyButton(
                  icon: Icons.add_rounded,
                  onTap: () => setState(() => _localQuantity++),
                ),
              ],
            ),
          ),
          const SizedBox(width: 14),

          // Tambah ke keranjang
          Expanded(
            child: ElevatedButton.icon(
              onPressed: _addToCart,
              icon: const Icon(
                Icons.shopping_bag_outlined,
                size: 18,
                color: Colors.white,
              ),
              label: const Text(
                "TAMBAH KE KERANJANG",
                style: TextStyle(
                  color: Colors.white,
                  fontWeight: FontWeight.bold,
                  fontSize: 13,
                  letterSpacing: 0.4,
                ),
              ),
              style: ElevatedButton.styleFrom(
                backgroundColor: primaryColor,
                padding: const EdgeInsets.symmetric(vertical: 15),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(14),
                ),
                elevation: 0,
              ),
            ),
          ),
        ],
      ),
    );
  }

  // ── HELPER ──────────────────────────────────────────────────────────────────

  String _formatNumber(double value) {
    final intVal = value.toInt();
    final str = intVal.toString();
    final buffer = StringBuffer();
    for (int i = 0; i < str.length; i++) {
      if (i > 0 && (str.length - i) % 3 == 0) buffer.write('.');
      buffer.write(str[i]);
    }
    return buffer.toString();
  }
}

// ── REVIEW TILE ──────────────────────────────────────────────────────────────

class _ReviewTile extends StatelessWidget {
  final String namaUser;
  final String komentar;
  final double rating;
  final Color primaryColor;

  const _ReviewTile({
    required this.namaUser,
    required this.komentar,
    required this.rating,
    required this.primaryColor,
  });

  @override
  Widget build(BuildContext context) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        // Avatar inisial
        Container(
          width: 36,
          height: 36,
          decoration: BoxDecoration(
            color: primaryColor.withOpacity(0.12),
            shape: BoxShape.circle,
          ),
          child: Center(
            child: Text(
              namaUser.isNotEmpty ? namaUser[0].toUpperCase() : "?",
              style: TextStyle(
                color: primaryColor,
                fontWeight: FontWeight.bold,
                fontSize: 14,
              ),
            ),
          ),
        ),
        const SizedBox(width: 10),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Expanded(
                    child: Text(
                      namaUser,
                      style: const TextStyle(
                        fontWeight: FontWeight.w600,
                        fontSize: 13,
                      ),
                    ),
                  ),
                  Row(
                    children: [
                      const Icon(
                        Icons.star_rounded,
                        color: Colors.amber,
                        size: 13,
                      ),
                      const SizedBox(width: 2),
                      Text(
                        rating.toStringAsFixed(1),
                        style: const TextStyle(
                          fontSize: 12,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ],
                  ),
                ],
              ),
              const SizedBox(height: 3),
              Text(
                komentar,
                style: TextStyle(
                  fontSize: 12,
                  color: Colors.grey.shade600,
                  height: 1.5,
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }
}

// ── QUANTITY BUTTON ──────────────────────────────────────────────────────────

class _QtyButton extends StatelessWidget {
  final IconData icon;
  final VoidCallback onTap;

  const _QtyButton({required this.icon, required this.onTap});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        width: 38,
        height: 38,
        decoration: const BoxDecoration(
          color: Colors.transparent,
          borderRadius: BorderRadius.all(Radius.circular(14)),
        ),
        child: Icon(icon, size: 18),
      ),
    );
  }
}

// ── CIRCLE ICON BUTTON ────────────────────────────────────────────────────────

class _CircleIconButton extends StatelessWidget {
  final IconData icon;
  final VoidCallback onTap;

  const _CircleIconButton({required this.icon, required this.onTap});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(8),
        decoration: BoxDecoration(
          color: Colors.white.withOpacity(0.18),
          shape: BoxShape.circle,
        ),
        child: Icon(icon, color: Colors.white, size: 18),
      ),
    );
  }
}

// ── PURNAMA LOGO ─────────────────────────────────────────────────────────────

class _PurnamaLogo extends StatelessWidget {
  final Color primaryColor;
  final Color secondaryColor;

  const _PurnamaLogo({
    required this.primaryColor,
    required this.secondaryColor,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      width: 38,
      height: 38,
      decoration: BoxDecoration(
        shape: BoxShape.circle,
        border: Border.all(color: Colors.white.withOpacity(0.8), width: 1.5),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.1),
            blurRadius: 4,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(20),
        child: Image.asset(
          'assets/icons/icon-purnama.png',
          fit: BoxFit.cover,
          errorBuilder: (context, error, stackTrace) {
            return Container(
              color: primaryColor,
              child: const Center(
                child: Text(
                  "P",
                  style: TextStyle(
                    color: Colors.white,
                    fontWeight: FontWeight.bold,
                    fontSize: 18,
                  ),
                ),
              ),
            );
          },
        ),
      ),
    );
  }
}