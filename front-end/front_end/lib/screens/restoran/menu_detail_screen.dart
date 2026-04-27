import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/cart_provider.dart';
import '../../services/api_services.dart'; // Import API Service
import 'menu_resto.dart';

class MenuDetailScreen extends StatefulWidget {
  final MenuResto menu;
  const MenuDetailScreen({super.key, required this.menu});

  @override
  State<MenuDetailScreen> createState() => _MenuDetailScreenState();
}

class _MenuDetailScreenState extends State<MenuDetailScreen> {
  int _localQuantity = 1;
  late Future<Map<String, dynamic>> _reviewData; // Variabel untuk data ulasan

  @override
  void initState() {
    super.initState();
    // 1. Ambil jumlah porsi jika sudah ada di keranjang global
    final cartProvider = Provider.of<CartProvider>(context, listen: false);
    if (cartProvider.items.containsKey(widget.menu.id)) {
      _localQuantity = cartProvider.items[widget.menu.id]!;
    }

    // 2. Load data ulasan khusus untuk menu ini (Port 8001)
    _reviewData = ApiServices.getRestoReviews(widget.menu.id);
  }

  @override
  Widget build(BuildContext context) {
    final cartProvider = context.read<CartProvider>();
    final primaryColor = Theme.of(context).primaryColor;

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
                child: const Icon(Icons.fastfood, size: 100, color: Colors.grey),
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
                    style: TextStyle(color: primaryColor, fontSize: 20, fontWeight: FontWeight.bold),
                  ),
                  const Divider(height: 40),

                  // ATUR JUMLAH PESANAN
                  const Text("Tentukan Jumlah Porsi:", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                  const SizedBox(height: 15),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                    decoration: BoxDecoration(
                      color: primaryColor.withOpacity(0.05),
                      borderRadius: BorderRadius.circular(15),
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        IconButton(
                          icon: Icon(Icons.remove_circle, color: primaryColor, size: 35),
                          onPressed: () => setState(() => _localQuantity > 1 ? _localQuantity-- : null),
                        ),
                        const SizedBox(width: 15),
                        Text("$_localQuantity", style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold)),
                        const SizedBox(width: 15),
                        IconButton(
                          icon: Icon(Icons.add_circle, color: primaryColor, size: 35),
                          onPressed: () => setState(() => _localQuantity++),
                        ),
                      ],
                    ),
                  ),

                  const SizedBox(height: 30),
                  const Text("Deskripsi Menu:", style: TextStyle(fontWeight: FontWeight.bold)),
                  const SizedBox(height: 5),
                  Text(widget.menu.deskripsi, style: const TextStyle(height: 1.5, color: Colors.black87)),

                  const Divider(height: 40),

                  // --- BAGIAN ULASAN PELANGGAN ---
                  Text(
                    "Ulasan Pelanggan",
                    style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: primaryColor),
                  ),
                  const SizedBox(height: 15),

                  FutureBuilder<Map<String, dynamic>>(
                    future: _reviewData,
                    builder: (context, snapshot) {
                      if (snapshot.connectionState == ConnectionState.waiting) {
                        return const Center(child: CircularProgressIndicator());
                      }

                      if (snapshot.hasError || snapshot.data?['success'] == false) {
                        return const Text("Belum ada ulasan untuk menu ini.");
                      }

                      List<dynamic> reviews = snapshot.data?['data'] ?? [];

                      if (reviews.isEmpty) {
                        return const Text(
                          "Belum ada ulasan. Jadilah yang pertama memberikan penilaian!",
                          style: TextStyle(color: Colors.grey, fontStyle: FontStyle.italic),
                        );
                      }

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
                                    // Menggambar Bintang Rating
                                    Row(
                                      children: List.generate(5, (starIndex) {
                                        return Icon(
                                          starIndex < (rev['rating'] ?? 0) ? Icons.star : Icons.star_border,
                                          color: Colors.amber,
                                          size: 16,
                                        );
                                      }),
                                    ),
                                    const SizedBox(width: 10),
                                    const Text("Pelanggan Purnama",
                                        style: TextStyle(fontSize: 11, color: Colors.grey, fontWeight: FontWeight.bold)),
                                  ],
                                ),
                                const SizedBox(height: 8),
                                Text("${rev['komentar']}", style: const TextStyle(fontSize: 14, color: Colors.black87)),
                              ],
                            ),
                          );
                        },
                      );
                    },
                  ),
                  const SizedBox(height: 100), // Ruang agar tidak tertutup tombol
                ],
              ),
            ),
          ],
        ),
      ),
      bottomNavigationBar: Container(
        padding: const EdgeInsets.all(20),
        decoration: const BoxDecoration(
            color: Colors.white, boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 10, spreadRadius: 1)]),
        child: ElevatedButton.icon(
          style: ElevatedButton.styleFrom(
            backgroundColor: primaryColor,
            padding: const EdgeInsets.all(18),
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
          ),
          onPressed: () {
            cartProvider.setQuantity(widget.menu, _localQuantity);
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(
                content: Text("${widget.menu.namaMenu} ditambahkan ke keranjang!"),
                backgroundColor: Colors.green,
                behavior: SnackBarBehavior.floating,
              ),
            );
            Navigator.pop(context);
          },
          icon: const Icon(Icons.add_shopping_cart, color: Colors.white),
          label: const Text("TAMBAHKAN KE KERANJANG",
              style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
        ),
      ),
    );
  }
}