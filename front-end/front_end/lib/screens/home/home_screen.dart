import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:vibration/vibration.dart';

// Services & Providers
import '../../services/api_services.dart';
import '../../providers/event_provider.dart';
import '../../providers/cart_provider.dart';

// Screens
import '../user/login_screen.dart';
import '../hotel/room_list_screen.dart';
import '../hotel/room_detail_screen.dart';
import '../hotel/room_type_screen.dart';
import '../restoran/menu_list_screen.dart';
import '../restoran/menu_detail_screen.dart';
import '../restoran/menu_resto.dart';
import '../restoran/cart_screen.dart';
import '../user/profile_screen.dart';
import '../notification/notification_screen.dart';
import '../home/unified_history_screen.dart';

// Widgets & Theme
import '../event/event_header.dart';
import '../../widgets/home_widgets.dart';

// ============================================================
// APP COLORS
// ============================================================
class _AppColors {
  static const Color navyDark  = Color(0xFF0C2D6B);
  static const Color gold      = Color(0xFFC9A227);
  static const Color bgPage    = Color(0xFFF2F4F8);
  static const Color textMuted = Color(0xFF6B7280);
  static const Color textHint  = Color(0xFF9CA3AF);
}

// ============================================================
// HOME SCREEN — root dengan nav
// ============================================================
class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});
  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen>
    with SingleTickerProviderStateMixin {
  int _currentIndex = 0;
  String _name     = "Tamu Purnama";
  String _photoUrl = "";
  bool _isGuest       = true;
  bool _hasShownPromo = false;

  late AnimationController _shakeController;
  late Animation<double>   _shakeAnimation;

  @override
  void initState() {
    super.initState();
    _checkAuth();
    _shakeController = AnimationController(
        duration: const Duration(milliseconds: 500), vsync: this);
    _shakeAnimation = TweenSequence<double>([
      TweenSequenceItem(tween: Tween(begin: 0.0,  end:  12.0), weight: 1),
      TweenSequenceItem(tween: Tween(begin: 12.0, end: -12.0), weight: 1),
      TweenSequenceItem(tween: Tween(begin: -12.0, end: 12.0), weight: 1),
      TweenSequenceItem(tween: Tween(begin: 12.0,  end:  0.0), weight: 1),
    ]).animate(
        CurvedAnimation(parent: _shakeController, curve: Curves.easeInOut));

    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (mounted) _checkAndShowPromo();
    });
  }

  @override
  void dispose() {
    _shakeController.dispose();
    super.dispose();
  }

  void _checkAuth() async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('auth_token');
    if (mounted) setState(() => _isGuest = (token == null || token.isEmpty));
    if (!_isGuest) await _loadUserData();
  }

  Future<void> _loadUserData() async {
    try {
      final result = await ApiServices.getUserProfile();
      if (result['success'] == true && mounted) {
        setState(() {
          _name     = result['data']['full_name'];
          final raw = result['data']['profile_photo'];
          _photoUrl = raw != null
              ? "$raw?t=${DateTime.now().millisecondsSinceEpoch}"
              : "";
        });
      }
    } catch (e) {
      debugPrint("Gagal load user data: $e");
    }
  }

  Future<void> _refreshData() async {
    await context.read<EventProvider>().fetchActiveTheme();
    if (!_isGuest) await _loadUserData();
  }

  void _checkAndShowPromo() async {
    if (_hasShownPromo) return;
    try {
      final result = await ApiServices.getActivePromo();
      if (result['success'] == true && mounted) {
        final promoData = result['data'];
        final ep      = context.read<EventProvider>();
        final primary = ep.eventCode != 'default' ? ep.primaryColor   : _AppColors.navyDark;
        final accent  = ep.eventCode != 'default' ? ep.secondaryColor : _AppColors.gold;
        _showPromoDialog(promoData, primary, accent);
        setState(() => _hasShownPromo = true);
      }
    } catch (e) {
      debugPrint("Gagal load promo: $e");
    }
  }

  void _showPromoDialog(Map<String, dynamic> promo, Color primary, Color accent) {
    final nominalText = promo['tipe_diskon'] == 'persen'
        ? "DISKON ${promo['nominal_potongan']}%"
        : "POTONGAN Rp ${_formatRupiah(promo['nominal_potongan'])}";

    final kodePromo = (promo['kode_promo'] as String?)?.isNotEmpty == true
        ? promo['kode_promo']
        : null;

    final kategori = (promo['kategori'] as String? ?? 'semua').toLowerCase();

  }

  void _handleLogout() async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (c) => AlertDialog(
        title: const Text("Konfirmasi"),
        content: const Text("Apakah Anda yakin ingin keluar?"),
        actions: [
          TextButton(
              onPressed: () => Navigator.pop(c),
              child: const Text("Batal")),
          TextButton(
              onPressed: () => Navigator.pop(c, true),
              child: const Text("Logout",
                  style: TextStyle(color: Colors.red))),
        ],
      ),
    );
    if (confirm == true) {
      await ApiServices.logout();
      final prefs = await SharedPreferences.getInstance();
      await prefs.clear();
      if (!mounted) return;
      Navigator.pushAndRemoveUntil(context,
          MaterialPageRoute(builder: (_) => const LoginScreen()),
          (r) => false);
    }
  }

  void _handleLockedAction() {
    Vibration.hasVibrator()
        .then((has) { if (has == true) Vibration.vibrate(duration: 100); });
    _shakeController.forward(from: 0.0);
    showDialog(
      context: context,
      builder: (c) => AlertDialog(
        shape:
            RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: const Text("Akses Terbatas",
            style: TextStyle(fontWeight: FontWeight.bold)),
        content: const Text(
            "Silakan masuk terlebih dahulu untuk menikmati fitur reservasi dan riwayat."),
        actions: [
          TextButton(
              onPressed: () => Navigator.pop(c),
              child: const Text("Nanti",
                  style: TextStyle(color: _AppColors.textMuted))),
          ElevatedButton(
            style: ElevatedButton.styleFrom(
                backgroundColor: _AppColors.navyDark),
            onPressed: () {
              Navigator.pop(c);
              Navigator.push(context,
                  MaterialPageRoute(builder: (_) => const LoginScreen()));
            },
            child: const Text("Masuk",
                style: TextStyle(color: Colors.white)),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final topPadding = MediaQuery.of(context).padding.top;
    return Scaffold(
      backgroundColor: _AppColors.bgPage,
      bottomNavigationBar: AnimatedBuilder(
        animation: _shakeAnimation,
        builder: (context, child) => Transform.translate(
            offset: Offset(_shakeAnimation.value, 0), child: child),
        child: _BottomNavBar(
          currentIndex: _currentIndex,
          onTap: (i) {
            if (_isGuest && (i == 3 || i == 4))
              _handleLockedAction();
            else
              setState(() => _currentIndex = i);
          },
        ),
      ),
      body: AnimatedSwitcher(
        duration: const Duration(milliseconds: 300),
        child: _getPage(_currentIndex, topPadding),
      ),
    );
  }

  Widget _getPage(int index, double top) {
    switch (index) {
      case 0:
        return _HomeDashboard(
          key: const ValueKey(0),
          topPadding: top,
          name: _name,
          photoUrl: _photoUrl,
          isGuest: _isGuest,
          onLockedTap: _handleLockedAction,
          onLogout: _handleLogout,
          onProfileTap: () => setState(() => _currentIndex = 4),
          onRefresh: _refreshData,
        );
      case 1: return const RoomListScreen();
      case 2: return const MenuListScreen();
      case 3: return const UnifiedHistoryScreen();
      case 4: return const ProfileScreen();
      default: return const SizedBox.shrink();
    }
  }
}

// ============================================================
// HOME DASHBOARD
// ============================================================
class _HomeDashboard extends StatefulWidget {
  final double topPadding;
  final String name, photoUrl;
  final bool isGuest;
  final VoidCallback onLockedTap, onLogout, onProfileTap;
  final Future<void> Function() onRefresh;

  const _HomeDashboard({
    super.key,
    required this.topPadding,
    required this.name,
    required this.photoUrl,
    required this.isGuest,
    required this.onLockedTap,
    required this.onLogout,
    required this.onProfileTap,
    required this.onRefresh,
  });

  @override
  State<_HomeDashboard> createState() => _HomeDashboardState();
}

class _HomeDashboardState extends State<_HomeDashboard> {
  List<dynamic> _roomTypes    = [];
  List<dynamic> _foodMenus    = [];
  List<dynamic> _activePromos = [];
  bool _loadingRooms  = true;
  bool _loadingFoods  = true;
  bool _loadingPromos = true;

  @override
  void initState() {
    super.initState();
    _fetchData();
  }

  Future<void> _fetchData() async {
    setState(() {
      _loadingRooms  = true;
      _loadingFoods  = true;
      _loadingPromos = true;
    });
    await Future.wait([
      _fetchRoomTypes(),
      _fetchFoodMenus(),
      _fetchActivePromos(),
    ]);
  }

  Future<void> _fetchRoomTypes() async {
    try {
      final result = await ApiServices.getRoomTypes();
      if (mounted) setState(() {
        _roomTypes    = result['success'] == true ? result['data'] : [];
        _loadingRooms = false;
      });
    } catch (_) {
      if (mounted) setState(() { _roomTypes = []; _loadingRooms = false; });
    }
  }

  Future<void> _fetchFoodMenus() async {
    try {
      final result = await ApiServices.getRestaurantMenus();
      if (mounted) setState(() {
        _foodMenus    = result['success'] == true ? result['data'] : [];
        _loadingFoods = false;
      });
    } catch (_) {
      if (mounted) setState(() { _foodMenus = []; _loadingFoods = false; });
    }
  }

  Future<void> _fetchActivePromos() async {
    try {
      final result = await ApiServices.getActivePromos();
      if (mounted) setState(() {
        _activePromos  = result['success'] == true ? result['data'] : [];
        _loadingPromos = false;
      });
    } catch (_) {
      if (mounted) setState(() { _activePromos = []; _loadingPromos = false; });
    }
  }

  Future<void> _handleRefresh() async {
    await widget.onRefresh();
    await _fetchData();
  }

  void _navigateToRoomDetail(Map<String, dynamic> item) {
    final room = RoomType(
      id:         item['id']                              ?? 0,
      namaTipe:   item['nama_tipe']  ?? item['name']      ?? "Kamar",
      hargaAsli:  (item['harga_asli']  as num?)?.toDouble() ?? 0.0,
      hargaAkhir: (item['harga_akhir'] as num?)?.toDouble() ?? 0.0,
      promoAktif: item['promo_aktif'],
      kapasitas:  (item['kapasitas'] as num?)?.toInt()    ?? 0,
      fasilitas:  item['fasilitas']                       ?? "",
      deskripsi:  item['deskripsi']                       ?? "",
    );
    Navigator.push(
      context,
      MaterialPageRoute(builder: (_) => RoomDetailScreen(room: room)),
    );
  }

  void _navigateToMenuDetail(Map<String, dynamic> item) {
    final kategoriRaw = item['kategori'];
    final String kategoriStr = kategoriRaw is Map
        ? (kategoriRaw['nama_kategori'] ?? "Umum")
        : (kategoriRaw?.toString() ?? "Umum");

    final statusRaw = item['status'];
    final String statusStr = statusRaw is Map
        ? (statusRaw['nama_status'] ?? "Tersedia")
        : (statusRaw?.toString() ?? "Tersedia");

    final menu = MenuResto(
      id:         item['id']                               ?? 0,
      namaMenu:   item['nama_menu']                        ?? "Menu",
      deskripsi:  item['deskripsi']                        ?? "",
      hargaAsli:  (item['harga_asli']  as num?)?.toDouble() ?? 0.0,
      hargaAkhir: (item['harga_akhir'] as num?)?.toDouble() ?? 0.0,
      promoAktif: item['promo_aktif'],
      fotoMenu:   item['foto_menu'],
      kategori:   kategoriStr,
      status:     statusStr,
    );
    Navigator.push(
      context,
      MaterialPageRoute(builder: (_) => MenuDetailScreen(menu: menu)),
    );
  }

  @override
  Widget build(BuildContext context) {
    final ep           = context.watch<EventProvider>();
    final primaryColor = ep.eventCode != 'default' ? ep.primaryColor   : _AppColors.navyDark;
    final accentColor  = ep.eventCode != 'default' ? ep.secondaryColor : _AppColors.gold;
    final cartProvider = context.watch<CartProvider>();

    return Column(
      children: [
        _Header(
          topPadding:   widget.topPadding,
          name:         widget.name,
          photoUrl:     widget.photoUrl,
          isGuest:      widget.isGuest,
          primaryColor: primaryColor,
          accentColor:  accentColor,
          onLockedTap:  widget.onLockedTap,
          onLogout:     widget.onLogout,
          onProfileTap: widget.onProfileTap,
          cartItems:    cartProvider.totalItems,
        ),

        Expanded(
          child: RefreshIndicator(
            onRefresh: _handleRefresh,
            color: accentColor,
            backgroundColor: Colors.white,
            child: SingleChildScrollView(
              physics: const AlwaysScrollableScrollPhysics(),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const EventHeader(),

                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 16),
                    child: Column(children: [
                      _BannerCard(
                        title: "Pesanan Restoran",
                        tag: "Restoran",
                        imageUrl:
                            "https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=600",
                        accentColor: accentColor,
                        onTap: () => Navigator.push(context,
                            MaterialPageRoute(builder: (_) => const MenuListScreen())),
                      ),
                      const SizedBox(height: 12),
                      _BannerCard(
                        title: "Reservasi Hotel",
                        tag: "Hotel",
                        imageUrl:
                            "https://images.unsplash.com/photo-1566073771259-6a8506099945?w=600",
                        accentColor: accentColor,
                        onTap: () => Navigator.push(context,
                            MaterialPageRoute(builder: (_) => const RoomListScreen())),
                      ),
                    ]),
                  ),

                  const SizedBox(height: 20),
                  _loadingPromos
                      ? const SizedBox(
                          height: 90,
                          child: Center(child: CircularProgressIndicator()))
                      : _PromoScrollStrip(
                          promos:       _activePromos,
                          primaryColor: primaryColor,
                          accentColor:  accentColor,
                        ),

                  // const SizedBox(height: 8),
                  // _SectionHeader(
                  //   title: "Destinasi Kamar Populer",
                  //   primaryColor: primaryColor,
                  //   onSeeAll: () => Navigator.push(context,
                  //       MaterialPageRoute(builder: (_) => const RoomListScreen())),
                  // ),
                  // _loadingRooms
                  //     ? const SizedBox(
                  //         height: 160,
                  //         child: Center(child: CircularProgressIndicator()))
                  //     : _HorizontalList(
                  //         type:        'room',
                  //         items:       _roomTypes,
                  //         accentColor: accentColor,
                  //         onItemTap:   (item) => _navigateToRoomDetail(item),
                  //       ),

                  const SizedBox(height: 20),
                  _SectionHeader(
                    title: "Makanan Terfavorit",
                    primaryColor: primaryColor,
                    onSeeAll: () => Navigator.push(context,
                        MaterialPageRoute(builder: (_) => const MenuListScreen())),
                  ),
                  _loadingFoods
                      ? const SizedBox(
                          height: 160,
                          child: Center(child: CircularProgressIndicator()))
                      : _HorizontalList(
                          type:        'food',
                          items:       _foodMenus,
                          accentColor: accentColor,
                          onItemTap:   (item) => _navigateToMenuDetail(item),
                        ),

                  const SizedBox(height: 20),
                  const AutoPromoSlider(),

                  const SizedBox(height: 32),
                ],
              ),
            ),
          ),
        ),
      ],
    );
  }
}

