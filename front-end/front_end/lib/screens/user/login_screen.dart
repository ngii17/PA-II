import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart'; // <--- Tambah Import ini
import '../../services/api_services.dart';
import '../home/home_screen.dart';
import 'register_screen.dart';
import 'forgot_password_screen.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final TextEditingController _emailController = TextEditingController();
  final TextEditingController _passwordController = TextEditingController();
  
  bool _isLoading = false;
  bool _obscurePassword = true; 

  void _showMessage(String msg, {bool isError = true}) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(msg),
        backgroundColor: isError ? Colors.red : Colors.green,
      ),
    );
  }

  void _handleLogin() async {
    if (_emailController.text.isEmpty || _passwordController.text.isEmpty) {
      _showMessage("Harap isi biodata login anda!");
      return;
    }

    setState(() => _isLoading = true);

    final result = await ApiServices.login(
      _emailController.text, 
      _passwordController.text
    );

    setState(() => _isLoading = false);

    if (result['success'] == true) {
      // --- PROSES SIMPAN DATA KE STORAGE HP (Langkah 11) ---
      final SharedPreferences prefs = await SharedPreferences.getInstance();
      
      // Ambil data user dari respon JSON Laravel Port 8000
      int userId = result['user']['id']; 
      String fullName = result['user']['full_name'];
      String token = result['access_token'];

      // Simpan permanen di memori HP agar tidak hilang saat aplikasi ditutup
      await prefs.setInt('user_id', userId);
      await prefs.setString('full_name', fullName);
      await prefs.setString('auth_token', token);

      _showMessage("Selamat Datang, $fullName!", isError: false);

      // Pindah ke Home
      Navigator.pushAndRemoveUntil(
        context,
        MaterialPageRoute(builder: (context) => const HomeScreen()),
        (route) => false,
      );
    } else {
      _showMessage(result['message'] ?? 'Login Gagal, silakan cek kembali data Anda.');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text("Login Purnama Hotel")),
      body: SingleChildScrollView( 
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const SizedBox(height: 50),
            const Text("Selamat Datang\nKembali!", 
              style: TextStyle(fontSize: 28, fontWeight: FontWeight.bold)),
            const SizedBox(height: 40),
            
            TextField(
              controller: _emailController,
              keyboardType: TextInputType.emailAddress,
              decoration: const InputDecoration(
                labelText: "Email",
                prefixIcon: Icon(Icons.email_outlined),
              ),
            ),
            const SizedBox(height: 15),
            
            TextField(
              controller: _passwordController,
              obscureText: _obscurePassword,
              decoration: InputDecoration(
                labelText: "Password",
                prefixIcon: const Icon(Icons.lock_outline),
                suffixIcon: IconButton(
                  icon: Icon(_obscurePassword ? Icons.visibility_off : Icons.visibility),
                  onPressed: () {
                    setState(() => _obscurePassword = !_obscurePassword);
                  },
                ),
              ),
            ),

            Align(
              alignment: Alignment.centerRight,
              child: TextButton(
                onPressed: () {
                  Navigator.push(
                    context, 
                    MaterialPageRoute(builder: (context) => const ForgotPasswordScreen())
                  );
                }, 
                child: const Text("Lupa Password?")
              ),
            ),

            const SizedBox(height: 30),
            
            _isLoading 
              ? const Center(child: CircularProgressIndicator())
              : SizedBox(
                  width: double.infinity,
                  child: ElevatedButton(
                    onPressed: _handleLogin, 
                    style: ElevatedButton.styleFrom(padding: const EdgeInsets.all(15)),
                    child: const Text("MASUK KE AKUN", style: TextStyle(fontSize: 16)),
                  ),
                ),
            
            const SizedBox(height: 20),
            
            Center(
              child: TextButton(
                onPressed: () {
                  Navigator.push(context, MaterialPageRoute(builder: (context) => const RegisterScreen()));
                }, 
                child: const Text("Belum punya akun? Daftar Sekarang")
              ),
            )
          ],
        ),
      ),
    );
  }
}