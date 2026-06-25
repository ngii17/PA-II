import 'dart:async';
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
import 'promo_detail_screen.dart';

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
  static const Color textMain  = Color(0xFF1F2937);
}

// ============================================================
// HOME SCREEN
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

  final ScrollController _scrollController = ScrollController();
  final GlobalKey _promoSectionKey = GlobalKey();

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
    _scrollController.dispose();
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

  void _scrollToPromo() {
    final ctx = _promoSectionKey.currentContext;    if (ctx == null) return;

    final renderBox = ctx.findRenderObject() as RenderBox?;
    if (renderBox == null) return;

    final scrollableBox = _scrollController.position.context.storageContext
        .findRenderObject() as RenderBox?;

    final offset = renderBox
        .localToGlobal(Offset.zero, ancestor: scrollableBox)
        .dy;

    final targetOffset = (_scrollController.offset + offset - 16)
        .clamp(0.0, _scrollController.position.maxScrollExtent);

    _scrollController.animateTo(
      targetOffset,
      duration: const Duration(milliseconds: 800),
      curve: Curves.easeInOut,
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
        child: Consumer<EventProvider>(
          builder: (context, ep, _) {
            final accentColor = ep.eventCode != 'default' 
                ? ep.secondaryColor 
                : _AppColors.gold;
            return _BottomNavBar(
              currentIndex: _currentIndex,
              accentColor: accentColor,
              onTap: (i) {
                if (_isGuest && (i == 3 || i == 4))
                  _handleLockedAction();
                else
                  setState(() => _currentIndex = i);
              },
            );
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
          scrollController: _scrollController,
          onPromoPressed: _scrollToPromo,
          promoSectionKey: _promoSectionKey,
        );
      case 1: return RoomListScreen(onBack: () => setState(() => _currentIndex = 0));      
      case 2: return MenuListScreen(onBack: () => setState(() => _currentIndex = 0));
      case 3: return UnifiedHistoryScreen(onBack: () => setState(() => _currentIndex = 0));
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
  final ScrollController scrollController;
  final VoidCallback onPromoPressed;
  final GlobalKey promoSectionKey;

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
    required this.scrollController,
    required this.onPromoPressed,
    required this.promoSectionKey,
  });

  @override
  State<_HomeDashboard> createState() => _HomeDashboardState();
}

class _HomeDashboardState extends State<_HomeDashboard> {
  List<dynamic> _roomTypes = [];
  List<dynamic> _foodMenus = [];
  List<dynamic> _activePromos = [];
  List<dynamic> _filteredRooms = [];
  List<dynamic> _filteredFoods = [];
  
  bool _loadingRooms = true;
  bool _loadingFoods = true;
  bool _loadingPromos = true;
  
  final TextEditingController _searchController = TextEditingController();

  late PageController _promoPageController;
  int _currentPromoPage = 0;
  Timer? _promoTimer;

  late PageController _hotelPageController;
  late PageController _foodPageController;
  int _currentHotelPage = 0;
  int _currentFoodPage = 0;
  Timer? _hotelTimer;
  Timer? _foodTimer;

  @override
  void initState() {
    super.initState();
    _fetchData();
    _promoPageController = PageController(viewportFraction: 0.92);
    _hotelPageController = PageController(viewportFraction: 0.85);
    _foodPageController = PageController(viewportFraction: 0.85);
    
    _searchController.addListener(_filterSearch);
  }

  @override
  void dispose() {
    _promoPageController.dispose();
    _hotelPageController.dispose();
    _foodPageController.dispose();
    _promoTimer?.cancel();
    _hotelTimer?.cancel();
    _foodTimer?.cancel();
    _searchController.dispose();
    super.dispose();
  }

  void _filterSearch() {
    final query = _searchController.text.toLowerCase().trim();
    setState(() {
      if (query.isEmpty) {
        _filteredRooms = _roomTypes;
        _filteredFoods = _foodMenus;
      } else {
        _filteredRooms = _roomTypes.where((item) {
          final name = (item['nama_tipe'] ?? item['name'] ?? '').toLowerCase();
          return name.contains(query);
        }).toList();
        _filteredFoods = _foodMenus.where((item) {
          final name = (item['nama_menu'] ?? '').toLowerCase();
          return name.contains(query);
        }).toList();
      }
    });
  }

