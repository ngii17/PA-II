import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../services/api_services.dart';
import '../../providers/cart_provider.dart';
import '../../providers/event_provider.dart';
import 'menu_resto.dart';
import 'cart_screen.dart';
import 'waiting_payment_screen.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import '../event/event_header.dart';
import '../notification/notification_screen.dart';

class CheckoutScreen extends StatefulWidget {
  final Map<int, int> cart;
  final List<MenuResto> allMenus;

  const CheckoutScreen({super.key, required this.cart, required this.allMenus});

  @override
  State<CheckoutScreen> createState() => _CheckoutScreenState();
}

class _CheckoutScreenState extends State<CheckoutScreen> {
  String _paymentMethod = "Transfer Bank";
  String _deliveryType = "Meja";
  final TextEditingController _locationController = TextEditingController();
  final TextEditingController _promoController = TextEditingController();
  double _discount = 0;
  String _promoName = "";
  int? _appliedPromoId; // ← simpan promo_id dari response backend
  bool _isProcessing = false;

  List<Map<String, dynamic>> _getCartItemsDetails() {
    List<Map<String, dynamic>> details = [];
    widget.cart.forEach((id, qty) {
      final menu = widget.allMenus.firstWhere((m) => m.id == id);
      details.add({
        'menu': menu,
        'qty': qty,
        'total': menu.hargaAkhir * qty,
      });
    });
    return details;
  }

  double _getSubtotal() {
    double total = 0;
    widget.cart.forEach((id, qty) {
      final menu = widget.allMenus.firstWhere((m) => m.id == id);
      total += (menu.hargaAkhir * qty);
    });
    return total;
  }

  String _processImageUrl(String? imageUrl) {
    String finalImageUrl = imageUrl ?? "";
    if (finalImageUrl.contains(RegExp(r'\d+\.\d+\.\d+\.\d+'))) {
      finalImageUrl = finalImageUrl.replaceAll(RegExp(r'\d+\.\d+\.\d+\.\d+'), ApiServices.ipAddress);
    } else if (finalImageUrl.isNotEmpty && !finalImageUrl.startsWith('http')) {
      finalImageUrl = "http://${ApiServices.ipAddress}:8001/storage/$finalImageUrl";
    }
    return finalImageUrl;
  }

  void _checkPromo() async {
    if (_promoController.text.isEmpty) return;

    final prefs = await SharedPreferences.getInstance();
    int userId = prefs.getInt('user_id') ?? 0;

    if (userId == 0) {
      _showSnackBar("Silakan login terlebih dahulu", Colors.red);
      return;
    }

    final result = await ApiServices.checkPromoCode(
      _promoController.text,
      'restoran',
      userId: userId,
      totalHarga: _getSubtotal(), // kirim subtotal, backend yang hitung potongan
    );

    if (result['success'] == true) {
      setState(() {
        _appliedPromoId = result['data']['promo_id'];
        _promoName      = result['data']['nama_promo'];
        // Pakai potongan_dihitung dari backend — fix bug voucher nominal > total harga
        _discount       = double.parse(result['data']['potongan_dihitung'].toString());
      });
      _showSnackBar("Promo berhasil dipasang!", Colors.green);
    } else {
      setState(() {
        _appliedPromoId = null;
        _discount       = 0;
        _promoName      = "";
      });
      _showSnackBar(result['message'] ?? "Kode promo tidak valid", Colors.red);
    }
  }

