import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../services/api_services.dart';
import '../../providers/event_provider.dart';
import 'login_screen.dart';
import '../../colors/login_constants.dart';
import '../../widgets/login_widgets.dart';

class ResetPasswordScreen extends StatefulWidget {
  final String email;
  const ResetPasswordScreen({super.key, required this.email});

  @override
  State<ResetPasswordScreen> createState() => _ResetPasswordScreenState();
}

class _ResetPasswordScreenState extends State<ResetPasswordScreen>
    with TickerProviderStateMixin {
  final TextEditingController _otpController = TextEditingController();
  final TextEditingController _newPasswordController = TextEditingController();
  final TextEditingController _confirmPasswordController = TextEditingController();

  bool _isLoading = false;
  bool _obscurePassword = true;
  bool _obscureConfirmPassword = true;
  bool _isOtpError = false;
  String? _pwError, _cpwError;

  late AnimationController _fadeController;
  late Animation<double> _fadeAnimation;
  late Animation<Offset> _slideAnimation;

  @override
  void initState() {
    super.initState();
    _fadeController = AnimationController(
        vsync: this, duration: const Duration(milliseconds: 500));
    _fadeAnimation =
        CurvedAnimation(parent: _fadeController, curve: Curves.easeOut);
    _slideAnimation =
        Tween<Offset>(begin: const Offset(0, 0.06), end: Offset.zero).animate(
            CurvedAnimation(parent: _fadeController, curve: Curves.easeOut));
    _fadeController.forward();
  }

  @override
  void dispose() {
    _fadeController.dispose();
    _otpController.dispose();
    _newPasswordController.dispose();
    _confirmPasswordController.dispose();
    super.dispose();
  }

  void _handleReset() async {
    setState(() {
      _isOtpError = false;
      _pwError = null;
      _cpwError = null;
    });

    if (_otpController.text.isEmpty ||
        _newPasswordController.text.isEmpty ||
        _confirmPasswordController.text.isEmpty) {
      ModernNotify.show(context, "Harap isi semua biodata reset anda!");
      return;
    }

    if (_otpController.text.length != 6) {
      setState(() => _isOtpError = true);
      ModernNotify.show(context, "Masukkan 6 digit kode OTP yang valid");
      return;
    }

    String password = _newPasswordController.text;
    if (!RegExp(r'^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).+$')
            .hasMatch(password) ||
        password.length < 8 ||
        password.length > 12) {
      setState(() => _pwError = "Kriteria salah");
      ModernNotify.show(context,
          "Sandi: 8-12 karakter, wajib ada Huruf Besar, Kecil, Angka, & Simbol");
      return;
    }

    if (password != _confirmPasswordController.text) {
      setState(() => _cpwError = "Tidak cocok");
      ModernNotify.show(context, "Konfirmasi kata sandi tidak cocok!");
      return;
    }

    setState(() => _isLoading = true);
    final result = await ApiServices.resetPassword(
        widget.email,
        _otpController.text,
        password,
        _confirmPasswordController.text);
    setState(() => _isLoading = false);

    if (result['success'] == true) {
      ModernNotify.show(context, "Kata sandi berhasil diperbarui!",
          isError: false);
      Future.delayed(const Duration(milliseconds: 2000), () {
        if (mounted) {
          Navigator.pushAndRemoveUntil(
            context,
            MaterialPageRoute(builder: (context) => const LoginScreen()),
            (route) => false,
          );
        }
      });
    } else {
      setState(() => _isOtpError = true);
      ModernNotify.show(context,
          result['message'] ?? "Kode OTP salah atau gagal mereset sandi.");
    }
  }

  @override
  Widget build(BuildContext context) {
    final eventProvider = context.watch<EventProvider>();
    final bool hasEvent = eventProvider.eventCode != 'default';
    final Color primaryColor =
        hasEvent ? eventProvider.primaryColor : AppTheme.primaryBlue;
    final Color secondaryColor =
        hasEvent ? eventProvider.secondaryColor : AppTheme.goldAccent;
    final double topPadding = MediaQuery.of(context).padding.top;

    final Color buttonColor =
        AppTheme.resolveButtonColor(primaryColor, secondaryColor);
    final Color buttonTextColor = AppTheme.resolveButtonTextColor(buttonColor);
    final LinearGradient headerGrad = hasEvent
        ? AppTheme.buildHeaderGradient(primaryColor, secondaryColor)
        : AppTheme.headerGradient;
    final Color onPrimary = AppTheme.contrastColor(primaryColor);

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      body: SingleChildScrollView(
        child: Column(
          children: [
            // Header gradien
            Container(
              width: double.infinity,
              padding: EdgeInsets.only(
                  top: topPadding + 30, left: 25, right: 25, bottom: 55),
              decoration: BoxDecoration(
                gradient: headerGrad,
                borderRadius: const BorderRadius.only(
                  bottomLeft: Radius.circular(50),
                  bottomRight: Radius.circular(50),
                ),
                boxShadow: [
                  BoxShadow(
                      color: primaryColor.withAlpha(60),
                      blurRadius: 20,
                      offset: const Offset(0, 8))
                ],
              ),
              child: FadeTransition(
                opacity: _fadeAnimation,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    GestureDetector(
                      onTap: () => Navigator.pop(context),
                      child: Container(
                        padding: const EdgeInsets.all(8),
                        decoration: BoxDecoration(
                          color: onPrimary.withAlpha(20),
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(
                              color: onPrimary.withAlpha(40), width: 1),
                        ),
                        child: Icon(Icons.arrow_back_ios_new_rounded,
                            color: onPrimary, size: 18),
                      ),
                    ),
                    const SizedBox(height: 24),
                    Container(
                      padding: const EdgeInsets.all(14),
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        color: onPrimary.withAlpha(20),
                        border: Border.all(
                            color: onPrimary.withAlpha(40), width: 1.5),
                      ),
                      child: Icon(Icons.security_update_good_rounded,
                          color: onPrimary, size: 32),
                    ),
                    const SizedBox(height: 18),
                    Text(
                      "Atur Ulang Sandi",
                      style: TextStyle(
                          color: onPrimary,
                          fontSize: 28,
                          fontWeight: FontWeight.bold),
                    ),
                    const SizedBox(height: 6),
                    Row(
                      children: [
                        Icon(Icons.email_outlined,
                            color: secondaryColor, size: 14),
                        const SizedBox(width: 6),
                        Flexible(
                          child: Text(
                            widget.email,
                            style: TextStyle(
                                color: secondaryColor,
                                fontSize: 13,
                                fontWeight: FontWeight.w700),
                            overflow: TextOverflow.ellipsis,
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ),
            // Card konten
            Transform.translate(
              offset: const Offset(0, -30),
              child: FadeTransition(
                opacity: _fadeAnimation,
                child: SlideTransition(
                  position: _slideAnimation,
                  child: Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 24),
                    child: Container(
                      padding: const EdgeInsets.all(28),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(24),
                        boxShadow: [
                          BoxShadow(
                            color: primaryColor.withAlpha(20),
                            blurRadius: 24,
                            offset: const Offset(0, 10),
                          ),
                        ],
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            "Masukkan Kode OTP",
                            style: TextStyle(
                                fontWeight: FontWeight.bold,
                                fontSize: 17,
                                color: primaryColor),
                          ),
                          const SizedBox(height: 6),
                          Text(
                            "Masukkan kode yang dikirim ke email Anda, lalu buat sandi baru.",
                            style: TextStyle(
                                color: Colors.grey.shade500, fontSize: 12),
                          ),
                          const SizedBox(height: 24),
                          // Input OTP kotak
                          ModernOtpInput(
                            controller: _otpController,
                            hasError: _isOtpError,
                            accentColor: primaryColor,
                          ),
                          const SizedBox(height: 28),
                          Row(
                            children: [
                              Expanded(
                                  child: Divider(color: Colors.grey.shade200)),
                              Padding(
                                padding: const EdgeInsets.symmetric(
                                    horizontal: 12),
                                child: Text("Buat Sandi Baru",
                                    style: TextStyle(
                                        color: primaryColor.withAlpha(150),
                                        fontSize: 11,
                                        fontWeight: FontWeight.w600,
                                        letterSpacing: 0.5)),
                              ),
                              Expanded(
                                  child: Divider(color: Colors.grey.shade200)),
                            ],
                          ),
                          const SizedBox(height: 20),
                          // Password baru
                          ModernInput(
                            controller: _newPasswordController,
                            label: "KATA SANDI BARU",
                            hint: "8-12 Karakter + Simbol",
                            icon: Icons.lock_outline,
                            isRequired: true,
                            isPassword: true,
                            obscureText: _obscurePassword,
                            errorText: _pwError,
                            accentColor: primaryColor,
                            onSuffixIconPressed: () => setState(
                                () => _obscurePassword = !_obscurePassword),
                          ),
                          const SizedBox(height: 16),
                          // Konfirmasi password
                          ModernInput(
                            controller: _confirmPasswordController,
                            label: "KONFIRMASI SANDI",
                            hint: "Ulangi sandi baru Anda",
                            icon: Icons.lock_reset_rounded,
                            isRequired: true,
                            isPassword: true,
                            obscureText: _obscureConfirmPassword,
                            errorText: _cpwError,
                            accentColor: primaryColor,
                            onSuffixIconPressed: () => setState(
                                () => _obscureConfirmPassword =
                                    !_obscureConfirmPassword),
                          ),
                          const SizedBox(height: 28),
                          EventAwareButton(
                            text: "SIMPAN SANDI BARU",
                            isLoading: _isLoading,
                            onPressed: _handleReset,
                            buttonColor: buttonColor,
                            textColor: buttonTextColor,
                          ),
                          const SizedBox(height: 20),
                        ],
                      ),
                    ),
                  ),
                ),
              ),
            ),
            const SizedBox(height: 20),
          ],
        ),
      ),
    );
  }
}