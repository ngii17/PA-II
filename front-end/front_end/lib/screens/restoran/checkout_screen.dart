import 'package:flutter/material.dart';
import 'package:provider/provider.dart'; // Tambahkan ini
import 'package:shared_preferences/shared_preferences.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../services/api_services.dart';
import '../../providers/cart_provider.dart'; // Tambahkan ini
import 'menu_resto.dart';
import 'waiting_payment_screen.dart';
import 'package:firebase_messaging/firebase_messaging.dart'; // <--- Tambahkan ini

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

  double _getSubtotal() {
    double total = 0;
    widget.cart.forEach((id, qty) {
      final menu = widget.allMenus.firstWhere((m) => m.id == id);
      total += (menu.harga * qty);
    });
    return total;
  }

  void _checkPromo() async {
    final result = await ApiServices.checkPromoCode(_promoController.text, 'restoran');
    if (result['success'] == true) {
      setState(() {
        _promoName = result['data']['nama_promo'];
        double pot = double.parse(result['data']['nominal_potongan'].toString());
        _discount = (result['data']['tipe_diskon'] == 'persen') 
            ? (_getSubtotal() * (pot / 100)) 
            : pot;
      });
      _showSnackBar("Promo berhasil dipasang!", Colors.green);
    } else {
      setState(() { _discount = 0; _promoName = ""; });
      _showSnackBar(result['message'] ?? "Kode promo tidak valid", Colors.red);
    }
  }

  // --- PERBAIKAN FUNGSI BAYAR ---
  void _payNow() async {
    if (_isProcessing) return;

    setState(() => _isProcessing = true);
    
    try {
      final SharedPreferences prefs = await SharedPreferences.getInstance();
      int? userId = prefs.getInt('user_id');

      // --- TAMBAHAN: Ambil Token FCM dari Firebase ---
      String? fcmToken = await FirebaseMessaging.instance.getToken();
      print("FCM Token Anda: $fcmToken"); // Untuk debugging di console

      List<Map<String, dynamic>> items = [];
      widget.cart.forEach((id, qty) => items.add({"menu_id": id, "jumlah": qty}));

      // Kirim data ke Laravel
      final result = await ApiServices.placeRestaurantOrder({
        "user_id": userId ?? 1,
        "fcm_token": fcmToken, // <--- SEKARANG TOKEN SUDAH TERKIRIM
        "metode_pembayaran": _paymentMethod,
        "total_harga": _getSubtotal() - _discount,
        "items": items
      });

      if (result['success'] == true) {
        if (mounted) {
          context.read<CartProvider>().clearCart();
        }

        if (_paymentMethod != "Bayar di Kasir") {
          String? redirectUrl = result['redirect_url'];
          
          // Perbaikan pengambilan orderId agar lebih aman
          int? orderId;
          if (result['data'] != null && result['data']['order_id'] != null) {
            orderId = result['data']['order_id'];
          } else {
            orderId = result['order_id'];
          }

          if (redirectUrl != null && orderId != null) {
            await launchUrl(Uri.parse(redirectUrl), mode: LaunchMode.externalApplication);
            if (!mounted) return;
            Navigator.push(context, MaterialPageRoute(builder: (context) => WaitingPaymentScreen(orderId: orderId!)));
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
        title: const Text("Berhasil!"),
        content: const Text("Pesanan diterima. Silakan lakukan pembayaran di Kasir."),
        actions: [
          TextButton(
            onPressed: () => Navigator.popUntil(context, (r) => r.isFirst),
            child: const Text("OK"),
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
    final primaryColor = Theme.of(context).primaryColor;
    final cartItems = _getCartItemsDetails();
    double subtotal = _getSubtotal();
    double grandTotal = subtotal - _discount;

    return Scaffold(
      appBar: AppBar(
        title: const Text("Konfirmasi Pesanan"),
        backgroundColor: primaryColor,
        foregroundColor: Colors.white,
      ),
      body: SingleChildScrollView(
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
                return ListTile(
                  contentPadding: EdgeInsets.zero,
                  title: Text(item['menu'].namaMenu, style: const TextStyle(fontWeight: FontWeight.bold)),
                  subtitle: Text("${item['qty']} x Rp ${item['menu'].harga.toStringAsFixed(0)}"),
                  trailing: Text("Rp ${item['total'].toStringAsFixed(0)}"),
                );
              },
            ),
            const Divider(height: 30),
            
            const Text("Metode Pembayaran", style: TextStyle(fontWeight: FontWeight.bold)),
            DropdownButton<String>(
              isExpanded: true,
              value: _paymentMethod,
              items: ["Transfer Bank", "E-Wallet", "Bayar di Kasir"].map((v) => DropdownMenuItem(value: v, child: Text(v))).toList(),
              onChanged: (v) => setState(() => _paymentMethod = v!),
            ),
            const SizedBox(height: 20),

            const Text("Punya Kode Promo?", style: TextStyle(fontWeight: FontWeight.bold)),
            const SizedBox(height: 10),
            Row(
              children: [
                Expanded(
                  child: TextField(
                    controller: _promoController,
                    decoration: const InputDecoration(hintText: "Kode Promo", border: OutlineInputBorder()),
                  ),
                ),
                const SizedBox(width: 10),
                ElevatedButton(onPressed: _checkPromo, child: const Text("CEK")),
              ],
            ),
            
            const SizedBox(height: 30),
            Container(
              padding: const EdgeInsets.all(15),
              decoration: BoxDecoration(color: Colors.grey[100], borderRadius: BorderRadius.circular(10)),
              child: Column(
                children: [
                  _buildPriceRow("Subtotal", "Rp ${subtotal.toStringAsFixed(0)}"),
                  if (_discount > 0) _buildPriceRow("Potongan Promo", "- Rp ${_discount.toStringAsFixed(0)}", color: Colors.red),
                  const Divider(),
                  _buildPriceRow("Total Bayar", "Rp ${grandTotal.toStringAsFixed(0)}", isBold: true, color: primaryColor),
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
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                      ),
                      child: const Text("BAYAR SEKARANG", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
                    ),
                  ),
            const SizedBox(height: 50),
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
          Text(label, style: TextStyle(fontWeight: isBold ? FontWeight.bold : FontWeight.normal)),
          Text(value, style: TextStyle(fontWeight: isBold ? FontWeight.bold : FontWeight.normal, color: color, fontSize: isBold ? 18 : 14)),
        ],
      ),
    );
  }
}