// ============================================================
// PROMO SCROLL STRIP
// ============================================================
class _PromoScrollStrip extends StatelessWidget {
  final List<dynamic> promos;
  final Color primaryColor, accentColor;

  const _PromoScrollStrip({
    required this.promos,
    required this.primaryColor,
    required this.accentColor,
  });

  @override
  Widget build(BuildContext context) {
    if (promos.isEmpty) return const SizedBox.shrink();

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16),
          child: Row(
            children: [
              Icon(Icons.local_offer_rounded, color: accentColor, size: 18),
              const SizedBox(width: 8),
              Text(
                "Promo Aktif",
                style: TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.bold,
                  color: primaryColor,
                ),
              ),
            ],
          ),
        ),
        const SizedBox(height: 12),
        SizedBox(
          height: 100,
          child: ListView.separated(
            scrollDirection: Axis.horizontal,
            padding: const EdgeInsets.symmetric(horizontal: 16),
            itemCount: promos.length,
            separatorBuilder: (_, __) => const SizedBox(width: 12),
            itemBuilder: (context, index) {
              final p = promos[index];

              final nominalText = p['tipe_diskon'] == 'persen'
                  ? "${p['nominal_potongan']}% OFF"
                  : "Rp ${_formatRupiah(p['nominal_potongan'])} OFF";

              final kode = (p['kode_promo'] as String?)?.isNotEmpty == true
                  ? p['kode_promo']
                  : null;

              final kategori = (p['kategori'] as String? ?? 'semua');
              final kategoriIcon = kategori == 'hotel'
                  ? Icons.hotel_rounded
                  : kategori == 'restoran'
                      ? Icons.restaurant_rounded
                      : Icons.card_giftcard_rounded;

              return Container(
                width: 220,
                padding: const EdgeInsets.all(14),
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    colors: [primaryColor, primaryColor.withOpacity(0.75)],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                  borderRadius: BorderRadius.circular(18),
                  boxShadow: [
                    BoxShadow(
                      color: primaryColor.withOpacity(0.25),
                      blurRadius: 10,
                      offset: const Offset(0, 4),
                    ),
                  ],
                ),
                child: Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.all(10),
                      decoration: BoxDecoration(
                        color: accentColor.withOpacity(0.2),
                        shape: BoxShape.circle,
                      ),
                      child: Icon(kategoriIcon, color: accentColor, size: 22),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Text(
                            p['nama_promo'] ?? "Promo",
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                            style: const TextStyle(
                              color: Colors.white,
                              fontWeight: FontWeight.bold,
                              fontSize: 13,
                            ),
                          ),
                          const SizedBox(height: 4),
                          Text(
                            nominalText,
                            style: TextStyle(
                              color: accentColor,
                              fontWeight: FontWeight.w900,
                              fontSize: 16,
                            ),
                          ),
                          if (kode != null) ...[
                            const SizedBox(height: 4),
                            Container(
                              padding: const EdgeInsets.symmetric(
                                  horizontal: 8, vertical: 2),
                              decoration: BoxDecoration(
                                color: Colors.white.withOpacity(0.15),
                                borderRadius: BorderRadius.circular(6),
                              ),
                              child: Text(
                                kode,
                                style: const TextStyle(
                                  color: Colors.white,
                                  fontSize: 10,
                                  fontWeight: FontWeight.bold,
                                  letterSpacing: 1.5,
                                ),
                              ),
                            ),
                          ],
                        ],
                      ),
                    ),
                  ],
                ),
              );
            },
          ),
        ),
      ],
    );
  }
}

