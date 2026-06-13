import 'package:flutter/material.dart';
import '../screens/restoran/menu_resto.dart'; // Sesuaikan dengan path model Anda

class CartProvider extends ChangeNotifier {
  /// Map untuk menyimpan data objek menu utuh berdasarkan ID
  final Map<int, MenuResto> _cartItems = {};
  
  /// Map untuk menyimpan jumlah (quantity) berdasarkan ID
  final Map<int, int> _itemQuantities = {};

  // ==========================================
  // GETTER UNTUK UI
  // ==========================================

  /// Mendapatkan list unik menu yang ada di keranjang
  List<MenuResto> get cartList => _cartItems.values.toList();

  /// Mendapatkan jumlah per ID (untuk badge atau counter di list)
  Map<int, int> get itemQuantities => _itemQuantities;
  Map<int, int> get items => _itemQuantities; // Alias untuk fleksibilitas

  /// Menghitung total item porsi (untuk badge di ikon keranjang belanja)
  int get totalItems {
    int total = 0;
    _itemQuantities.forEach((_, qty) => total += qty);
    return total;
  }

  /// SINKRONISASI BACKEND: Menghitung total harga yang harus dibayar
  /// Menggunakan [hargaAkhir] (Harga setelah diskon/promo)
  double get totalPrice {
    double total = 0;
    _itemQuantities.forEach((id, qty) {
      if (_cartItems.containsKey(id)) {
        total += (_cartItems[id]!.hargaAkhir * qty);
      }
    });
    return total;
  }

   // Method Alias (agar tidak error saat dipanggil: cartProvider.getTotalPrice())
  double getTotalPrice() => totalPrice;

  /// OPSIONAL: Menghitung total harga asli sebelum diskon
  /// Berguna jika Anda ingin menampilkan "Hemat Rp xxx" di UI
  double get totalOriginalPrice {
    double total = 0;
    _itemQuantities.forEach((id, qty) {
      if (_cartItems.containsKey(id)) {
        total += (_cartItems[id]!.hargaAsli * qty);
      }
    });
    return total;
  }

  /// Mendapatkan selisih total penghematan
  double get totalSavings => totalOriginalPrice - totalPrice;

  // ==========================================
  // LOGIKA MANIPULASI DATA (AKSI)
  // ==========================================

  /// 1. Tambah ke Keranjang (Increment)
  void addToCart(MenuResto menu) {
    if (_itemQuantities.containsKey(menu.id)) {
      _itemQuantities[menu.id] = _itemQuantities[menu.id]! + 1;
      // Update objek menu untuk memastikan harga/promo terbaru dari backend tersimpan
      _cartItems[menu.id] = menu; 
    } else {
      _cartItems[menu.id] = menu;
      _itemQuantities[menu.id] = 1;
    }
    notifyListeners();
  }

  /// 2. Kurangi dari Keranjang (Decrement)
  void removeFromCart(int menuId) {
    if (_itemQuantities.containsKey(menuId)) {
      if (_itemQuantities[menuId]! > 1) {
        _itemQuantities[menuId] = _itemQuantities[menuId]! - 1;
      } else {
        // Jika sisa 1, hapus permanen dari map
        _itemQuantities.remove(menuId);
        _cartItems.remove(menuId);
      }
      notifyListeners();
    }
  }

  /// 3. Atur Jumlah Secara Spesifik (Set Quantity)
  /// Digunakan di halaman Detail Menu atau Input Manual
  void setQuantity(MenuResto menu, int qty) {
    if (qty > 0) {
      _cartItems[menu.id] = menu;
      _itemQuantities[menu.id] = qty;
    } else {
      _itemQuantities.remove(menu.id);
      _cartItems.remove(menu.id);
    }
    notifyListeners();
  }

  /// 4. Hapus Satu Baris Menu (Delete Item)
  /// Digunakan jika ada tombol "Hapus" (tong sampah) di keranjang
  void deleteItem(int menuId) {
    _itemQuantities.remove(menuId);
    _cartItems.remove(menuId);
    notifyListeners();
  }

  /// 5. Reset Keranjang
  void clearCart() {
    _cartItems.clear();
    _itemQuantities.clear();
    notifyListeners();
  }
}