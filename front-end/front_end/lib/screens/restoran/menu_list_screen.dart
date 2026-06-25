// screens/restoran/menu_list_screen.dart

import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../services/api_services.dart';
import '../../providers/event_provider.dart';
import '../../providers/cart_provider.dart';
import 'menu_resto.dart';
import 'menu_detail_screen.dart';
import 'cart_screen.dart';
import '../event/event_header.dart';
import '../notification/notification_screen.dart';
import 'package:shared_preferences/shared_preferences.dart';

class _AppColors {
  static const Color navyDark  = Color(0xFF0C2D6B);
  static const Color navy      = Color(0xFF1A4A9E);
  static const Color gold      = Color(0xFFC9A227);
  static const Color bgPage    = Color(0xFFF2F4F8);
  static const Color bgCard    = Color(0xFFFFFFFF);
  static const Color textDark  = Color(0xFF1F2937);
  static const Color textMuted = Color(0xFF6B7280);
  static const Color textHint  = Color(0xFF9CA3AF);
}

class MenuListScreen extends StatefulWidget {
  final VoidCallback? onBack; // ✅ tambah ini

  const MenuListScreen({super.key, this.onBack}); // ✅ update constructor

  @override
  State<MenuListScreen> createState() => _MenuListScreenState();
}



class _MenuListScreenState extends State<MenuListScreen> {
  late Future<Map<String, dynamic>> _menuData;
  String _searchQuery      = '';
  String _selectedCategory = 'All';
  List<String> _categories = ['All'];

  @override
  void initState() {
    super.initState();
    _refreshMenus();
  }

  void _refreshMenus() {
    setState(() {
      _menuData = ApiServices.getRestaurantMenus().then((result) {
        if (result['success'] == true) {
          final List<dynamic> list = result['data'];
          final Set<String> cats = {'All'};
          for (var item in list) {
            final menu = MenuResto.fromJson(item);
            cats.add(menu.kategori);
          }
          if (mounted) setState(() => _categories = cats.toList());
        }
        return result;
      });
    });
  }

  List<MenuResto> _filterMenus(List<MenuResto> menus) {
    return menus.where((menu) {
      final matchesCategory = _selectedCategory == 'All' ||
          menu.kategori.toLowerCase() == _selectedCategory.toLowerCase();
      final matchesSearch = _searchQuery.isEmpty ||
          menu.namaMenu.toLowerCase().contains(_searchQuery.toLowerCase());
      return matchesCategory && matchesSearch;
    }).toList();
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
            style: TextStyle(color: _AppColors.gold, fontWeight: FontWeight.w900, fontSize: 18, letterSpacing: 0),
          ),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final topPadding   = MediaQuery.of(context).padding.top;
    final cartProvider = context.watch<CartProvider>();
    final ep           = context.watch<EventProvider>();
    final Color primaryColor =
        ep.eventCode != 'default' ? ep.primaryColor : _AppColors.navyDark;
    final Color accentColor =
        ep.eventCode != 'default' ? ep.secondaryColor : _AppColors.gold;
    final String eventName = ep.activeTheme['nama_event'] ?? '';