// ============================================================
// SECTION HEADER
// ============================================================
class _SectionHeader extends StatelessWidget {
  final String title;
  final Color primaryColor;
  final VoidCallback onSeeAll;

  const _SectionHeader({
    required this.title,
    required this.primaryColor,
    required this.onSeeAll,
  });

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            title,
            style: TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.bold,
              color: primaryColor,
            ),
          ),
          TextButton(
            onPressed: onSeeAll,
            child: const Text(
              "Lihat Semua",
              style: TextStyle(
                color: _AppColors.gold,
                fontWeight: FontWeight.bold,
              ),
            ),
          ),
        ],
      ),
    );
  }
}

// ============================================================
// HORIZONTAL LIST — FIXED: URL gambar dibangun dengan benar
// ============================================================
class _HorizontalList extends StatelessWidget {
  final String type;
  final List<dynamic> items;
  final Color accentColor;
  final Function(Map<String, dynamic>) onItemTap;

  const _HorizontalList({
    required this.type,
    required this.items,
    required this.accentColor,
    required this.onItemTap,
  });

  // Helper bangun URL gambar — handle URL internet (seeder) & path lokal (upload)
  String _buildImageUrl(String? rawFoto) {
    if (rawFoto == null || rawFoto.isEmpty) return "";
    if (rawFoto.startsWith('http')) return rawFoto; // ✅ URL internet langsung pakai
    return "http://${ApiServices.ipAddress}:8001/storage/$rawFoto"; // ✅ path lokal tambah base
  }

