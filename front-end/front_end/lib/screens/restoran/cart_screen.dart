import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/cart_provider.dart';
import '../../providers/event_provider.dart';
import 'checkout_screen.dart';
import 'menu_resto.dart'; // Pastikan path import ini benar sesuai folder Anda
import '../event/event_header.dart';
import '../notification/notification_screen.dart';

class CartScreen extends StatelessWidget {
  const CartScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final cartProvider = context.watch<CartProvider>();
    final eventProvider = context.watch<EventProvider>();
    final primaryColor = eventProvider.primaryColor;
    final secondaryColor = eventProvider.secondaryColor;
    final topPadding = MediaQuery.of(context).padding.top;
    final cartItems = cartProvider.cartList;

    return Scaffold(
      backgroundColor: Colors.grey.shade50,
      body: Column(
        children: [
          // Header gradien premium
          Container(
            width: double.infinity,
            padding: EdgeInsets.only(top: topPadding + 16, left: 20, right: 20, bottom: 28),
            decoration: BoxDecoration(
              gradient: LinearGradient(
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
                colors: [
                  primaryColor,
                  primaryColor.withOpacity(0.85),
                  secondaryColor.withOpacity(0.7),
                ],
              ),
              borderRadius: const BorderRadius.only(
                bottomLeft: Radius.circular(36),
                bottomRight: Radius.circular(36),
              ),
              boxShadow: [
                BoxShadow(
                  color: primaryColor.withOpacity(0.35),
                  blurRadius: 16,
                  offset: const Offset(0, 6),
                ),
              ],
            ),
            child: Column(
              children: [
                Row(
                  children: [
                    _PurnamaLogo(primaryColor: primaryColor, secondaryColor: secondaryColor),
                    const SizedBox(width: 10),
                    const Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text("Hotel & Restoran",
                            style: TextStyle(color: Colors.white60, fontSize: 9, letterSpacing: 1.2)),
                        Text("PURNAMA BALIGE",
                            style: TextStyle(color: Colors.white, fontSize: 13, fontWeight: FontWeight.w800, letterSpacing: 0.8)),
                      ],
                    ),
                    const Spacer(),
                    _HeaderIconButton(
                      icon: Icons.notifications_none_rounded,
                      onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const NotificationScreen())),
                      primaryColor: primaryColor,
                    ),
                  ],
                ),
                const SizedBox(height: 18),
                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Icon(Icons.shopping_cart_outlined, color: secondaryColor, size: 20),
                    const SizedBox(width: 8),
                    const Text(
                      "Keranjang Saya",
                      style: TextStyle(color: Colors.white, fontSize: 20, fontWeight: FontWeight.w800, letterSpacing: 0.5),
                    ),
                  ],
                ),
              ],
            ),
          ),
          const EventHeader(),
          Expanded(
            child: cartItems.isEmpty
                ? _buildEmptyState(context, primaryColor) // Perbaikan: Kirim context ke fungsi
                : ListView.builder(
                    padding: const EdgeInsets.all(16),
                    itemCount: cartItems.length,
                    itemBuilder: (context, index) {
                      final menu = cartItems[index];
                      int qty = cartProvider.itemQuantities[menu.id] ?? 0;
                      return _CartItemCard(
                        menu: menu,
                        quantity: qty,
                        primaryColor: primaryColor,
                        onRemove: () => cartProvider.removeFromCart(menu.id),
                        onAdd: () => cartProvider.addToCart(menu),
                      );
                    },
                  ),
          ),
          if (cartItems.isNotEmpty)
            _buildBottomBar(context, cartProvider, primaryColor),
        ],
      ),
    );
  }

  // Fungsi Empty State diperbaiki dengan parameter BuildContext
  Widget _buildEmptyState(BuildContext context, Color primaryColor) {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(Icons.shopping_basket_outlined, size: 80, color: Colors.grey.shade400),
          const SizedBox(height: 16),
          Text("Keranjang Anda kosong", style: TextStyle(color: Colors.grey.shade600, fontSize: 16)),
          const SizedBox(height: 24),
          ElevatedButton.icon(
            onPressed: () => Navigator.pop(context),
            icon: const Icon(Icons.restaurant_menu_rounded),
            label: const Text("Mulai Belanja"),
            style: ElevatedButton.styleFrom(
              backgroundColor: primaryColor,
              foregroundColor: Colors.white,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildBottomBar(BuildContext context, CartProvider cartProvider, Color primaryColor) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        boxShadow: [
          BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10, offset: const Offset(0, -5)),
        ],
        borderRadius: const BorderRadius.vertical(top: Radius.circular(20)),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text("Total Bayar:", style: TextStyle(fontSize: 12, color: Colors.grey)),
              Text(
                "Rp ${cartProvider.totalPrice.toStringAsFixed(0)}",
                style: TextStyle(fontSize: 22, fontWeight: FontWeight.bold, color: primaryColor),
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
                    allMenus: cartProvider.cartList,
                  ),
                ),
              );
            },
            style: ElevatedButton.styleFrom(
              backgroundColor: primaryColor,
              foregroundColor: Colors.white,
              padding: const EdgeInsets.symmetric(horizontal: 28, vertical: 14),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
              elevation: 3,
            ),
            child: const Text("CHECKOUT", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
          ),
        ],
      ),
    );
  }
}

