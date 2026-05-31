import 'package:flutter/material.dart';
import '../screens/restoran/menu_resto.dart';

class CartProvider extends ChangeNotifier {
  // Data asli penyimpanan objek menu
  final Map<int, MenuResto> _cartItems = {};
  // Data jumlah porsi per menu ID
  final Map<int, int> _itemQuantities = {};

  // --- GETTER UNTUK KEMUDAHAN AKSES ---
  
  // Digunakan di MenuListScreen & HomeScreen (badge)
  Map<int, int> get items => _itemQuantities;

  // Digunakan di CartScreen & CheckoutScreen
  Map<int, int> get itemQuantities => _itemQuantities;

  // SINKRONISASI: Menghitung total harga menggunakan hargaAkhir (setelah diskon)
  double getTotalPrice() {
    double total = 0;
    _itemQuantities.forEach((id, qty) {
      if (_cartItems.containsKey(id)) {
        // Menggunakan hargaAkhir agar sinkron dengan promo 3% (atau promo lainnya)
        total += (_cartItems[id]!.hargaAkhir * qty);
      }
    });
    return total;
  }

  // Getter properti untuk digunakan di UI (CartScreen)
  double get totalPrice => getTotalPrice();

  // Daftar menu unik untuk ditampilkan di halaman keranjang
  List<MenuResto> get cartList => _cartItems.values.toList();

  // Hitung jumlah total porsi secara keseluruhan (untuk angka di ikon keranjang)
  int get totalItems {
    int total = 0;
    _itemQuantities.forEach((key, value) => total += value);
    return total;
  }

  // --- FUNGSI AKSI (LOGIKA MANIPULASI DATA) ---

  // 1. Fungsi Tambah (Increment +1)
  void addToCart(MenuResto menu) {
    if (_itemQuantities.containsKey(menu.id)) {
      _itemQuantities[menu.id] = _itemQuantities[menu.id]! + 1;
    } else {
      _cartItems[menu.id] = menu;
      _itemQuantities[menu.id] = 1;
    }
    notifyListeners(); // Update semua layar yang mendengarkan (Home, Resto, dll)
  }

  // 2. Fungsi Kurang (Decrement -1)
  void removeFromCart(int menuId) {
    if (_itemQuantities.containsKey(menuId)) {
      if (_itemQuantities[menuId]! > 1) {
        _itemQuantities[menuId] = _itemQuantities[menuId]! - 1;
      } else {
        // Jika porsi tinggal 1 dan dikurangi, hapus total dari keranjang
        _itemQuantities.remove(menuId);
        _cartItems.remove(menuId);
      }
    }
    notifyListeners();
  }

  // 3. FUNGSI SET QUANTITY (Digunakan oleh MenuDetailScreen)
  // Digunakan saat user mengatur jumlah banyak sekaligus lewat halaman Detail
  void setQuantity(MenuResto menu, int qty) {
    if (qty > 0) {
      _cartItems[menu.id] = menu;
      _itemQuantities[menu.id] = qty;
    } else {
      // Jika qty disetel 0, hapus dari keranjang
      _itemQuantities.remove(menu.id);
      _cartItems.remove(menu.id);
    }
    notifyListeners();
  }

  // 4. Kosongkan Keranjang
  // Dipanggil setelah proses checkout sukses atau user melakukan logout
  void clearCart() {
    _cartItems.clear();
    _itemQuantities.clear();
    notifyListeners();
  }
}