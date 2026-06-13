import 'dart:async';
import 'package:flutter/material.dart';
import '../colors/login_constants.dart';
import '../../services/api_services.dart';

// ==========================================
// 1. SEARCH BAR FUNGSIONAL
// ==========================================
class HomeSearchBar extends StatelessWidget {
  final bool isLocked;
  final VoidCallback onLockedTap;
  final Function(String) onSearch;
  final Color iconColor;

  const HomeSearchBar({
    super.key,
    required this.isLocked,
    required this.onLockedTap,
    required this.onSearch,
    this.iconColor = AppTheme.primaryBlue,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 25),
      padding: const EdgeInsets.symmetric(horizontal: 15),
      height: 55,
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: const [BoxShadow(color: Colors.black12, blurRadius: 15, offset: Offset(0, 8))],
      ),
      child: TextField(
        onTap: isLocked ? onLockedTap : null,
        readOnly: isLocked,
        onSubmitted: onSearch,
        decoration: InputDecoration(
          hintText: "Cari kamar atau menu resto...",
          hintStyle: const TextStyle(color: Colors.grey, fontSize: 14),
          prefixIcon: Icon(Icons.search_rounded, color: iconColor),
          border: InputBorder.none,
          contentPadding: const EdgeInsets.symmetric(vertical: 15),
        ),
      ),
    );
  }
}

// ==========================================
// 2. AUTO-SLIDING PROMO SLIDER (FIXED CLASS)
// ==========================================
class AutoPromoSlider extends StatefulWidget {
  const AutoPromoSlider({super.key});
  @override
  State<AutoPromoSlider> createState() => _AutoPromoSliderState();
}

class _AutoPromoSliderState extends State<AutoPromoSlider> {
  final PageController _pageController = PageController(viewportFraction: 0.85);
  int _currentPage = 0;
  Timer? _timer;

  final List<Map<String, String>> promos = [
    {"title": "DISKON 30% AKHIR PEKAN", "img": "https://images.unsplash.com/photo-1566073771259-6a8506099945?w=500"},
    {"title": "PROMO DINNER ROMANTIS", "img": "https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?w=500"},
    {"title": "VOUCHER PENGINAP BARU", "img": "https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=500"},
  ];

  @override
  void initState() {
    super.initState();
    // Precache promo images after first frame to avoid jank during page transitions
    WidgetsBinding.instance.addPostFrameCallback((_) {
      for (final p in promos) {
        precacheImage(NetworkImage(p['img']!), context);
      }
    });

    _timer = Timer.periodic(const Duration(seconds: 4), (Timer timer) {
      if (_currentPage < promos.length - 1) { _currentPage++; } else { _currentPage = 0; }
      if (_pageController.hasClients) {
        _pageController.animateToPage(_currentPage, duration: const Duration(milliseconds: 500), curve: Curves.easeInOut);
      }
    });
  }

  @override
  void dispose() { _timer?.cancel(); _pageController.dispose(); super.dispose(); }

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      height: 180,
      child: PageView.builder(
        controller: _pageController,
        itemCount: promos.length,
        itemBuilder: (context, index) {
          final promo = promos[index];
          return RepaintBoundary(
            child: Container(
              margin: const EdgeInsets.only(right: 15),
              child: ClipRRect(
                borderRadius: BorderRadius.circular(25),
                child: Stack(
                  fit: StackFit.expand,
                  children: [
                    // network image with loadingBuilder to avoid black frame while loading
                    Image.network(
                      promo['img']!,
                      fit: BoxFit.cover,
                      loadingBuilder: (context, child, loadingProgress) {
                        if (loadingProgress == null) return child;
                        return Container(color: Colors.grey.shade200);
                      },
                      errorBuilder: (context, error, stack) => Container(color: Colors.grey.shade300),
                    ),
                    Container(
                      decoration: BoxDecoration(
                        gradient: LinearGradient(begin: Alignment.topCenter, end: Alignment.bottomCenter, colors: [Colors.transparent, Colors.black.withAlpha(160)]),
                      ),
                      padding: const EdgeInsets.all(20),
                      alignment: Alignment.bottomLeft,
                      child: Text(promo['title']!, style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
                    ),
                  ],
                ),
              ),
            ),
          );
        },
      ),
    );
  }
}