  @override
  Widget build(BuildContext context) {
    if (items.isEmpty) {
      return const Padding(
        padding: EdgeInsets.all(16),
        child: Text("Tidak ada data",
            style: TextStyle(color: Colors.grey)),
      );
    }
    return SizedBox(
      height: 175,
      child: ListView.separated(
        scrollDirection: Axis.horizontal,
        padding: const EdgeInsets.symmetric(horizontal: 16),
        itemCount: items.length,
        separatorBuilder: (_, __) => const SizedBox(width: 12),
        itemBuilder: (context, index) {
          final item = items[index] as Map<String, dynamic>;
          final String name;
          final String imageUrl;

          if (type == 'room') {
            name     = item['nama_tipe'] ?? item['name'] ?? "Kamar";
            imageUrl = _buildImageUrl(item['foto']);         // ✅ FIXED
          } else {
            name     = item['nama_menu'] ?? "Menu";
            imageUrl = _buildImageUrl(item['foto_menu']);    // ✅ FIXED
          }

          final String priceText = () {
            final harga = (item['harga_akhir'] as num?)?.toDouble() ?? 0.0;
            if (harga > 0) {
              return type == 'room'
                  ? "Rp ${_formatRupiah(harga)}/mlm"
                  : "Rp ${_formatRupiah(harga)}";
            }
            return "";
          }();

          return GestureDetector(
            onTap: () => onItemTap(item),
            child: _ItemCard(
              imageUrl:    imageUrl,
              name:        name,
              priceText:   priceText,
              accentColor: accentColor,
            ),
          );
        },
      ),
    );
  }
}

