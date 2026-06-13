import 'dart:math';
import 'dart:async';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../home/home_screen.dart';
import '../../providers/event_provider.dart';
import '../../widgets/splash_widgets.dart';

class PremiumSplashScreen extends StatefulWidget {
  final VoidCallback? onFinished; // Callback saat animasi selesai

  const PremiumSplashScreen({super.key, this.onFinished});

  @override
  State<PremiumSplashScreen> createState() => _PremiumSplashScreenState();
}

class _PremiumSplashScreenState extends State<PremiumSplashScreen>
    with TickerProviderStateMixin {
  late AnimationController _main;
  late AnimationController _particle;

  @override
  void initState() {
    super.initState();

    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (mounted) {
        context.read<EventProvider>().fetchActiveTheme();
      }
    });

    _main = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 8500),
    )..forward();

    _particle = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 2800),
    )..repeat();

    _main.addStatusListener((status) {
      if (status == AnimationStatus.completed && mounted) {
        if (widget.onFinished != null) {
          widget.onFinished!();
        } else {
          // Default navigasi ke Home jika tidak ada callback
          Navigator.pushReplacement(
            context,
            PageRouteBuilder(
              pageBuilder: (_, __, ___) => const HomeScreen(),
              transitionDuration: const Duration(milliseconds: 1600),
              transitionsBuilder: (_, a, __, child) => FadeTransition(
                opacity: CurvedAnimation(parent: a, curve: Curves.easeInOut),
                child: child,
              ),
            ),
          );
        }
      }
    });
  }

  @override
  void dispose() {
    _main.dispose();
    _particle.dispose();
    super.dispose();
  }

  double _p(double v, double s, double e) =>
      ((v - s) / (e - s)).clamp(0.0, 1.0);

  @override
  Widget build(BuildContext context) {
    final size = MediaQuery.of(context).size;

    return Scaffold(
      backgroundColor: const Color(0xFF000B29),
      body: AnimatedBuilder(
        animation: _main,
        builder: (ctx, _) {
          final v = _main.value;
          return Stack(
            clipBehavior: Clip.none,
            children: [
              Container(
                decoration: const BoxDecoration(
                  gradient: RadialGradient(
                    center: Alignment.center,
                    radius: 1.2,
                    colors: [Color(0xFF001240), Color(0xFF000B29)],
                  ),
                ),
              ),
              _buildFallingStar(v, size),
              if (v >= 0.18)
                CustomPaint(
                  size: size,
                  painter: _ShockwavePainter(
                    progress: _p(v, 0.18, 0.60),
                    maxRadius: size.width * 0.88,
                  ),
                ),
              if (v >= 0.18 && v <= 0.52) _buildLightBurst(v, size),
              if (v >= 0.20 && v <= 0.40) _buildFlash(v),
              if (v >= 0.30)
                AnimatedBuilder(
                  animation: _particle,
                  builder: (_, __) => CustomPaint(
                    size: size,
                    painter: _GoldParticlePainter(
                      loopT: _particle.value,
                      globalOpacity: Curves.easeOut
                          .transform(_p(v, 0.30, 0.56))
                          .clamp(0.0, 1.0),
                    ),
                  ),
                ),
              if (v >= 0.42) _buildModernUI(v, size),
            ],
          );
        },
      ),
    );
  }

  Widget _buildFallingStar(double v, Size size) {
    if (v >= 0.24) return const SizedBox.shrink();
    final double fallRaw = _p(v, 0.00, 0.18);
    final double fallP = Curves.easeInExpo.transform(fallRaw);
    final double startY = -size.height * 1.10;
    final double offsetY = startY * (1.0 - fallP);
    final double tailLen = fallRaw > 0.3 ? Curves.easeIn.transform((fallRaw - 0.3) / 0.7) * 180.0 : 0.0;
    double opacity = 1.0;
    if (v >= 0.18) opacity = 1.0 - Curves.easeOut.transform(_p(v, 0.18, 0.24));

    return Transform.translate(
      offset: Offset(0, offsetY),
      child: SizedBox.expand(
        child: CustomPaint(
          painter: SharpStarPainter(scale: 1.0, opacity: opacity, tailLength: tailLen),
        ),
      ),
    );
  }

  Widget _buildLightBurst(double v, Size size) {
    final double p = _p(v, 0.18, 0.52);
    final double opacity = p < 0.22 ? Curves.easeOut.transform(p / 0.22) : 1.0 - Curves.easeIn.transform((p - 0.22) / 0.78);
    final double radius = (size.width * 0.12 + Curves.easeOut.transform(p) * size.width * 0.50);
    return Center(
      child: Opacity(
        opacity: (opacity * 0.55).clamp(0.0, 1.0),
        child: Container(
          width: radius * 2,
          height: radius * 2,
          decoration: BoxDecoration(
            shape: BoxShape.circle,
            gradient: RadialGradient(
              colors: [
                const Color(0xFFFFE57F).withOpacity(0.95),
                const Color(0xFFD4AF37).withOpacity(0.45),
                Colors.transparent,
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildFlash(double v) {
    final double p = _p(v, 0.20, 0.40);
    final double opacity = p < 0.28 ? p / 0.28 : 1.0 - ((p - 0.28) / 0.72);
    return Opacity(
      opacity: (opacity * 0.72).clamp(0.0, 1.0),
      child: Container(color: const Color(0xFFD4AF37)),
    );
  }

  Widget _buildModernUI(double v, Size size) {
    return Center(
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          _buildTopOrnament(v),
          const SizedBox(height: 18),
          _buildPurnamaText(v),
          const SizedBox(height: 14),
          _buildDivider(v),
          const SizedBox(height: 16),
          _buildSubtitle(v),
          const SizedBox(height: 10),
          _buildBottomOrnament(v),
        ],
      ),
    );
  }

  Widget _buildTopOrnament(double v) {
    final double p = Curves.easeOut.transform(_p(v, 0.42, 0.60));
    return Opacity(
      opacity: p,
      child: Transform.translate(
        offset: Offset(0, -10 * (1 - p)),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            _diamond(3.5),
            const SizedBox(width: 7),
            _line(56 * p),
            const SizedBox(width: 7),
            _diamond(6),
            const SizedBox(width: 7),
            _line(56 * p),
            const SizedBox(width: 7),
            _diamond(3.5),
          ],
        ),
      ),
    );
  }

  Widget _buildPurnamaText(double v) {
    const text = 'PURNAMA';
    return Row(
      mainAxisAlignment: MainAxisAlignment.center,
      mainAxisSize: MainAxisSize.min,
      children: List.generate(text.length, (i) {
        final double start = 0.44 + i * 0.034;
        final double end = (start + 0.20).clamp(0.0, 1.0);
        final double raw = _p(v, start, end);
        final double slideP = Curves.easeOutBack.transform(raw);
        final double fadeP = _p(v, start, (start + 0.13).clamp(0.0, 1.0));
        return Transform.translate(
          offset: Offset(0, -26 * (1 - slideP)),
          child: Opacity(
            opacity: fadeP.clamp(0.0, 1.0),
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 1.0),
              child: ShaderMask(
                shaderCallback: (b) => const LinearGradient(
                  begin: Alignment.topCenter,
                  end: Alignment.bottomCenter,
                  colors: [
                    Color(0xFFFFE57F),
                    Color(0xFFD4AF37),
                    Color(0xFF9A7B1A),
                  ],
                  stops: [0.0, 0.45, 1.0],
                ).createShader(b),
                child: Text(
                  text[i],
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 58,
                    fontWeight: FontWeight.w900,
                    letterSpacing: 2,
                    height: 1.0,
                  ),
                ),
              ),
            ),
          ),
        );
      }),
    );
  }

  Widget _buildDivider(double v) {
    final double p = Curves.easeOut.transform(_p(v, 0.72, 0.86));
    return Opacity(
      opacity: p,
      child: Container(
        width: 150 * p,
        height: 1,
        decoration: BoxDecoration(
          gradient: LinearGradient(
            colors: [
              Colors.transparent,
              const Color(0xFFD4AF37).withOpacity(0.85),
              Colors.transparent,
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildSubtitle(double v) {
    final double p = Curves.easeOut.transform(_p(v, 0.78, 0.94));
    return Opacity(
      opacity: p,
      child: Transform.translate(
        offset: Offset(0, 8 * (1 - p)),
        child: const Text(
          'Hotel & Restaurant Balige',
          style: TextStyle(
            color: Color(0xFF8A96B0),
            fontSize: 13,
            fontStyle: FontStyle.italic,
            letterSpacing: 2.8,
            fontWeight: FontWeight.w300,
          ),
        ),
      ),
    );
  }

  Widget _buildBottomOrnament(double v) {
    final double p = Curves.easeOut.transform(_p(v, 0.84, 0.97));
    return Opacity(
      opacity: p,
      child: Transform.translate(
        offset: Offset(0, 8 * (1 - p)),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            _line(38 * p),
            const SizedBox(width: 8),
            _diamond(3),
            const SizedBox(width: 8),
            _line(38 * p),
          ],
        ),
      ),
    );
  }

  Widget _diamond(double s) =>
      Transform.rotate(angle: pi / 4, child: Container(width: s, height: s, color: const Color(0xFFD4AF37)));

  Widget _line(double w) => Container(
        width: w.clamp(0.0, double.infinity),
        height: 1,
        color: const Color(0xFFD4AF37).withOpacity(0.5),
      );
}

class _ShockwavePainter extends CustomPainter {
  final double progress;
  final double maxRadius;
  const _ShockwavePainter({required this.progress, required this.maxRadius});
  @override
  void paint(Canvas canvas, Size size) {
    final center = Offset(size.width / 2, size.height / 2);
    for (int i = 0; i < 3; i++) {
      final double delay = i * 0.20;
      final double p = ((progress - delay) / (1.0 - delay)).clamp(0.0, 1.0);
      if (p <= 0) continue;
      final double ep = Curves.easeOut.transform(p);
      final double radius = maxRadius * ep;
      final double opacity = (1.0 - ep) * (i == 0 ? 0.55 : 0.30);
      canvas.drawCircle(
        center,
        radius,
        Paint()
          ..color = const Color(0xFFD4AF37).withOpacity(opacity.clamp(0.0, 1.0))
          ..style = PaintingStyle.stroke
          ..strokeWidth = (3.5 - i * 0.9) * (1 - ep * 0.5),
      );
    }
  }
  @override
  bool shouldRepaint(covariant _ShockwavePainter o) => o.progress != progress;
}

class _GoldParticlePainter extends CustomPainter {
  final double loopT;
  final double globalOpacity;
  static final _rng = Random(99);
  static final List<_Pt> _pts = List.generate(
    32,
    (i) => _Pt(
      x: _rng.nextDouble(),
      y: _rng.nextDouble(),
      speed: 0.03 + _rng.nextDouble() * 0.05,
      r: 1.2 + _rng.nextDouble() * 2.8,
      phase: _rng.nextDouble(),
    ),
  );
  const _GoldParticlePainter({required this.loopT, required this.globalOpacity});
  @override
  void paint(Canvas canvas, Size size) {
    if (globalOpacity <= 0) return;
    final paint = Paint()..style = PaintingStyle.fill;
    for (final p in _pts) {
      final double animY = (p.y - p.speed * loopT) % 1.0;
      final double dx = size.width * p.x + sin((loopT + p.phase) * 2 * pi) * 14;
      final double dy = size.height * (animY < 0 ? animY + 1.0 : animY);
      paint
        ..color = const Color(0xFFD4AF37).withOpacity(globalOpacity * 0.10)
        ..maskFilter = const MaskFilter.blur(BlurStyle.normal, 2.5);
      canvas.drawCircle(Offset(dx, dy), p.r * 2, paint);
      paint
        ..color = const Color(0xFFFFE57F).withOpacity(globalOpacity * 0.65)
        ..maskFilter = null;
      canvas.drawCircle(Offset(dx, dy), p.r * 0.5, paint);
    }
  }
  @override
  bool shouldRepaint(covariant _GoldParticlePainter o) =>
      o.loopT != loopT || o.globalOpacity != globalOpacity;
}

class _Pt {
  final double x, y, speed, r, phase;
  const _Pt({required this.x, required this.y, required this.speed, required this.r, required this.phase});
}