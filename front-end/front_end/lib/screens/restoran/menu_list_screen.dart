import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../services/api_services.dart';
import '../../providers/event_provider.dart';
import '../../providers/cart_provider.dart'; 
import 'menu_resto.dart';
import 'menu_detail_screen.dart';
import 'cart_screen.dart';
import '../event/event_header.dart';

class MenuListScreen extends StatefulWidget {
  const MenuListScreen({super.key});

  @override
  State<MenuListScreen> createState() => _MenuListScreenState();
}

class _MenuListScreenState extends State<MenuListScreen> {
  late Future<Map<String, dynamic>> _menuData;

  @override
  void initState() {
    super.initState();
    _menuData = ApiServices.getRestaurantMenus();
  }

  @override
  Widget build(BuildContext context) {
    // Hubungkan ke Provider
    final cartProvider = context.watch<CartProvider>();
    final eventProvider = context.watch<EventProvider>();
    
    final primaryColor = Theme.of(context).primaryColor;
    final eventName = eventProvider.activeTheme['nama_event'];

    return Scaffold(
      appBar: AppBar(
        title: const Text("Purnama Resto"),
        backgroundColor: primaryColor,
        foregroundColor: Colors.white,
      ),
      body: FutureBuilder<Map<String, dynamic>>(
        future: _menuData,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return Center(child: CircularProgressIndicator(color: primaryColor));
          }
          if (snapshot.hasError) return const Center(child: Text("Gagal memuat menu."));

          List<dynamic> listJson = snapshot.data?['data'] ?? [];
          List<MenuResto> menus = listJson.map((e) => MenuResto.fromJson(e)).toList();

          return Stack(
            children: [
              Column(
                children: [
                  const EventHeader(),
                  if (eventProvider.activeTheme['event_code'] != 'default')
                    Container(
                      width: double.infinity,
                      padding: const EdgeInsets.all(10),
                      color: primaryColor.withOpacity(0.1),
                      child: Text("Menu Eksklusif $eventName", 
                        textAlign: TextAlign.center,
                        style: TextStyle(color: primaryColor, fontWeight: FontWeight.bold)),
                    ),
                  Expanded(
                    child: ListView.builder(
                      padding: EdgeInsets.only(bottom: cartProvider.totalItems > 0 ? 100 : 20, left: 15, right: 15, top: 10),
                      itemCount: menus.length,
                      itemBuilder: (context, index) {
                        final menu = menus[index];
                        // Sekarang 'items' sudah dikenali karena sudah ditambah di Provider
                        int quantity = cartProvider.items[menu.id] ?? 0;

                        return Card(
                          margin: const EdgeInsets.only(bottom: 15),
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                          child: ListTile(
                            onTap: () {
                              Navigator.push(
                                context,
                                MaterialPageRoute(builder: (context) => MenuDetailScreen(menu: menu)),
                              );
                            },
                            leading: ClipRRect(
                              borderRadius: BorderRadius.circular(8),
                              child: Image.network(menu.fotoMenu ?? "", width: 60, height: 60, fit: BoxFit.cover, 
                                errorBuilder: (c,e,s) => const Icon(Icons.fastfood)),
                            ),
                            title: Text(menu.namaMenu, style: const TextStyle(fontWeight: FontWeight.bold)),
                            subtitle: Text("Rp ${menu.harga.toStringAsFixed(0)}"),
                            trailing: Row(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                if (quantity > 0)
                                  IconButton(
                                    icon: const Icon(Icons.remove_circle_outline, color: Colors.red),
                                    onPressed: () => cartProvider.removeFromCart(menu.id),
                                  ),
                                if (quantity > 0) Text("$quantity", style: const TextStyle(fontWeight: FontWeight.bold)),
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
                  ),
                ],
              ),

              // --- BAR BAWAH (Review Pesanan) ---
              if (cartProvider.totalItems > 0)
                Positioned(
                  bottom: 0, left: 0, right: 0,
                  child: Container(
                    padding: const EdgeInsets.all(20),
                    decoration: const BoxDecoration(
                      color: Colors.white,
                      boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 10, spreadRadius: 1)],
                      borderRadius: BorderRadius.vertical(top: Radius.circular(20))
                    ),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Column(
                          mainAxisSize: MainAxisSize.min,
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text("${cartProvider.totalItems} Item dipilih", style: const TextStyle(fontSize: 12)),
                            // Sekarang 'getTotalPrice()' sudah dikenali
                            Text("Total: Rp ${cartProvider.getTotalPrice().toStringAsFixed(0)}", 
                              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: primaryColor)),
                          ],
                        ),
                        ElevatedButton(
                          onPressed: () {
                            Navigator.push(
                              context, 
                              MaterialPageRoute(builder: (context) => const CartScreen())
                            );
                          },
                          style: ElevatedButton.styleFrom(
                            backgroundColor: primaryColor,
                            padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10))
                          ),
                          child: const Text("add to cart", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                        )
                      ],
                    ),
                  ),
                ),
            ],
          );
        },
      ),
    );
  }
}