// ==========================================
// 3. DYNAMIC REVIEW DARI DATABASE
// ==========================================
class DynamicReviewSection extends StatelessWidget {
  const DynamicReviewSection({super.key});

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Padding(
          padding: EdgeInsets.symmetric(horizontal: 25),
          child: Text("Ulasan Tamu", style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
        ),
        const SizedBox(height: 15),
        FutureBuilder<Map<String, dynamic>>(
          future: ApiServices.getHotelReviews(0), 
          builder: (context, snapshot) {
            if (snapshot.connectionState == ConnectionState.waiting) return const Center(child: CircularProgressIndicator());
            final reviews = snapshot.data?['data'] ?? [];
            if (reviews.isEmpty) return const SizedBox();

            return SizedBox(
              height: 150,
              child: ListView.builder(
                scrollDirection: Axis.horizontal,
                padding: const EdgeInsets.only(left: 25),
                itemCount: reviews.length,
                itemBuilder: (context, index) {
                  final rev = reviews[index];
                  return Container(
                    width: 280, margin: const EdgeInsets.only(right: 15, bottom: 10),
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(25), boxShadow: [BoxShadow(color: Colors.black.withAlpha(8), blurRadius: 10)], border: Border.all(color: Colors.grey.shade100)),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(children: List.generate(5, (s) => Icon(s < (rev['rating'] ?? 0) ? Icons.star_rounded : Icons.star_outline_rounded, color: AppTheme.goldAccent, size: 18))),
                        const SizedBox(height: 10),
                        Expanded(child: Text("\"${rev['komentar']}\"", maxLines: 3, style: const TextStyle(fontSize: 13, fontStyle: FontStyle.italic))),
                        const Text("- Tamu Terverifikasi", style: TextStyle(fontSize: 10, color: Colors.grey, fontWeight: FontWeight.bold)),
                      ],
                    ),
                  );
                },
              ),
            );
          },
        ),
      ],
    );
  }
}

// ==========================================
// 4. ANIMATED NAVBAR (INTERAKTIF)
// ==========================================
class AnimatedNavBar extends StatelessWidget {
  final int currentIndex;
  final Function(int) onTap;
  final Color backgroundColor;
  final Color activeColor;
  final Color inactiveColor;

  const AnimatedNavBar({
    super.key,
    required this.currentIndex,
    required this.onTap,
    this.backgroundColor = AppTheme.primaryBlue,
    this.activeColor = AppTheme.goldAccent,
    this.inactiveColor = Colors.white38,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      height: 75,
      padding: const EdgeInsets.symmetric(horizontal: 10),
      decoration: BoxDecoration(
        color: backgroundColor,
        borderRadius: BorderRadius.circular(30),
        boxShadow: const [BoxShadow(color: Colors.black45, blurRadius: 20, offset: Offset(0, 10))],
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceAround,
        children: [
          _navItem(0, Icons.home_rounded, "Home"),
          _navItem(1, Icons.king_bed_rounded, "Hotel"),
          _navItem(2, Icons.restaurant_rounded, "Resto"),
          _navItem(3, Icons.receipt_long_rounded, "Riwayat"),
          _navItem(4, Icons.person_rounded, "Profil"),
        ],
      ),
    );
  }

  Widget _navItem(int index, IconData icon, String label) {
    bool active = currentIndex == index;
    return GestureDetector(
      onTap: () => onTap(index),
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 300),
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
        decoration: BoxDecoration(color: active ? activeColor.withAlpha(51) : Colors.transparent, borderRadius: BorderRadius.circular(20)),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(icon, color: active ? activeColor : inactiveColor, size: active ? 28 : 24),
            if (active) Text(label, style: TextStyle(color: activeColor.computeLuminance() > 0.5 ? Colors.black : Colors.white, fontSize: 10, fontWeight: FontWeight.bold)),
          ],
        ),
      ),
    );
  }
}