  void _payNow() async {
    if (_locationController.text.isEmpty) {
      _showSnackBar("Mohon isi nomor $_deliveryType Anda", Colors.orange);
      return;
    }
    if (_isProcessing) return;
    setState(() => _isProcessing = true);

    try {
      final prefs = await SharedPreferences.getInstance();
      int? userId = prefs.getInt('user_id');
      String? fcmToken;
      try {
        fcmToken = await FirebaseMessaging.instance.getToken().timeout(const Duration(seconds: 5));
      } catch (e) {
        print("LOG_ERROR: Gagal ambil token: $e");
      }

      List<Map<String, dynamic>> items = [];
      widget.cart.forEach((id, qty) => items.add({"menu_id": id, "jumlah": qty}));

      final result = await ApiServices.placeRestaurantOrder({
        "user_id"          : userId ?? 1,
        "fcm_token"        : fcmToken,
        "metode_pembayaran": _paymentMethod,
        "total_harga"      : _getSubtotal(), // ← kirim SUBTOTAL sebelum diskon, backend yang potong
        "tipe_pengantaran" : _deliveryType,
        "nomor_lokasi"     : _locationController.text,
        "items"            : items,
        "promo_id"         : _appliedPromoId, // ← null jika tidak ada promo
      });

      if (result['success'] == true) {
        if (mounted) context.read<CartProvider>().clearCart();
        if (_paymentMethod != "Bayar di Kasir") {
          String? redirectUrl = result['redirect_url'];
          int? orderId = result['data'] != null ? result['data']['order_id'] : result['order_id'];
          if (redirectUrl != null && orderId != null) {
            await launchUrl(Uri.parse(redirectUrl), mode: LaunchMode.externalApplication);
            if (!mounted) return;
            Navigator.push(context, MaterialPageRoute(builder: (context) => WaitingPaymentScreen(orderId: orderId)));
          }
        } else {
          _showSuccessCashDialog();
        }
      } else {
        _showSnackBar(result['message'] ?? "Gagal memproses pesanan", Colors.red);
      }
    } catch (e) {
      _showSnackBar("Terjadi kesalahan sistem: $e", Colors.red);
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
        title: const Text("Berhasil!", style: TextStyle(fontWeight: FontWeight.bold)),
        content: const Text("Pesanan diterima. Silakan lakukan pembayaran di Kasir."),
        actions: [
          TextButton(
            onPressed: () => Navigator.popUntil(context, (r) => r.isFirst),
            child: const Text("OK", style: TextStyle(fontWeight: FontWeight.bold)),
          )
        ],
      ),
    );
  }

  void _showSnackBar(String msg, Color color) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(msg), backgroundColor: color, behavior: SnackBarBehavior.floating),
    );
  }

  @override
  Widget build(BuildContext context) {
    final eventProvider = context.watch<EventProvider>();
    final primaryColor = eventProvider.primaryColor;
    final secondaryColor = eventProvider.secondaryColor;
    final onPrimary = primaryColor.computeLuminance() > 0.5 ? Colors.black87 : Colors.white;
    final cartProvider = context.watch<CartProvider>();

    final cartItems = _getCartItemsDetails();
    double subtotal = _getSubtotal();
    double grandTotal = subtotal - _discount;

    return Scaffold(
      backgroundColor: Colors.grey.shade50,
      appBar: AppBar(
        title: const Text("Konfirmasi Pesanan", style: TextStyle(fontWeight: FontWeight.bold)),
        backgroundColor: Colors.transparent,
        foregroundColor: onPrimary,
        elevation: 0,
        flexibleSpace: Container(
          decoration: BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
              colors: [primaryColor, secondaryColor.withOpacity(0.85)],
            ),
          ),
        ),
        leading: _PurnamaLogo(),
        actions: [
          Stack(
            clipBehavior: Clip.none,
            children: [
              IconButton(
                icon: const Icon(Icons.shopping_bag_outlined),
                onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const CartScreen())),
              ),
              if (cartProvider.totalItems > 0)
                Positioned(
                  top: 4,
                  right: 4,
                  child: Container(
                    width: 16,
                    height: 16,
                    decoration: BoxDecoration(
                      color: secondaryColor,
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
          IconButton(
            icon: const Icon(Icons.notifications_none_rounded),
            onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const NotificationScreen())),
          ),
        ],
      ),
      body: SingleChildScrollView(
        child: Column(
          children: [
            const EventHeader(),
            Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text("Daftar Pesanan", style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                  const SizedBox(height: 10),
                  ListView.builder(
                    shrinkWrap: true,
                    physics: const NeverScrollableScrollPhysics(),
                    itemCount: cartItems.length,
                    itemBuilder: (context, index) {
                      final item = cartItems[index];
                      final MenuResto menu = item['menu'];
                      final String processedImageUrl = _processImageUrl(menu.fotoMenu);

                      return ListTile(
                        contentPadding: EdgeInsets.zero,
                        leading: ClipRRect(
                          borderRadius: BorderRadius.circular(8),
                          child: Image.network(
                            processedImageUrl,
                            width: 50,
                            height: 50,
                            fit: BoxFit.cover,
                            errorBuilder: (_, __, ___) => Container(
                              width: 50,
                              height: 50,
                              color: Colors.grey[200],
                              child: const Icon(Icons.fastfood),
                            ),
                          ),
                        ),
                        title: Text(menu.namaMenu, style: const TextStyle(fontWeight: FontWeight.bold)),
                        subtitle: Text("${item['qty']} x Rp ${menu.hargaAkhir.toStringAsFixed(0)}"),
                        trailing: Text(
                          "Rp ${item['total'].toStringAsFixed(0)}",
                          style: TextStyle(color: primaryColor, fontWeight: FontWeight.bold),
                        ),
                      );
                    },
                  ),
                  const Divider(height: 40),
                  const Text("Lokasi Pengantaran", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                  const SizedBox(height: 5),
                  Row(
                    children: [
                      Expanded(
                        child: RadioListTile<String>(
                          title: const Text("Meja"),
                          value: "Meja",
                          groupValue: _deliveryType,
                          contentPadding: EdgeInsets.zero,
                          activeColor: primaryColor,
                          onChanged: (v) => setState(() => _deliveryType = v!),
                        ),
                      ),
                      Expanded(
                        child: RadioListTile<String>(
                          title: const Text("Kamar"),
                          value: "Kamar",
                          groupValue: _deliveryType,
                          contentPadding: EdgeInsets.zero,
                          activeColor: primaryColor,
                          onChanged: (v) => setState(() => _deliveryType = v!),
                        ),
                      ),
                    ],
                  ),
                  TextField(
                    controller: _locationController,
                    keyboardType: TextInputType.number,
                    decoration: InputDecoration(
                      labelText: "Nomor $_deliveryType Anda",
                      hintText: "Contoh: 05",
                      prefixIcon: Icon(_deliveryType == "Meja" ? Icons.table_restaurant : Icons.bed),
                      border: OutlineInputBorder(borderRadius: BorderRadius.circular(16)),
                      focusedBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(16),
                        borderSide: BorderSide(color: primaryColor),
                      ),
                      filled: true,
                      fillColor: Colors.white,
                    ),
                  ),
                  const SizedBox(height: 30),
                  const Text("Metode Pembayaran", style: TextStyle(fontWeight: FontWeight.bold)),
                  const SizedBox(height: 10),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 16),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(16),
                      border: Border.all(color: Colors.grey.shade200),
                    ),
                    child: DropdownButtonHideUnderline(
                      child: DropdownButton<String>(
                        isExpanded: true,
                        value: _paymentMethod,
                        items: [
                          "Transfer Bank",
                          "E-Wallet",
                          if (_deliveryType == "Meja") "Bayar di Kasir",
                        ].map((v) => DropdownMenuItem(value: v, child: Text(v, style: const TextStyle(fontSize: 14)))).toList(),
                        onChanged: (v) => setState(() => _paymentMethod = v!),
                        dropdownColor: Colors.white,
                        borderRadius: BorderRadius.circular(12),
                      ),
                    ),
                  ),
                  const SizedBox(height: 25),
                  const Text("Punya Kode Promo?", style: TextStyle(fontWeight: FontWeight.bold)),
                  const SizedBox(height: 10),
                  Row(
                    children: [
                      Expanded(
                        child: Container(
                          decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(16),
                            border: Border.all(color: Colors.grey.shade200),
                          ),
                          child: TextField(
                            controller: _promoController,
                            decoration: const InputDecoration(
                              hintText: "Masukkan kode promo",
                              border: InputBorder.none,
                              contentPadding: EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(width: 10),
                      ElevatedButton(
                        onPressed: _checkPromo,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: secondaryColor,
                          foregroundColor: Colors.white,
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(30)),
                          padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 14),
                        ),
                        child: const Text("CEK", style: TextStyle(fontWeight: FontWeight.bold)),
                      ),
                    ],
                  ),
                  // Tampilkan nama promo jika sudah terpasang
                  if (_promoName.isNotEmpty)
                    Padding(
                      padding: const EdgeInsets.only(top: 8),
                      child: Row(
                        children: [
                          const Icon(Icons.check_circle, color: Colors.green, size: 16),
                          const SizedBox(width: 6),
                          Text(
                            _promoName,
                            style: const TextStyle(color: Colors.green, fontWeight: FontWeight.w600, fontSize: 13),
                          ),
                          const Spacer(),
                          // Tombol hapus promo
                          GestureDetector(
                            onTap: () => setState(() {
                              _appliedPromoId = null;
                              _discount = 0;
                              _promoName = "";
                              _promoController.clear();
                            }),
                            child: const Icon(Icons.close, color: Colors.red, size: 16),
                          ),
                        ],
                      ),
                    ),
                  const SizedBox(height: 30),
                  Container(
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(20),
                      boxShadow: [
                        BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 10, offset: const Offset(0, 4)),
                      ],
                    ),
                    child: Column(
                      children: [
                        _buildPriceRow("Subtotal", "Rp ${subtotal.toStringAsFixed(0)}"),
                        if (_discount > 0)
                          _buildPriceRow("Potongan Promo", "- Rp ${_discount.toStringAsFixed(0)}", color: Colors.red),
                        const Divider(height: 30),
                        _buildPriceRow(
                          "Total Bayar",
                          "Rp ${grandTotal.toStringAsFixed(0)}",
                          isBold: true,
                          color: primaryColor,
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 30),
                  _isProcessing
                      ? const Center(child: CircularProgressIndicator())
                      : SizedBox(
                          width: double.infinity,
                          height: 55,
                          child: ElevatedButton(
                            onPressed: _payNow,
                            style: ElevatedButton.styleFrom(
                              backgroundColor: primaryColor,
                              foregroundColor: onPrimary,
                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                              elevation: 4,
                            ),
                            child: const Text("BAYAR SEKARANG", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                          ),
                        ),
                  const SizedBox(height: 50),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildPriceRow(String label, String value, {bool isBold = false, Color color = Colors.black}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            label,
            style: TextStyle(fontWeight: isBold ? FontWeight.bold : FontWeight.normal, color: Colors.grey.shade700),
          ),
          Text(
            value,
            style: TextStyle(fontWeight: isBold ? FontWeight.bold : FontWeight.w600, fontSize: isBold ? 20 : 14, color: color),
          ),
        ],
      ),
    );
  }
}

class _PurnamaLogo extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(left: 8),
      child: Image.asset(
        'assets/icons/icon-purnama.png',
        width: 34,
        height: 34,
        errorBuilder: (_, __, ___) => Container(
          width: 34,
          height: 34,
          decoration: BoxDecoration(
            shape: BoxShape.circle,
            gradient: const LinearGradient(
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
              colors: [Color(0xFF1A4A9E), Color(0xFF0C2D6B)],
            ),
            border: Border.all(color: const Color(0xFFC9A227), width: 2),
          ),
          child: const Center(
            child: Text(
              "P",
              style: TextStyle(color: Color(0xFFC9A227), fontWeight: FontWeight.w900, fontSize: 16),
            ),
          ),
        ),
      ),
    );
  }
}