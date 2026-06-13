import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import '../colors/login_constants.dart';

// ============================================================
// 1. MODERN INPUT — MENDUKUNG ACCENT COLOR EVENT
// ============================================================
class ModernInput extends StatefulWidget {
  final TextEditingController controller;
  final String label;
  final String hint;
  final IconData icon;
  final bool isRequired;
  final bool isPassword;
  final bool? obscureText;
  final String? errorText;
  final VoidCallback? onSuffixIconPressed;
  final int? maxLines;

  /// Warna aksen (border, ikon, label) — mengikuti warna event
  final Color accentColor;

  const ModernInput({
    super.key,
    required this.controller,
    required this.label,
    required this.hint,
    required this.icon,
    this.isRequired = false,
    this.isPassword = false,
    this.obscureText,
    this.errorText,
    this.onSuffixIconPressed,
    this.maxLines,
    this.accentColor = AppTheme.primaryBlue,
  });

  @override
  State<ModernInput> createState() => _ModernInputState();
}

class _ModernInputState extends State<ModernInput> {
  bool _isFocused = false;

  @override
  Widget build(BuildContext context) {
    final bool hasError = widget.errorText != null;
    final Color activeColor = hasError ? Colors.red : widget.accentColor;
    final Color borderColor = _isFocused
        ? activeColor
        : hasError
            ? Colors.red.withAlpha(140)
            : Colors.grey.shade200;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        // ── LABEL ──
        Row(
          children: [
            Text(
              widget.label,
              style: TextStyle(
                color: widget.accentColor.withAlpha(180),
                fontSize: 11,
                fontWeight: FontWeight.w700,
                letterSpacing: 0.8,
              ),
            ),
            if (widget.isRequired)
              Text(
                " *",
                style: TextStyle(
                    color: Colors.red.shade400,
                    fontSize: 12,
                    fontWeight: FontWeight.bold),
              ),
          ],
        ),
        const SizedBox(height: 8),

        // ── INPUT FIELD ──
        Focus(
          onFocusChange: (v) => setState(() => _isFocused = v),
          child: AnimatedContainer(
            duration: const Duration(milliseconds: 200),
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(14),
              border: Border.all(
                color: borderColor,
                width: _isFocused ? 2.0 : 1.5,
              ),
              color: hasError
                  ? Colors.red.withAlpha(8)
                  : _isFocused
                      ? widget.accentColor.withAlpha(6)
                      : Colors.white,
              boxShadow: _isFocused
                  ? [
                      BoxShadow(
                        color: activeColor.withAlpha(30),
                        blurRadius: 10,
                        offset: const Offset(0, 4),
                      )
                    ]
                  : [],
            ),
            child: TextField(
              controller: widget.controller,
              obscureText: widget.obscureText ?? false,
              maxLines: widget.isPassword ? 1 : (widget.maxLines ?? 1),
              decoration: InputDecoration(
                hintText: widget.hint,
                hintStyle: TextStyle(
                    color: Colors.grey.shade400, fontSize: 14),
                prefixIcon: Icon(widget.icon,
                    color: _isFocused
                        ? widget.accentColor
                        : Colors.grey.shade400,
                    size: 20),
                suffixIcon: widget.isPassword
                    ? IconButton(
                        onPressed: widget.onSuffixIconPressed,
                        icon: Icon(
                          widget.obscureText == true
                              ? Icons.visibility_off_outlined
                              : Icons.visibility_outlined,
                          color: _isFocused
                              ? widget.accentColor
                              : Colors.grey.shade400,
                          size: 20,
                        ),
                      )
                    : null,
                border: InputBorder.none,
                enabledBorder: InputBorder.none,
                focusedBorder: InputBorder.none,
                contentPadding: const EdgeInsets.symmetric(
                    horizontal: 16, vertical: 14),
              ),
            ),
          ),
        ),