// ============================================================
// ITEM CARD
// ============================================================
class _ItemCard extends StatelessWidget {
  final String imageUrl, name, priceText;
  final Color accentColor;

  const _ItemCard({
    required this.imageUrl,
    required this.name,
    required this.priceText,
    required this.accentColor,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      width: 140,
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.06),
            blurRadius: 8,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          ClipRRect(
            borderRadius:
                const BorderRadius.vertical(top: Radius.circular(18)),
            child: imageUrl.isNotEmpty
                ? Image.network(
                    imageUrl,
                    height: 100,
                    width: 140,
                    fit: BoxFit.cover,
                    errorBuilder: (_, __, ___) => Container(
                      height: 100,
                      width: 140,
                      color: Colors.grey[200],
                      child: const Icon(Icons.broken_image,
                          color: Colors.grey),
                    ),
                  )
                : Container(
                    height: 100,
                    width: 140,
                    color: Colors.grey[200],
                    child: const Icon(Icons.image_not_supported,
                        color: Colors.grey),
                  ),
          ),
          Padding(
            padding: const EdgeInsets.all(10),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  name,
                  style: const TextStyle(
                    fontWeight: FontWeight.bold,
                    fontSize: 13,
                  ),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
                if (priceText.isNotEmpty) ...[
                  const SizedBox(height: 4),
                  Text(
                    priceText,
                    style: TextStyle(
                      fontSize: 11,
                      color: accentColor,
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                ],
              ],
            ),
          ),
        ],
      ),
    );
  }
}

