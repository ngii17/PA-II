import 'package:flutter/material.dart';
import 'package:vibration/vibration.dart';

class ShakeWrapper extends StatefulWidget {
  final Widget child;
  const ShakeWrapper({super.key, required this.child});

  // Fungsi static agar bisa dipanggil dari mana saja lewat GlobalKey
  static void shake(GlobalKey<ShakeWrapperState> key) {
    key.currentState?.triggerShake();
  }

  @override
  State<ShakeWrapper> createState() => ShakeWrapperState();
}

class ShakeWrapperState extends State<ShakeWrapper> with SingleTickerProviderStateMixin {
  late AnimationController _controller;
  late Animation<double> _animation;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      duration: const Duration(milliseconds: 500),
      vsync: this,
    );

    // Animasi goyang kiri-kanan yang presisi (pasti balik ke 0)
    _animation = TweenSequence<double>([
      TweenSequenceItem(tween: Tween(begin: 0.0, end: 12.0), weight: 1),
      TweenSequenceItem(tween: Tween(begin: 12.0, end: -12.0), weight: 1),
      TweenSequenceItem(tween: Tween(begin: -12.0, end: 12.0), weight: 1),
      TweenSequenceItem(tween: Tween(begin: 12.0, end: 0.0), weight: 1),
    ]).animate(CurvedAnimation(parent: _controller, curve: Curves.easeInOut));
  }

  void triggerShake() {
    _controller.forward(from: 0.0);
    // Getar Fisik HP (Pola: Tunggu 0ms, Getar 100ms, Tunggu 50ms, Getar 100ms)
    Vibration.hasVibrator().then((has) {
      if (has == true) Vibration.vibrate(pattern: [0, 100, 50, 100]);
    });
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return AnimatedBuilder(
      animation: _animation,
      builder: (context, child) => Transform.translate(
        offset: Offset(_animation.value, 0),
        child: child,
      ),
      child: widget.child,
    );
  }
}