        // ── ERROR TEXT ──
        if (hasError) ...[
          const SizedBox(height: 6),
          Row(
            children: [
              const Icon(Icons.error_outline_rounded,
                  color: Colors.red, size: 14),
              const SizedBox(width: 4),
              Text(
                widget.errorText!,
                style: const TextStyle(
                    color: Colors.red,
                    fontSize: 11,
                    fontWeight: FontWeight.w600),
              ),
            ],
          ),
        ],
      ],
    );
  }
}

// ============================================================
// 2. MODERN PHONE INPUT — MENDUKUNG ACCENT COLOR EVENT
// ============================================================
class ModernPhoneInput extends StatefulWidget {
  final TextEditingController controller;
  final String label;
  final String? errorText;
  final Function(String) onNumberChanged;
  final Color accentColor;

  const ModernPhoneInput({
    super.key,
    required this.controller,
    required this.label,
    required this.onNumberChanged,
    this.errorText,
    this.accentColor = AppTheme.primaryBlue,
  });

  @override
  State<ModernPhoneInput> createState() => _ModernPhoneInputState();
}

class _ModernPhoneInputState extends State<ModernPhoneInput> {
  bool _isFocused = false;

  @override
  Widget build(BuildContext context) {
    final bool hasError = widget.errorText != null;
    final Color borderColor = _isFocused
        ? widget.accentColor
        : hasError
            ? Colors.red.withAlpha(140)
            : Colors.grey.shade200;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Text(
              widget.label,
              style: TextStyle(
                color: widget.accentColor.withAlpha(180),
                fontSize: 11,
                fontWeight: FontWeight.w700,
                letterSpacing: 0.8,
              ),
            ),
            Text(" *",
                style: TextStyle(
                    color: Colors.red.shade400,
                    fontSize: 12,
                    fontWeight: FontWeight.bold)),
          ],
        ),
        const SizedBox(height: 8),
        Focus(
          onFocusChange: (v) => setState(() => _isFocused = v),
          child: AnimatedContainer(
            duration: const Duration(milliseconds: 200),
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(14),
              border: Border.all(
                  color: borderColor,
                  width: _isFocused ? 2.0 : 1.5),
              color: _isFocused
                  ? widget.accentColor.withAlpha(6)
                  : Colors.white,
              boxShadow: _isFocused
                  ? [
                      BoxShadow(
                          color: widget.accentColor.withAlpha(30),
                          blurRadius: 10,
                          offset: const Offset(0, 4))
                    ]
                  : [],
            ),
            child: Row(
              children: [
                // ── PREFIX +62 ──
                Container(
                  padding: const EdgeInsets.symmetric(
                      horizontal: 14, vertical: 14),
                  decoration: BoxDecoration(
                    border: Border(
                        right: BorderSide(
                            color: Colors.grey.shade200, width: 1.5)),
                  ),
                  child: Row(
                    children: [
                      Text("🇮🇩",
                          style: const TextStyle(fontSize: 18)),
                      const SizedBox(width: 6),
                      Text("+62",
                          style: TextStyle(
                              color: widget.accentColor,
                              fontWeight: FontWeight.bold,
                              fontSize: 14)),
                    ],
                  ),
                ),
                Expanded(
                  child: TextField(
                    controller: widget.controller,
                    keyboardType: TextInputType.phone,
                    inputFormatters: [
                      FilteringTextInputFormatter.digitsOnly
                    ],
                    onChanged: (val) =>
                        widget.onNumberChanged("+62$val"),
                    decoration: InputDecoration(
                      hintText: "8xx-xxxx-xxxx",
                      hintStyle: TextStyle(
                          color: Colors.grey.shade400, fontSize: 14),
                      border: InputBorder.none,
                      enabledBorder: InputBorder.none,
                      focusedBorder: InputBorder.none,
                      contentPadding: const EdgeInsets.symmetric(
                          horizontal: 14, vertical: 14),
                    ),
                  ),
                ),
              ],
            ),
          ),
        ),
        if (hasError) ...[
          const SizedBox(height: 6),
          Row(
            children: [
              const Icon(Icons.error_outline_rounded,
                  color: Colors.red, size: 14),
              const SizedBox(width: 4),
              Text(
                widget.errorText!,
                style: const TextStyle(
                    color: Colors.red,
                    fontSize: 11,
                    fontWeight: FontWeight.w600),
              ),
            ],
          ),
        ],
      ],
    );
  }
}

