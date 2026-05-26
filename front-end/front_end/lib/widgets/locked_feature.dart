import 'dart:ui';
import 'package:flutter/material.dart';
import '../colors/login_constants.dart';

class LockedFeature extends StatelessWidget {
  final Widget child;
  final bool isLocked;
  final String title;

  const LockedFeature({
    super.key,
    required this.child,
    required this.isLocked,
    this.title = "Daftar Akun\nUntuk Akses", // Default Text Bahasa Indonesia
  });

  @override
  Widget build(BuildContext context) {
    // Jika tidak terkunci, kembalikan widget asli secara normal
    if (!isLocked) return child;

    return ClipRRect(
      // Radius disamakan dengan radius kartu di Home (22-25)
      borderRadius: BorderRadius.circular(22),
      child: Stack(
        alignment: Alignment.center,
        children: [
          // 1. KONTEN ASLI (DI-BLUR)
          ImageFiltered(
            imageFilter: ImageFilter.blur(sigmaX: 6, sigmaY: 6), // Blur sedikit lebih tebal agar elegan
            child: AbsorbPointer(
              absorbing: true, // Mematikan interaksi klik pada fitur di bawahnya
              child: child,
            ),
          ),

          // 2. OVERLAY GELAP TRANSPARAN
          Positioned.fill(
            child: Container(
              color: Colors.black.withOpacity(0.35), // Memberikan kontras agar teks putih terbaca
            ),
          ),

          // 3. VISUAL KUNCI MODERN (Glassmorphism Style)
          Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              // Lingkaran Ikon Kunci
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(0.2), // Efek Kaca
                  shape: BoxShape.circle,
                  border: Border.all(
                    color: Colors.white.withOpacity(0.4), 
                    width: 1.5
                  ),
                ),
                child: const Icon(
                  Icons.lock_person_rounded, // Ikon lebih modern
                  color: Colors.white,
                  size: 26,
                ),
              ),
              const SizedBox(height: 12),
              // Label Teks
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 10),
                child: Text(
                  title.toUpperCase(),
                  textAlign: TextAlign.center,
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 10,
                    fontWeight: FontWeight.w900,
                    letterSpacing: 1.2,
                    shadows: [
                      Shadow(color: Colors.black26, blurRadius: 10, offset: Offset(0, 2))
                    ],
                  ),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}