  void _startAutoScrollPromo() {
    _promoTimer?.cancel();
    if (_activePromos.length > 1) {
      _promoTimer = Timer.periodic(const Duration(seconds: 4), (timer) {
        if (_promoPageController.hasClients && mounted) {
          final nextPage = (_currentPromoPage + 1) % _activePromos.length;
          _promoPageController.animateToPage(
            nextPage,
            duration: const Duration(milliseconds: 600),
            curve: Curves.easeInOut,
          );
          setState(() => _currentPromoPage = nextPage);
        }
      });
    }
  }

  void _startAutoScrollHotel() {
    _hotelTimer?.cancel();
    final items = _filteredRooms.isNotEmpty ? _filteredRooms : _roomTypes;
    if (items.length > 1) {
      _hotelTimer = Timer.periodic(const Duration(seconds: 4), (timer) {
        if (_hotelPageController.hasClients && mounted) {
          final nextPage = (_currentHotelPage + 1) % items.length;
          _hotelPageController.animateToPage(
            nextPage,
            duration: const Duration(milliseconds: 600),
            curve: Curves.easeInOut,
          );
          setState(() => _currentHotelPage = nextPage);
        }
      });
    }
  }

  void _startAutoScrollFood() {
    _foodTimer?.cancel();
    final items = _filteredFoods.isNotEmpty ? _filteredFoods : _foodMenus;
    if (items.length > 1) {
      _foodTimer = Timer.periodic(const Duration(seconds: 4), (timer) {
        if (_foodPageController.hasClients && mounted) {
          final nextPage = (_currentFoodPage + 1) % items.length;
          _foodPageController.animateToPage(
            nextPage,
            duration: const Duration(milliseconds: 600),
            curve: Curves.easeInOut,
          );
          setState(() => _currentFoodPage = nextPage);
        }
      });
    }
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
    _filteredRooms = _roomTypes;
    _filteredFoods = _foodMenus;
    _startAutoScrollPromo();
    _startAutoScrollHotel();
    _startAutoScrollFood();
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
    _startAutoScrollPromo();
    _startAutoScrollHotel();
    _startAutoScrollFood();
  }

