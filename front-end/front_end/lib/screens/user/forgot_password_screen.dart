import 'package:flutter/material.dart';
import '../../services/api_services.dart';
import 'reset_password_screen.dart';
import '../../colors/login_constants.dart';
import '../../widgets/login_widgets.dart';

class ForgotPasswordScreen extends StatefulWidget {
  const ForgotPasswordScreen({super.key});

  @override
  State<ForgotPasswordScreen> createState() => _ForgotPasswordScreenState();
}

class _ForgotPasswordScreenState extends State<ForgotPasswordScreen> {
  // 1. Controller untuk Input Email
  final TextEditingController _emailController = TextEditingController();
  
  // 2. State UI
  bool _isLoading = false;
  String? _emailError;

  // --- LOGIKA LUPA KATA SANDI ---
  void _handleForgot() async {
    // Reset status error dan lakukan validasi lokal
    setState(() {
      _emailError = _emailController.text.isEmpty ? "Email wajib diisi" : 
                    !_emailController.text.contains("@") ? "Format email salah" : null;
    });

    if (_emailError != null) {
      ModernNotify.show(context, "Mohon masukkan alamat email yang valid.");
      return;
    }

    setState(() => _isLoading = true);
    
    // Memanggil API Forgot Password (Port 8000)
    final result = await ApiServices.forgotPassword(_emailController.text);
    
    setState(() => _isLoading = false);

    if (result['success'] == true) {
      // 1. Munculkan notifikasi berhasil
      ModernNotify.show(context, "Kode OTP telah dikirim ke email Anda.", isError: false);
      
      // 2. Jeda navigasi agar user sempat membaca notifikasi sebelum pindah halaman
      Future.delayed(const Duration(milliseconds: 2500), () {
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
      // Munculkan notifikasi gagal (email tidak ditemukan)
      ModernNotify.show(context, result['message'] ?? "Email tidak terdaftar di sistem kami.");
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: SingleChildScrollView(
        child: Column(
          children: [
            // --- HEADER MODERN DENGAN GRADIEN NAVY ---
            Stack(
              children: [
                Container(
                  width: double.infinity,
                  padding: const EdgeInsets.symmetric(vertical: 60),
                  decoration: const BoxDecoration(
                    gradient: AppTheme.headerGradient,
                    borderRadius: BorderRadius.only(
                      bottomLeft: Radius.circular(60), 
                      bottomRight: Radius.circular(60)
                    ),
                  ),
                  child: Column(
                    children: [
                      Container(
                        padding: const EdgeInsets.all(12),
                        decoration: BoxDecoration(
                          shape: BoxShape.circle, 
                          border: Border.all(color: Colors.white30, width: 2)
                        ),
                        child: const Icon(Icons.lock_reset_rounded, color: Colors.white, size: 60),
                      ),
                      const SizedBox(height: 15),
                      const Text(
                        "LUPA KATA SANDI", 
                        style: TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.bold, letterSpacing: 2)
                      ),
                      const Text(
                        "HOTEL & RESTAURANT PURNAMA", 
                        style: TextStyle(color: Colors.white60, fontSize: 10, letterSpacing: 3)
                      ),
                    ],
                  ),
                ),
                // Tombol Kembali
                Positioned(
                  top: 50, 
                  left: 20, 
                  child: IconButton(
                    icon: const Icon(Icons.arrow_back_ios_new_rounded, color: Colors.white), 
                    onPressed: () => Navigator.pop(context)
                  )
                ),
              ],
            ),

            // --- AREA KONTEN ---
            Padding(
              padding: const EdgeInsets.all(35.0),
              child: Column(
                children: [
                  const Icon(Icons.mark_email_read_outlined, size: 55, color: Colors.black87),
                  const SizedBox(height: 15),
                  const Text(
                    "Masukkan email Anda untuk menerima kode OTP guna mengatur ulang kata sandi.", 
                    textAlign: TextAlign.center, 
                    style: TextStyle(color: Colors.grey, fontSize: 13, height: 1.5)
                  ),
                  const SizedBox(height: 40),

                  // Input Email menggunakan ModernInput kustom
                  ModernInput(
                    controller: _emailController, 
                    label: "ALAMAT EMAIL", 
                    hint: "Masukkan email terdaftar", 
                    icon: Icons.email_outlined, 
                    isRequired: true, 
                    errorText: _emailError
                  ),

                  const SizedBox(height: 40),

                  // Tombol Aksi atau Loading
                  _isLoading 
                    ? const CircularProgressIndicator(color: AppTheme.primaryBlue)
                    : SizedBox(
                        width: double.infinity,
                        height: 55,
                        child: ElevatedButton(
                          onPressed: _handleForgot, 
                          style: ElevatedButton.styleFrom(
                            backgroundColor: AppTheme.goldAccent, 
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)), 
                            elevation: 0
                          ),
                          child: const Text(
                            "KIRIM KODE OTP", 
                            style: TextStyle(color: AppTheme.primaryBlue, fontWeight: FontWeight.bold, fontSize: 16)
                          ),
                        ),
                      ),

                  const SizedBox(height: 35),

                  // Navigasi Kembali ke Login
                  Row(
                    mainAxisAlignment: MainAxisAlignment.center, 
                    children: [
                      const Text("Ingat sandi Anda? ", style: TextStyle(color: Colors.grey)),
                      GestureDetector(
                        onTap: () => Navigator.pop(context), 
                        child: const Text(
                          "Masuk Sekarang", 
                          style: TextStyle(color: AppTheme.goldAccent, fontWeight: FontWeight.bold)
                        )
                      ),
                    ]
                  ),
                  const SizedBox(height: 30),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}