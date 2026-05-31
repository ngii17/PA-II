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
    _refreshMenus();
  }

  void _refreshMenus() {
    setState(() {
      _menuData = ApiServices.getRestaurantMenus();
    });
  }

  @override
  Widget build(BuildContext context) {
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
      body: RefreshIndicator(
        onRefresh: () async => _refreshMenus(),
        child: FutureBuilder<Map<String, dynamic>>(
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
                          int quantity = cartProvider.items[menu.id] ?? 0;
                          
                          // Logika deteksi promo
                          bool hasPromo = menu.promoAktif != null && (menu.hargaAkhir < menu.hargaAsli);

                          return Card(
                            margin: const EdgeInsets.only(bottom: 15),
                            elevation: 2,
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                            child: Padding(
                              padding: const EdgeInsets.symmetric(vertical: 8),
                              child: ListTile(
                                onTap: () {
                                  Navigator.push(
                                    context,
                                    MaterialPageRoute(builder: (context) => MenuDetailScreen(menu: menu)),
                                  );
                                },
                                leading: Stack(
                                  children: [
                                    ClipRRect(
                                      borderRadius: BorderRadius.circular(8),
                                      child: Image.network(menu.fotoMenu ?? "", width: 70, height: 70, fit: BoxFit.cover, 
                                        errorBuilder: (c,e,s) => const Icon(Icons.fastfood, size: 40)),
                                    ),
                                    // LABEL DISKON DI ATAS GAMBAR
                                    if (hasPromo)
                                      Positioned(
                                        top: 0, left: 0,
                                        child: Container(
                                          padding: const EdgeInsets.symmetric(horizontal: 5, vertical: 2),
                                          decoration: const BoxDecoration(
                                            color: Colors.red,
                                            borderRadius: BorderRadius.only(topLeft: Radius.circular(8), bottomRight: Radius.circular(8))
                                          ),
                                          child: const Text("PROMO", style: TextStyle(color: Colors.white, fontSize: 8, fontWeight: FontWeight.bold)),
                                        ),
                                      ),
                                  ],
                                ),
                                title: Text(menu.namaMenu, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                                subtitle: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    if (hasPromo)
                                      Text(
                                        menu.promoAktif!, 
                                        style: const TextStyle(color: Colors.red, fontSize: 10, fontWeight: FontWeight.bold)
                                      ),
                                    const SizedBox(height: 2),
                                    Row(
                                      children: [
                                        Text(
                                          "Rp ${menu.hargaAkhir.toStringAsFixed(0)}", 
                                          style: TextStyle(fontWeight: FontWeight.bold, color: primaryColor, fontSize: 14)
                                        ),
                                        const SizedBox(width: 8),
                                        if (hasPromo)
                                          Text(
                                            "Rp ${menu.hargaAsli.toStringAsFixed(0)}", 
                                            style: const TextStyle(color: Colors.grey, decoration: TextDecoration.lineThrough, fontSize: 11)
                                          ),
                                      ],
                                    ),
                                  ],
                                ),
                                trailing: Row(
                                  mainAxisSize: MainAxisSize.min,
                                  children: [
                                    if (quantity > 0)
                                      IconButton(
                                        icon: const Icon(Icons.remove_circle_outline, color: Colors.red, size: 28),
                                        onPressed: () => cartProvider.removeFromCart(menu.id),
                                      ),
                                    if (quantity > 0) Text("$quantity", style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                                    IconButton(
                                      icon: Icon(Icons.add_circle_outline, color: primaryColor, size: 28),
                                      onPressed: () => cartProvider.addToCart(menu),
                                    ),
                                  ],
                                ),
                              ),
                            ),
                          );
                        },
                      ),
                    ),
                  ],
                ),

                if (cartProvider.totalItems > 0)
                  Positioned(
                    bottom: 0, left: 0, right: 0,
                    child: Container(
                      padding: const EdgeInsets.all(20),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 10, spreadRadius: 1)],
                        borderRadius: const BorderRadius.vertical(top: Radius.circular(25))
                      ),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Column(
                            mainAxisSize: MainAxisSize.min,
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text("${cartProvider.totalItems} Item dipilih", style: const TextStyle(fontSize: 12, color: Colors.grey)),
                              Text("Total: Rp ${cartProvider.getTotalPrice().toStringAsFixed(0)}", 
                                style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: primaryColor)),
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
                              padding: const EdgeInsets.symmetric(horizontal: 25, vertical: 15),
                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12))
                            ),
                            child: const Text("LIHAT KERANJANG", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                          )
                        ],
                      ),
                    ),
                  ),
              ],
            );
          },
        ),
      ),
    );
  }
}