// ============================================================
// 3. EVENT AWARE BUTTON — TOMBOL UTAMA DENGAN KONTRAS TERJAMIN
// ============================================================
/// Tombol aksi utama yang memastikan warna tombol dan teks
/// selalu kontras, tidak menyatu dengan background event.
class EventAwareButton extends StatefulWidget {
  final String text;
  final bool isLoading;
  final VoidCallback onPressed;
  final Color buttonColor;
  final Color textColor;
  final double height;

  const EventAwareButton({
    super.key,
    required this.text,
    required this.isLoading,
    required this.onPressed,
    required this.buttonColor,
    required this.textColor,
    this.height = 54,
  });

  @override
  State<EventAwareButton> createState() => _EventAwareButtonState();
}

class _EventAwareButtonState extends State<EventAwareButton>
    with SingleTickerProviderStateMixin {
  late AnimationController _pressController;
  late Animation<double> _scaleAnimation;

  @override
  void initState() {
    super.initState();
    _pressController = AnimationController(
        vsync: this, duration: const Duration(milliseconds: 120));
    _scaleAnimation = Tween<double>(begin: 1.0, end: 0.97)
        .animate(_pressController);
  }

  @override
  void dispose() {
    _pressController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return ScaleTransition(
      scale: _scaleAnimation,
      child: GestureDetector(
        onTapDown: (_) => _pressController.forward(),
        onTapUp: (_) => _pressController.reverse(),
        onTapCancel: () => _pressController.reverse(),
        onTap: widget.isLoading ? null : widget.onPressed,
        child: AnimatedContainer(
          duration: const Duration(milliseconds: 200),
          width: double.infinity,
          height: widget.height,
          decoration: BoxDecoration(
            color: widget.isLoading
                ? widget.buttonColor.withAlpha(160)
                : widget.buttonColor,
            borderRadius: BorderRadius.circular(16),
            boxShadow: widget.isLoading
                ? []
                : [
                    BoxShadow(
                      color: widget.buttonColor.withAlpha(80),
                      blurRadius: 16,
                      offset: const Offset(0, 6),
                    ),
                  ],
          ),
          child: widget.isLoading
              ? Center(
                  child: SizedBox(
                    width: 22,
                    height: 22,
                    child: CircularProgressIndicator(
                      strokeWidth: 2.5,
                      valueColor:
                          AlwaysStoppedAnimation(widget.textColor),
                    ),
                  ),
                )
              : Center(
                  child: Text(
                    widget.text,
                    style: TextStyle(
                      color: widget.textColor,
                      fontWeight: FontWeight.w800,
                      fontSize: 14,
                      letterSpacing: 1.0,
                    ),
                  ),
                ),
        ),
      ),
    );
  }
}

// ============================================================
// 4. MODERN BUTTON (LEGACY — TETAP ADA UNTUK KOMPATIBILITAS)
// ============================================================
class ModernButton extends StatelessWidget {
  final String text;
  final bool isLoading;
  final VoidCallback onPressed;

  const ModernButton({
    super.key,
    required this.text,
    required this.isLoading,
    required this.onPressed,
  });

  @override
  Widget build(BuildContext context) {
    return EventAwareButton(
      text: text,
      isLoading: isLoading,
      onPressed: onPressed,
      buttonColor: AppTheme.goldAccent,
      textColor: AppTheme.primaryBlue,
    );
  }
}

// ============================================================
// 5. MODERN OTP INPUT — MENDUKUNG ACCENT COLOR EVENT
// ============================================================
class ModernOtpInput extends StatelessWidget {
  final TextEditingController controller;
  final bool hasError;
  final Color accentColor;

  const ModernOtpInput({
    super.key,
    required this.controller,
    required this.hasError,
    this.accentColor = AppTheme.primaryBlue,
  });

