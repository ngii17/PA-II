import 'dart:math';
import 'package:flutter/material.dart';

// Bintang kecil — ukuran FIXED, tidak ikut scale membesar
// scale hanya dipakai saat fase impact (meledak sebentar lalu hilang)
class SharpStarPainter extends CustomPainter {
  final double scale;   // 1.0 = normal kecil, naik saat impact
  final double opacity;
  final double tailLength; // panjang ekor meteor (0 = tidak ada ekor)

  SharpStarPainter({
    required this.scale,
    required this.opacity,
    this.tailLength = 0,
  });

  @override
  void paint(Canvas canvas, Size size) {
    if (opacity <= 0) return;

    canvas.save();
    canvas.translate(size.width / 2, size.height / 2);

    // ── EKOR METEOR (hanya saat jatuh, tailLength > 0) ────────────────────
    if (tailLength > 0) {
      final tailPaint = Paint()
        ..shader = LinearGradient(
          begin: Alignment.bottomCenter,
          end: Alignment.topCenter,
          colors: [
            const Color(0xFFFFE57F).withOpacity(opacity * 0.85),
            const Color(0xFFD4AF37).withOpacity(opacity * 0.3),
            Colors.transparent,
          ],
        ).createShader(Rect.fromLTWH(-6, -tailLength, 12, tailLength))
        ..style = PaintingStyle.fill
        ..maskFilter = const MaskFilter.blur(BlurStyle.normal, 4);

      final tailPath = Path()
        ..moveTo(-5, 0)
        ..lineTo(0, -tailLength)
        ..lineTo(5, 0)
        ..close();
      canvas.drawPath(tailPath, tailPaint);

      // Glow ekor tipis
      canvas.drawPath(
        tailPath,
        Paint()
          ..color = const Color(0xFFFFFFFF).withOpacity(opacity * 0.25)
          ..style = PaintingStyle.fill
          ..maskFilter = const MaskFilter.blur(BlurStyle.normal, 8),
      );
    }

    // ── GLOW BINTANG ──────────────────────────────────────────────────────
    final double outerR = 12.0 * scale; // kecil! fixed 12px saat normal
    final double innerR = outerR * 0.42;

    canvas.drawPath(
      _starPath(outerR * 1.8, innerR * 1.8),
      Paint()
        ..color = const Color(0xFFD4AF37)
            .withOpacity((opacity * 0.25).clamp(0.0, 1.0))
        ..style = PaintingStyle.fill
        ..maskFilter = const MaskFilter.blur(BlurStyle.normal, 14),
    );

    // ── BINTANG UTAMA ─────────────────────────────────────────────────────
    canvas.drawPath(
      _starPath(outerR, innerR),
      Paint()
        ..color = const Color(0xFFD4AF37).withOpacity(opacity.clamp(0.0, 1.0))
        ..style = PaintingStyle.fill,
    );

    // ── HIGHLIGHT PUTIH ───────────────────────────────────────────────────
    canvas.drawPath(
      _starPath(outerR * 0.45, innerR * 0.45),
      Paint()
        ..color =
            Colors.white.withOpacity((opacity * 0.75).clamp(0.0, 1.0))
        ..style = PaintingStyle.fill,
    );

    canvas.restore();
  }

  Path _starPath(double outer, double inner) {
    final path = Path();
    for (int i = 0; i < 10; i++) {
      final r = i.isEven ? outer : inner;
      final angle = (i * 36 - 90) * pi / 180;
      final x = r * cos(angle);
      final y = r * sin(angle);
      i == 0 ? path.moveTo(x, y) : path.lineTo(x, y);
    }
    return path..close();
  }

  @override
  bool shouldRepaint(covariant SharpStarPainter old) =>
      old.scale != scale ||
      old.opacity != opacity ||
      old.tailLength != tailLength;
}