import 'package:flutter/material.dart';
import 'package:pinput/pinput.dart';
import 'package:provider/provider.dart';
import '../../services/api_services.dart';
import '../../providers/event_provider.dart';
import 'login_screen.dart';
import '../../widgets/login_widgets.dart'; // ← tambahkan ini

class OtpScreen extends StatefulWidget {
  final String email;
  const OtpScreen({super.key, required this.email});

  @override
  State<OtpScreen> createState() => _OtpScreenState();
}

class _OtpScreenState extends State<OtpScreen> with TickerProviderStateMixin {
  final TextEditingController _otpController = TextEditingController();
  bool _isLoading = false;
  String? _errorMessage;

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
    super.dispose();
  }

  void _handleVerify() async {
    if (_otpController.text.isEmpty || _otpController.text.length != 6) {
      setState(() => _errorMessage = "Masukkan 6 digit kode OTP");
      ModernNotify.show(context, "Masukkan 6 digit kode OTP yang valid.");
      return;
    }

    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    final result = await ApiServices.verifyOtp(widget.email, _otpController.text);
    setState(() => _isLoading = false);

// SESUDAH
if (result['success'] == true) {
  if (!mounted) return;
  ModernNotify.show(context, result['message'] ?? "Email berhasil diverifikasi!", isError: false);
  Future.delayed(const Duration(milliseconds: 1500), () {
    if (mounted) {
      Navigator.pushAndRemoveUntil(
        context,
        MaterialPageRoute(builder: (context) => const LoginScreen()),
        (route) => false,
      );
    }
  });
  } else {
    setState(() => _errorMessage = result['message'] ?? 'Kode OTP salah atau sudah expired');
    ModernNotify.show(context, _errorMessage!);
  }
} // ← INI yang hilang, penutup method _handleVerify()

  @override
  Widget build(BuildContext context) {
    final eventProvider = context.watch<EventProvider>();
    final bool hasEvent = eventProvider.eventCode != 'default';
    final Color primaryColor = hasEvent ? eventProvider.primaryColor : const Color(0xFF00197D);
    final Color secondaryColor = hasEvent ? eventProvider.secondaryColor : const Color(0xFFD4AF37);
    final double topPadding = MediaQuery.of(context).padding.top;

    // Warna tombol yang kontras
    final Color buttonColor =
        (primaryColor.computeLuminance() - secondaryColor.computeLuminance()).abs() < 0.08
            ? primaryColor
            : secondaryColor;
    final Color buttonTextColor = buttonColor.computeLuminance() > 0.5 ? Colors.black87 : Colors.white;

    final Color onPrimary = primaryColor.computeLuminance() > 0.5 ? Colors.black87 : Colors.white;
    final LinearGradient headerGrad = LinearGradient(
      begin: Alignment.topLeft,
      end: Alignment.bottomRight,
      colors: [primaryColor, secondaryColor.withOpacity(0.85)],
    );

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      body: SingleChildScrollView(
        child: Column(
          children: [
            // Header gradien
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
                    // Icon verifikasi
                    Container(
                      padding: const EdgeInsets.all(14),
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        color: onPrimary.withOpacity(0.2),
                        border: Border.all(color: onPrimary.withOpacity(0.4), width: 1.5),
                      ),
                      child: Icon(Icons.mark_email_read_outlined, color: onPrimary, size: 32),
                    ),
                    const SizedBox(height: 18),
                    Text(
                      "Verifikasi Email",
                      style: TextStyle(color: onPrimary, fontSize: 28, fontWeight: FontWeight.bold),
                    ),
                    const SizedBox(height: 8),
                    Text(
                      "Kode OTP 6 digit telah dikirim ke",
                      style: TextStyle(color: onPrimary.withOpacity(0.7), fontSize: 13),
                    ),
                    const SizedBox(height: 4),
                    Row(
                      children: [
                        Icon(Icons.email_outlined, color: secondaryColor, size: 14),
                        const SizedBox(width: 6),
                        Flexible(
                          child: Text(
                            widget.email,
                            style: TextStyle(color: secondaryColor, fontSize: 13, fontWeight: FontWeight.w700),
                            overflow: TextOverflow.ellipsis,
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ),
            // Form card
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
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            "Masukkan Kode OTP",
                            style: TextStyle(fontWeight: FontWeight.bold, fontSize: 17, color: primaryColor),
                          ),
                          const SizedBox(height: 6),
                          Text(
                            "Kode berlaku 10 menit. Periksa folder spam jika tidak diterima.",
                            style: TextStyle(color: Colors.grey.shade500, fontSize: 12),
                          ),
                          const SizedBox(height: 28),
                          // Pinput OTP
                          Center(
                            child: Pinput(
                              controller: _otpController,
                              length: 6,
                              showCursor: true,
                              onCompleted: (pin) => _handleVerify(),
                              defaultPinTheme: PinTheme(
                                width: 48,
                                height: 56,
                                textStyle: TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: primaryColor),
                                decoration: BoxDecoration(
                                  color: const Color(0xFFF8F9FA),
                                  border: Border.all(color: primaryColor.withOpacity(0.5), width: 1.5),
                                  borderRadius: BorderRadius.circular(14),
                                ),
                              ),
                              focusedPinTheme: PinTheme(
                                width: 48,
                                height: 56,
                                textStyle: TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: primaryColor),
                                decoration: BoxDecoration(
                                  color: Colors.white,
                                  border: Border.all(color: primaryColor, width: 2.5),
                                  borderRadius: BorderRadius.circular(14),
                                  boxShadow: [
                                    BoxShadow(
                                      color: primaryColor.withOpacity(0.25),
                                      blurRadius: 10,
                                      offset: const Offset(0, 4),
                                    ),
                                  ],
                                ),
                              ),
                              submittedPinTheme: PinTheme(
                                width: 48,
                                height: 56,
                                textStyle: TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: primaryColor),
                                decoration: BoxDecoration(
                                  color: primaryColor.withOpacity(0.1),
                                  border: Border.all(color: primaryColor.withOpacity(0.5), width: 1.5),
                                  borderRadius: BorderRadius.circular(14),
                                ),
                              ),
                            ),
                          ),
                          if (_errorMessage != null) ...[
                            const SizedBox(height: 18),
                            Container(
                              padding: const EdgeInsets.all(12),
                              decoration: BoxDecoration(
                                color: Colors.red.withOpacity(0.1),
                                border: Border.all(color: Colors.red.withOpacity(0.5)),
                                borderRadius: BorderRadius.circular(12),
                              ),
                              child: Row(
                                children: [
                                  const Icon(Icons.error_outline_rounded, color: Colors.red, size: 18),
                                  const SizedBox(width: 10),
                                  Expanded(
                                    child: Text(
                                      _errorMessage!,
                                      style: const TextStyle(color: Colors.red, fontSize: 12, fontWeight: FontWeight.w600),
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ],
                          const SizedBox(height: 28),
                          // Tombol Verifikasi
                          _isLoading
                              ? Center(child: CircularProgressIndicator(color: primaryColor))
                              : SizedBox(
                                  width: double.infinity,
                                  child: ElevatedButton(
                                    onPressed: _handleVerify,
                                    style: ElevatedButton.styleFrom(
                                      backgroundColor: buttonColor,
                                      foregroundColor: buttonTextColor,
                                      padding: const EdgeInsets.symmetric(vertical: 16),
                                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                                    ),
                                    child: const Text(
                                      "VERIFIKASI SEKARANG",
                                      style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
                                    ),
                                  ),
                                ),
                          const SizedBox(height: 14),
                          // Tombol Kembali
                          SizedBox(
                            width: double.infinity,
                            height: 52,
                            child: OutlinedButton(
                              onPressed: _isLoading ? null : () => Navigator.pop(context),
                              style: OutlinedButton.styleFrom(
                                side: BorderSide(color: primaryColor.withOpacity(0.6), width: 1.5),
                                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                              ),
                              child: Text(
                                "KEMBALI",
                                style: TextStyle(color: primaryColor, fontWeight: FontWeight.w700, fontSize: 14),
                              ),
                            ),
                          ),
                          const SizedBox(height: 18),
                          // Info hint
                          Container(
                            padding: const EdgeInsets.all(12),
                            decoration: BoxDecoration(
                              color: primaryColor.withOpacity(0.05),
                              border: Border.all(color: primaryColor.withOpacity(0.2)),
                              borderRadius: BorderRadius.circular(12),
                            ),
                            child: Row(
                              children: [
                                Icon(Icons.info_outline_rounded, color: primaryColor.withOpacity(0.7), size: 18),
                                const SizedBox(width: 10),
                                Expanded(
                                  child: Text(
                                    "Jika tidak menerima kode, periksa folder spam email Anda",
                                    style: TextStyle(color: primaryColor.withOpacity(0.6), fontSize: 11, fontWeight: FontWeight.w600),
                                  ),
                                ),
                              ],
                            ),
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