  void _navigateToRoomDetail(Map<String, dynamic> item) {
    final room = RoomType.fromJson(item);
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (_) => RoomDetailScreen(room: room),
      ),
    );
  }

  void _navigateToMenuDetail(Map<String, dynamic> item) {
    final menu = MenuResto.fromJson(item);
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (_) => MenuDetailScreen(menu: menu),
      ),
    );
  }

  String _buildImageUrl(String? rawFoto) {
    if (rawFoto == null || rawFoto.isEmpty) return "";
    if (rawFoto.startsWith('http')) return rawFoto;
    return "https://purnama-hotel.duckdns.org/storage/$rawFoto";
  }

  // ============================================================
  // FORMAT RUPIAH DENGAN PEMISAH TITIK
  // ============================================================
  String _formatRupiahWithDot(dynamic value) {
    final num angka = (value is num) ? value : num.tryParse(value.toString()) ?? 0;
    final parts = angka.toStringAsFixed(0).split('.');
    final number = parts[0];
    final formatted = number.replaceAllMapped(
      RegExp(r'(\d)(?=(\d{3})+(?!\d))'),
      (match) => '${match[1]}.',
    );
    return 'Rp $formatted';
  }

  // ============================================================
  // BUILD QUICK STATS
  // ============================================================
  Widget _buildQuickStats() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 20),
      child: Row(
        children: [
          Expanded(
            child: Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(12),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.04),
                    blurRadius: 8,
                    offset: const Offset(0, 2),
                  ),
                ],
              ),
              child: Column(
                children: [
                  const Icon(Icons.hotel_rounded, color: Color(0xFF0C2D6B), size: 22),
                  const SizedBox(height: 4),
                  Text(
                    '${_roomTypes.length}',
                    style: const TextStyle(
                      fontWeight: FontWeight.bold,
                      fontSize: 16,
                      color: Color(0xFF0C2D6B),
                    ),
                  ),
                  Text(
                    'Tipe Kamar',
                    style: TextStyle(
                      color: Colors.grey[600],
                      fontSize: 10,
                    ),
                  ),
                ],
              ),
            ),
          ),
          const SizedBox(width: 10),
          Expanded(
            child: Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(12),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.04),
                    blurRadius: 8,
                    offset: const Offset(0, 2),
                  ),
                ],
              ),
              child: Column(
                children: [
                  const Icon(Icons.restaurant_menu_rounded, color: Color(0xFFC9A227), size: 22),
                  const SizedBox(height: 4),
                  Text(
                    '${_foodMenus.length}',
                    style: const TextStyle(
                      fontWeight: FontWeight.bold,
                      fontSize: 16,
                      color: Color(0xFFC9A227),
                    ),
                  ),
                  Text(
                    'Menu',
                    style: TextStyle(
                      color: Colors.grey[600],
                      fontSize: 10,
                    ),
                  ),
                ],
              ),
            ),
          ),
          const SizedBox(width: 10),
          Expanded(
            child: Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(12),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.04),
                    blurRadius: 8,
                    offset: const Offset(0, 2),
                  ),
                ],
              ),
              child: Column(
                children: [
                  const Icon(Icons.local_offer_rounded, color: Colors.teal, size: 22),
                  const SizedBox(height: 4),
                  Text(
                    '${_activePromos.length}',
                    style: const TextStyle(
                      fontWeight: FontWeight.bold,
                      fontSize: 16,
                      color: Colors.teal,
                    ),
                  ),
                  Text(
                    'Promo',
                    style: TextStyle(
                      color: Colors.grey[600],
                      fontSize: 10,
                    ),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  // ============================================================
  // BUILD QUICK ACCESS PILLS
  // ============================================================
  Widget _buildQuickAccessPills(Color primaryColor, Color accentColor) {
    return Padding(
      padding: const EdgeInsets.fromLTRB(20, 4, 20, 0),
      child: Row(
        children: [
          Expanded(
            child: GestureDetector(
              onTap: () => Navigator.push(context,
                  MaterialPageRoute(builder: (_) => const RoomListScreen())),
              child: Container(
                padding: const EdgeInsets.symmetric(vertical: 12),
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    colors: [primaryColor, primaryColor.withOpacity(0.7)],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                  borderRadius: BorderRadius.circular(14),
                  boxShadow: [
                    BoxShadow(
                      color: primaryColor.withOpacity(0.3),
                      blurRadius: 10,
                      offset: const Offset(0, 4),
                    ),
                  ],
                ),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    const Icon(Icons.hotel_rounded, color: Colors.white, size: 18),
                    const SizedBox(width: 6),
                    const Text(
                      'Hotel',
                      style: TextStyle(
                        color: Colors.white,
                        fontWeight: FontWeight.bold,
                        fontSize: 12,
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),
          const SizedBox(width: 10),
          Expanded(
            child: GestureDetector(
              onTap: () => Navigator.push(context,
                  MaterialPageRoute(builder: (_) => const MenuListScreen())),
              child: Container(
                padding: const EdgeInsets.symmetric(vertical: 12),
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    colors: [accentColor, accentColor.withOpacity(0.7)],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                  borderRadius: BorderRadius.circular(14),
                  boxShadow: [
                    BoxShadow(
                      color: accentColor.withOpacity(0.3),
                      blurRadius: 10,
                      offset: const Offset(0, 4),
                    ),
                  ],
                ),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    const Icon(Icons.restaurant_menu_rounded, color: Colors.white, size: 18),
                    const SizedBox(width: 6),
                    const Text(
                      'Restoran',
                      style: TextStyle(
                        color: Colors.white,
                        fontWeight: FontWeight.bold,
                        fontSize: 12,
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),
          const SizedBox(width: 10),
          Expanded(
            child: GestureDetector(
              onTap: widget.onPromoPressed,
              child: Container(
                padding: const EdgeInsets.symmetric(vertical: 12),
                decoration: BoxDecoration(
                  gradient: const LinearGradient(
                    colors: [Colors.teal, Colors.tealAccent],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                  borderRadius: BorderRadius.circular(14),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.teal.withOpacity(0.3),
                      blurRadius: 10,
                      offset: const Offset(0, 4),
                    ),
                  ],
                ),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    const Icon(Icons.local_offer_rounded, color: Colors.white, size: 18),
                    const SizedBox(width: 6),
                    const Text(
                      'Promo',
                      style: TextStyle(
                        color: Colors.white,
                        fontWeight: FontWeight.bold,
                        fontSize: 12,
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

  // ============================================================
  // BUILD PROMO SECTION - BEDA TAMPILAN
  // ============================================================
  Widget _buildPromoSection(Color primaryColor, Color accentColor) {
    if (_activePromos.isEmpty) return const SizedBox.shrink();

    return Column(
      key: widget.promoSectionKey,
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 20),
          child: Row(
            children: [
              Container(
                padding: const EdgeInsets.all(6),
                decoration: BoxDecoration(
                  color: Colors.teal.withOpacity(0.12),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: const Icon(Icons.local_offer_rounded, color: Colors.teal, size: 14),
              ),
              const SizedBox(width: 8),
              const Text(
                "Promo Spesial",
                style: TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.bold,
                  color: Colors.teal,
                ),
              ),
              const Spacer(),
              if (_activePromos.length > 1)
                Row(
                  children: List.generate(
                    _activePromos.length,
                    (index) => AnimatedContainer(
                      duration: const Duration(milliseconds: 300),
                      margin: const EdgeInsets.symmetric(horizontal: 2),
                      width: _currentPromoPage == index ? 16 : 6,
                      height: 6,
                      decoration: BoxDecoration(
                        borderRadius: BorderRadius.circular(3),
                        color: _currentPromoPage == index
                            ? Colors.teal
                            : Colors.grey.withOpacity(0.3),
                      ),
                    ),
                  ),
                ),
            ],
          ),
        ),
        const SizedBox(height: 12),
        SizedBox(
          height: 120,
          child: PageView.builder(
            controller: _promoPageController,
            onPageChanged: (index) {
              setState(() => _currentPromoPage = index);
            },
            itemCount: _activePromos.length,
            itemBuilder: (context, index) {
              final p = _activePromos[index];
              return _buildPromoCard(p, accentColor);
            },
          ),
        ),
        const SizedBox(height: 8),
      ],
    );
  }

  Widget _buildPromoCard(Map<String, dynamic> p, Color accentColor) {
    final nominalText = p['tipe_diskon'] == 'persen'
        ? "${p['nominal_potongan']}% OFF"
        : _formatRupiahWithDot(p['nominal_potongan']);

    final kode = (p['kode_promo'] as String?)?.isNotEmpty == true
        ? p['kode_promo']
        : null;

    final kategori = (p['kategori'] as String? ?? 'semua');
    final kategoriIcon = kategori == 'hotel'
        ? Icons.hotel_rounded
        : kategori == 'restoran'
            ? Icons.restaurant_rounded
            : Icons.card_giftcard_rounded;

    return GestureDetector(
      onTap: () {
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (_) => PromoDetailScreen(
              promo: p,
              accentColor: accentColor,
            ),
          ),
        );
      },
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 4),
        child: Container(
          decoration: BoxDecoration(
            gradient: const LinearGradient(
              colors: [Color(0xFFFF6B35), Color(0xFFF7931E)],
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ),
            borderRadius: BorderRadius.circular(16),
            boxShadow: [
              BoxShadow(
                color: const Color(0xFFFF6B35).withOpacity(0.3),
                blurRadius: 12,
                offset: const Offset(0, 4),
              ),
            ],
          ),
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
          child: Row(
            children: [
              Container(
                padding: const EdgeInsets.all(10),
                decoration: const BoxDecoration(
                  color: Colors.white,
                  shape: BoxShape.circle,
                ),
                child: Icon(kategoriIcon, color: const Color(0xFFFF6B35), size: 22),
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
                        fontWeight: FontWeight.w700,
                        fontSize: 13,
                      ),
                    ),
                    const SizedBox(height: 2),
                    Text(
                      nominalText,
                      style: const TextStyle(
                        color: Colors.white,
                        fontWeight: FontWeight.w900,
                        fontSize: 16,
                      ),
                    ),
                    if (kode != null) ...[
                      const SizedBox(height: 2),
                      Container(
                        padding: const EdgeInsets.symmetric(
                            horizontal: 8, vertical: 2),
                        decoration: BoxDecoration(
                          color: Colors.white.withOpacity(0.2),
                          borderRadius: BorderRadius.circular(4),
                        ),
                        child: Text(
                          "KODE: $kode",
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 9,
                            fontWeight: FontWeight.bold,
                            letterSpacing: 1,
                          ),
                        ),
                      ),
                    ],
                  ],
                ),
              ),
              const Icon(
                Icons.chevron_right_rounded,
                color: Colors.white,
                size: 24,
              ),
            ],
          ),
        ),
      ),
    );
  }

  // ============================================================
  // BUILD HOTEL CARD - BEDA TAMPILAN
  // ============================================================
  Widget _buildHotelCard(Map<String, dynamic> item, Color accentColor) {
    final room = RoomType.fromJson(item);
    final imageUrl = _buildImageUrl(room.foto);

    return GestureDetector(
      onTap: () => _navigateToRoomDetail(item),
      child: Container(
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.08),
              blurRadius: 10,
              offset: const Offset(0, 4),
            ),
          ],
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Stack(
              children: [
                ClipRRect(
                  borderRadius: const BorderRadius.vertical(top: Radius.circular(16)),
                  child: imageUrl.isNotEmpty
                      ? Image.network(
                          imageUrl,
                          height: 120,
                          width: double.infinity,
                          fit: BoxFit.cover,
                          errorBuilder: (_, __, ___) => Container(
                            height: 120,
                            color: Colors.grey[100],
                            child: const Icon(Icons.broken_image, color: Colors.grey),
                          ),
                        )
                      : Container(
                          height: 120,
                          color: Colors.grey[100],
                          child: const Icon(Icons.image_not_supported, color: Colors.grey),
                        ),
                ),
                Positioned(
                  top: 8,
                  right: 8,
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    decoration: BoxDecoration(
                      color: accentColor,
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Text(
                      '${room.kapasitas} Org',
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 9,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
                ),
              ],
            ),
            Padding(
              padding: const EdgeInsets.all(12),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    room.namaTipe,
                    style: const TextStyle(
                      fontWeight: FontWeight.bold,
                      fontSize: 13,
                      color: Color(0xFF1F2937),
                    ),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                  const SizedBox(height: 4),
                  Row(
                    children: [
                      Text(
                        _formatRupiahWithDot(room.hargaAkhir),
                        style: TextStyle(
                          fontSize: 12,
                          color: accentColor,
                          fontWeight: FontWeight.w800,
                        ),
                      ),
                      const SizedBox(width: 4),
                      Text(
                        '/ malam',
                        style: TextStyle(
                          fontSize: 10,
                          color: Colors.grey[400],
                          fontWeight: FontWeight.w500,
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  // ============================================================
  // BUILD HOTEL SECTION
  // ============================================================
  Widget _buildHotelSection(Color primaryColor, Color accentColor) {
    final items = _filteredRooms.isNotEmpty ? _filteredRooms : _roomTypes;
    if (items.isEmpty) {
      return const Padding(
        padding: EdgeInsets.all(16),
        child: Text("Tidak ada data hotel",
            style: TextStyle(color: Colors.grey)),
      );
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const SizedBox(height: 8),
        SizedBox(
          height: 200,
          child: PageView.builder(
            controller: _hotelPageController,
            onPageChanged: (index) {
              setState(() => _currentHotelPage = index);
            },
            itemCount: items.length,
            itemBuilder: (context, index) {
              final item = items[index];
              return Padding(
                padding: const EdgeInsets.symmetric(horizontal: 6),
                child: _buildHotelCard(item, accentColor),
              );
            },
          ),
        ),
        if (items.length > 1)
          Padding(
            padding: const EdgeInsets.only(top: 8),
            child: Center(
              child: Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: List.generate(
                  items.length > 5 ? 5 : items.length,
                  (index) => AnimatedContainer(
                    duration: const Duration(milliseconds: 300),
                    margin: const EdgeInsets.symmetric(horizontal: 3),
                    width: _currentHotelPage == index ? 16 : 6,
                    height: 5,
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(3),
                      color: _currentHotelPage == index
                          ? accentColor
                          : Colors.grey.withOpacity(0.3),
                    ),
                  ),
                ),
              ),
            ),
          ),
        const SizedBox(height: 4),
      ],
    );
  }

  // ============================================================
  // BUILD FOOD CARD - BEDA TAMPILAN
  // ============================================================
  Widget _buildFoodCard(Map<String, dynamic> item, Color accentColor) {
    final menu = MenuResto.fromJson(item);
    final imageUrl = _buildImageUrl(menu.fotoMenu);

    return GestureDetector(
      onTap: () => _navigateToMenuDetail(item),
      child: Container(
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.08),
              blurRadius: 10,
              offset: const Offset(0, 4),
            ),
          ],
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Stack(
              children: [
                ClipRRect(
                  borderRadius: const BorderRadius.vertical(top: Radius.circular(16)),
                  child: imageUrl.isNotEmpty
                      ? Image.network(
                          imageUrl,
                          height: 120,
                          width: double.infinity,
                          fit: BoxFit.cover,
                          errorBuilder: (_, __, ___) => Container(
                            height: 120,
                            color: Colors.grey[100],
                            child: const Icon(Icons.broken_image, color: Colors.grey),
                          ),
                        )
                      : Container(
                          height: 120,
                          color: Colors.grey[100],
                          child: const Icon(Icons.image_not_supported, color: Colors.grey),
                        ),
                ),
                Positioned(
                  top: 8,
                  right: 8,
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    decoration: BoxDecoration(
                      color: Colors.green,
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: const Text(
                      'POPULER',
                      style: TextStyle(
                        color: Colors.white,
                        fontSize: 9,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
                ),
              ],
            ),
            Padding(
              padding: const EdgeInsets.all(12),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    menu.namaMenu,
                    style: const TextStyle(
                      fontWeight: FontWeight.bold,
                      fontSize: 13,
                      color: Color(0xFF1F2937),
                    ),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                  const SizedBox(height: 4),
                  Text(
                    _formatRupiahWithDot(menu.hargaAkhir),
                    style: TextStyle(
                      fontSize: 12,
                      color: accentColor,
                      fontWeight: FontWeight.w800,
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  // ============================================================
  // BUILD FOOD SECTION
  // ============================================================
  Widget _buildFoodSection(Color primaryColor, Color accentColor) {
    final items = _filteredFoods.isNotEmpty ? _filteredFoods : _foodMenus;
    if (items.isEmpty) {
      return const Padding(
        padding: EdgeInsets.all(16),
        child: Text("Tidak ada data makanan",
            style: TextStyle(color: Colors.grey)),
      );
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const SizedBox(height: 8),
        SizedBox(
          height: 200,
          child: PageView.builder(
            controller: _foodPageController,
            onPageChanged: (index) {
              setState(() => _currentFoodPage = index);
            },
            itemCount: items.length,
            itemBuilder: (context, index) {
              final item = items[index];
              return Padding(
                padding: const EdgeInsets.symmetric(horizontal: 6),
                child: _buildFoodCard(item, accentColor),
              );
            },
          ),
        ),
        if (items.length > 1)
          Padding(
            padding: const EdgeInsets.only(top: 8),
            child: Center(
              child: Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: List.generate(
                  items.length > 5 ? 5 : items.length,
                  (index) => AnimatedContainer(
                    duration: const Duration(milliseconds: 300),
                    margin: const EdgeInsets.symmetric(horizontal: 3),
                    width: _currentFoodPage == index ? 16 : 6,
                    height: 5,
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(3),
                      color: _currentFoodPage == index
                          ? accentColor
                          : Colors.grey.withOpacity(0.3),
                    ),
                  ),
                ),
              ),
            ),
          ),
        const SizedBox(height: 4),
      ],
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
        // ── HEADER ──
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

        // ── SEARCH BAR ──
        // ── SEARCH BAR ──
        Transform.translate(
          offset: const Offset(0, -22),
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 20),
            child: _SearchBar(
              accentColor: accentColor,
              controller: _searchController,
            ),
          ),
        ),

        // ── KONTEN SCROLLABLE ──
        Expanded(
          child: Transform.translate(
            offset: const Offset(0, -22),
            child: RefreshIndicator(
              onRefresh: _handleRefresh,
              color: accentColor,
              backgroundColor: Colors.white,
              displacement: 20,
              child: SingleChildScrollView(
                controller: widget.scrollController,
                physics: const AlwaysScrollableScrollPhysics(),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const EventHeader(),

                    const SizedBox(height: 8),

                    // ── QUICK STATS ──
                    _buildQuickStats(),

                    const SizedBox(height: 16),

                    // ── QUICK ACCESS PILLS ──
                    _buildQuickAccessPills(primaryColor, accentColor),

                    const SizedBox(height: 20),

                    // ── PROMO STRIP ──
                    _loadingPromos
                        ? const SizedBox(
                            height: 90,
                            child: Center(child: CircularProgressIndicator()))
                        : _buildPromoSection(primaryColor, accentColor),

                    const SizedBox(height: 24),

                    // ── HOTEL TERFAVORIT ──
                    _SectionHeader(
                      title: "Hotel Terfavorit",
                      primaryColor: primaryColor,
                      onSeeAll: () => Navigator.push(context,
                          MaterialPageRoute(builder: (_) => const RoomListScreen())),
                    ),
                    _loadingRooms
                        ? const SizedBox(
                            height: 160,
                            child: Center(child: CircularProgressIndicator()))
                        : _buildHotelSection(primaryColor, accentColor),

                    const SizedBox(height: 24),

                    // ── MAKANAN TERFAVORIT ──
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
                        : _buildFoodSection(primaryColor, accentColor),

                    const SizedBox(height: 32),
                  ],
                ),
              ),
            ),
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
      padding: const EdgeInsets.fromLTRB(20, 0, 12, 8),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Row(
            children: [
              Container(
                width: 4,
                height: 18,
                decoration: BoxDecoration(
                  color: primaryColor,
                  borderRadius: BorderRadius.circular(4),
                ),
              ),
              const SizedBox(width: 10),
              Text(
                title,
                style: TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.bold,
                  color: primaryColor,
                ),
              ),
            ],
          ),
          TextButton(
            onPressed: onSeeAll,
            style: TextButton.styleFrom(
              foregroundColor: _AppColors.gold,
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
            ),
            child: const Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                Text(
                  "Lihat Semua",
                  style: TextStyle(
                    color: _AppColors.gold,
                    fontWeight: FontWeight.bold,
                    fontSize: 12,
                  ),
                ),
                SizedBox(width: 2),
                Icon(Icons.chevron_right_rounded, color: _AppColors.gold, size: 16),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

// ============================================================
// HEADER
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
          top: topPadding + 12, left: 20, right: 20, bottom: 24),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [
            primaryColor,
            primaryColor.withOpacity(0.88),
            accentColor.withOpacity(0.55),
          ],
        ),
        borderRadius: const BorderRadius.vertical(bottom: Radius.circular(28)),
        boxShadow: [
          BoxShadow(
            color: primaryColor.withOpacity(0.35),
            blurRadius: 24,
            offset: const Offset(0, 10),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            crossAxisAlignment: CrossAxisAlignment.center,
            children: [
              Container(
                padding: const EdgeInsets.all(2),
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  border: Border.all(color: accentColor, width: 2),
                ),
                child: ClipOval(
                  child: Image.asset(
                    'assets/icons/icon-purnama.png',
                    width: 34,
                    height: 34,
                    fit: BoxFit.cover,
                    errorBuilder: (c, e, s) =>
                        const Icon(Icons.hotel, color: Colors.white, size: 20),
                  ),
                ),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  mainAxisSize: MainAxisSize.min,
                  children: const [
                    Text(
                      "Hotel & Restoran",
                      style: TextStyle(
                        color: Colors.white60,
                        fontSize: 10,
                        letterSpacing: 1.2,
                      ),
                    ),
                    Text(
                      "PURNAMA BALIGE",
                      style: TextStyle(
                        color: Colors.white,
                        fontWeight: FontWeight.w900,
                        fontSize: 14,
                        letterSpacing: 0.5,
                      ),
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
                          borderRadius: BorderRadius.circular(10),
                          boxShadow: [
                            BoxShadow(
                              color: accentColor.withOpacity(0.4),
                              blurRadius: 8,
                              offset: const Offset(0, 3),
                            ),
                          ],
                        ),
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
                        width: 32,
                        height: 32,
                        decoration: BoxDecoration(
                          shape: BoxShape.circle,
                          border: Border.all(color: accentColor, width: 2),
                        ),
                        child: ClipOval(
                          child: photoUrl.isNotEmpty
                              ? Image.network(
                                  photoUrl,
                                  width: 32,
                                  height: 32,
                                  fit: BoxFit.cover,
                                  errorBuilder: (_, __, ___) => Container(
                                    color: Colors.white24,
                                    child: const Icon(Icons.person,
                                        color: Colors.white, size: 16),
                                  ),
                                )
                              : Container(
                                  color: Colors.white24,
                                  child: const Icon(Icons.person,
                                      color: Colors.white, size: 16),
                                ),
                        ),
                      ),
                    ),
                  ],
                ],
              ),
            ],
          ),

          const SizedBox(height: 6),

          Text(
            isGuest ? "Selamat Datang 👋" : "Halo, 👋",
            style: const TextStyle(color: Colors.white70, fontSize: 12),
          ),
          const SizedBox(height: 2),
          Text(
            isGuest ? "PURNAMA BALIGE" : name,
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
            style: const TextStyle(
                color: Colors.white,
                fontSize: 18,
                fontWeight: FontWeight.w900,
                letterSpacing: 0.3),
          ),
        ],
      ),
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
        onTap: () => Navigator.push(context,
            MaterialPageRoute(builder: (_) => const CartScreen())),
        size: iconSize,
      ),
      if (count > 0)
        Positioned(
          right: 2,
          top: 2,
          child: CircleAvatar(
            radius: 7,
            backgroundColor: Colors.red,
            child: Text(count.toString(),
                style: const TextStyle(fontSize: 8, color: Colors.white)),
          ),
        ),
    ],
  );
}

// ============================================================
// CIRCULAR ICON
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
  final TextEditingController controller;

  const _SearchBar({
    required this.accentColor,
    required this.controller,
  });

  @override
  Widget build(BuildContext context) => Container(
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
                controller: controller,
                decoration: const InputDecoration(
                  hintText: "Cari hotel atau restoran...",
                  border: InputBorder.none,
                  hintStyle: TextStyle(
                    color: _AppColors.textHint,
                    fontSize: 13,
                  ),
                  isDense: true,
                  contentPadding: EdgeInsets.zero,
                ),
                style: const TextStyle(
                  color: _AppColors.textMain,
                  fontSize: 13,
                ),
              ),
            ),
            if (controller.text.isNotEmpty)
              IconButton(
                onPressed: () => controller.clear(),
                icon: Icon(Icons.clear_rounded, color: Colors.grey[400], size: 18),
                padding: EdgeInsets.zero,
                constraints: const BoxConstraints(),
              ),
          ],
        ),
      );
}

