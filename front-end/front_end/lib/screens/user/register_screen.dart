import 'package:flutter/material.dart';
import '../../services/api_services.dart'; 
import 'otp_screen.dart'; 

class RegisterScreen extends StatefulWidget {
  const RegisterScreen({super.key});

  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> {
  // Controller sesuai kriteria
  final TextEditingController _usernameController = TextEditingController();
  final TextEditingController _fullNameController = TextEditingController();
  final TextEditingController _emailController = TextEditingController();
  final TextEditingController _phoneController = TextEditingController();
  final TextEditingController _addressController = TextEditingController();
  final TextEditingController _passwordController = TextEditingController();
  final TextEditingController _confirmPasswordController = TextEditingController();

  bool _isLoading = false;

  // Variabel untuk status sembunyi/lihat password
  bool _obscurePassword = true;
  bool _obscureConfirmPassword = true;

  void _showMessage(String msg, {bool isError = true}) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(msg),
        backgroundColor: isError ? Colors.red : Colors.green,
      ),
    );
  }

  void _handleRegister() async {
    // --- 1. VALIDASI USERNAME ---
    String username = _usernameController.text;
    if (username.length < 3 || username.length > 8 || !RegExp(r'^[A-Za-z]+[@$!%*?&]?$').hasMatch(username)) {
      _showMessage("Username: 3-8 karakter, awali huruf");
      return;
    }

    // --- 2. VALIDASI NAMA LENGKAP ---
    String fullName = _fullNameController.text;
    if (fullName.length < 3 || fullName.length > 20 || !RegExp(r'^[a-zA-Z\s]+$').hasMatch(fullName)) {
      _showMessage("Nama Lengkap: 3-20 karakter dan hanya boleh huruf");
      return;
    }

    // --- 3. VALIDASI EMAIL ---
    if (_emailController.text.isEmpty || !_emailController.text.contains("@")) {
      _showMessage("Masukkan alamat email");
      return;
    }
    
    // --- 4. VALIDASI NO HP ---
    if (!_phoneController.text.startsWith("+62") || _phoneController.text.length < 10 || _phoneController.text.length > 16) {
      _showMessage("No HP: Wajib awali +62 dan panjang 10-16 angka");
      return;
    }

    // --- 5. VALIDASI PASSWORD KUAT ---
    String password = _passwordController.text;
    if (password.length < 8 || password.length > 12 || 
        !RegExp(r'^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).+$').hasMatch(password)) {
      _showMessage("Password: 8-12 karakter, wajib ada Huruf Besar, Kecil, Angka, & Simbol");
      return;
    }

    // --- 6. VALIDASI KONFIRMASI PASSWORD ---
    if (password != _confirmPasswordController.text) {
      _showMessage("Konfirmasi password tidak cocok!");
      return;
    }

    // Validasi Alamat
    if (_addressController.text.isEmpty) {
      _showMessage("Mohon isi alamat lengkap");
      return;
    }

    setState(() => _isLoading = true);

    Map<String, dynamic> data = {
      'username': _usernameController.text,
      'full_name': _fullNameController.text,
      'email': _emailController.text,
      'phone': _phoneController.text,
      'address': _addressController.text,
      'password': _passwordController.text,
      'password_confirmation': _confirmPasswordController.text,
    };

    final result = await ApiServices.register(data);

    setState(() => _isLoading = false);

    if (result['success'] == true) {
      _showMessage(result['message'], isError: false);
      Navigator.push(
        context,
        MaterialPageRoute(
          builder: (context) => OtpScreen(email: result['email']),
        ),
      );
    } else {
      String errorMsg = result['message'] ?? "Registrasi Gagal";
      if (result['errors'] != null) {
        errorMsg = result['errors'].values.first[0];
      }
      _showMessage(errorMsg);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text("Register Purnama Berkelas")),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          children: [
            TextField(
              controller: _usernameController, 
              decoration: const InputDecoration(labelText: "Username (contoh: sandra)")
            ),
            const SizedBox(height: 10),
            TextField(
              controller: _fullNameController, 
              decoration: const InputDecoration(labelText: "Nama Lengkap (hanya menggunakan Huruf)")
            ),
            const SizedBox(height: 10),
            TextField(
              controller: _emailController, 
              keyboardType: TextInputType.emailAddress,
              decoration: const InputDecoration(labelText: "Email (Gmail)")
            ),
            const SizedBox(height: 10),
            TextField(
              controller: _phoneController, 
              keyboardType: TextInputType.phone,
              decoration: const InputDecoration(labelText: "No HP (+62xxxx)", hintText: "+62812345678")
            ),
            const SizedBox(height: 10),
            TextField(
              controller: _addressController, 
              decoration: const InputDecoration(labelText: "Alamat")
            ),
            const SizedBox(height: 10),
            
            // Kolom Password dengan Ikon Mata
            TextField(
              controller: _passwordController, 
              obscureText: _obscurePassword, 
              decoration: InputDecoration(
                labelText: "Password (8-12, Simbol, Angka, Besar/Kecil)",
                suffixIcon: IconButton(
                  icon: Icon(_obscurePassword ? Icons.visibility_off : Icons.visibility),
                  onPressed: () {
                    setState(() {
                      _obscurePassword = !_obscurePassword;
                    });
                  },
                ),
              ),
            ),
            const SizedBox(height: 10),
            
            // Kolom Konfirmasi Password dengan Ikon Mata
            TextField(
              controller: _confirmPasswordController, 
              obscureText: _obscureConfirmPassword, 
              decoration: InputDecoration(
                labelText: "Konfirmasi Password",
                suffixIcon: IconButton(
                  icon: Icon(_obscureConfirmPassword ? Icons.visibility_off : Icons.visibility),
                  onPressed: () {
                    setState(() {
                      _obscureConfirmPassword = !_obscureConfirmPassword;
                    });
                  },
                ),
              ),
            ),
            const SizedBox(height: 30),
            
            _isLoading 
              ? const CircularProgressIndicator() 
              : SizedBox(
                  width: double.infinity,
                  child: ElevatedButton(
                    onPressed: _handleRegister, 
                    style: ElevatedButton.styleFrom(padding: const EdgeInsets.symmetric(vertical: 15)),
                    child: const Text("DAFTAR SEKARANG")
                  ),
                ),
          ],
        ),
      ),
    );
  }
}