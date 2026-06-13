import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/event_provider.dart';

class AuthHeader extends StatelessWidget {
  final double height;
  const AuthHeader({super.key, this.height = 220});

  @override
  Widget build(BuildContext context) {
    final eventProvider = context.watch<EventProvider>();
    final Color primary = eventProvider.primaryColor;
    final Color secondary = eventProvider.secondaryColor;

    return Container(
      width: double.infinity,
      height: height,
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topCenter,
          end: Alignment.bottomCenter,
          colors: [primary, Color.lerp(primary, secondary, 0.3) ?? primary],
        ),
        borderRadius: const BorderRadius.vertical(bottom: Radius.elliptical(150, 60)),
      ),
      child: Center(
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 50, vertical: 20),
          child: Image.asset(
            'assets/images/logo_purnama.png',
            fit: BoxFit.contain,
          ),
        ),
      ),
    );
  }
}