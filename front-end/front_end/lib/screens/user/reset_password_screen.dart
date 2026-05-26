import 'package:flutter/material.dart';
import '../../services/api_services.dart';
import 'login_screen.dart';
import '../../colors/login_constants.dart';
import '../../widgets/login_widgets.dart';

class ResetPasswordScreen extends StatefulWidget {
  final String email;
  const ResetPasswordScreen({super.key, required this.email});

  @override
  State<ResetPasswordScreen> createState() => _ResetPasswordScreenState();
}

class _ResetPasswordScreenState extends State<ResetPasswordScreen> {
  // 1. Controller untuk Input
  final TextEditingController _otpController = TextEditingController();
  final TextEditingController _newPasswordController = TextEditingController();
  final TextEditingController _confirmPasswordController = TextEditingController(); 
  
  // 2. State UI
  bool _isLoading = false;
  bool _obscurePassword = true;
  bool _obscureConfirmPassword = true;
  bool _isOtpError = false;

  // Variabel untuk menampung pesan error per field
  String? _pwError, _cpwError;

  // --- LOGIKA RESET PASSWORD ---
  void _handleReset() async {
    // Reset status error sebelum validasi
    setState(() {
      _isOtpError = false;
      _pwError = null;
      _cpwError = null;
    });

    // A. Validasi Input Kosong
    if (_otpController.text.isEmpty || 
        _newPasswordController.text.isEmpty || 
        _confirmPasswordController.text.isEmpty) {
      ModernNotify.show(context, "Harap isi semua biodata reset anda!");
      return;
    }

    // B. Validasi Kode OTP (Wajib 6 Digit)
    if (_otpController.text.length != 6) {
      setState(() => _isOtpError = true);
      ModernNotify.show(context, "Masukkan 6 digit kode OTP yang valid");
      return;
    }

    // C. Validasi Kriteria Sandi (8-12 karakter, Besar, Kecil, Angka, Simbol)
    String password = _newPasswordController.text;
    if (!RegExp(r'^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).+$').hasMatch(password) || 
        password.length < 8 || password.length > 12) {
      setState(() => _pwError = "Kriteria salah");
      ModernNotify.show(context, "Sandi: 8-12 karakter, wajib ada Huruf Besar, Kecil, Angka, & Simbol");
      return;
    }

    // D. Validasi Konfirmasi Password Harus Sama
    if (password != _confirmPasswordController.text) {
      setState(() => _cpwError = "Tidak cocok");
      ModernNotify.show(context, "Konfirmasi kata sandi tidak cocok!");
      return;
    }

    // --- PROSES KE API ---
    setState(() => _isLoading = true);

    final result = await ApiServices.resetPassword(
      widget.email, 
      _otpController.text, 
      password,
      _confirmPasswordController.text
    );

    setState(() => _isLoading = false);

    if (result['success'] == true) {
      ModernNotify.show(context, "Kata sandi berhasil diperbarui!", isError: false);
      
      // Delay sebentar agar user bisa melihat pesan sukses
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
      ModernNotify.show(context, result['message'] ?? "Kode OTP salah atau gagal mereset sandi.");
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: SingleChildScrollView( 
        child: Column(
          children: [
            // --- HEADER MODERN DENGAN GRADIEN ---
            Stack(
              children: [
                Container(
                  width: double.infinity,
                  padding: const EdgeInsets.symmetric(vertical: 60),
                  decoration: const BoxDecoration(
                    gradient: AppTheme.headerGradient,
                    borderRadius: BorderRadius.only(
                      bottomLeft: Radius.circular(60),
                      bottomRight: Radius.circular(60),
                    ),
                  ),
                  child: Column(
                    children: [
                      Container(
                        padding: const EdgeInsets.all(12),
                        decoration: BoxDecoration(
                          shape: BoxShape.circle,
                          border: Border.all(color: Colors.white30, width: 2),
                        ),
                        child: const Icon(Icons.security_update_good_rounded, color: Colors.white, size: 60),
                      ),
                      const SizedBox(height: 15),
                      const Text(
                        "ATUR ULANG SANDI", 
                        style: TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.bold, letterSpacing: 2)
                      ),
                      const SizedBox(height: 5),
                      Text(
                        widget.email, 
                        style: const TextStyle(color: Colors.white70, fontSize: 13, fontWeight: FontWeight.w500)
                      ),
                    ],
                  ),
                ),
                // Tombol Kembali
                Positioned(
                  top: 50, left: 20,
                  child: IconButton(
                    icon: const Icon(Icons.arrow_back_ios_new_rounded, color: Colors.white),
                    onPressed: () => Navigator.pop(context),
                  ),
                ),
              ],
            ),

            Padding(
              padding: const EdgeInsets.all(35.0),
              child: Column(
                children: [
                  const Text(
                    "Masukkan Kode OTP", 
                    style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: AppTheme.primaryBlue)
                  ),
                  const SizedBox(height: 20),

                  // --- INPUT OTP KOTAK-KOTAK MODERN ---
                  ModernOtpInput(
                    controller: _otpController,
                    hasError: _isOtpError,
                  ),

                  const SizedBox(height: 40),

                  // Input Password Baru
                  ModernInput(
                    controller: _newPasswordController,
                    label: "KATA SANDI BARU",
                    hint: "8-12 Karakter + Simbol",
                    icon: Icons.lock_outline,
                    isRequired: true,
                    isPassword: true,
                    obscureText: _obscurePassword,
                    errorText: _pwError,
                    onSuffixIconPressed: () => setState(() => _obscurePassword = !_obscurePassword),
                  ),
                  const SizedBox(height: 20),

                  // Konfirmasi Password Baru
                  ModernInput(
                    controller: _confirmPasswordController,
                    label: "KONFIRMASI SANDI",
                    hint: "Ulangi sandi baru Anda",
                    icon: Icons.lock_reset_rounded,
                    isRequired: true,
                    isPassword: true,
                    obscureText: _obscureConfirmPassword,
                    errorText: _cpwError,
                    onSuffixIconPressed: () => setState(() => _obscureConfirmPassword = !_obscureConfirmPassword),
                  ),

                  const SizedBox(height: 40),

                  // Tombol Aksi
                  _isLoading 
                    ? const CircularProgressIndicator(color: AppTheme.primaryBlue)
                    : SizedBox(
                        width: double.infinity,
                        height: 55,
                        child: ElevatedButton(
                          onPressed: _handleReset, 
                          style: ElevatedButton.styleFrom(
                            backgroundColor: AppTheme.goldAccent,
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
                            elevation: 0,
                          ),
                          child: const Text(
                            "SIMPAN SANDI BARU", 
                            style: TextStyle(color: AppTheme.primaryBlue, fontWeight: FontWeight.bold, fontSize: 16)
                          ),
                        ),
                      ),
                  
                  const SizedBox(height: 40),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}