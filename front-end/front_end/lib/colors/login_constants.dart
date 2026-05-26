import 'package:flutter/material.dart';

class AppTheme {
  // ==========================================
  // WARNA & GRADIEN KHUSUS HOTEL (NAVY)
  // ==========================================
  static const Color primaryBlue = Color(0xFF00197D); 
  
  static const LinearGradient headerGradient = LinearGradient(
    begin: Alignment.topCenter,
    end: Alignment.bottomCenter,
    colors: [
      Color(0xFF000B29), // Biru Sangat Tua
      Color(0xFF00197D), // Biru Navy
    ],
  );

  // ==========================================
  // WARNA & GRADIEN KHUSUS RESTORAN (GOLD D4AF37)
  // ==========================================
  static const Color goldAccent = Color(0xFFD4AF37); // Warna Utama Resto

  static const LinearGradient restoGradient = LinearGradient(
    begin: Alignment.topCenter,
    end: Alignment.bottomCenter,
    colors: [
      Color(0xFF8B732A), // Gold Gelap (Deep Gold)
      Color(0xFFD4AF37), // Metallic Gold (D4AF37)
    ],
  );

  // ==========================================
  // WARNA PENDUKUNG LAINNYA
  // ==========================================
  static const Color backgroundColor = Colors.white;
  static const Color fieldColor = Color(0xFFF3F4F6);
}