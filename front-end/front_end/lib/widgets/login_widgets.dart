import 'dart:async';
import 'package:flutter/material.dart';
import 'package:intl_phone_field/intl_phone_field.dart';
import 'package:pinput/pinput.dart';
import '../colors/login_constants.dart';

/* 
=============================================================================
SECTION 1: MODERN TEXT INPUT (Fleksibel: Hotel/Resto)
=============================================================================
*/
class ModernInput extends StatelessWidget {
  final TextEditingController controller;
  final String label;
  final String hint;
  final IconData icon;
  final bool isPassword;
  final bool obscureText;
  final VoidCallback? onSuffixIconPressed;
  final String? errorText;
  final bool isRequired;
  final bool readOnly;
  final int maxLines;
  final Color? activeColor; // Tambahan: Biar bisa ganti Navy ke Gold

  const ModernInput({
    super.key,
    required this.controller,
    required this.label,
    required this.hint,
    required this.icon,
    this.isPassword = false,
    this.obscureText = false,
    this.onSuffixIconPressed,
    this.errorText,
    this.isRequired = false,
    this.readOnly = false,
    this.maxLines = 1,
    this.activeColor,
  });

  @override
  Widget build(BuildContext context) {
    final bool hasError = errorText != null;
    final Color themeColor = activeColor ?? AppTheme.primaryBlue;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Row(children: [
              Text(label, style: TextStyle(fontWeight: FontWeight.bold, fontSize: 11, color: hasError ? Colors.red : Colors.black54)),
              if (isRequired) const Text(" *", style: TextStyle(color: Colors.red, fontWeight: FontWeight.bold)),
            ]),
            if (hasError) Text(errorText!, style: const TextStyle(color: Colors.red, fontSize: 10, fontWeight: FontWeight.bold)),
          ],
        ),
        const SizedBox(height: 5),
        Container(
          decoration: BoxDecoration(
            color: const Color(0xFFF3F4F6),
            borderRadius: BorderRadius.circular(15),
            border: Border.all(color: hasError ? Colors.red : Colors.grey.shade300, width: 1.2),
          ),
          child: TextField(
            controller: controller,
            obscureText: isPassword ? obscureText : false,
            readOnly: readOnly,
            maxLines: maxLines,
            style: const TextStyle(fontSize: 14),
            cursorColor: themeColor,
            decoration: InputDecoration(
              prefixIcon: Padding(
                padding: EdgeInsets.only(bottom: maxLines > 1 ? 40 : 0),
                child: Icon(icon, color: hasError ? Colors.red : themeColor, size: 20),
              ),
              hintText: hint,
              hintStyle: const TextStyle(color: Colors.grey, fontSize: 13),
              border: InputBorder.none,
              contentPadding: const EdgeInsets.symmetric(vertical: 15, horizontal: 10),
              suffixIcon: isPassword ? IconButton(
                icon: Icon(obscureText ? Icons.visibility_off : Icons.visibility, color: Colors.grey, size: 18),
                onPressed: onSuffixIconPressed,
              ) : null,
            ),
          ),
        ),
      ],
    );
  }
}

/* 
=============================================================================
SECTION 2: MODERN PHONE INPUT (Fleksibel: Hotel/Resto)
=============================================================================
*/
class ModernPhoneInput extends StatelessWidget {
  final TextEditingController controller;
  final String label;
  final String? errorText;
  final Function(String) onNumberChanged;
  final Color? activeColor;

  const ModernPhoneInput({
    super.key, 
    required this.controller, 
    required this.label, 
    this.errorText, 
    required this.onNumberChanged,
    this.activeColor,
  });

  @override
  Widget build(BuildContext context) {
    final bool hasError = errorText != null;
    final Color themeColor = activeColor ?? AppTheme.primaryBlue;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Row(children: [
              Text(label, style: TextStyle(fontWeight: FontWeight.bold, fontSize: 11, color: hasError ? Colors.red : Colors.black54)),
              const Text(" *", style: TextStyle(color: Colors.red, fontWeight: FontWeight.bold)),
            ]),
            if (hasError) Text(errorText!, style: const TextStyle(color: Colors.red, fontSize: 10, fontWeight: FontWeight.bold)),
          ],
        ),
        const SizedBox(height: 5),
        Container(
          height: 55,
          decoration: BoxDecoration(
            color: const Color(0xFFF3F4F6),
            borderRadius: BorderRadius.circular(15),
            border: Border.all(color: hasError ? Colors.red : Colors.grey.shade300, width: 1.2),
          ),
          child: IntlPhoneField(
            controller: controller,
            initialCountryCode: 'ID',
            showDropdownIcon: false,
            disableLengthCheck: true,
            textAlignVertical: TextAlignVertical.center,
            flagsButtonPadding: const EdgeInsets.only(left: 15),
            style: const TextStyle(fontSize: 14),
            cursorColor: themeColor,
            decoration: const InputDecoration(
              hintText: '812XXXX', 
              border: InputBorder.none, 
              contentPadding: EdgeInsets.symmetric(vertical: 15), 
              counterText: ''
            ),
            onChanged: (phone) => onNumberChanged(phone.completeNumber),
          ),
        ),
      ],
    );
  }
}

/* 
=============================================================================
SECTION 3: MODERN OTP/PIN INPUT (Fleksibel: Hotel/Resto)
=============================================================================
*/
class ModernOtpInput extends StatelessWidget {
  final TextEditingController controller;
  final bool hasError;
  final Color? activeColor;
  
