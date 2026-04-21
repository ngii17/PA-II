import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../services/api_services.dart';
import 'menu_resto.dart';
import 'waiting_payment_screen.dart'; // <--- IMPORT HALAMAN MENUNGGU

class CartScreen extends StatefulWidget {
  final Map<int, int> cart;
  final List<MenuResto> allMenus;

  const CartScreen({super.key, required this.cart, required this.allMenus});

  @override
  State<CartScreen> createState() => _CartScreenState();
}

class _CartScreenState extends State<CartScreen> {
  late Map<int, int> _localCart;
  String _paymentMethod = "Transfer Bank";
  bool _isProcessing = false;

  @override
  void initState() {
    super.initState();
    _localCart = Map.from(widget.cart);
  }

  double _calculateGrandTotal() {
    double total = 0;
    _localCart.forEach((id, qty) {
      final menu = widget.allMenus.firstWhere((m) => m.id == id);
      total += (menu.harga * qty);
    });
    return total;
  }

  // --- LOGIKA PROSES PESANAN (Sama seperti Hotel) ---
void _processOrder() async {
    if (_localCart.isEmpty) return;

    // Tampilkan Loading
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => const Center(child: CircularProgressIndicator()),
    );

    final SharedPreferences prefs = await SharedPreferences.getInstance();
    int? userId = prefs.getInt('user_id');

    List<Map<String, dynamic>> items = [];
    _localCart.forEach((menuId, jumlah) {
      items.add({"menu_id": menuId, "jumlah": jumlah});
    });

    Map<String, dynamic> requestData = {
      "user_id": userId ?? 1,
      "metode_pembayaran": _paymentMethod,
      "items": items
    };

    final result = await ApiServices.placeRestaurantOrder(requestData);

    if (!mounted) return;
    Navigator.pop(context); // Tutup loading

    if (result['success'] == true) {
      // AMBIL DATA DENGAN AMAN
      // Kita cek apakah ada di result['data'] atau langsung di result
      int orderId = result['data'] != null ? result['data']['order_id'] : result['order_id'];
      String? redirectUrl = result['redirect_url'];

      if (_paymentMethod != "Bayar di Kasir" && redirectUrl != null) {
        final Uri url = Uri.parse(redirectUrl);
        
        // Membuka Browser
        await launchUrl(url, mode: LaunchMode.externalApplication);
        
        // Pindah ke Waiting Payment
        if (!mounted) return;
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (context) => WaitingPaymentScreen(orderId: orderId),
          ),
        );
      } else {
        _showCashSuccessDialog();
      }
    } else {
      // JIKA GAGAL (Misal: Stok Habis)
      _showSnackBar(result['message'] ?? "Gagal memproses pesanan", Colors.red);
    }
  }

  void _showCashSuccessDialog() {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => AlertDialog(
        title: const Text("Pesanan Berhasil"),
        content: const Text("Silakan lakukan pembayaran langsung di Kasir Restoran."),
        actions: [
          TextButton(
            onPressed: () => Navigator.popUntil(context, (route) => route.isFirst), 
            child: const Text("OK")
          ),
        ],
      ),
    );
  }

  void _showSnackBar(String msg, Color color) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(msg), backgroundColor: color),
    );
  }

  @override
  Widget build(BuildContext context) {
    final cartItems = widget.allMenus.where((m) => _localCart.containsKey(m.id)).toList();

    return Scaffold(
      appBar: AppBar(
        title: const Text("Review Pesanan"),
        backgroundColor: Colors.orangeAccent,
        foregroundColor: Colors.white,
      ),
      body: _localCart.isEmpty
          ? const Center(child: Text("Keranjang kosong"))
          : Column(
              children: [
                Expanded(
                  child: ListView.builder(
                    padding: const EdgeInsets.all(15),
                    itemCount: cartItems.length,
                    itemBuilder: (context, index) {
                      final menu = cartItems[index];
                      int qty = _localCart[menu.id]!;

                      return Card(
                        margin: const EdgeInsets.only(bottom: 10),
                        child: ListTile(
                          title: Text(menu.namaMenu, style: const TextStyle(fontWeight: FontWeight.bold)),
                          subtitle: Text("Rp ${(menu.harga * qty).toStringAsFixed(0)}"),
                          trailing: Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              IconButton(
                                icon: const Icon(Icons.remove_circle_outline, color: Colors.red),
                                onPressed: () => setState(() {
                                  if (_localCart[menu.id]! > 1) {
                                    _localCart[menu.id] = _localCart[menu.id]! - 1;
                                  } else {
                                    _localCart.remove(menu.id);
                                  }
                                }),
                              ),
                              Text("$qty"),
                              IconButton(
                                icon: const Icon(Icons.add_circle_outline, color: Colors.green),
                                onPressed: () => setState(() => _localCart[menu.id] = _localCart[menu.id]! + 1),
                              ),
                            ],
                          ),
                        ),
                      );
                    },
                  ),
                ),
                Container(
                  padding: const EdgeInsets.all(20),
                  decoration: const BoxDecoration(
                    color: Colors.white,
                    boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 10)],
                  ),
                  child: Column(
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          const Text("Metode Bayar:", style: TextStyle(fontWeight: FontWeight.bold)),
                          DropdownButton<String>(
                            value: _paymentMethod,
                            items: ["Transfer Bank", "E-Wallet", "Bayar di Kasir"]
                                .map((v) => DropdownMenuItem(value: v, child: Text(v)))
                                .toList(),
                            onChanged: (v) => setState(() => _paymentMethod = v!),
                          )
                        ],
                      ),
                      const Divider(),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Text("Total: Rp ${_calculateGrandTotal().toStringAsFixed(0)}", 
                            style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                          _isProcessing 
                            ? const CircularProgressIndicator()
                            : ElevatedButton(
                                onPressed: _processOrder,
                                style: ElevatedButton.styleFrom(backgroundColor: Colors.orangeAccent),
                                child: const Text("BAYAR SEKARANG", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                              )
                        ],
                      )
                    ],
                  ),
                )
              ],
            ),
    );
  }
}