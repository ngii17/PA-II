import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:vibration/vibration.dart';

// Services & Providers
import '../../providers/cart_provider.dart';
import '../../services/api_services.dart';
import '../../notification/notification_service.dart';

// Screens
import 'menu_resto.dart';
import 'waiting_payment_screen.dart';
import '../user/login_screen.dart';
import '../user/register_screen.dart';

// Widgets & Theme
import '../../colors/login_constants.dart';
import '../../widgets/login_widgets.dart';

class MenuDetailScreen extends StatefulWidget {
  final MenuResto menu;
  const MenuDetailScreen({super.key, required this.menu});

  @override
  State<MenuDetailScreen> createState() => _MenuDetailScreenState();
}

class _MenuDetailScreenState extends State<MenuDetailScreen> with SingleTickerProviderStateMixin {
  int _localQuantity = 1;
  late Future<Map<String, dynamic>> _reviewData;
  bool _isProcessing = false;
  String _paymentMethod = "Transfer Bank";
  
  // Controller untuk efek getar/shake visual
  late AnimationController _shakeController;
  late Animation<double> _shakeAnimation;

  @override
  void initState() {
    super.initState();
    
    // Sinkronisasi jumlah porsi jika sudah ada di keranjang lokal
    final cartProvider = Provider.of<CartProvider>(context, listen: false);
    if (cartProvider.items.containsKey(widget.menu.id)) {
      _localQuantity = cartProvider.items[widget.menu.id]!;
    }
    
    _reviewData = ApiServices.getRestoReviews(widget.menu.id);

    // Inisialisasi Animasi Shake
    _shakeController = AnimationController(
      duration: const Duration(milliseconds: 500),
      vsync: this,
    );
    _shakeAnimation = TweenSequence<double>([
      TweenSequenceItem(tween: Tween(begin: 0.0, end: 12.0), weight: 1),
      TweenSequenceItem(tween: Tween(begin: 12.0, end: -12.0), weight: 1),
      TweenSequenceItem(tween: Tween(begin: -12.0, end: 12.0), weight: 1),
      TweenSequenceItem(tween: Tween(begin: 12.0, end: 0.0), weight: 1),
    ]).animate(CurvedAnimation(parent: _shakeController, curve: Curves.easeInOut));
  }

  @override
  void dispose() {
    _shakeController.dispose();
    super.dispose();
  }