  const ModernOtpInput({
    super.key, 
    required this.controller, 
    this.hasError = false,
    this.activeColor,
  });

  @override
  Widget build(BuildContext context) {
    final Color themeColor = activeColor ?? AppTheme.primaryBlue;

    final defaultPinTheme = PinTheme(
      width: 52,
      height: 58,
      textStyle: TextStyle(fontSize: 22, color: themeColor, fontWeight: FontWeight.bold),
      decoration: BoxDecoration(
        color: const Color(0xFFF3F4F6),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: Colors.grey.shade300),
      ),
    );

    return Pinput(
      length: 6,
      controller: controller,
      defaultPinTheme: defaultPinTheme,
      focusedPinTheme: defaultPinTheme.copyDecorationWith(
        border: Border.all(color: themeColor, width: 2),
        color: Colors.white,
      ),
      errorPinTheme: defaultPinTheme.copyDecorationWith(
        border: Border.all(color: Colors.red, width: 2),
        color: const Color(0xFFFFF1F0),
      ),
      forceErrorState: hasError,
      autofocus: true,
      showCursor: true,
      cursor: Column(
        mainAxisAlignment: MainAxisAlignment.end,
        children: [
          Container(margin: const EdgeInsets.only(bottom: 9), width: 22, height: 2, color: themeColor),
        ],
      ),
    );
  }
}

/* 
=============================================================================
SECTION 4: MODERN NOTIFICATION DIALOG (Soft & Premium)
=============================================================================
*/
class ModernNotify {
  static void show(BuildContext context, String msg, {bool isError = true}) {
    showGeneralDialog(
      context: context,
      barrierDismissible: false,
      barrierLabel: '',
      barrierColor: Colors.black45,
      transitionDuration: const Duration(milliseconds: 400),
      pageBuilder: (ctx, anim1, anim2) => const SizedBox(),
      transitionBuilder: (ctx, anim1, anim2, child) {
        return Transform.scale(
          scale: anim1.value,
          child: Opacity(
            opacity: anim1.value,
            child: Center(
              child: Material(
                color: Colors.transparent,
                child: Container(
                  margin: const EdgeInsets.symmetric(horizontal: 50),
                  padding: const EdgeInsets.all(25),
                  decoration: BoxDecoration(
                    color: Colors.white, 
                    borderRadius: BorderRadius.circular(25), 
                    boxShadow: [BoxShadow(color: Colors.black26, blurRadius: 20)]
                  ),
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Container(
                        padding: const EdgeInsets.all(15),
                        decoration: BoxDecoration(
                          color: isError ? const Color(0xFFFFF1F0) : const Color(0xFFF6FFED), 
                          shape: BoxShape.circle
                        ),
                        child: Icon(
                          isError ? Icons.info_outline_rounded : Icons.check_circle_outline_rounded, 
                          color: isError ? const Color(0xFFCF1322) : const Color(0xFF389E0D), 
                          size: 45
                        ),
                      ),
                      const SizedBox(height: 20),
                      Text(
                        isError ? "Pemberitahuan" : "Berhasil", 
                        style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: isError ? const Color(0xFFCF1322) : const Color(0xFF389E0D))
                      ),
                      const SizedBox(height: 12),
                      Text(
                        msg, 
                        textAlign: TextAlign.center, 
                        style: const TextStyle(fontSize: 14, color: Colors.black87, height: 1.5, fontWeight: FontWeight.w500)
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ),
        );
      },
    );

    Timer(const Duration(seconds: 2), () {
      if (Navigator.canPop(context)) {
        Navigator.pop(context);
      }
    });
  }
}

/* 
=============================================================================
SECTION 5: MODERN GRADIENT BUTTON
Tombol utama dengan efek gradien mewah (Bisa Navy atau Gold)
=============================================================================
*/
class ModernButton extends StatelessWidget {
  final String text;
  final VoidCallback? onPressed;
  final bool isLoading;
  final bool isResto; // Jika true pakai gradien Gold, jika false pakai Navy

  const ModernButton({
    super.key,
    required this.text,
    required this.onPressed,
    this.isLoading = false,
    this.isResto = false,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      height: 55,
      decoration: BoxDecoration(
        // Memilih gradien berdasarkan konteks (Hotel atau Resto)
        gradient: isResto ? AppTheme.restoGradient : AppTheme.headerGradient,
        borderRadius: BorderRadius.circular(15),
        boxShadow: [
          BoxShadow(
            color: (isResto ? AppTheme.goldAccent : AppTheme.primaryBlue).withOpacity(0.3),
            blurRadius: 12,
            offset: const Offset(0, 6),
          ),
        ],
      ),
      child: ElevatedButton(
        onPressed: isLoading ? null : onPressed,
        style: ElevatedButton.styleFrom(
          backgroundColor: Colors.transparent, // Transparan agar gradien terlihat
          shadowColor: Colors.transparent,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
        ),
        child: isLoading
            ? const SizedBox(
                height: 20,
                width: 20,
                child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2),
              )
            : Text(
                text,
                style: const TextStyle(
                  color: Colors.white,
                  fontWeight: FontWeight.bold,
                  fontSize: 16,
                  letterSpacing: 1.2,
                ),
              ),
      ),
    );
  }
}