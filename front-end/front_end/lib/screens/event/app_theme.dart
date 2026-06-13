import 'package:flutter/material.dart';

class AppTheme {
  // ==========================================
  // 1. DEFAULT BRAND COLORS (untuk fallback)
  // ==========================================
  static const Color primaryBlue = Color(0xFF00197D);
  static const Color goldAccent = Color(0xFFD4AF37);

  // ==========================================
  // 2. GRADIEN BAWAAN (const untuk digunakan di dekorasi)
  // ==========================================
  static const LinearGradient headerGradient = LinearGradient(
    begin: Alignment.topCenter,
    end: Alignment.bottomCenter,
    colors: [primaryBlue, goldAccent],
  );

  static const LinearGradient restoGradient = LinearGradient(
    begin: Alignment.topCenter,
    end: Alignment.bottomCenter,
    colors: [goldAccent, Color(0xFFFFE9B3)],
  );

  // ==========================================
  // 3. FUNGSI KONVERSI WARNA
  // ==========================================
  /// Mengubah string hex (contoh "#FFAA00" atau "FFAA00") menjadi objek Color Flutter.
  static Color hexToColor(String hexString) {
    final buffer = StringBuffer();
    if (hexString.length == 6 || hexString.length == 7) buffer.write('ff');
    buffer.write(hexString.replaceFirst('#', ''));
    return Color(int.parse(buffer.toString(), radix: 16));
  }

  /// Menentukan apakah suatu warna terang (luminance > 0.5)
  static bool isLightColor(Color color) {
    return color.computeLuminance() > 0.5;
  }

  /// Menghasilkan gradien header dinamis berdasarkan warna primer dan sekunder.
  static LinearGradient buildHeaderGradient(Color primary, Color secondary) {
    return LinearGradient(
      begin: Alignment.topCenter,
      end: Alignment.bottomCenter,
      colors: [primary.withAlpha(235), secondary.withAlpha(210)],
    );
  }

  /// Memilih warna latar tombol yang sesuai (prioritas sekunder jika cukup berbeda).
  static Color resolveButtonColor(Color primary, Color secondary) {
    final diff = (primary.computeLuminance() - secondary.computeLuminance()).abs();
    return diff < 0.08 ? primary : secondary;
  }

  /// Menentukan warna teks pada tombol berdasarkan kontras.
  static Color resolveButtonTextColor(Color bg) => isLightColor(bg) ? Colors.black87 : Colors.white;

  /// Memberikan warna teks kontras untuk latar belakang tertentu.
  static Color contrastColor(Color bg) => isLightColor(bg) ? Colors.black87 : Colors.white;

  // ==========================================
  // 4. PEMBANGKIT TEMA UTAMA (berdasarkan data dari Laravel)
  // ==========================================
  static ThemeData getTheme(Map<String, dynamic> themeData) {
    // Ambil warna primer & sekunder, jika tidak ada pakai default navy & gold
    String primaryHex = themeData['primary_color'] ?? '#00197D';
    Color primaryColor = hexToColor(primaryHex);

    String secondaryHex = themeData['secondary_color'] ?? '#D4AF37';
    Color secondaryColor = hexToColor(secondaryHex);

    // Tentukan warna teks berdasarkan kontras
    bool primaryIsLight = isLightColor(primaryColor);
    bool secondaryIsLight = isLightColor(secondaryColor);
    Color primaryTextColor = primaryIsLight ? Colors.black87 : Colors.white;
    Color secondaryTextColor = secondaryIsLight ? Colors.black87 : Colors.white;

    return ThemeData(
      useMaterial3: true,

      // Skema warna dasar
      colorScheme: ColorScheme.fromSeed(
        seedColor: primaryColor,
        primary: primaryColor,
        secondary: secondaryColor,
        brightness: Brightness.light,
      ),

      // AppBar dinamis
      appBarTheme: AppBarTheme(
        backgroundColor: primaryColor,
        foregroundColor: primaryTextColor,
        centerTitle: true,
        elevation: 4,
        toolbarHeight: 70,
      ),

      // Tombol Elevated (solid)
      elevatedButtonTheme: ElevatedButtonThemeData(
        style: ElevatedButton.styleFrom(
          backgroundColor: secondaryColor,
          foregroundColor: secondaryTextColor,
          elevation: 6,
          padding: const EdgeInsets.symmetric(horizontal: 28, vertical: 14),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
          textStyle: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14, letterSpacing: 0.5),
        ),
      ),

      // Tombol TextButton
      textButtonTheme: TextButtonThemeData(
        style: TextButton.styleFrom(
          foregroundColor: secondaryColor,
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        ),
      ),

      // Tombol Outlined (border)
      outlinedButtonTheme: OutlinedButtonThemeData(
        style: OutlinedButton.styleFrom(
          foregroundColor: secondaryColor,
          side: BorderSide(color: secondaryColor, width: 2.5),
          padding: const EdgeInsets.symmetric(horizontal: 28, vertical: 14),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
        ),
      ),

      // Dekorasi input (TextField, TextFormField)
      inputDecorationTheme: InputDecorationTheme(
        filled: true,
        fillColor: Colors.white,
        contentPadding: const EdgeInsets.symmetric(horizontal: 18, vertical: 14),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: BorderSide(color: primaryColor.withAlpha(102), width: 1.5),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: BorderSide(color: primaryColor.withAlpha(102), width: 1.5),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: BorderSide(color: secondaryColor, width: 2.5),
        ),
        labelStyle: TextStyle(color: primaryColor.withAlpha(179)),
        prefixIconColor: primaryColor,
        suffixIconColor: primaryColor,
      ),
    );
  }
}