  // --- FUNGSI PROTEKSI GUEST ---
  void _showGuestCaution() async {
    // Getar Fisik
    Vibration.hasVibrator().then((has) { if (has == true) Vibration.vibrate(duration: 100); });
    // Shake Layar Visual
    _shakeController.forward(from: 0.0);

    final SharedPreferences prefs = await SharedPreferences.getInstance();
    bool hasReg = prefs.getBool('has_registered') ?? false;

    if (!mounted) return;
    showDialog(
      context: context,
      builder: (c) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(25)),
        title: const Row(
          children: [
            Icon(Icons.lock_person_rounded, color: AppTheme.goldAccent),
            SizedBox(width: 10),
            Text("Butuh Akun", style: TextStyle(fontWeight: FontWeight.bold)),
          ],
        ),
        content: const Text("Fitur pemesanan dinonaktifkan untuk Tamu. Silakan masuk atau daftar untuk memesan makanan lezat kami."),
        actions: [
          TextButton(onPressed: () => Navigator.pop(c), child: const Text("Batal", style: TextStyle(color: Colors.grey))),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: AppTheme.primaryBlue, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10))),
            onPressed: () {
              Navigator.pop(c);
              Navigator.push(context, MaterialPageRoute(builder: (c) => hasReg ? const LoginScreen() : const RegisterScreen()));
            },
            child: Text(hasReg ? "Login" : "Daftar", style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
          ),
        ],
      ),
    );
  }

  // --- LOGIKA PESAN (DENGAN CEK TOKEN REAL-TIME) ---
  void _handleOrder() async {
    final SharedPreferences prefs = await SharedPreferences.getInstance();
    String? token = prefs.getString('auth_token');

    // JIKA TOKEN TIDAK ADA (GUEST) -> BLOKIR SEKARANG JUGA
    if (token == null || token.isEmpty) {
      _showGuestCaution();
      return;
    }

    // JIKA LOLOS (LOGIN) -> LANJUTKAN API
    setState(() => _isProcessing = true);
    int? userId = prefs.getInt('user_id');

    String? realFcmToken = await PushNotificationService.getDeviceToken();

    Map<String, dynamic> requestData = {
      "user_id": userId ?? 1,
      "fcm_token": realFcmToken ?? "",
      "metode_pembayaran": _paymentMethod,
      "items": [{"menu_id": widget.menu.id, "jumlah": _localQuantity}]
    };

    final result = await ApiServices.placeRestaurantOrder(requestData);
    setState(() => _isProcessing = false);

    if (result['success'] == true) {
      context.read<CartProvider>().removeFromCart(widget.menu.id);
      int orderId = result['data']['order_id'];
      String? redirectUrl = result['redirect_url'];

      if (_paymentMethod != "Bayar di Kasir" && redirectUrl != null) {
        await launchUrl(Uri.parse(redirectUrl), mode: LaunchMode.externalApplication);
        if (!mounted) return;
        Navigator.push(context, MaterialPageRoute(builder: (context) => WaitingPaymentScreen(orderId: orderId)));
      } else {
        _showSuccessDialog("Pesanan diterima! Nomor Antrean #$orderId.");
      }
    } else {
      ModernNotify.show(context, result['message'] ?? "Gagal diproses");
    }
  }

  void _showSuccessDialog(String msg) {
    showDialog(
      context: context, 
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: const Text("Berhasil", style: TextStyle(fontWeight: FontWeight.bold)), 
        content: Text(msg),
        actions: [
          ElevatedButton(
            onPressed: () => Navigator.popUntil(context, (r) => r.isFirst), 
            style: ElevatedButton.styleFrom(backgroundColor: AppTheme.primaryBlue),
            child: const Text("OK", style: TextStyle(color: Colors.white))
          )
        ],
      )
    );
  }

  @override
  Widget build(BuildContext context) {
    const Color restoColor = AppTheme.goldAccent;
    const Color navyColor = AppTheme.primaryBlue;
    double totalBayar = widget.menu.harga * _localQuantity;

    return Scaffold(
      backgroundColor: Colors.white,
      extendBodyBehindAppBar: true,
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        leading: Padding(
          padding: const EdgeInsets.all(8.0),
          child: CircleAvatar(
            backgroundColor: Colors.black.withOpacity(0.3),
            child: IconButton(
              icon: const Icon(Icons.arrow_back_ios_new_rounded, color: Colors.white, size: 18),
              onPressed: () => Navigator.pop(context),
            ),
          ),
        ),
      ),
      // Bungkus body dengan animasi shake
      body: AnimatedBuilder(
        animation: _shakeAnimation,
        builder: (context, child) => Transform.translate(offset: Offset(_shakeAnimation.value, 0), child: child),
        child: SingleChildScrollView(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Stack(
                children: [
                  ClipRRect(
                    borderRadius: const BorderRadius.only(bottomLeft: Radius.circular(50), bottomRight: Radius.circular(50)),
                    child: Image.network(
                      widget.menu.fotoMenu ?? "", 
                      height: 350, width: double.infinity, fit: BoxFit.cover, 
                      errorBuilder: (c,e,s) => Container(height: 350, color: Colors.grey[200], child: const Icon(Icons.fastfood, size: 80, color: Colors.grey)),
                    ),
                  ),
                  Positioned.fill(
                    child: Container(
                      decoration: BoxDecoration(
                        gradient: LinearGradient(begin: Alignment.topCenter, end: Alignment.bottomCenter, colors: [Colors.transparent, Colors.black.withOpacity(0.6)]),
                        borderRadius: const BorderRadius.only(bottomLeft: Radius.circular(50), bottomRight: Radius.circular(50)),
                      ),
                    ),
                  ),
                ],
              ),

              Padding(
                padding: const EdgeInsets.all(25),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(widget.menu.namaMenu, style: const TextStyle(fontSize: 26, fontWeight: FontWeight.w900, color: navyColor)),
                    const SizedBox(height: 8),
                    Text("Rp ${widget.menu.harga.toStringAsFixed(0)}", style: const TextStyle(color: restoColor, fontSize: 20, fontWeight: FontWeight.bold)),
                    
                    const Padding(padding: EdgeInsets.symmetric(vertical: 25), child: Divider()),

                    const Text("Sesuaikan Pesanan", style: TextStyle(fontWeight: FontWeight.w900, fontSize: 16, color: navyColor)),
                    const SizedBox(height: 20),
                    _buildSelectionRow(
                      "Jumlah Porsi", 
                      Row(
                        children: [
                          _circleBtn(Icons.remove, () {
                            if (_localQuantity > 1) {
                              setState(() => _localQuantity--);
                              // Keranjang lokal tetap bisa update meskipun guest
                              context.read<CartProvider>().setQuantity(widget.menu, _localQuantity);
                            }
                          }),
                          Padding(
                            padding: const EdgeInsets.symmetric(horizontal: 15),
                            child: Text("$_localQuantity", style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                          ),
                          _circleBtn(Icons.add, () {
                            setState(() => _localQuantity++);
                            context.read<CartProvider>().setQuantity(widget.menu, _localQuantity);
                          }),
                        ],
                      )
                    ),
                    const SizedBox(height: 15),
                    _buildSelectionRow(
                      "Metode Bayar", 
                      DropdownButton<String>(
                        value: _paymentMethod,
                        underline: const SizedBox(),
                        items: ["Transfer Bank", "E-Wallet", "Bayar di Kasir"].map((v) => DropdownMenuItem(value: v, child: Text(v, style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w600)))).toList(),
                        onChanged: (v) => setState(() => _paymentMethod = v!),
                      )
                    ),

                    const SizedBox(height: 40),
                    const Text("Ulasan Pelanggan", style: TextStyle(fontSize: 18, fontWeight: FontWeight.w900, color: navyColor)),
                    const SizedBox(height: 15),
                    
                    FutureBuilder<Map<String, dynamic>>(
                      future: _reviewData,
                      builder: (context, snapshot) {
                        if (snapshot.connectionState == ConnectionState.waiting) return const Center(child: CircularProgressIndicator());
                        List<dynamic> reviews = snapshot.data?['data'] ?? [];
                        if (reviews.isEmpty) return const Text("Belum ada ulasan.", style: TextStyle(color: Colors.grey, fontStyle: FontStyle.italic));
                        return Column(children: reviews.map((rev) => _buildReviewCard(rev, restoColor)).toList());
                      },
                    ),
                    const SizedBox(height: 120), 
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
      bottomNavigationBar: Container(
        padding: const EdgeInsets.fromLTRB(25, 20, 25, 35),
        decoration: BoxDecoration(
          color: Colors.white, 
          borderRadius: const BorderRadius.vertical(top: Radius.circular(35)),
          boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 20, offset: const Offset(0, -5))]
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Column(
              mainAxisSize: MainAxisSize.min, crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text("Total Bayar", style: TextStyle(fontSize: 12, color: Colors.grey, fontWeight: FontWeight.bold)),
                Text("Rp ${totalBayar.toStringAsFixed(0)}", style: const TextStyle(fontSize: 22, fontWeight: FontWeight.w900, color: navyColor)),
              ],
            ),
            _isProcessing 
              ? const CircularProgressIndicator(color: restoColor)
              : ElevatedButton(
                  onPressed: _handleOrder, // <--- Proteksi sudah dipasang di dalam fungsi ini
                  style: ElevatedButton.styleFrom(
                    backgroundColor: restoColor, 
                    foregroundColor: navyColor,
                    elevation: 0,
                    padding: const EdgeInsets.symmetric(horizontal: 30, vertical: 16),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(18))
                  ),
                  child: const Text("PESAN SEKARANG", style: TextStyle(fontWeight: FontWeight.w900, letterSpacing: 0.5)),
                ),
          ],
        ),
      ),
    );
  }

  // Widget Helper ... (sisa kodenya sama)
  Widget _buildSelectionRow(String label, Widget action) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 15, vertical: 8),
      decoration: BoxDecoration(color: const Color(0xFFF8F9FA), borderRadius: BorderRadius.circular(15)),
      child: Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [Text(label, style: const TextStyle(fontWeight: FontWeight.w600, color: Colors.black54)), action]),
    );
  }

  Widget _circleBtn(IconData icon, VoidCallback onTap) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(6),
        decoration: const BoxDecoration(color: AppTheme.primaryBlue, shape: BoxShape.circle),
        child: Icon(icon, color: Colors.white, size: 18),
      ),
    );
  }

  Widget _buildReviewCard(dynamic rev, Color color) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.all(15),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20), border: Border.all(color: Colors.grey.shade100)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(children: [
            Row(children: List.generate(5, (s) => Icon(s < (rev['rating'] ?? 0) ? Icons.star_rounded : Icons.star_outline_rounded, color: color, size: 16))),
            const SizedBox(width: 10), const Text("Verified Guest", style: TextStyle(fontSize: 10, color: Colors.grey, fontWeight: FontWeight.bold)),
          ]),
          const SizedBox(height: 8), Text("${rev['komentar']}", style: const TextStyle(fontSize: 13, height: 1.4)),
        ],
      ),
    );
  }
}