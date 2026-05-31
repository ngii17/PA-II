import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/cart_provider.dart';
import '../../services/api_services.dart';
import 'menu_resto.dart';
import 'checkout_screen.dart';

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
    final cartProvider = Provider.of<CartProvider>(context, listen: false);
    if (cartProvider.items.containsKey(widget.menu.id)) {
      _localQuantity = cartProvider.items[widget.menu.id]!;
    }
    _reviewData = ApiServices.getRestoReviews(widget.menu.id);
  }

  void _refreshReviews() {
    setState(() {
      _reviewData = ApiServices.getRestoReviews(widget.menu.id);
    });
  }

  void _navigateToCheckout() {
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (context) => CheckoutScreen(
          cart: {widget.menu.id: _localQuantity},
          allMenus: [widget.menu],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final primaryColor = Theme.of(context).primaryColor;
    double totalBayar = widget.menu.hargaAkhir * _localQuantity;
    bool hasPromo = widget.menu.promoAktif != null && (widget.menu.hargaAkhir < widget.menu.hargaAsli);

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
            Stack(
              children: [
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
                if (hasPromo)
                  Positioned(
                    top: 20,
                    right: 0,
                    child: Container(
                      padding: const EdgeInsets.symmetric(horizontal: 15, vertical: 8),
                      decoration: const BoxDecoration(
                        color: Colors.red,
                        borderRadius: BorderRadius.only(
                          topLeft: Radius.circular(20),
                          bottomLeft: Radius.circular(20),
                        ),
                      ),
                      child: const Text(
                        "PROMO SPESIAL",
                        style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 12),
                      ),
                    ),
                  ),
              ],
            ),
            Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(widget.menu.namaMenu, style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold)),
                  const SizedBox(height: 5),
                  if (hasPromo)
                    Text(
                      widget.menu.promoAktif!,
                      style: const TextStyle(color: Colors.red, fontWeight: FontWeight.bold, fontSize: 14),
                    ),
                  Row(
                    children: [
                      Text(
                        "Rp ${widget.menu.hargaAkhir.toStringAsFixed(0)}",
                        style: TextStyle(color: primaryColor, fontSize: 22, fontWeight: FontWeight.bold),
                      ),
                      const SizedBox(width: 12),
                      if (hasPromo)
                        Text(
                          "Rp ${widget.menu.hargaAsli.toStringAsFixed(0)}",
                          style: const TextStyle(color: Colors.grey, decoration: TextDecoration.lineThrough, fontSize: 16),
                        ),
                    ],
                  ),
                  const Divider(height: 40),
                  const Text("Sesuaikan Pesanan", style: TextStyle(fontWeight: FontWeight.bold)),
                  const SizedBox(height: 15),
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
                  Text("Ulasan Pelanggan", style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: primaryColor)),
                  const SizedBox(height: 15),
                  FutureBuilder<Map<String, dynamic>>(
                    future: _reviewData,
                    builder: (context, snapshot) {
                      if (snapshot.connectionState == ConnectionState.waiting) return const Center(child: CircularProgressIndicator());
                      
                      final List<dynamic> reviews = snapshot.data?['data'] ?? [];
                      
                      if (reviews.isEmpty) return const Center(child: Padding(
                        padding: EdgeInsets.symmetric(vertical: 20),
                        child: Text("Belum ada ulasan.", style: TextStyle(color: Colors.grey)),
                      ));
                      
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
                              color: Colors.white,
                              borderRadius: BorderRadius.circular(12),
                              border: Border.all(color: Colors.grey[200]!),
                            ),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Row(
                                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                  children: [
                                    // MENAMPILKAN NAMA USER DARI BACKEND
                                    Text(rev['nama_user'] ?? "User", style: const TextStyle(fontWeight: FontWeight.bold)),
                                    Text(rev['tanggal'] ?? "", style: const TextStyle(fontSize: 10, color: Colors.grey)),
                                  ],
                                ),
                                const SizedBox(height: 5),
                                Row(
                                  children: List.generate(5, (s) => Icon(
                                    s < rev['rating'] ? Icons.star : Icons.star_border, 
                                    color: Colors.amber, size: 16
                                  )),
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
      bottomNavigationBar: Container(
        padding: const EdgeInsets.all(20),
        decoration: const BoxDecoration(color: Colors.white, boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 10)]),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text("Total Bayar", style: TextStyle(fontSize: 12)),
                Text("Rp ${totalBayar.toStringAsFixed(0)}", style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: primaryColor)),
              ],
            ),
            ElevatedButton(
              onPressed: _navigateToCheckout, 
              style: ElevatedButton.styleFrom(
                backgroundColor: primaryColor,
                padding: const EdgeInsets.symmetric(horizontal: 25, vertical: 15),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
              ),
              child: const Text("PESAN SEKARANG", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
            ),
          ],
        ),
      ),
    );
  }
}