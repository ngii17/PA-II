import 'dart:ui';
import 'package:flutter/material.dart';

class LockedFeature extends StatelessWidget {
  final Widget child;
  final bool isLocked;
  final String title;

  const LockedFeature({
    super.key,
    required this.child,
    required this.isLocked,
    this.title = "Daftar Akun\nUntuk Akses",
  });

  @override
  Widget build(BuildContext context) {
    if (!isLocked) return child;

    return ClipRRect(
      borderRadius: BorderRadius.circular(22),
      child: Stack(
        alignment: Alignment.center,
        children: [
          ImageFiltered(
            imageFilter: ImageFilter.blur(sigmaX: 6, sigmaY: 6),
            child: AbsorbPointer(absorbing: true, child: child),
          ),
          Positioned.fill(
            child: Container(color: Colors.black.withOpacity(0.35)),
          ),
          Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(0.2),
                  shape: BoxShape.circle,
                  border: Border.all(color: Colors.white.withOpacity(0.4), width: 1.5),
                ),
                child: const Icon(Icons.lock_person_rounded, color: Colors.white, size: 26),
              ),
              const SizedBox(height: 12),
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
                    shadows: [Shadow(color: Colors.black26, blurRadius: 10, offset: Offset(0, 2))],
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