    return Scaffold(
      backgroundColor: _AppColors.bgPage,
      body: Column(
        children: [
          // ── HEADER TETAP (STICKY) ──
          _MenuHeader(
            topPadding: topPadding,
            primaryColor: primaryColor,
            accentColor: accentColor,
            cartProvider: cartProvider,
            searchQuery: _searchQuery,
            onSearchChanged: (v) => setState(() => _searchQuery = v),
            buildPurnamaLogo: _buildPurnamaLogo,
            onBack: widget.onBack ?? () { // ✅ tambah ini
              if (Navigator.canPop(context)) Navigator.pop(context);
            },
          ),

          // ── KATEGORI TETAP (STICKY) ──
          Container(
            color: _AppColors.bgPage,
            padding: const EdgeInsets.only(bottom: 8),
            child: _CategoryChips(
              categories: _categories,
              selected: _selectedCategory,
              primaryColor: primaryColor,
              onSelect: (cat) => setState(() => _selectedCategory = cat),
            ),
          ),

          // ── BODY (SCROLLABLE) ──
          Expanded(
            child: RefreshIndicator(
              onRefresh: () async => _refreshMenus(),
              color: primaryColor,
              child: FutureBuilder<Map<String, dynamic>>(
                future: _menuData,
                builder: (context, snapshot) {
                  if (snapshot.connectionState == ConnectionState.waiting) {
                    return Center(child: CircularProgressIndicator(color: primaryColor));
                  }

                  if (snapshot.hasError) {
                    return Center(
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          const Icon(Icons.error_outline, size: 48, color: Colors.grey),
                          const SizedBox(height: 16),
                          const Text("Gagal memuat menu.", style: TextStyle(color: _AppColors.textMuted)),
                          const SizedBox(height: 16),
                          ElevatedButton(
                            onPressed: _refreshMenus,
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
                  final List<MenuResto> allMenus = listJson.map((e) => MenuResto.fromJson(e)).toList();
                  final List<MenuResto> filteredMenus = _filterMenus(allMenus);

                  return CustomScrollView(
                    physics: const BouncingScrollPhysics(),
                    slivers: [
                      const SliverToBoxAdapter(child: EventHeader()),
                      if (ep.activeTheme['event_code'] != 'default')
                        SliverToBoxAdapter(
                          child: Container(
                            width: double.infinity,
                            margin: const EdgeInsets.fromLTRB(16, 0, 16, 8),
                            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
                            decoration: BoxDecoration(
                              color: primaryColor.withOpacity(0.1),
                              borderRadius: BorderRadius.circular(12),
                              border: Border.all(color: primaryColor.withOpacity(0.3)),
                            ),
                            child: Text(
                              "✨ Menu Eksklusif $eventName",
                              textAlign: TextAlign.center,
                              style: TextStyle(color: primaryColor, fontWeight: FontWeight.bold, fontSize: 13),
                            ),
                          ),
                        ),
                      SliverToBoxAdapter(
                        child: Padding(
                          padding: const EdgeInsets.fromLTRB(16, 12, 16, 4),
                          child: Text(
                            "${filteredMenus.length} menu ditemukan",
                            style: const TextStyle(fontSize: 12, color: _AppColors.textMuted),
                          ),
                        ),
                      ),
                      filteredMenus.isEmpty
                          ? const SliverFillRemaining(
                              child: Center(
                                child: Column(
                                  mainAxisAlignment: MainAxisAlignment.center,
                                  children: [
                                    Icon(Icons.fastfood_outlined, size: 60, color: _AppColors.textHint),
                                    SizedBox(height: 12),
                                    Text("Menu tidak ditemukan", style: TextStyle(color: _AppColors.textMuted, fontSize: 14)),
                                  ],
                                ),
                              ),
                            )
                          : SliverPadding(
                              padding: const EdgeInsets.fromLTRB(16, 4, 16, 120),
                              sliver: SliverGrid(
                                delegate: SliverChildBuilderDelegate(
                                  (context, index) {
                                    final menu = filteredMenus[index];
                                    return _MenuCard(
                                      menu: menu,
                                      primaryColor: primaryColor,
                                      accentColor: accentColor,
                                      cartProvider: cartProvider,
                                    );
                                  },
                                  childCount: filteredMenus.length,
                                ),
                                gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                                  crossAxisCount: 2,
                                  childAspectRatio: 0.62,
                                  crossAxisSpacing: 12,
                                  mainAxisSpacing: 16,
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
      floatingActionButton: cartProvider.totalItems > 0
          ? Container(
              margin: const EdgeInsets.only(bottom: 8),
              child: FloatingActionButton.extended(
                onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const CartScreen())),
                backgroundColor: primaryColor,
                elevation: 6,
                icon: const Icon(Icons.shopping_cart_rounded, color: Colors.white),
                label: Text(
                  "${cartProvider.totalItems} item  •  Rp ${_formatPrice(cartProvider.totalPrice)}",
                  style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
                ),
              ),
            )
          : null,
      floatingActionButtonLocation: FloatingActionButtonLocation.centerFloat,
    );
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
}

class _MenuHeader extends StatelessWidget {
  final double      topPadding;
  final Color       primaryColor;
  final Color       accentColor;
  final CartProvider cartProvider;
  final String      searchQuery;
  final ValueChanged<String> onSearchChanged;
  final Widget Function() buildPurnamaLogo;
  final VoidCallback onBack;

  const _MenuHeader({
    required this.topPadding,
    required this.primaryColor,
    required this.accentColor,
    required this.cartProvider,
    required this.searchQuery,
    required this.onSearchChanged,
    required this.buildPurnamaLogo,
    required this.onBack,
  });

  @override
  Widget build(BuildContext context) {
    return Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        Container(
          width: double.infinity,
          padding: EdgeInsets.only(
              top: topPadding + 16,
              left: 20,
              right: 20,
              bottom: 28),
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
              Row(
                children: [
                  // ── TOMBOL BACK ──
                  GestureDetector(
                    onTap: onBack,
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
                  const SizedBox(width: 10),
                  buildPurnamaLogo(),
                  const SizedBox(width: 10),
                  const Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text("Hotel & Restoran",
                          style: TextStyle(color: Colors.white60, fontSize: 9, letterSpacing: 1.2)),
                      Text("PURNAMA BALIGE",
                          style: TextStyle(color: Colors.white, fontSize: 13, fontWeight: FontWeight.w800, letterSpacing: 0.8)),
                    ],
                  ),
                  const Spacer(),
                  Stack(
                    clipBehavior: Clip.none,
                    children: [
                      _HeaderIconButton(
                        icon: Icons.shopping_bag_outlined,
                        onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const CartScreen())),
                      ),
                      if (cartProvider.totalItems > 0)
                        Positioned(
                          top: -4,
                          right: -4,
                          child: Container(
                            width: 18,
                            height: 18,
                            decoration: BoxDecoration(
                              color: accentColor,
                              shape: BoxShape.circle,
                              border: Border.all(color: primaryColor, width: 1.5),
                            ),
                            child: Center(
                              child: Text(
                                "${cartProvider.totalItems}",
                                style: const TextStyle(color: Colors.white, fontSize: 9, fontWeight: FontWeight.bold),
                              ),
                            ),
                          ),
                        ),
                    ],
                  ),
                  const SizedBox(width: 4),
                  _HeaderIconButton(
                    icon: Icons.notifications_none_rounded,
                    onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const NotificationScreen())),
                  ),
                ],
              ),
              const SizedBox(height: 18),
              Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.restaurant_menu_rounded, color: accentColor, size: 20),
                  const SizedBox(width: 8),
                  const Text(
                    "Menu Restoran",
                    style: TextStyle(color: Colors.white, fontSize: 20, fontWeight: FontWeight.w800, letterSpacing: 0.5),
                  ),
                ],
              ),
            ],
          ),
        ),
        Transform.translate(
          offset: const Offset(0, -14),
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16),
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
                        hintText: "Cari menu makanan atau minuman...",
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
        ),
      ],
    );
  }
}

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

