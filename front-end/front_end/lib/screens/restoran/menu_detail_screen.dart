import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/cart_provider.dart';
import '../../services/api_services.dart';
import 'menu_resto.dart';
import 'checkout_screen.dart'; // Import halaman konfirmasi

class MenuDetailScreen extends StatefulWidget {
  final MenuResto menu;
  const MenuDetailScreen({super.key, required this.menu});

  @override
  State<MenuDetailScreen> createState() => _MenuDetailScreenState();
}

class _MenuDetailScreenState extends State<MenuDetailScreen> {
  int _localQuantity = 1;
  late Future<Map<String, dynamic>> _reviewData;

  @override
  void initState() {
    super.initState();
    // Cek apakah item ini sudah ada di keranjang untuk menyesuaikan jumlah porsi awal
    final cartProvider = Provider.of<CartProvider>(context, listen: false);
    if (cartProvider.itemQuantities.containsKey(widget.menu.id)) {
      _localQuantity = cartProvider.itemQuantities[widget.menu.id]!;
    }
    _reviewData = ApiServices.getRestoReviews(widget.menu.id);
  }

  // --- FUNGSI ARAHKAN KE KONFIRMASI PESANAN ---
  void _navigateToCheckout() {
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (context) => CheckoutScreen(
          // Kirim data dalam bentuk Map (id: jumlah) sesuai kebutuhan CheckoutScreen
          cart: {widget.menu.id: _localQuantity},
          // Kirim data menu dalam bentuk List
          allMenus: [widget.menu],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
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
            // Gambar Menu
            Image.network(
              widget.menu.fotoMenu ?? "",
              height: 250,
              width: double.infinity,
              fit: BoxFit.cover,
              errorBuilder: (c, e, s) => Container(
                height: 200,
                color: Colors.grey[200],
                child: const Icon(Icons.fastfood, size: 100, color: Colors.white),
              ),
            ),
            Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(widget.menu.namaMenu, style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold)),
                  Text(
                    "Rp ${widget.menu.harga.toStringAsFixed(0)}",
                    style: TextStyle(color: primaryColor, fontSize: 18, fontWeight: FontWeight.bold),
                  ),
                  const Divider(height: 40),
                  
                  const Text("Sesuaikan Pesanan", style: TextStyle(fontWeight: FontWeight.bold)),
                  const SizedBox(height: 15),
                  
                  // Input Jumlah Porsi
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text("Jumlah Porsi"),
                      Row(
                        children: [
                          IconButton(
                            icon: Icon(Icons.remove_circle_outline, color: primaryColor),
                            onPressed: () => setState(() => _localQuantity > 1 ? _localQuantity-- : null),
                          ),
                          Text("$_localQuantity", style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                          IconButton(
                            icon: Icon(Icons.add_circle_outline, color: primaryColor),
                            onPressed: () => setState(() => _localQuantity++),
                          ),
                        ],
                      )
                    ],
                  ),
                  
                  const Divider(height: 40),
                  
                  // Ulasan Pelanggan
                  Text(
                    "Ulasan Pelanggan",
                    style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: primaryColor),
                  ),
                  const SizedBox(height: 15),
                  FutureBuilder<Map<String, dynamic>>(
                    future: _reviewData,
                    builder: (context, snapshot) {
                      if (snapshot.connectionState == ConnectionState.waiting) return const Center(child: CircularProgressIndicator());
                      List<dynamic> reviews = snapshot.data?['data'] ?? [];
                      if (reviews.isEmpty) return const Text("Belum ada ulasan.", style: TextStyle(color: Colors.grey));
                      
                      return ListView.builder(
                        shrinkWrap: true,
                        physics: const NeverScrollableScrollPhysics(),
                        itemCount: reviews.length,
                        itemBuilder: (context, index) {
                          final rev = reviews[index];
                          return Container(
                            margin: const EdgeInsets.only(bottom: 15),
                            padding: const EdgeInsets.all(15),
                            decoration: BoxDecoration(
                              color: primaryColor.withOpacity(0.05),
                              borderRadius: BorderRadius.circular(12),
                            ),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Row(
                                  children: [
                                    Row(
                                      children: List.generate(
                                        5,
                                        (s) => Icon(
                                          s < rev['rating'] ? Icons.star : Icons.star_border,
                                          color: Colors.amber,
                                          size: 16,
                                        ),
                                      ),
                                    ),
                                    const SizedBox(width: 10),
                                    const Text("Pelanggan Resto", style: TextStyle(fontSize: 11, color: Colors.grey)),
                                  ],
                                ),
                                const SizedBox(height: 8),
                                Text("${rev['komentar']}"),
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
      
      // Bottom Navigation Bar untuk Aksi Order
      bottomNavigationBar: Container(
        padding: const EdgeInsets.all(20),
        decoration: const BoxDecoration(
          color: Colors.white,
          boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 10)],
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text("Total Bayar", style: TextStyle(fontSize: 12)),
                Text(
                  "Rp ${totalBayar.toStringAsFixed(0)}",
                  style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: primaryColor),
                ),
              ],
            ),
            ElevatedButton(
              onPressed: _navigateToCheckout, // Pindah ke Konfirmasi Pesanan
              style: ElevatedButton.styleFrom(
                backgroundColor: primaryColor,
                padding: const EdgeInsets.symmetric(horizontal: 25, vertical: 15),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
              ),
              child: const Text(
                "PESAN SEKARANG",
                style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
              ),
            ),
          ],
        ),
      ),
    );
  }
}