// ============================================================
// HEADER (TERMASUK SEARCH BAR)
// ============================================================
class _Header extends StatelessWidget {
  final double topPadding;
  final String name, photoUrl;
  final bool isGuest;
  final Color primaryColor, accentColor;
  final VoidCallback onLockedTap, onLogout, onProfileTap;
  final int cartItems;

  const _Header({
    required this.topPadding,
    required this.name,
    required this.photoUrl,
    required this.isGuest,
    required this.primaryColor,
    required this.accentColor,
    required this.onLockedTap,
    required this.onLogout,
    required this.onProfileTap,
    required this.cartItems,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: EdgeInsets.only(
          top: topPadding + 16, left: 20, right: 20, bottom: 24),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [primaryColor, primaryColor.withOpacity(0.85)],
        ),
        borderRadius:
            const BorderRadius.vertical(bottom: Radius.circular(32)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
        Row(
          crossAxisAlignment: CrossAxisAlignment.center,
          children: [
            Image.asset(
              'assets/icons/icon-purnama.png',
              width: 36,
              height: 36,
              errorBuilder: (c, e, s) =>
                  const Icon(Icons.hotel, color: Colors.white),
            ),
            const SizedBox(width: 10),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisSize: MainAxisSize.min,
                children: const [
                  Text(
                    "Hotel & Restoran",
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: TextStyle(
                      color: Colors.white60,
                      fontSize: 10,
                      letterSpacing: 1
                    )
                  ),
                  Text(
                    "PURNAMA BALIGE",
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: TextStyle(
                      color: Colors.white,
                      fontWeight: FontWeight.w900,
                      fontSize: 14
                    )
                  ),
                ],
              ),
            ),
            const SizedBox(width: 8),
            Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                if (isGuest) ...[
                  _CircularIcon(icon: Icons.notifications_none, onTap: onLockedTap),
                  const SizedBox(width: 8),
                  GestureDetector(
                    onTap: () => Navigator.push(context,
                        MaterialPageRoute(builder: (_) => const LoginScreen())),
                    child: Container(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 14, vertical: 8),
                      decoration: BoxDecoration(
                          color: accentColor,
                          borderRadius: BorderRadius.circular(10)),
                      child: const Text("MASUK",
                          style: TextStyle(
                              color: Colors.white,
                              fontWeight: FontWeight.bold,
                              fontSize: 11)),
                    ),
                  ),
                ] else ...[
                  _CartIcon(count: cartItems),
                  _CircularIcon(
                    icon: Icons.notifications_none,
                    onTap: () => Navigator.push(
                        context,
                        MaterialPageRoute(
                            builder: (_) => const NotificationScreen())),
                  ),
                  _CircularIcon(icon: Icons.power_settings_new, onTap: onLogout),
                  const SizedBox(width: 8),
                  GestureDetector(
                    onTap: onProfileTap,
                    child: Container(
                      width: 30,
                      height: 30,
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        border: Border.all(color: Colors.white38, width: 1.5),
                      ),
                      child: ClipOval(
                        child: photoUrl.isNotEmpty
                            ? Image.network(
                                photoUrl,
                                width: 30,
                                height: 30,
                                fit: BoxFit.cover,
                                errorBuilder: (_, __, ___) => Container(
                                  color: Colors.white24,
                                  child: const Icon(Icons.person, color: Colors.white, size: 16),
                                ),
                              )
                            : Container(
                                color: Colors.white24,
                                child: const Icon(Icons.person, color: Colors.white, size: 16),
                              ),
                      ),
                    ),
                  ),
                ],
              ],
            ),
          ],
        ),
        const SizedBox(height: 25),
        Text(
          isGuest ? "Selamat Datang di" : "Halo,",
          style: const TextStyle(color: Colors.white70, fontSize: 14),
        ),
        Text(
          isGuest ? "PURNAMA BALIGE" : name,
          maxLines: 1,
          overflow: TextOverflow.ellipsis,
          style: const TextStyle(
              color: Colors.white,
              fontSize: 24,
              fontWeight: FontWeight.w900),
        ),
        const SizedBox(height: 20),
        _SearchBar(accentColor: accentColor),
      ]),
    );
  }
}