class _CategoryChips extends StatelessWidget {
  final List<String>          categories;
  final String                selected;
  final Color                 primaryColor;
  final ValueChanged<String>  onSelect;

  const _CategoryChips({
    required this.categories,
    required this.selected,
    required this.primaryColor,
    required this.onSelect,
  });

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      height: 48,
      child: ListView.builder(
        scrollDirection: Axis.horizontal,
        padding: const EdgeInsets.symmetric(horizontal: 16),
        itemCount: categories.length,
        itemBuilder: (context, index) {
          final cat = categories[index];
          final isSelected = selected == cat;
          return GestureDetector(
            onTap: () => onSelect(cat),
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
                  cat,
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
    );
  }
}

class _MenuCard extends StatelessWidget {
  final MenuResto    menu;
  final Color        primaryColor;
  final Color        accentColor;
  final CartProvider cartProvider;

  const _MenuCard({
    required this.menu,
    required this.primaryColor,
    required this.accentColor,
    required this.cartProvider,
  });

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
    final bool hasPromo =
        menu.promoAktif != null && (menu.hargaAkhir < menu.hargaAsli);

    String finalImageUrl = menu.fotoMenu ?? "";
    
    if (finalImageUrl.isNotEmpty && !finalImageUrl.startsWith('http')) {
      finalImageUrl = "https://purnama-hotel.duckdns.org/storage/$finalImageUrl";
    }

