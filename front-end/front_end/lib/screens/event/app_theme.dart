import 'package:flutter/material.dart';

class AppTheme {
  // --- FUNGSI AJAIB: MENGUBAH TULISAN #HEX MENJADI WARNA FLUTTER ---
  static Color hexToColor(String hexString) {
    final buffer = StringBuffer();
    // Jika formatnya #FFFFFF (7 karakter) atau FFFFFF (6 karakter)
    if (hexString.length == 6 || hexString.length == 7) buffer.write('ff');
    buffer.write(hexString.replaceFirst('#', ''));
    return Color(int.parse(buffer.toString(), radix: 16));
  }

  // --- FUNGSI UTAMA: MENGHASILKAN TEMA BERDASARKAN DATA DARI LARAVEL ---
  static ThemeData getTheme(Map<String, dynamic> themeData) {
    // 1. Ambil warna utama dari Laravel, jika gagal/kosong pakai Biru Default
    String primaryHex = themeData['primary_color'] ?? '#448AFF';
    Color primaryColor = hexToColor(primaryHex);

    // 2. Ambil warna sekunder (aksen)
    String secondaryHex = themeData['secondary_color'] ?? '#E3F2FD';
    Color secondaryColor = hexToColor(secondaryHex);

    return ThemeData(
      useMaterial3: true,
      
      // Mengatur warna dasar aplikasi
      colorScheme: ColorScheme.fromSeed(
        seedColor: primaryColor,
        primary: primaryColor,
        secondary: secondaryColor,
      ),

      // Mengatur warna AppBar (Kepala Aplikasi) secara otomatis
      appBarTheme: AppBarTheme(
        backgroundColor: primaryColor,
        foregroundColor: Colors.white,
        centerTitle: true,
        elevation: 0,
      ),

      // Mengatur gaya Tombol secara otomatis
      elevatedButtonTheme: ElevatedButtonThemeData(
        style: ElevatedButton.styleFrom(
          backgroundColor: primaryColor,
          foregroundColor: Colors.white,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
        ),
      ),

      // Mengatur gaya inputan (TextField)
      inputDecorationTheme: InputDecorationTheme(
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(10)),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(10),
          borderSide: BorderSide(color: primaryColor, width: 2),
        ),
      ),
    );
  }
}