// ============================================================
// CART ICON
// ============================================================
class _CartIcon extends StatelessWidget {
  final int count;
  final double iconSize;
  const _CartIcon({required this.count, this.iconSize = 22});

  @override
  Widget build(BuildContext context) => Stack(
    children: [
      _CircularIcon(
        icon: Icons.shopping_bag_outlined,
        onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const CartScreen())),
        size: iconSize,
      ),
      if (count > 0)
        Positioned(
          right: 2,
          top: 2,
          child: CircleAvatar(
            radius: 7,
            backgroundColor: Colors.red,
            child: Text(count.toString(), style: const TextStyle(fontSize: 8, color: Colors.white)),
          ),
        ),
    ],
  );
}

// ============================================================
// CIRCULAR ICON BUTTON
// ============================================================
class _CircularIcon extends StatelessWidget {
  final IconData icon;
  final VoidCallback onTap;
  final double size;
  const _CircularIcon({required this.icon, required this.onTap, this.size = 22});

  @override
  Widget build(BuildContext context) => IconButton(
    onPressed: onTap,
    icon: Icon(icon, color: Colors.white, size: size),
    padding: EdgeInsets.zero,
    constraints: const BoxConstraints(),
  );
}

// ============================================================
// SEARCH BAR
// ============================================================
class _SearchBar extends StatelessWidget {
  final Color accentColor;
  const _SearchBar({required this.accentColor});

  @override
  Widget build(BuildContext context) => Container(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          boxShadow: [
            BoxShadow(
                color: Colors.black.withOpacity(0.1),
                blurRadius: 10,
                offset: const Offset(0, 4))
          ],
        ),
        child: Row(children: [
          Icon(Icons.search, color: accentColor),
          const SizedBox(width: 12),
          const Expanded(
            child: Text("Cari hotel atau restoran...",
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
                style: TextStyle(
                    color: _AppColors.textHint, fontSize: 14)),
          ),
        ]),
      );
}