    return GestureDetector(
      onTap: () => Navigator.push(context, MaterialPageRoute(builder: (context) => MenuDetailScreen(menu: menu))),
      child: Container(
        decoration: BoxDecoration(
          color: _AppColors.bgCard,
          borderRadius: BorderRadius.circular(20),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.07),
              blurRadius: 10,
              offset: const Offset(0, 4),
            ),
          ],
        ),
        clipBehavior: Clip.antiAlias,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Expanded(
              flex: 5,
              child: Stack(
                fit: StackFit.expand,
                children: [
                  Image.network(
                    finalImageUrl, 
                    fit: BoxFit.cover,
                    errorBuilder: (_, __, ___) => Container(
                      color: const Color(0xFFF3F4F6),
                      child: const Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Icon(Icons.fastfood, size: 40, color: Colors.grey),
                          SizedBox(height: 4),
                          Text("No Image", style: TextStyle(fontSize: 8, color: Colors.grey)),
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
                  if (hasPromo)
                    Positioned(
                      top: 8,
                      left: 8,
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                        decoration: BoxDecoration(color: Colors.red.shade600, borderRadius: BorderRadius.circular(8)),
                        child: const Text("PROMO", style: TextStyle(color: Colors.white, fontSize: 9, fontWeight: FontWeight.bold, letterSpacing: 0.5)),
                      ),
                    ),
                  Positioned(
                    top: 8,
                    right: 8,
                    child: Container(
                      padding: const EdgeInsets.symmetric(horizontal: 7, vertical: 3),
                      decoration: BoxDecoration(color: Colors.black.withOpacity(0.45), borderRadius: BorderRadius.circular(8)),
                      child: Text(menu.kategori, style: const TextStyle(color: Colors.white, fontSize: 8, fontWeight: FontWeight.w600)),
                    ),
                  ),
                ],
              ),
            ),
            Expanded(
              flex: 4,
              child: Padding(
                padding: const EdgeInsets.fromLTRB(10, 8, 10, 10),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(menu.namaMenu, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: _AppColors.textDark), maxLines: 1, overflow: TextOverflow.ellipsis),
                        const SizedBox(height: 3),
                        Text(menu.deskripsi, style: const TextStyle(fontSize: 10, color: _AppColors.textMuted), maxLines: 2, overflow: TextOverflow.ellipsis),
                      ],
                    ),                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text("Rp ${_formatPrice(menu.hargaAkhir)}", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: primaryColor)),
                            if (hasPromo)
                              Text("Rp ${_formatPrice(menu.hargaAsli)}", style: const TextStyle(fontSize: 9, color: _AppColors.textMuted, decoration: TextDecoration.lineThrough)),
                          ],
                        ),
                        GestureDetector(
                          onTap: () async {
                            final prefs = await SharedPreferences.getInstance();
                            int? userId = prefs.getInt('user_id');

                            if (userId == null || userId == 0) {
                              if (context.mounted) {
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

                            cartProvider.addToCart(menu);
                            ScaffoldMessenger.of(context).showSnackBar(
                              SnackBar(
                                content: Text("${menu.namaMenu} ditambahkan ke keranjang"),
                                duration: const Duration(seconds: 1),
                                backgroundColor: primaryColor,
                                behavior: SnackBarBehavior.floating,
                                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                              ),
                            );
                          },
                          child: Container(
                            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                            decoration: BoxDecoration(color: primaryColor, borderRadius: BorderRadius.circular(20)),
                            child: const Row(mainAxisSize: MainAxisSize.min, children: [Icon(Icons.add_rounded, color: Colors.white, size: 13), SizedBox(width: 3), Text("Pesan", style: TextStyle(fontSize: 11, color: Colors.white, fontWeight: FontWeight.bold))]),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}