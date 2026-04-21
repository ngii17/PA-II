import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../services/api_services.dart';
import 'menu_resto.dart';
import 'cart_screen.dart'; // Import halaman checkout

class MenuListScreen extends StatefulWidget {
  const MenuListScreen({super.key});

  @override
  State<MenuListScreen> createState() => _MenuListScreenState();
}

class _MenuListScreenState extends State<MenuListScreen> {
  late Future<Map<String, dynamic>> _menuData;
  
  // --- STATE KERANJANG BELANJA ---
  Map<int, int> _cart = {}; 
  double _totalPrice = 0;

  @override
  void initState() {
    super.initState();
    _menuData = ApiServices.getRestaurantMenus();
  }

  // Fungsi tambah ke keranjang
  void _addToCart(MenuResto menu) {
    setState(() {
      _cart[menu.id] = (_cart[menu.id] ?? 0) + 1;
      _totalPrice += menu.harga;
    });
  }

  // Fungsi kurang dari keranjang
  void _removeFromCart(MenuResto menu) {
    if (_cart.containsKey(menu.id) && _cart[menu.id]! > 0) {
      setState(() {
        _cart[menu.id] = _cart[menu.id]! - 1;
        _totalPrice -= menu.harga;
        if (_cart[menu.id] == 0) _cart.remove(menu.id);
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Purnama Resto"),
        backgroundColor: Colors.orangeAccent,
        foregroundColor: Colors.white,
      ),
      body: FutureBuilder<Map<String, dynamic>>(
        future: _menuData,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(child: CircularProgressIndicator());
          }
          if (snapshot.hasError || snapshot.data?['success'] == false) {
            return const Center(child: Text("Gagal memuat menu."));
          }

          // AMBIL DATA MENU DARI SERVER
          List<dynamic> listJson = snapshot.data?['data'] ?? [];
          List<MenuResto> menus = listJson.map((e) => MenuResto.fromJson(e)).toList();

          if (menus.isEmpty) {
            return const Center(child: Text("Belum ada menu yang tersedia."));
          }

          // Menggunakan Stack agar tombol keranjang bisa melayang di bawah
          return Stack(
            children: [
              ListView.builder(
                padding: EdgeInsets.only(
                  left: 15, 
                  right: 15, 
                  top: 15, 
                  bottom: _totalPrice > 0 ? 100 : 15 // Beri ruang di bawah agar tidak tertutup tombol
                ),
                itemCount: menus.length,
                itemBuilder: (context, index) {
                  final menu = menus[index];
                  int quantity = _cart[menu.id] ?? 0;

                  return Card(
                    margin: const EdgeInsets.only(bottom: 15),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                    child: ListTile(
                      leading: ClipRRect(
                        borderRadius: BorderRadius.circular(8),
                        child: Image.network(
                          menu.fotoMenu ?? "", 
                          width: 60, height: 60, fit: BoxFit.cover, 
                          errorBuilder: (_, __, ___) => const Icon(Icons.fastfood, size: 30),
                        ),
                      ),
                      title: Text(menu.namaMenu, style: const TextStyle(fontWeight: FontWeight.bold)),
                      subtitle: Text("Rp ${menu.harga.toStringAsFixed(0)}"),
                      trailing: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          if (quantity > 0)
                            IconButton(
                              icon: const Icon(Icons.remove_circle_outline, color: Colors.red), 
                              onPressed: () => _removeFromCart(menu),
                            ),
                          if (quantity > 0) 
                            Text("$quantity", style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                          IconButton(
                            icon: const Icon(Icons.add_circle_outline, color: Colors.green), 
                            onPressed: () => _addToCart(menu),
                          ),
                        ],
                      ),
                    ),
                  );
                },
              ),

              // --- TOMBOL KERANJANG (Hanya muncul jika ada item) ---
              if (_totalPrice > 0)
                Positioned(
                  bottom: 0,
                  left: 0,
                  right: 0,
                  child: Container(
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 10, spreadRadius: 1)],
                      borderRadius: const BorderRadius.vertical(top: Radius.circular(20)),
                    ),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Column(
                          mainAxisSize: MainAxisSize.min,
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text("${_cart.length} Item Terpilih", style: const TextStyle(fontSize: 12)),
                            Text(
                              "Total: Rp ${_totalPrice.toStringAsFixed(0)}", 
                              style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Colors.orangeAccent),
                            ),
                          ],
                        ),
                        ElevatedButton(
                          onPressed: () {
                            // PINDAH KE HALAMAN CHECKOUT (CartScreen)
                            Navigator.push(
                              context, 
                              MaterialPageRoute(
                                builder: (context) => CartScreen(
                                  cart: _cart, 
                                  allMenus: menus, // Sekarang variabel menus sudah dikenali
                                ),
                              ),
                            ).then((_) {
                              // Opsional: Jika ingin keranjang kosong setelah balik dari CartScreen
                              // setState(() { _cart.clear(); _totalPrice = 0; });
                            });
                          },
                          style: ElevatedButton.styleFrom(
                            backgroundColor: Colors.orangeAccent, 
                            padding: const EdgeInsets.symmetric(horizontal: 25, vertical: 12),
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                          ),
                          child: const Text("LIHAT KERANJANG", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                        ),
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