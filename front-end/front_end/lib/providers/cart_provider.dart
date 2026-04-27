import 'package:flutter/material.dart';
import '../screens/restoran/menu_resto.dart';

class CartProvider extends ChangeNotifier {
  // Data asli penyimpanan
  final Map<int, MenuResto> _cartItems = {};
  final Map<int, int> _itemQuantities = {};

  // --- GETTER UNTUK KEMUDAHAN AKSES DI BERBAGAI HALAMAN ---
  
  // Digunakan di MenuListScreen & HomeScreen
  Map<int, int> get items => _itemQuantities;

  // Digunakan di CartScreen (tanpa kurung)
  Map<int, int> get itemQuantities => _itemQuantities;

  // Digunakan untuk menghitung total harga (sebagai fungsi)
  double getTotalPrice() {
    double total = 0;
    _itemQuantities.forEach((id, qty) {
      if (_cartItems.containsKey(id)) {
        total += (_cartItems[id]!.harga * qty);
      }
    });
    return total;
  }

  // Digunakan di CartScreen (sebagai getter properti)
  double get totalPrice => getTotalPrice();

  // Daftar menu unik untuk ditampilkan di List
  List<MenuResto> get cartList => _cartItems.values.toList();

  // Hitung jumlah total porsi (untuk angka di ikon keranjang Home)
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
    notifyListeners();
  }

  // 2. Fungsi Kurang (Decrement -1)
  void removeFromCart(int menuId) {
    if (_itemQuantities.containsKey(menuId)) {
      if (_itemQuantities[menuId]! > 1) {
        _itemQuantities[menuId] = _itemQuantities[menuId]! - 1;
      } else {
        _itemQuantities.remove(menuId);
        _cartItems.remove(menuId);
      }
    }
    notifyListeners();
  }

  // 3. FUNGSI SET QUANTITY (PENTING: Digunakan oleh MenuDetailScreen)
  // Menetapkan angka porsi secara spesifik
  void setQuantity(MenuResto menu, int qty) {
    if (qty > 0) {
      _cartItems[menu.id] = menu;
      _itemQuantities[menu.id] = qty;
    } else {
      // Jika qty disetel 0 atau kurang, hapus dari keranjang
      _itemQuantities.remove(menu.id);
      _cartItems.remove(menu.id);
    }
    // Beritahu semua widget (Home, Katalog, dll) untuk update tampilan
    notifyListeners();
  }

  // 4. Kosongkan Keranjang (Setelah bayar sukses)
  void clearCart() {
    _cartItems.clear();
    _itemQuantities.clear();
    notifyListeners();
  }
}