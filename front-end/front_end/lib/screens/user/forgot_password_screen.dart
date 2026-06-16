import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../services/api_services.dart';
import '../../providers/event_provider.dart';
import 'reset_password_screen.dart';
import '../../widgets/login_widgets.dart';

class ForgotPasswordScreen extends StatefulWidget {
  const ForgotPasswordScreen({super.key});

  @override
  State<ForgotPasswordScreen> createState() => _ForgotPasswordScreenState();
}

class _ForgotPasswordScreenState extends State<ForgotPasswordScreen>
    with TickerProviderStateMixin {
  final TextEditingController _emailController = TextEditingController();
  bool _isLoading = false;
  String? _emailError;

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
    _emailController.dispose();
    super.dispose();
  }

  void _handleForgot() async {
    setState(() {
      _emailError = _emailController.text.isEmpty
          ? "Email wajib diisi"
          : !_emailController.text.contains("@")
              ? "Format email salah"
              : null;
    });

    if (_emailError != null) {
      _showSnackBar(_emailError!, Colors.red);
      return;
    }

    setState(() => _isLoading = true);
    final result = await ApiServices.forgotPassword(_emailController.text);
    setState(() => _isLoading = false);

    if (result['success'] == true) {
      _showSnackBar("Kode OTP telah dikirim ke email Anda.", Colors.green);
      Future.delayed(const Duration(milliseconds: 1500), () {
        if (mounted) {
          Navigator.push(
            context,
            MaterialPageRoute(
              builder: (context) => ResetPasswordScreen(email: _emailController.text),
            ),
          );
        }
      });
    } else {
      _showSnackBar(result['message'] ?? "Email tidak terdaftar di sistem kami.", Colors.red);
    }
  }

  void _showSnackBar(String msg, Color color) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(msg), backgroundColor: color, behavior: SnackBarBehavior.floating),
    );
  }

  @override
  Widget build(BuildContext context) {
    final eventProvider = context.watch<EventProvider>();
    final bool hasEvent = eventProvider.eventCode != 'default';
    final Color primaryColor = hasEvent ? eventProvider.primaryColor : const Color(0xFF00197D);
    final Color secondaryColor = hasEvent ? eventProvider.secondaryColor : const Color(0xFFD4AF37);
    final Color onPrimary = primaryColor.computeLuminance() > 0.5 ? Colors.black87 : Colors.white;

    // Warna tombol (pilih yang kontras)
    final Color buttonColor = (primaryColor.computeLuminance() - secondaryColor.computeLuminance()).abs() < 0.08
        ? primaryColor
        : secondaryColor;
    final Color buttonTextColor = buttonColor.computeLuminance() > 0.5 ? Colors.black87 : Colors.white;

    final LinearGradient headerGrad = LinearGradient(
      begin: Alignment.topLeft,
      end: Alignment.bottomRight,
      colors: [primaryColor, secondaryColor.withOpacity(0.85)],
    );

    final double topPadding = MediaQuery.of(context).padding.top;

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      body: SingleChildScrollView(
        child: Column(
          children: [
            // Header gradien dengan animasi fade
            Container(
              width: double.infinity,
              padding: EdgeInsets.only(top: topPadding + 30, left: 25, right: 25, bottom: 55),
              decoration: BoxDecoration(
                gradient: headerGrad,
                borderRadius: const BorderRadius.only(
                  bottomLeft: Radius.circular(50),
                  bottomRight: Radius.circular(50),
                ),
                boxShadow: [
                  BoxShadow(
                    color: primaryColor.withOpacity(0.3),
                    blurRadius: 20,
                    offset: const Offset(0, 8),
                  ),
                ],
              ),
              child: FadeTransition(
                opacity: _fadeAnimation,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Back button
                    GestureDetector(
                      onTap: () => Navigator.pop(context),
                      child: Container(
                        padding: const EdgeInsets.all(8),
                        decoration: BoxDecoration(
                          color: onPrimary.withOpacity(0.2),
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(color: onPrimary.withOpacity(0.4), width: 1),
                        ),
                        child: Icon(Icons.arrow_back_ios_new_rounded, color: onPrimary, size: 18),
                      ),
                    ),
                    const SizedBox(height: 24),
                    Container(
                      padding: const EdgeInsets.all(14),
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        color: onPrimary.withOpacity(0.2),
                        border: Border.all(color: onPrimary.withOpacity(0.4), width: 1.5),
                      ),
                      child: Icon(Icons.lock_reset_rounded, color: onPrimary, size: 32),
                    ),
                    const SizedBox(height: 18),
                    Text(
                      "Lupa Kata Sandi?",
                      style: TextStyle(color: onPrimary, fontSize: 28, fontWeight: FontWeight.bold),
                    ),
                    const SizedBox(height: 6),
                    Text(
                      "Kami akan kirim kode OTP ke email Anda",
                      style: TextStyle(color: onPrimary.withOpacity(0.7), fontSize: 13),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      "HOTEL & RESTAURANT PURNAMA",
                      style: TextStyle(
                        color: secondaryColor,
                        fontSize: 9,
                        fontWeight: FontWeight.w700,
                        letterSpacing: 2,
                      ),
                    ),
                  ],
                ),
              ),
            ),
            // Form card dengan animasi slide
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
                            color: primaryColor.withOpacity(0.12),
                            blurRadius: 24,
                            offset: const Offset(0, 10),
                          ),
                        ],
                      ),
                      child: Column(
                        children: [
                          Icon(Icons.mark_email_read_outlined, size: 52, color: primaryColor.withOpacity(0.7)),
                          const SizedBox(height: 14),
                          Text(
                            "Masukkan email terdaftar Anda untuk menerima kode OTP guna mengatur ulang kata sandi.",
                            textAlign: TextAlign.center,
                            style: TextStyle(color: Colors.grey.shade600, fontSize: 13, height: 1.5),
                          ),
                          const SizedBox(height: 28),
                          // Email field
                          Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                "ALAMAT EMAIL",
                                style: TextStyle(
                                  fontSize: 12,
                                  fontWeight: FontWeight.w600,
                                  color: Colors.grey.shade700,
                                  letterSpacing: 0.8,
                                ),
                              ),
                              const SizedBox(height: 6),
                              TextField(
                                controller: _emailController,
                                keyboardType: TextInputType.emailAddress,
                                decoration: InputDecoration(
                                  hintText: "Masukkan email terdaftar",
                                  prefixIcon: Icon(Icons.email_outlined, color: primaryColor),
                                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(14)),
                                  focusedBorder: OutlineInputBorder(
                                    borderRadius: BorderRadius.circular(14),
                                    borderSide: BorderSide(color: primaryColor, width: 2),
                                  ),
                                  errorText: _emailError,
                                  contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 28),
                          // Tombol Kirim OTP
                          _isLoading
                              ? CircularProgressIndicator(color: primaryColor)
                              : SizedBox(
                                  width: double.infinity,
                                  child: ElevatedButton(
                                    onPressed: _handleForgot,
                                    style: ElevatedButton.styleFrom(
                                      backgroundColor: buttonColor,
                                      foregroundColor: buttonTextColor,
                                      padding: const EdgeInsets.symmetric(vertical: 16),
                                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                                    ),
                                    child: const Text(
                                      "KIRIM KODE OTP",
                                      style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
                                    ),
                                  ),
                                ),
                          const SizedBox(height: 24),
                          Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              const Text("Ingat sandi Anda? ", style: TextStyle(color: Colors.grey)),
                              GestureDetector(
                                onTap: () => Navigator.pop(context),
                                child: Text(
                                  "Masuk Sekarang",
                                  style: TextStyle(color: secondaryColor, fontWeight: FontWeight.bold),
                                ),
                              ),
                            ],
                          ),
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