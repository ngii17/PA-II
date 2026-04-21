import 'package:flutter/material.dart';
import '../../services/api_services.dart';
import '../home/home_screen.dart'; // Import tujuan akhir setelah sukses
import 'login_screen.dart'; // Import untuk kembali ke login jika verifikasi berhasil

class OtpScreen extends StatefulWidget {
  final String email; // Menampung email kiriman dari halaman Register

  const OtpScreen({super.key, required this.email});

  @override
  State<OtpScreen> createState() => _OtpScreenState();
}

class _OtpScreenState extends State<OtpScreen> {
  final TextEditingController _otpController = TextEditingController();
  bool _isLoading = false;

  void _handleVerify() async {
    setState(() => _isLoading = true);

    // Memanggil fungsi verifikasi di ApiServices
    final result = await ApiServices.verifyOtp(widget.email, _otpController.text);

    setState(() => _isLoading = false);

    if (result['success'] == true) {
      // Jika VERIFIKASI BERHASIL
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(result['message'])),
      );

      // PINDAH KE HALAMAN HOME (Atau Login)
      Navigator.pushAndRemoveUntil(
        context,
        MaterialPageRoute(builder: (context) => const LoginScreen()),
        (route) => false, // Hapus semua riwayat halaman sebelumnya agar tidak bisa "back" ke OTP
      );
    } else {
      // Jika KODE SALAH
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(result['message'] ?? 'Kode OTP Salah')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text("Verifikasi Kode")),
      body: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Text("Kami telah mengirimkan kode OTP ke email:", textAlign: TextAlign.center),
            Text(widget.email, style: const TextStyle(fontWeight: FontWeight.bold)),
            const SizedBox(height: 30),
            
            TextField(
              controller: _otpController,
              keyboardType: TextInputType.number,
              textAlign: TextAlign.center,
              decoration: const InputDecoration(
                labelText: "Masukkan 6 Digit OTP",
                border: OutlineInputBorder(),
              ),
            ),
            const SizedBox(height: 30),

            _isLoading 
              ? const CircularProgressIndicator()
              : ElevatedButton(
                  onPressed: _handleVerify, 
                  child: const Text("VERIFIKASI SEKARANG")
                ),
          ],
        ),
      ),
    );
  }
}