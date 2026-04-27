import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/cart_provider.dart';
import 'checkout_screen.dart';

class CartScreen extends StatelessWidget {
  const CartScreen({super.key});

  @override
  Widget build(BuildContext context) {
    // Hubungkan ke Provider
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
          ? const Center(child: Text("Keranjang Anda kosong"))
          : ListView.builder(
              padding: const EdgeInsets.all(15),
              itemCount: cartItems.length,
              itemBuilder: (context, index) {
                final menu = cartItems[index];
                // Panggil itemQuantities sesuai yang ada di Provider
                int qty = cartProvider.itemQuantities[menu.id]!;

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
                          onPressed: () => cartProvider.removeFromCart(menu.id),
                        ),
                        Text("$qty", style: const TextStyle(fontSize: 16)),
                        IconButton(
                          icon: const Icon(Icons.add_circle_outline, color: Colors.green),
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
              decoration: const BoxDecoration(
                color: Colors.white, 
                boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 10)]
              ),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Column(
                    mainAxisSize: MainAxisSize.min,
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text("Total Bayar:", style: TextStyle(fontSize: 12)),
                      // Panggil totalPrice sebagai property (bukan fungsi)
                      Text(
                        "Rp ${cartProvider.totalPrice.toStringAsFixed(0)}", 
                        style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold)
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
                      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12)
                    ),
                    child: const Text("LANJUT KE CHECKOUT", style: TextStyle(color: Colors.white)),
                  )
                ],
              ),
            ),
    );
  }
}