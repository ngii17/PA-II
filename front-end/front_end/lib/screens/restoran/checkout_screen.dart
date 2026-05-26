import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:firebase_messaging/firebase_messaging.dart';

// Services & Providers
import '../../services/api_services.dart';
import '../../providers/cart_provider.dart';

// Screens
import 'menu_resto.dart';
import 'waiting_payment_screen.dart';

// Widgets & Constants
import '../../colors/login_constants.dart';
import '../../widgets/login_widgets.dart';

class CheckoutScreen extends StatefulWidget {
  final Map<int, int> cart;
  final List<MenuResto> allMenus;

  const CheckoutScreen({super.key, required this.cart, required this.allMenus});

  @override
  State<CheckoutScreen> createState() => _CheckoutScreenState();
}

class _CheckoutScreenState extends State<CheckoutScreen> {
  String _paymentMethod = "Transfer Bank";
  final TextEditingController _promoController = TextEditingController();
  double _discount = 0;
  String _promoName = "";
  bool _isProcessing = false;

  // Mendapatkan detail item dari keranjang
  List<Map<String, dynamic>> _getCartItemsDetails() {
    List<Map<String, dynamic>> details = [];
    widget.cart.forEach((id, qty) {
      final menu = widget.allMenus.firstWhere((m) => m.id == id);
      details.add({
        'menu': menu,
        'qty': qty,
        'total': menu.harga * qty,
      });
    });
    return details;
  }

  // Hitung Subtotal asli
  double _getSubtotal() {
    double total = 0;
    widget.cart.forEach((id, qty) {
      final menu = widget.allMenus.firstWhere((m) => m.id == id);
      total += (menu.harga * qty);
    });
    return total;
  }

  // Logika Cek Kode Promo (Port 8000)
  void _checkPromo() async {
    if (_promoController.text.isEmpty) return;
    
    final result = await ApiServices.checkPromoCode(_promoController.text, 'restoran');
    if (result['success'] == true) {
      setState(() {
        _promoName = result['data']['nama_promo'];
        double pot = double.parse(result['data']['nominal_potongan'].toString());
        _discount = (result['data']['tipe_diskon'] == 'persen') 
            ? (_getSubtotal() * (pot / 100)) 
            : pot;
      });
      ModernNotify.show(context, "Promo '$_promoName' berhasil dipasang!", isError: false);
    } else {
      setState(() { _discount = 0; _promoName = ""; });
      ModernNotify.show(context, result['message'] ?? "Kode promo tidak valid");
    }
  }

  // --- LOGIKA UTAMA PEMBAYARAN & ORDER ---
  void _payNow() async {
    if (_isProcessing) return;
    setState(() => _isProcessing = true);
    
    try {
      final SharedPreferences prefs = await SharedPreferences.getInstance();
      int? userId = prefs.getInt('user_id');

      // AMBIL TOKEN FCM UNTUK NOTIFIKASI KASIR/DAPUR
      String? fcmToken = await FirebaseMessaging.instance.getToken();

      List<Map<String, dynamic>> items = [];
      widget.cart.forEach((id, qty) => items.add({"menu_id": id, "jumlah": qty}));

      // Kirim Data Order ke Server Laravel
      final result = await ApiServices.placeRestaurantOrder({
        "user_id": userId ?? 1,
        "fcm_token": fcmToken,
        "metode_pembayaran": _paymentMethod,
        "total_harga": _getSubtotal() - _discount,
        "items": items
      });

      if (result['success'] == true) {
        // 1. KOSONGKAN KERANJANG DI PROVIDER
        if (mounted) {
          context.read<CartProvider>().clearCart();
        }

        // 2. LOGIKA ARAHAN PEMBAYARAN
        if (_paymentMethod != "Bayar di Kasir") {
          String? redirectUrl = result['redirect_url'];
          int? orderId = result['data'] != null ? result['data']['order_id'] : result['order_id'];

          if (redirectUrl != null && orderId != null) {
            await launchUrl(Uri.parse(redirectUrl), mode: LaunchMode.externalApplication);
            if (!mounted) return;
            Navigator.push(context, MaterialPageRoute(builder: (context) => WaitingPaymentScreen(orderId: orderId!)));
          }
        } else {
          _showSuccessCashDialog();
        }
      } else {
        ModernNotify.show(context, result['message'] ?? "Gagal memproses pesanan");
      }
    } catch (e) {
      ModernNotify.show(context, "Kesalahan Sistem: $e");
    } finally {
      if (mounted) setState(() => _isProcessing = false);
    }
  }