  @override
  Widget build(BuildContext context) {
    return TextField(
      controller: controller,
      keyboardType: TextInputType.number,
      maxLength: 6,
      textAlign: TextAlign.center,
      inputFormatters: [FilteringTextInputFormatter.digitsOnly],
      style: TextStyle(
        fontSize: 22,
        fontWeight: FontWeight.bold,
        letterSpacing: 16,
        color: accentColor,
      ),
      decoration: InputDecoration(
        counterText: "",
        hintText: "• • • • • •",
        hintStyle: TextStyle(
            color: accentColor.withAlpha(80),
            letterSpacing: 12,
            fontSize: 20),
        filled: true,
        fillColor: hasError
            ? Colors.red.withAlpha(10)
            : accentColor.withAlpha(8),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: BorderSide(
              color: hasError
                  ? Colors.red.withAlpha(140)
                  : accentColor.withAlpha(80),
              width: 1.5),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: BorderSide(
              color: hasError ? Colors.red : accentColor, width: 2.5),
        ),
      ),
    );
  }
}

// ============================================================
// 6. MODERN NOTIFY — SNACKBAR NOTIFIKASI
// ============================================================
class ModernNotify {
  static void show(
    BuildContext context,
    String message, {
    bool isError = true,
  }) {
    final overlay = Overlay.of(context);
    final entry = OverlayEntry(
      builder: (context) => _NotifyWidget(
          message: message, isError: isError),
    );
    overlay.insert(entry);
    Future.delayed(const Duration(seconds: 3), () => entry.remove());
  }
}

class _NotifyWidget extends StatefulWidget {
  final String message;
  final bool isError;
  const _NotifyWidget({required this.message, required this.isError});

  @override
  State<_NotifyWidget> createState() => _NotifyWidgetState();
}

class _NotifyWidgetState extends State<_NotifyWidget>
    with SingleTickerProviderStateMixin {
  late AnimationController _ctrl;
  late Animation<Offset> _slide;
  late Animation<double> _fade;

  @override
  void initState() {
    super.initState();
    _ctrl = AnimationController(
        vsync: this, duration: const Duration(milliseconds: 350));
    _slide =
        Tween<Offset>(begin: const Offset(0, -1), end: Offset.zero).animate(
            CurvedAnimation(parent: _ctrl, curve: Curves.easeOutBack));
    _fade = CurvedAnimation(parent: _ctrl, curve: Curves.easeOut);
    _ctrl.forward();
    Future.delayed(const Duration(milliseconds: 2600), () {
      if (mounted) _ctrl.reverse();
    });
  }

  @override
  void dispose() {
    _ctrl.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Positioned(
      top: MediaQuery.of(context).padding.top + 12,
      left: 20,
      right: 20,
      child: SlideTransition(
        position: _slide,
        child: FadeTransition(
          opacity: _fade,
          child: Material(
            color: Colors.transparent,
            child: Container(
              padding:
                  const EdgeInsets.symmetric(horizontal: 18, vertical: 14),
              decoration: BoxDecoration(
                color: widget.isError
                    ? const Color(0xFF2D1B1B)
                    : const Color(0xFF1B2D1F),
                borderRadius: BorderRadius.circular(16),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withAlpha(60),
                    blurRadius: 20,
                    offset: const Offset(0, 8),
                  )
                ],
              ),
              child: Row(
                children: [
                  Container(
                    padding: const EdgeInsets.all(6),
                    decoration: BoxDecoration(
                      color: widget.isError
                          ? Colors.red.withAlpha(40)
                          : Colors.green.withAlpha(40),
                      shape: BoxShape.circle,
                    ),
                    child: Icon(
                      widget.isError
                          ? Icons.error_outline_rounded
                          : Icons.check_circle_outline_rounded,
                      color: widget.isError ? Colors.red : Colors.green,
                      size: 18,
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Text(
                      widget.message,
                      style: const TextStyle(
                          color: Colors.white,
                          fontSize: 13,
                          fontWeight: FontWeight.w600),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}