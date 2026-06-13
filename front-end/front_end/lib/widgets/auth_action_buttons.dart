import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/event_provider.dart';

class GoldButton extends StatelessWidget {
  final String text;
  final VoidCallback onPressed;
  final bool isLoading;

  const GoldButton({super.key, required this.text, required this.onPressed, this.isLoading = false});

  @override
  Widget build(BuildContext context) {
    final eventProvider = context.watch<EventProvider>();
    final Color sColor = eventProvider.secondaryColor;
    final Color textColor = sColor.computeLuminance() > 0.5 ? Colors.black : Colors.white;

    return SizedBox(
      width: double.infinity,
      child: ElevatedButton(
        onPressed: isLoading ? null : onPressed,
        style: ElevatedButton.styleFrom(
          backgroundColor: sColor,
          foregroundColor: textColor,
          padding: const EdgeInsets.symmetric(vertical: 15),
          elevation: 4,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        ),
        child: isLoading
            ? SizedBox(height: 20, width: 20, child: CircularProgressIndicator(color: textColor, strokeWidth: 2))
            : Text(text, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
      ),
    );
  }
}

class GoogleButton extends StatelessWidget {
  final VoidCallback onPressed;
  const GoogleButton({super.key, required this.onPressed});

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      width: double.infinity,
      child: OutlinedButton.icon(
        onPressed: onPressed,
        icon: const Icon(Icons.g_mobiledata, size: 35, color: Colors.red),
        label: const Text("Lanjutkan dengan Menggunakan", style: TextStyle(color: Colors.black)),
        style: OutlinedButton.styleFrom(
          backgroundColor: const Color(0xFFF3F3F3),
          padding: const EdgeInsets.symmetric(vertical: 10),
          side: const BorderSide(color: Colors.black12),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        ),
      ),
    );
  }
}