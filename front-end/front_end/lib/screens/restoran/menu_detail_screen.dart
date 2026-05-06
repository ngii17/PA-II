import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../providers/cart_provider.dart';
import '../../services/api_services.dart';
import '../../notification/notification_service.dart'; // <--- 1. IMPORT SERVICE NOTIFIKASI
import 'menu_resto.dart';
import 'waiting_payment_screen.dart';

class MenuDetailScreen extends StatefulWidget {
  final MenuResto menu;
  const MenuDetailScreen({super.key, required this.menu});

  @override
  State<MenuDetailScreen> createState() => _MenuDetailScreenState();
}

class _MenuDetailScreenState extends State<MenuDetailScreen> {
  int _localQuantity = 1;
  late Future<Map<String, dynamic>> _reviewData;
  bool _isProcessing = false;
  String _paymentMethod = "Transfer Bank";

  @override
  void initState() {
    super.initState();
    final cartProvider = Provider.of<CartProvider>(context, listen: false);
    if (cartProvider.items.containsKey(widget.menu.id)) {
      _localQuantity = cartProvider.items[widget.menu.id]!;
    }
    _reviewData = ApiServices.getRestoReviews(widget.menu.id);
  }

  // --- 2. PERBAIKAN FUNGSI PESAN (AMBIL TOKEN ASLI) ---
  void _handleOrder() async {
    setState(() => _isProcessing = true);

    final SharedPreferences prefs = await SharedPreferences.getInstance();
    int? userId = prefs.getInt('user_id');

    // AMBIL TOKEN HP ASLI DARI FIREBASE
    String? realFcmToken = await PushNotificationService.getDeviceToken();
    print("DEBUG_RESTO_TOKEN: $realFcmToken");

    // Susun data pendaftaran (Sertakan fcm_token)
    Map<String, dynamic> requestData = {
      "user_id": userId ?? 1,
      "fcm_token": realFcmToken ?? "", // <--- SEKARANG KIRIM TOKEN ASLI
      "metode_pembayaran": _paymentMethod,
      "items": [
        {"menu_id": widget.menu.id, "jumlah": _localQuantity}
      ]
    };

    final result = await ApiServices.placeRestaurantOrder(requestData);

    setState(() => _isProcessing = false);

    if (result['success'] == true) {
      int orderId = result['data']['order_id'];
      String? redirectUrl = result['redirect_url'];

      if (_paymentMethod != "Bayar di Kasir" && redirectUrl != null) {
        final Uri url = Uri.parse(redirectUrl);
        await launchUrl(url, mode: LaunchMode.externalApplication);
        
        if (!mounted) return;
        Navigator.push(
          context, 
          MaterialPageRoute(builder: (context) => WaitingPaymentScreen(orderId: orderId))
        );
      } else {
        _showSuccessDialog("Pesanan Berhasil! Silakan ke Kasir.");
      }
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(result['message'] ?? "Gagal diproses"), backgroundColor: Colors.red)
      );
    }
  }

  void _showSuccessDialog(String msg) {
    showDialog(
      context: context, 
      builder: (context) => AlertDialog(
        title: const Text("Berhasil"), 
        content: Text(msg),
        actions: [
          TextButton(
            onPressed: () => Navigator.popUntil(context, (r) => r.isFirst), 
            child: const Text("OK")
          )
        ],
      )
    );
  }

  @override
  Widget build(BuildContext context) {
    final cartProvider = context.read<CartProvider>();
    final primaryColor = Theme.of(context).primaryColor;
    double totalBayar = widget.menu.harga * _localQuantity;

    return Scaffold(
      appBar: AppBar(
        title: Text(widget.menu.namaMenu), 
        backgroundColor: primaryColor,
        foregroundColor: Colors.white,
      ),
      body: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Image.network(
              widget.menu.fotoMenu ?? "", 
              height: 250, width: double.infinity, fit: BoxFit.cover, 
              errorBuilder: (c,e,s) => Container(
                height: 200, color: Colors.grey[200], child: const Icon(Icons.fastfood, size: 100, color: Colors.white)
              )
            ),
            Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(widget.menu.namaMenu, style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold)),
                  Text("Rp ${widget.menu.harga.toStringAsFixed(0)}", 
                      style: TextStyle(color: primaryColor, fontSize: 18, fontWeight: FontWeight.bold)),
                  const Divider(height: 40),
                  const Text("Sesuaikan Pesanan", style: TextStyle(fontWeight: FontWeight.bold)),
                  const SizedBox(height: 15),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text("Jumlah Porsi"),
                      Row(
                        children: [
                          IconButton(icon: Icon(Icons.remove_circle_outline, color: primaryColor), onPressed: () => setState(() => _localQuantity > 1 ? _localQuantity-- : null)),
                          Text("$_localQuantity", style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                          IconButton(icon: Icon(Icons.add_circle_outline, color: primaryColor), onPressed: () => setState(() => _localQuantity++)),
                        ],
                      )
                    ],
                  ),
                  const SizedBox(height: 10),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text("Metode Bayar"),
                      DropdownButton<String>(
                        value: _paymentMethod,
                        items: ["Transfer Bank", "E-Wallet", "Bayar di Kasir"].map((v) => DropdownMenuItem(value: v, child: Text(v))).toList(),
                        onChanged: (v) => setState(() => _paymentMethod = v!),
                      )
                    ],
                  ),
                  const Divider(height: 40),
                  Text("Ulasan Pelanggan", style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: primaryColor)),
                  const SizedBox(height: 15),
                  FutureBuilder<Map<String, dynamic>>(
                    future: _reviewData,
                    builder: (context, snapshot) {
                      if (snapshot.connectionState == ConnectionState.waiting) return const Center(child: CircularProgressIndicator());
                      List<dynamic> reviews = snapshot.data?['data'] ?? [];
                      if (reviews.isEmpty) return const Text("Belum ada ulasan.", style: TextStyle(color: Colors.grey));
                      return ListView.builder(
                        shrinkWrap: true, physics: const NeverScrollableScrollPhysics(),
                        itemCount: reviews.length,
                        itemBuilder: (context, index) {
                          final rev = reviews[index];
                          return Container(
                            margin: const EdgeInsets.only(bottom: 15), padding: const EdgeInsets.all(15),
                            decoration: BoxDecoration(color: primaryColor.withOpacity(0.05), borderRadius: BorderRadius.circular(12)),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Row(children: [
                                  Row(children: List.generate(5, (s) => Icon(s < rev['rating'] ? Icons.star : Icons.star_border, color: Colors.amber, size: 16))),
                                  const SizedBox(width: 10), const Text("Pelanggan Resto", style: TextStyle(fontSize: 11, color: Colors.grey)),
                                ]),
                                const SizedBox(height: 8), Text("${rev['komentar']}"),
                              ],
                            ),
                          );
                        },
                      );
                    },
                  ),
                  const SizedBox(height: 100), 
                ],
              ),
            ),
          ],
        ),
      ),
      bottomNavigationBar: Container(
        padding: const EdgeInsets.all(20),
        decoration: const BoxDecoration(color: Colors.white, boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 10)]),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Column(
              mainAxisSize: MainAxisSize.min, crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text("Total Bayar", style: TextStyle(fontSize: 12)),
                Text("Rp ${totalBayar.toStringAsFixed(0)}", style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: primaryColor)),
              ],
            ),
            _isProcessing 
              ? CircularProgressIndicator(color: primaryColor)
              : ElevatedButton(
                  onPressed: _handleOrder,
                  style: ElevatedButton.styleFrom(backgroundColor: primaryColor, padding: const EdgeInsets.symmetric(horizontal: 25, vertical: 15)),
                  child: const Text("PESAN SEKARANG", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                ),
          ],
        ),
      ),
    );
  }
}