// ============================================================
// BOTTOM NAV BAR
// ============================================================
class _BottomNavBar extends StatelessWidget {
  final int currentIndex;
  final Color accentColor;
  final ValueChanged<int> onTap;

  const _BottomNavBar({
    required this.currentIndex,
    required this.accentColor,
    required this.onTap,
  });

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
        top: 8,
        left: 8,
        right: 8,
        bottom: bottomPadding > 0 ? bottomPadding : 8,
      ),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: const BorderRadius.vertical(top: Radius.circular(24)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.08),
            blurRadius: 20,
            offset: const Offset(0, -4),
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
              padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
              decoration: BoxDecoration(
                color: active
                    ? accentColor.withOpacity(0.12)
                    : Colors.transparent,
                borderRadius: BorderRadius.circular(20),
              ),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Icon(
                    items[i]['icon'] as IconData,
                    color: active ? accentColor : Colors.grey[400],
                    size: active ? 26 : 22,
                  ),
                  const SizedBox(height: 2),
                  Text(
                    items[i]['label'] as String,
                    style: TextStyle(
                      color: active ? accentColor : Colors.grey[400],
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
// HELPER - FORMAT RUPIAH DENGAN TITIK
// ============================================================
String _formatRupiah(dynamic value) {
  final num angka = (value is num) ? value : num.tryParse(value.toString()) ?? 0;
  final parts = angka.toStringAsFixed(0).split('.');
  final number = parts[0];
  final formatted = number.replaceAllMapped(
    RegExp(r'(\d)(?=(\d{3})+(?!\d))'),
    (match) => '${match[1]}.',
  );
  return 'Rp $formatted';
}