import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../services/api_services.dart';
import 'otp_screen.dart';
import 'login_screen.dart';
import '../../colors/login_constants.dart';
import '../../widgets/login_widgets.dart';
import '../../widgets/shake_wrapper.dart';

class RegisterScreen extends StatefulWidget {
  const RegisterScreen({super.key});
  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> with TickerProviderStateMixin {
  // ── CONTROLLER ASLI 100% ──
  final TextEditingController _usernameController = TextEditingController();
  final TextEditingController _fullNameController = TextEditingController();
  final TextEditingController _emailController = TextEditingController();
  final TextEditingController _phoneController = TextEditingController();
  final TextEditingController _addressController = TextEditingController();
  final TextEditingController _passwordController = TextEditingController();
  final TextEditingController _confirmPasswordController = TextEditingController();
  final GlobalKey<ShakeWrapperState> _shakeKey = GlobalKey<ShakeWrapperState>();

  String _fullPhoneNumber = "";
  bool _isLoading = false;
  bool _obscurePassword = true;
  bool _obscureConfirmPassword = true;
  String? _uError, _fnError, _eError, _pError, _aError, _pwError, _cpwError;

  late AnimationController _fadeController;
  late Animation<double> _fadeAnimation;
  late Animation<Offset> _slideAnimation;

  @override
  void initState() {
    super.initState();
    _fadeController = AnimationController(vsync: this, duration: const Duration(milliseconds: 600));
    _fadeAnimation = CurvedAnimation(parent: _fadeController, curve: Curves.easeOut);
    _slideAnimation = Tween<Offset>(begin: const Offset(0, 0.08), end: Offset.zero)
        .animate(CurvedAnimation(parent: _fadeController, curve: Curves.easeOut));
    _fadeController.forward();
  }

  @override
  void dispose() {
    _fadeController.dispose();
    super.dispose();
  }

  // ── VALIDASI ASLI 100% ──
  void _handleRegister() async {
    setState(() {
      _uError = _fnError = _eError = _pError = _aError = _pwError = _cpwError = null;
    });

    bool hasError = false;

    if (_usernameController.text.length < 3 ||
        _usernameController.text.length > 8 ||
        !RegExp(r'^[A-Za-z]+[@$!%*?&]?$').hasMatch(_usernameController.text)) {
      _uError = "3-8 Karakter, awali huruf";
      hasError = true;
    }
    if (_fullNameController.text.length < 3 ||
        _fullNameController.text.length > 20 ||
        !RegExp(r'^[a-zA-Z\s]+$').hasMatch(_fullNameController.text)) {
      _fnError = "3-20 Karakter (Hanya Huruf)";
      hasError = true;
    }
    if (!_emailController.text.contains("@") || _emailController.text.isEmpty) {
      _eError = "Email tidak valid";
      hasError = true;
    }
    if (!_fullPhoneNumber.startsWith("+62") || _fullPhoneNumber.length < 10 || _fullPhoneNumber.length > 16) {
      _pError = "Wajib +62 (10-16 angka)";
      hasError = true;
    }
    if (_addressController.text.isEmpty) {
      _aError = "Alamat wajib diisi";
      hasError = true;
    }
    String pw = _passwordController.text;
    if (pw.length < 8 || pw.length > 12 ||
        !RegExp(r'^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).+$').hasMatch(pw)) {
      _pwError = "8-12 Char (Besar, Kecil, Angka, Simbol)";
      hasError = true;
    }
    if (pw != _confirmPasswordController.text) {
      _cpwError = "Sandi tidak cocok";
      hasError = true;
    }

    if (hasError) {
      ShakeWrapper.shake(_shakeKey);
      ModernNotify.show(context, "Beberapa data belum sesuai kriteria.");
      return;
    }

    setState(() => _isLoading = true);
    final result = await ApiServices.register({
      'username': _usernameController.text,
      'full_name': _fullNameController.text,
      'email': _emailController.text,
      'phone': _fullPhoneNumber,
      'address': _addressController.text,
      'password': _passwordController.text,
      'password_confirmation': _confirmPasswordController.text,
    });
    if (!mounted) return;
    setState(() => _isLoading = false);

    if (result['success'] == true) {
      final SharedPreferences prefs = await SharedPreferences.getInstance();
      await prefs.setBool('has_registered', true);
      ModernNotify.show(context, "Pendaftaran Berhasil!", isError: false);
      Future.delayed(const Duration(seconds: 2), () {
        if (mounted) Navigator.push(context, MaterialPageRoute(builder: (_) => OtpScreen(email: result['email'])));
      });
    } else {
      ShakeWrapper.shake(_shakeKey);
      String errorMsg = result['message'] ?? "Gagal mendaftar.";
      if (result['errors'] != null) errorMsg = result['errors'].values.first[0];
      ModernNotify.show(context, errorMsg);
    }
  }

  void _goToLogin() {
    Navigator.pushReplacement(
      context,
      PageRouteBuilder(
        pageBuilder: (_, __, ___) => const LoginScreen(),
        transitionsBuilder: (_, anim, __, child) => SlideTransition(
          position: Tween<Offset>(begin: const Offset(-1, 0), end: Offset.zero)
              .animate(CurvedAnimation(parent: anim, curve: Curves.easeInOut)),
          child: child,
        ),
        transitionDuration: const Duration(milliseconds: 350),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final size = MediaQuery.of(context).size;
    final topPad = MediaQuery.of(context).padding.top;

    return Scaffold(
      backgroundColor: AppTheme.primaryBlue,
      body: Stack(
        children: [
          // ── DEKORASI LATAR ──
          Positioned(
            top: -60, left: -60,
            child: Container(
              width: 200, height: 200,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: AppTheme.goldAccent.withOpacity(0.07),
              ),
            ),
          ),
          Positioned(
            top: 100, left: 30,
            child: Container(
              width: 60, height: 60,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: AppTheme.goldAccent.withOpacity(0.05),
              ),
            ),
          ),

          Column(
            children: [
              // ── HEADER ──
              SizedBox(
                height: size.height * 0.26,
                child: Padding(
                  padding: EdgeInsets.only(top: topPad + 20, left: 30, right: 30),
                  child: FadeTransition(
                    opacity: _fadeAnimation,
                    child: SlideTransition(
                      position: _slideAnimation,
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            children: [
                              Container(
                                padding: const EdgeInsets.all(8),
                                decoration: BoxDecoration(
                                  shape: BoxShape.circle,
                                  border: Border.all(color: AppTheme.goldAccent, width: 1.5),
                                  color: Colors.white.withOpacity(0.05),
                                ),
                                child: const Icon(Icons.waves_rounded, color: AppTheme.goldAccent, size: 22),
                              ),
                              const SizedBox(width: 10),
                              Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  const Text("PURNAMA", style: TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.bold, letterSpacing: 2)),
                                  Text("HOTEL & RESTAURANT", style: TextStyle(color: Colors.white.withOpacity(0.45), fontSize: 9, letterSpacing: 3)),
                                ],
                              ),
                            ],
                          ),
                          const SizedBox(height: 22),
                          const Text(
                            "Sign up",
                            style: TextStyle(color: Colors.white, fontSize: 30, fontWeight: FontWeight.bold),
                          ),
                          const SizedBox(height: 6),
                          Text(
                            "Daftar & nikmati pengalaman terbaik bersama kami",
                            style: TextStyle(color: Colors.white.withOpacity(0.55), fontSize: 13),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
              ),

              // ── CARD PUTIH ──
              Expanded(
                child: FadeTransition(
                  opacity: _fadeAnimation,
                  child: SlideTransition(
                    position: _slideAnimation,
                    child: Container(
                      width: double.infinity,
                      decoration: const BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.only(
                          topLeft: Radius.circular(36),
                          topRight: Radius.circular(36),
                        ),
                      ),
                      child: Column(
                        children: [
                          _buildTabToggle(),
                          Expanded(
                            child: SingleChildScrollView(
                              padding: const EdgeInsets.fromLTRB(28, 8, 28, 40),
                              child: ShakeWrapper(
                                key: _shakeKey,
                                child: Column(
                                  children: [
                                    // ── SEMUA ModernInput ASLI ──
                                    ModernInput(controller: _usernameController, label: "USERNAME", hint: "Contoh: sandra", icon: Icons.person_outline, isRequired: true, errorText: _uError),
                                    const SizedBox(height: 16),
                                    ModernInput(controller: _fullNameController, label: "NAMA LENGKAP", hint: "Gunakan huruf saja", icon: Icons.badge_outlined, isRequired: true, errorText: _fnError),
                                    const SizedBox(height: 16),
                                    ModernInput(controller: _emailController, label: "EMAIL", hint: "user@gmail.com", icon: Icons.email_outlined, isRequired: true, errorText: _eError),
                                    const SizedBox(height: 16),

                                    // ── ModernPhoneInput ASLI ──
                                    ModernPhoneInput(
                                      controller: _phoneController,
                                      label: "NOMOR HP",
                                      errorText: _pError,
                                      onNumberChanged: (val) => setState(() => _fullPhoneNumber = val),
                                    ),
                                    const SizedBox(height: 16),

                                    ModernInput(controller: _addressController, label: "ALAMAT", hint: "Alamat lengkap saat ini", icon: Icons.map_outlined, isRequired: true, errorText: _aError),
                                    const SizedBox(height: 16),
                                    ModernInput(
                                      controller: _passwordController,
                                      label: "KATA SANDI",
                                      hint: "8-12 Char (Besar, Kecil, Angka, Simbol)",
                                      icon: Icons.lock_outline,
                                      isRequired: true,
                                      isPassword: true,
                                      obscureText: _obscurePassword,
                                      errorText: _pwError,
                                      onSuffixIconPressed: () => setState(() => _obscurePassword = !_obscurePassword),
                                    ),
                                    const SizedBox(height: 16),
                                    ModernInput(
                                      controller: _confirmPasswordController,
                                      label: "KONFIRMASI SANDI",
                                      hint: "Ulangi sandi Anda",
                                      icon: Icons.lock_reset,
                                      isRequired: true,
                                      isPassword: true,
                                      obscureText: _obscureConfirmPassword,
                                      errorText: _cpwError,
                                      onSuffixIconPressed: () => setState(() => _obscureConfirmPassword = !_obscureConfirmPassword),
                                    ),

                                    const SizedBox(height: 28),

                                    // ── ModernButton ASLI ──
                                    ModernButton(
                                      text: "DAFTAR SEKARANG",
                                      isLoading: _isLoading,
                                      onPressed: _handleRegister,
                                    ),

                                    const SizedBox(height: 22),
                                    Row(
                                      mainAxisAlignment: MainAxisAlignment.center,
                                      children: [
                                        const Text("Sudah punya akun? ", style: TextStyle(color: Colors.grey)),
                                        GestureDetector(
                                          onTap: _goToLogin,
                                          child: const Text("Masuk di Sini", style: TextStyle(color: AppTheme.goldAccent, fontWeight: FontWeight.bold)),
                                        ),
                                      ],
                                    ),
                                    const SizedBox(height: 20),
                                  ],
                                ),
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildTabToggle() {
    return Container(
      margin: const EdgeInsets.fromLTRB(28, 20, 28, 16),
      height: 48,
      decoration: BoxDecoration(
        color: const Color(0xFFF0F4F8),
        borderRadius: BorderRadius.circular(14),
      ),
      child: Row(
        children: [
          // ── TIDAK AKTIF: SIGN IN ──
          Expanded(
            child: GestureDetector(
              onTap: _goToLogin,
              child: const Center(
                child: Text("Sign in", style: TextStyle(color: Colors.grey, fontWeight: FontWeight.w600, fontSize: 14)),
              ),
            ),
          ),
          // ── AKTIF: SIGN UP ──
          Expanded(
            child: Container(
              margin: const EdgeInsets.all(4),
              decoration: BoxDecoration(
                color: AppTheme.primaryBlue,
                borderRadius: BorderRadius.circular(11),
                boxShadow: [
                  BoxShadow(color: AppTheme.primaryBlue.withOpacity(0.25), blurRadius: 8, offset: const Offset(0, 3)),
                ],
              ),
              child: const Center(
                child: Text("Sign up", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 14)),
              ),
            ),
          ),
        ],
      ),
    );
  }
}