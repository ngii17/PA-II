import 'package:flutter/material.dart';
import '../../services/api_services.dart';
import 'login_screen.dart';

class ResetPasswordScreen extends StatefulWidget {
  final String email;
  const ResetPasswordScreen({super.key, required this.email});

  @override
  State<ResetPasswordScreen> createState() => _ResetPasswordScreenState();
}

class _ResetPasswordScreenState extends State<ResetPasswordScreen> {
  final TextEditingController _otpController = TextEditingController();
  final TextEditingController _newPasswordController = TextEditingController();
  final TextEditingController _confirmPasswordController = TextEditingController(); 
  
  bool _isLoading = false;
  // --- 1. TAMBAHAN: Variabel status mata untuk kedua password ---
  bool _obscurePassword = true;
  bool _obscureConfirmPassword = true;

  // Fungsi pembantu untuk memunculkan pesan
  void _showMessage(String msg, {bool isError = true}) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(msg),
        backgroundColor: isError ? Colors.red : Colors.green,
      ),
    );
  }

  void _handleReset() async {
    // --- 2. PERBAIKAN: Validasi input kosong sesuai permintaan ---
    if (_otpController.text.isEmpty || 
        _newPasswordController.text.isEmpty || 
        _confirmPasswordController.text.isEmpty) {
      _showMessage("Harap isi semua biodata reset anda!");
      return;
    }

    String password = _newPasswordController.text;
    String confirm = _confirmPasswordController.text;

    // Validasi Password Berkelas (8-12 karakter, Besar, Kecil, Angka, Simbol)
    if (!RegExp(r'^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).+$').hasMatch(password) || 
        password.length < 8 || password.length > 12) {
      _showMessage("Password: 8-12 karakter, wajib ada Huruf Besar, Kecil, Angka, & Simbol");
      return;
    }

    // Validasi Konfirmasi Password Harus Sama
    if (password != confirm) {
      _showMessage("Konfirmasi password tidak cocok!");
      return;
    }

    if (_otpController.text.length != 6) {
      _showMessage("Masukkan 6 digit kode OTP");
      return;
    }

    setState(() => _isLoading = true);

    final result = await ApiServices.resetPassword(
      widget.email, 
      _otpController.text, 
      password,
      confirm
    );

    setState(() => _isLoading = false);

    if (result['success'] == true) {
      _showMessage(result['message'], isError: false);
      
      Navigator.pushAndRemoveUntil(
        context,
        MaterialPageRoute(builder: (context) => const LoginScreen()),
        (route) => false,
      );
    } else {
      _showMessage(result['message'] ?? "Gagal reset password");
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text("Reset Password")),
      body: SingleChildScrollView( 
        padding: const EdgeInsets.all(20),
        child: Column(
          children: [
            Text("Email: ${widget.email}", style: const TextStyle(fontWeight: FontWeight.bold)),
            const SizedBox(height: 20),
            
            // Input OTP
            TextField(
              controller: _otpController,
              keyboardType: TextInputType.number,
              decoration: const InputDecoration(
                labelText: "Masukkan 6 Digit OTP", 
                border: OutlineInputBorder(),
                prefixIcon: Icon(Icons.vpn_key),
              ),
            ),
            const SizedBox(height: 15),

            // --- 3. PERBAIKAN: Input Password Baru dengan IKON MATA ---
            TextField(
              controller: _newPasswordController,
              obscureText: _obscurePassword,
              decoration: InputDecoration(
                labelText: "Password Baru", 
                border: const OutlineInputBorder(),
                prefixIcon: const Icon(Icons.lock_outline),
                suffixIcon: IconButton(
                  icon: Icon(_obscurePassword ? Icons.visibility_off : Icons.visibility),
                  onPressed: () => setState(() => _obscurePassword = !_obscurePassword),
                ),
                helperText: "8-12 karakter, sertakan simbol & angka",
              ),
            ),
            const SizedBox(height: 15),

            // --- 4. PERBAIKAN: Input Konfirmasi dengan IKON MATA ---
            TextField(
              controller: _confirmPasswordController,
              obscureText: _obscureConfirmPassword,
              decoration: InputDecoration(
                labelText: "Konfirmasi Password Baru", 
                border: const OutlineInputBorder(),
                prefixIcon: const Icon(Icons.lock_reset),
                suffixIcon: IconButton(
                  icon: Icon(_obscureConfirmPassword ? Icons.visibility_off : Icons.visibility),
                  onPressed: () => setState(() => _obscureConfirmPassword = !_obscureConfirmPassword),
                ),
              ),
            ),
            const SizedBox(height: 30),

            _isLoading 
              ? const CircularProgressIndicator()
              : SizedBox(
                  width: double.infinity,
                  child: ElevatedButton(
                    onPressed: _handleReset, 
                    style: ElevatedButton.styleFrom(padding: const EdgeInsets.all(15)),
                    child: const Text("UPDATE PASSWORD"),
                  ),
                )
          ],
        ),
      ),
    );
  }
}