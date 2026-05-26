import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/cart_provider.dart';
import '../../colors/login_constants.dart';
import 'checkout_screen.dart';

class CartScreen extends StatelessWidget {
  const CartScreen({super.key});

  @override
  Widget build(BuildContext context) {
    // 1. Hubungkan ke Provider & Warna Tema
    final cartProvider = context.watch<CartProvider>();
    final cartItems = cartProvider.cartList;
    final primaryColor = Theme.of(context).primaryColor;

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text("Keranjang Saya", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18)),
        centerTitle: true,
        backgroundColor: AppTheme.primaryBlue,
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: cartItems.isEmpty
          ? _buildEmptyState()
          : ListView.builder(
              padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 20),
              itemCount: cartItems.length,
              itemBuilder: (context, index) {
                final menu = cartItems[index];
                // Panggil itemQuantities sesuai struktur Provider Anda
                int qty = cartProvider.itemQuantities[menu.id] ?? 0;

                return Container(
                  margin: const EdgeInsets.only(bottom: 15),
                  padding: const EdgeInsets.all(14),
                  decoration: BoxDecoration(
                    color: const Color(0xFFF8F9FA),
                    borderRadius: BorderRadius.circular(22),
                    border: Border.all(color: Colors.grey.shade100),
                    boxShadow: [
                      BoxShadow(color: Colors.black.withOpacity(0.02), blurRadius: 10, offset: const Offset(0, 4))
                    ],
                  ),
                  child: Row(
                    children: [
                      // Thumbnail Ikon Menu
                      Container(
                        width: 65, height: 65,
                        decoration: BoxDecoration(
                          color: AppTheme.goldAccent.withOpacity(0.15),
                          borderRadius: BorderRadius.circular(15),
                        ),
                        child: const Icon(Icons.restaurant_menu_rounded, color: AppTheme.primaryBlue, size: 28),
                      ),
                      const SizedBox(width: 15),
                      // Info Nama & Harga
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              menu.namaMenu, 
                              style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15, color: AppTheme.primaryBlue)
                            ),
                            const SizedBox(height: 4),
                            Text(
                              "Rp ${menu.harga.toStringAsFixed(0)}", 
                              style: const TextStyle(color: AppTheme.goldAccent, fontWeight: FontWeight.w700, fontSize: 14)
                            ),
                          ],
                        ),
                      ),
                      // Tombol Kontrol Jumlah (Quantity)
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 4, vertical: 4),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(30),
                          boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 5)],
                        ),
                        child: Row(
                          children: [
                            _buildQtyBtn(Icons.remove, () => cartProvider.removeFromCart(menu.id), Colors.grey.shade300),
                            Padding(
                              padding: const EdgeInsets.symmetric(horizontal: 12),
                              child: Text("$qty", style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 16)),
                            ),
                            _buildQtyBtn(Icons.add, () => cartProvider.addToCart(menu), AppTheme.primaryBlue),
                          ],
                        ),
                      ),
                    ],
                  ),
                );
              },
            ),
      // 2. BOTTOM PANEL (Hanya muncul jika ada isi)
      bottomNavigationBar: cartItems.isEmpty 
          ? null 
          : Container(
              padding: const EdgeInsets.fromLTRB(25, 20, 25, 35),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: const BorderRadius.vertical(top: Radius.circular(35)),
                boxShadow: [
                  BoxShadow(color: Colors.black.withOpacity(0.08), blurRadius: 20, offset: const Offset(0, -5))
                ],
              ),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Column(
                    mainAxisSize: MainAxisSize.min,
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text("Total Pembayaran", style: TextStyle(fontSize: 12, color: Colors.grey, fontWeight: FontWeight.bold)),
                      const SizedBox(height: 4),
                      Text(
                        "Rp ${cartProvider.totalPrice.toStringAsFixed(0)}", 
                        style: const TextStyle(fontSize: 22, fontWeight: FontWeight.w900, color: AppTheme.primaryBlue)
                      ),
                    ],
                  ),
                  ElevatedButton(
                    onPressed: () {
                      Navigator.push(
                        context, 
                        MaterialPageRoute(
                          builder: (context) => CheckoutScreen(
                            cart: cartProvider.itemQuantities, 
                            allMenus: cartProvider.cartList
                          )
                        )
                      );
                    },
                    style: ElevatedButton.styleFrom(
                      backgroundColor: AppTheme.goldAccent,
                      foregroundColor: AppTheme.primaryBlue,
                      elevation: 0,
                      padding: const EdgeInsets.symmetric(horizontal: 30, vertical: 16),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(18)),
                    ),
                    child: const Text("CHECKOUT", style: TextStyle(fontWeight: FontWeight.w900, letterSpacing: 1)),
                  )
                ],
              ),
            ),
    );
  }

  // Widget State Keranjang Kosong
  Widget _buildEmptyState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Container(
            padding: const EdgeInsets.all(30),
            decoration: BoxDecoration(color: Colors.grey[50], shape: BoxShape.circle),
            child: Icon(Icons.shopping_basket_outlined, size: 100, color: Colors.grey.shade300),
          ),
          const SizedBox(height: 20),
          const Text(
            "Keranjang Anda masih kosong", 
            style: TextStyle(color: Colors.grey, fontSize: 16, fontWeight: FontWeight.w500)
          ),
        ],
      ),
    );
  }

  // Widget Tombol Lingkaran +/-
  Widget _buildQtyBtn(IconData icon, VoidCallback onTap, Color color) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(6),
        decoration: BoxDecoration(color: color, shape: BoxShape.circle),
        child: Icon(icon, color: Colors.white, size: 18),
      ),
    );
  }
}