// Widget Logo Purnama
class _PurnamaLogo extends StatelessWidget {
  final Color primaryColor;
  final Color secondaryColor;
  const _PurnamaLogo({required this.primaryColor, required this.secondaryColor});

  @override
  Widget build(BuildContext context) {
    return Image.asset(
      'assets/icons/logo-purnama.png',
      width: 38,
      height: 38,
      errorBuilder: (_, __, ___) => Container(
        width: 38,
        height: 38,
        decoration: BoxDecoration(
          shape: BoxShape.circle,
          gradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [primaryColor, primaryColor.withOpacity(0.7)],
          ),
          border: Border.all(color: secondaryColor, width: 2),
        ),
        child: const Center(
          child: Text(
            "P",
            style: TextStyle(color: Color(0xFFD4AF37), fontWeight: FontWeight.w900, fontSize: 18),
          ),
        ),
      ),
    );
  }
}

class _HeaderIconButton extends StatelessWidget {
  final IconData icon;
  final VoidCallback onTap;
  final Color primaryColor;
  const _HeaderIconButton({required this.icon, required this.onTap, required this.primaryColor});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        width: 34,
        height: 34,
        decoration: BoxDecoration(
          color: Colors.white.withOpacity(0.12),
          shape: BoxShape.circle,
        ),
        child: Icon(icon, color: Colors.white70, size: 18),
      ),
    );
  }
}

// Widget Card Item Keranjang
class _CartItemCard extends StatelessWidget {
  final MenuResto menu;
  final int quantity;
  final Color primaryColor;
  final VoidCallback onRemove;
  final VoidCallback onAdd;

  const _CartItemCard({
    required this.menu,
    required this.quantity,
    required this.primaryColor,
    required this.onRemove,
    required this.onAdd,
  });

  @override
  Widget build(BuildContext context) {
    final bool hasPromo = menu.promoAktif != null && (menu.hargaAkhir < menu.hargaAsli);
    
    return Card(
      elevation: 2,
      margin: const EdgeInsets.only(bottom: 12),
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
      child: Padding(
        padding: const EdgeInsets.all(12),
        child: Row(
          children: [
            ClipRRect(
              borderRadius: BorderRadius.circular(12),
              child: Image.network(
                menu.fotoMenu ?? "",
                width: 60,
                height: 60,
                fit: BoxFit.cover,
                errorBuilder: (_, __, ___) => Container(
                  width: 60,
                  height: 60,
                  color: Colors.grey.shade200,
                  child: const Icon(Icons.fastfood, color: Colors.grey),
                ),
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(menu.namaMenu, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
                  if (hasPromo)
                    Text(
                      menu.promoAktif!, 
                      style: const TextStyle(color: Colors.red, fontSize: 11, fontWeight: FontWeight.bold)
                    ),
                  const SizedBox(height: 4),
                  Text(
                    "Rp ${(menu.hargaAkhir * quantity).toStringAsFixed(0)}",
                    style: TextStyle(color: primaryColor, fontWeight: FontWeight.bold, fontSize: 14),
                  ),
                ],
              ),
            ),
            Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                IconButton(
                  icon: const Icon(Icons.remove_circle_outline, color: Colors.red),
                  onPressed: onRemove,
                ),
                Text("$quantity", style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                IconButton(
                  icon: Icon(Icons.add_circle_outline, color: primaryColor),
                  onPressed: onAdd,
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}