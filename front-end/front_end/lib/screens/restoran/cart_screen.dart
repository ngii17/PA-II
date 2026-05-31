import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/cart_provider.dart';
import 'checkout_screen.dart';

class CartScreen extends StatelessWidget {
  const CartScreen({super.key});

  @override
  Widget build(BuildContext context) {
    // Hubungkan ke Provider untuk memantau perubahan isi keranjang
    final cartProvider = context.watch<CartProvider>();
    final primaryColor = Theme.of(context).primaryColor;
    
    final cartItems = cartProvider.cartList;

    return Scaffold(
      appBar: AppBar(
        title: const Text("Keranjang Belanja"),
        backgroundColor: primaryColor,
        foregroundColor: Colors.white,
      ),
      body: cartItems.isEmpty
          ? const Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.shopping_basket_outlined, size: 80, color: Colors.grey),
                  SizedBox(height: 10),
                  Text("Keranjang Anda kosong", style: TextStyle(color: Colors.grey)),
                ],
              ),
            )
          : ListView.builder(
              padding: const EdgeInsets.all(15),
              itemCount: cartItems.length,
              itemBuilder: (context, index) {
                final menu = cartItems[index];
                // Mengambil jumlah per item dari Map di Provider
                int qty = cartProvider.itemQuantities[menu.id] ?? 0;

                return Card(
                  elevation: 2,
                  margin: const EdgeInsets.only(bottom: 12),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  child: ListTile(
                    contentPadding: const EdgeInsets.symmetric(horizontal: 15, vertical: 8),
                    title: Text(
                      menu.namaMenu, 
                      style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16)
                    ),
                    // SINKRONISASI: Menggunakan hargaAkhir (harga setelah diskon)
                    subtitle: Text(
                      "Rp ${(menu.hargaAkhir * qty).toStringAsFixed(0)}",
                      style: TextStyle(color: primaryColor, fontWeight: FontWeight.w600),
                    ),
                    trailing: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        IconButton(
                          icon: const Icon(Icons.remove_circle_outline, color: Colors.red),
                          onPressed: () => cartProvider.removeFromCart(menu.id),
                        ),
                        Text(
                          "$qty", 
                          style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold)
                        ),
                        IconButton(
                          icon: Icon(Icons.add_circle_outline, color: primaryColor),
                          onPressed: () => cartProvider.addToCart(menu),
                        ),
                      ],
                    ),
                  ),
                );
              },
            ),
      bottomNavigationBar: cartItems.isEmpty 
          ? null 
          : Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: Colors.white, 
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.05), 
                    blurRadius: 10, 
                    offset: const Offset(0, -5)
                  )
                ],
                borderRadius: const BorderRadius.vertical(top: Radius.circular(20))
              ),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Column(
                    mainAxisSize: MainAxisSize.min,
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text("Total Bayar:", style: TextStyle(fontSize: 12, color: Colors.grey)),
                      // Menampilkan total harga dari seluruh item di keranjang
                      Text(
                        "Rp ${cartProvider.totalPrice.toStringAsFixed(0)}", 
                        style: TextStyle(
                          fontSize: 20, 
                          fontWeight: FontWeight.bold, 
                          color: primaryColor
                        )
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
                      backgroundColor: primaryColor,
                      padding: const EdgeInsets.symmetric(horizontal: 25, vertical: 15),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      elevation: 0
                    ),
                    child: const Text(
                      "LANJUT KE CHECKOUT", 
                      style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)
                    ),
                  )
                ],
              ),
            ),
    );
  }
}