// ============================================================
// BANNER CARD
// ============================================================
class _BannerCard extends StatelessWidget {
  final String title, tag, imageUrl;
  final Color accentColor;
  final VoidCallback onTap;

  const _BannerCard({
    required this.title,
    required this.tag,
    required this.imageUrl,
    required this.accentColor,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: ClipRRect(
        borderRadius: BorderRadius.circular(20),
        child: Container(
          height: 150,
          width: double.infinity,
          decoration: BoxDecoration(
            image: DecorationImage(
                image: NetworkImage(imageUrl), fit: BoxFit.cover),
          ),
          child: Container(
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(
              gradient: LinearGradient(
                colors: [
                  Colors.black.withOpacity(0.8),
                  Colors.transparent
                ],
                begin: Alignment.bottomLeft,
                end: Alignment.topRight,
              ),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisAlignment: MainAxisAlignment.end,
              children: [
                Container(
                  padding: const EdgeInsets.symmetric(
                      horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(
                      color: accentColor,
                      borderRadius: BorderRadius.circular(8)),
                  child: Text(
                    tag.toUpperCase(),
                    style: const TextStyle(
                        color: Colors.white,
                        fontSize: 10,
                        fontWeight: FontWeight.bold),
                  ),
                ),
                const SizedBox(height: 8),
                Text(title,
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(
                        color: Colors.white,
                        fontSize: 20,
                        fontWeight: FontWeight.bold)),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

// ============================================================
// BOTTOM NAV BAR
// ============================================================
class _BottomNavBar extends StatelessWidget {
  final int currentIndex;
  final ValueChanged<int> onTap;

  const _BottomNavBar({required this.currentIndex, required this.onTap});

  @override
  Widget build(BuildContext context) {
    final bottomPadding = MediaQuery.of(context).padding.bottom;

    const items = [
      {'icon': Icons.home_rounded,            'label': "Beranda"},
      {'icon': Icons.hotel_rounded,           'label': "Kamar"},
      {'icon': Icons.restaurant_menu_rounded, 'label': "Menu"},
      {'icon': Icons.receipt_long_rounded,    'label': "Riwayat"},
      {'icon': Icons.person_rounded,          'label': "Profil"},
    ];

    return Container(
      padding: EdgeInsets.only(
        top: 10,
        left: 8,
        right: 8,
        bottom: bottomPadding > 0 ? bottomPadding : 10,
      ),
      decoration: const BoxDecoration(
        color: _AppColors.navyDark,
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
        boxShadow: [
          BoxShadow(
            color: Colors.black38,
            blurRadius: 15,
            offset: Offset(0, -4),
          ),
        ],
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceAround,
        children: List.generate(items.length, (i) {
          final bool active = currentIndex == i;
          return GestureDetector(
            onTap: () => onTap(i),
            child: AnimatedContainer(
              duration: const Duration(milliseconds: 250),
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
              decoration: BoxDecoration(
                color: active
                    ? Colors.white.withOpacity(0.12)
                    : Colors.transparent,
                borderRadius: BorderRadius.circular(20),
              ),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Icon(
                    items[i]['icon'] as IconData,
                    color: active ? _AppColors.gold : Colors.white54,
                    size: active ? 26 : 22,
                  ),
                  const SizedBox(height: 2),
                  Text(
                    items[i]['label'] as String,
                    style: TextStyle(
                      color: active ? _AppColors.gold : Colors.white54,
                      fontSize: 10,
                      fontWeight: active
                          ? FontWeight.bold
                          : FontWeight.normal,
                    ),
                  ),
                ],
              ),
            ),
          );
        }),
      ),
    );
  }
}

// ============================================================
// HELPER — format angka jadi Rupiah singkat
// ============================================================
String _formatRupiah(dynamic value) {
  final num angka = (value is num) ? value : num.tryParse(value.toString()) ?? 0;
  if (angka >= 1000000) {
    return "${(angka / 1000000).toStringAsFixed(angka % 1000000 == 0 ? 0 : 1)}Jt";
  } else if (angka >= 1000) {
    return "${(angka / 1000).toStringAsFixed(angka % 1000 == 0 ? 0 : 0)}K";
  }
  return angka.toStringAsFixed(0);
}