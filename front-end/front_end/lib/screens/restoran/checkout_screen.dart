import 'package:flutter/material.dart';
import 'package:provider/provider.dart'; 
import 'package:shared_preferences/shared_preferences.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../services/api_services.dart';
import '../../providers/cart_provider.dart'; 
import 'menu_resto.dart';
import 'waiting_payment_screen.dart';
import 'package:firebase_messaging/firebase_messaging.dart'; 

class CheckoutScreen extends StatefulWidget {
  final Map<int, int> cart;
  final List<MenuResto> allMenus;

  const CheckoutScreen({super.key, required this.cart, required this.allMenus});

  @override
  State<CheckoutScreen> createState() => _CheckoutScreenState();
}

class _CheckoutScreenState extends State<CheckoutScreen> {
  String _paymentMethod = "Transfer Bank";
  
  // --- STATE LOKASI PENGANTARAN ---
  String _deliveryType = "Meja"; 
  final TextEditingController _locationController = TextEditingController();
  
  final TextEditingController _promoController = TextEditingController();
  double _discount = 0;
  String _promoName = "";
  bool _isProcessing = false;

  // --- SINKRONISASI: Menggunakan hargaAkhir dari Model ---
  List<Map<String, dynamic>> _getCartItemsDetails() {
    List<Map<String, dynamic>> details = [];
    widget.cart.forEach((id, qty) {
      final menu = widget.allMenus.firstWhere((m) => m.id == id);
      details.add({
        'menu': menu,
        'qty': qty,
        'total': menu.hargaAkhir * qty, // Perubahan ke hargaSetelahDiskon
      });
    });
    return details;
  }

  // --- SINKRONISASI: Menggunakan hargaAkhir untuk Subtotal ---
  double _getSubtotal() {
    double total = 0;
    widget.cart.forEach((id, qty) {
      final menu = widget.allMenus.firstWhere((m) => m.id == id);
      total += (menu.hargaAkhir * qty);
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

  void _payNow() async {
    // Validasi input nomor meja/kamar
    if (_locationController.text.isEmpty) {
      _showSnackBar("Mohon isi nomor $_deliveryType Anda", Colors.orange);
      return;
    }

    if (_isProcessing) return;
    setState(() => _isProcessing = true);
    
    try {
      final SharedPreferences prefs = await SharedPreferences.getInstance();
      int? userId = prefs.getInt('user_id');

      // Ambil Token FCM dengan proteksi timeout
      String? fcmToken;
      try {
        fcmToken = await FirebaseMessaging.instance.getToken().timeout(const Duration(seconds: 5));
      } catch (e) {
        print("LOG_ERROR: Gagal ambil token: $e");
      }

      List<Map<String, dynamic>> items = [];
      widget.cart.forEach((id, qty) => items.add({"menu_id": id, "jumlah": qty}));

      // Kirim data ke Laravel Port 8001
      final result = await ApiServices.placeRestaurantOrder({
        "user_id": userId ?? 1,
        "fcm_token": fcmToken,
        "metode_pembayaran": _paymentMethod,
        "total_harga": _getSubtotal() - _discount,
        "tipe_pengantaran": _deliveryType,    
        "nomor_lokasi": _locationController.text, 
        "items": items
      });

      if (result['success'] == true) {
        if (mounted) {
          context.read<CartProvider>().clearCart();
        }

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
                final MenuResto menu = item['menu'];
                return ListTile(
                  contentPadding: EdgeInsets.zero,
                  title: Text(menu.namaMenu, style: const TextStyle(fontWeight: FontWeight.bold)),
                  // SINKRONISASI: Menampilkan harga akhir (setelah diskon promo)
                  subtitle: Text("${item['qty']} x Rp ${menu.hargaAkhir.toStringAsFixed(0)}"),
                  trailing: Text("Rp ${item['total'].toStringAsFixed(0)}"),
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
                    onChanged: (v) {
                      setState(() {
                        _deliveryType = v!;
                      });
                    },
                  ),
                ),
                Expanded(
                  child: RadioListTile<String>(
                    title: const Text("Kamar"),
                    value: "Kamar",
                    groupValue: _deliveryType,
                    contentPadding: EdgeInsets.zero,
                    activeColor: primaryColor,
                    onChanged: (v) {
                      setState(() {
                        _deliveryType = v!;
                        // Jika pindah ke Kamar, pastikan metode bayar bukan 'Bayar di Kasir'
                        if (_paymentMethod == "Bayar di Kasir") {
                          _paymentMethod = "Transfer Bank";
                        }
                      });
                    },
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
                border: const OutlineInputBorder(),
                focusedBorder: OutlineInputBorder(borderSide: BorderSide(color: primaryColor)),
              ),
            ),
            const SizedBox(height: 30),

            const Text("Metode Pembayaran", style: TextStyle(fontWeight: FontWeight.bold)),
            const SizedBox(height: 10),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 12),
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(10),
                border: Border.all(color: Colors.grey[300]!),
              ),
              child: DropdownButtonHideUnderline(
                child: DropdownButton<String>(
                  isExpanded: true,
                  value: _paymentMethod,
                  // LOGIKA: 'Bayar di Kasir' hanya muncul jika _deliveryType adalah 'Meja'
                  items: [
                    "Transfer Bank", 
                    "E-Wallet", 
                    if (_deliveryType == "Meja") "Bayar di Kasir"
                  ].map((v) => DropdownMenuItem(value: v, child: Text(v))).toList(),
                  onChanged: (v) => setState(() => _paymentMethod = v!),
                ),
              ),
            ),
            const SizedBox(height: 25),

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
                ElevatedButton(
                  onPressed: _checkPromo,
                  style: ElevatedButton.styleFrom(backgroundColor: primaryColor),
                  child: const Text("CEK", style: TextStyle(color: Colors.white)),
                ),
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