  void _showSuccessCashDialog() {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: const Text("Pesanan Diterima!", style: TextStyle(fontWeight: FontWeight.bold)),
        content: const Text("Silakan lakukan pembayaran di Kasir Restoran dengan menyebutkan ID Pesanan Anda."),
        actions: [
          ElevatedButton(
            onPressed: () => Navigator.popUntil(context, (r) => r.isFirst),
            style: ElevatedButton.styleFrom(backgroundColor: AppTheme.primaryBlue),
            child: const Text("OK", style: TextStyle(color: Colors.white)),
          )
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final cartItems = _getCartItemsDetails();
    double subtotal = _getSubtotal();
    double grandTotal = subtotal - _discount;

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        title: const Text("Konfirmasi Pesanan", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18)),
        backgroundColor: AppTheme.primaryBlue,
        foregroundColor: Colors.white,
        elevation: 0,
        centerTitle: true,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(25),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            _buildSectionTitle("Daftar Menu"),
            const SizedBox(height: 12),
            _buildOrderList(cartItems),
            
            const SizedBox(height: 30),
            _buildSectionTitle("Metode Pembayaran"),
            const SizedBox(height: 12),
            _buildPaymentDropdown(),

            const SizedBox(height: 30),
            _buildSectionTitle("Promo & Voucher"),
            const SizedBox(height: 12),
            _buildPromoSection(),
            
            const SizedBox(height: 40),
            _buildPriceSummary(subtotal, grandTotal),
            
            const SizedBox(height: 40),
            _isProcessing
                ? const Center(child: CircularProgressIndicator(color: AppTheme.primaryBlue))
                : ModernButton(
                    text: "BAYAR SEKARANG", 
                    onPressed: _payNow,
                    isResto: true, // Warna Gold untuk Resto
                  ),
            const SizedBox(height: 60),
          ],
        ),
      ),
    );
  }

  Widget _buildSectionTitle(String title) {
    return Text(title, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w900, color: AppTheme.primaryBlue, letterSpacing: 0.5));
  }

  Widget _buildOrderList(List<Map<String, dynamic>> items) {
    return Container(
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20), boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.02), blurRadius: 10)]),
      child: ListView.separated(
        shrinkWrap: true,
        physics: const NeverScrollableScrollPhysics(),
        itemCount: items.length,
        separatorBuilder: (c, i) => const Divider(height: 1, indent: 20, endIndent: 20),
        itemBuilder: (context, index) {
          final item = items[index];
          return ListTile(
            contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 8),
            title: Text(item['menu'].namaMenu, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
            subtitle: Text("${item['qty']} porsi x Rp ${item['menu'].harga.toStringAsFixed(0)}", style: const TextStyle(fontSize: 12, color: Colors.grey)),
            trailing: Text("Rp ${item['total'].toStringAsFixed(0)}", style: const TextStyle(fontWeight: FontWeight.bold, color: AppTheme.primaryBlue)),
          );
        },
      ),
    );
  }

  Widget _buildPaymentDropdown() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 20),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(15), border: Border.all(color: Colors.grey.shade200)),
      child: DropdownButtonHideUnderline(
        child: DropdownButton<String>(
          isExpanded: true,
          value: _paymentMethod,
          items: ["Transfer Bank", "E-Wallet", "Bayar di Kasir"].map((v) => DropdownMenuItem(value: v, child: Text(v, style: const TextStyle(fontWeight: FontWeight.bold)))).toList(),
          onChanged: (v) => setState(() => _paymentMethod = v!),
        ),
      ),
    );
  }

  Widget _buildPromoSection() {
    return Row(
      children: [
        Expanded(
          child: ModernInput(
            controller: _promoController, 
            label: "KODE PROMO", 
            hint: "Masukkan kode", 
            icon: Icons.confirmation_number_outlined,
            activeColor: AppTheme.goldAccent,
          ),
        ),
        const SizedBox(width: 12),
        Padding(
          padding: const EdgeInsets.only(top: 20),
          child: SizedBox(
            height: 55,
            child: ElevatedButton(
              onPressed: _checkPromo,
              style: ElevatedButton.styleFrom(backgroundColor: AppTheme.primaryBlue, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15))),
              child: const Text("CEK", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildPriceSummary(double subtotal, double grandTotal) {
    return Container(
      padding: const EdgeInsets.all(25),
      decoration: BoxDecoration(
        color: AppTheme.primaryBlue.withOpacity(0.04), 
        borderRadius: BorderRadius.circular(25),
        border: Border.all(color: AppTheme.primaryBlue.withOpacity(0.1)),
      ),
      child: Column(
        children: [
          _summaryRow("Subtotal", "Rp ${subtotal.toStringAsFixed(0)}"),
          if (_discount > 0) ...[
            const SizedBox(height: 10),
            _summaryRow("Potongan Promo", "- Rp ${_discount.toStringAsFixed(0)}", color: Colors.red),
          ],
          const Padding(padding: EdgeInsets.symmetric(vertical: 15), child: Divider()),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text("Total Bayar", style: TextStyle(fontWeight: FontWeight.w900, fontSize: 16)),
              Text("Rp ${grandTotal.toStringAsFixed(0)}", style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 20, color: AppTheme.goldAccent)),
            ],
          ),
        ],
      ),
    );
  }

  Widget _summaryRow(String label, String value, {Color color = Colors.black87}) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(label, style: const TextStyle(color: Colors.grey, fontWeight: FontWeight.w600)),
        Text(value, style: TextStyle(fontWeight: FontWeight.bold, color: color)),
      ],
    );
  }
}