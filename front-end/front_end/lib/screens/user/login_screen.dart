import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../services/api_services.dart';
import '../home/home_screen.dart';
import 'register_screen.dart';
import 'forgot_password_screen.dart';
import '../../colors/login_constants.dart';
import '../../widgets/login_widgets.dart';
import '../../widgets/shake_wrapper.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});
  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> with TickerProviderStateMixin {
  final TextEditingController _emailController = TextEditingController();
  final TextEditingController _passwordController = TextEditingController();
  final GlobalKey<ShakeWrapperState> _shakeKey = GlobalKey<ShakeWrapperState>();

  bool _isLoading = false;
  bool _obscurePassword = true;
  String? _emailError;
  String? _passwordError;

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
    _emailController.dispose();
    _passwordController.dispose();
    super.dispose();
  }

  // ── LOGIKA LOGIN ASLI 100% ──
  void _handleLogin() async {
    setState(() {
      _emailError = _emailController.text.isEmpty ? "Email wajib diisi" : null;
      _passwordError = _passwordController.text.isEmpty ? "Sandi wajib diisi" : null;
    });

    if (_emailError != null || _passwordError != null) {
      ShakeWrapper.shake(_shakeKey);
      ModernNotify.show(context, "Harap lengkapi data login Anda.");
      return;
    }

    setState(() => _isLoading = true);
    final result = await ApiServices.login(_emailController.text, _passwordController.text);
    setState(() => _isLoading = false);

    if (result['success'] == true) {
      final SharedPreferences prefs = await SharedPreferences.getInstance();
      await prefs.setInt('user_id', result['user']['id']);
      await prefs.setString('full_name', result['user']['full_name']);
      await prefs.setString('auth_token', result['access_token']);
      await prefs.setBool('has_registered', true);

      if (!mounted) return;
      ModernNotify.show(context, "Selamat datang kembali!", isError: false);
      Future.delayed(const Duration(milliseconds: 1500), () {
        if (mounted) {
          Navigator.pushAndRemoveUntil(
            context,
            MaterialPageRoute(builder: (context) => const HomeScreen()),
            (route) => false,
          );
        }
      });
    } else {
      ShakeWrapper.shake(_shakeKey);
      ModernNotify.show(context, result['message'] ?? 'Email atau kata sandi salah.');
    }
  }

  void _goToRegister() {
    Navigator.pushReplacement(
      context,
      PageRouteBuilder(
        pageBuilder: (_, __, ___) => const RegisterScreen(),
        transitionsBuilder: (_, anim, __, child) => SlideTransition(
          position: Tween<Offset>(begin: const Offset(1, 0), end: Offset.zero)
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
          // ── DEKORASI LINGKARAN LATAR ──
          Positioned(
            top: -60, right: -60,
            child: Container(
              width: 200, height: 200,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: AppTheme.goldAccent.withOpacity(0.07),
              ),
            ),
          ),
          Positioned(
            top: 80, right: 30,
            child: Container(
              width: 80, height: 80,
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
                height: size.height * 0.30,
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
                          // Logo
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
                          const SizedBox(height: 28),
                          const Text(
                            "Sign in",
                            style: TextStyle(color: Colors.white, fontSize: 30, fontWeight: FontWeight.bold),
                          ),
                          const SizedBox(height: 6),
                          Text(
                            "Masuk untuk akses penuh layanan kami",
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
                          // ── TAB TOGGLE ──
                          _buildTabToggle(),

                          // ── FORM ──
                          Expanded(
                            child: SingleChildScrollView(
                              padding: const EdgeInsets.fromLTRB(28, 8, 28, 30),
                              child: ShakeWrapper(
                                key: _shakeKey,
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    ModernInput(
                                      controller: _emailController,
                                      label: "ALAMAT EMAIL",
                                      hint: "Masukkan email kamu",
                                      icon: Icons.email_outlined,
                                      isRequired: true,
                                      errorText: _emailError,
                                    ),
                                    const SizedBox(height: 18),
                                    ModernInput(
                                      controller: _passwordController,
                                      label: "KATA SANDI",
                                      hint: "Masukkan kata sandi",
                                      icon: Icons.lock_outline,
                                      isRequired: true,
                                      isPassword: true,
                                      obscureText: _obscurePassword,
                                      errorText: _passwordError,
                                      onSuffixIconPressed: () => setState(() => _obscurePassword = !_obscurePassword),
                                    ),
                                    Align(
                                      alignment: Alignment.centerRight,
                                      child: TextButton(
                                        onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const ForgotPasswordScreen())),
                                        child: const Text("Lupa kata sandi?", style: TextStyle(color: AppTheme.goldAccent, fontWeight: FontWeight.bold, fontSize: 12)),
                                      ),
                                    ),
                                    const SizedBox(height: 8),

                                    // ── TOMBOL MASUK (ModernButton asli) ──
                                    ModernButton(
                                      text: "MASUK SEKARANG",
                                      isLoading: _isLoading,
                                      onPressed: _handleLogin,
                                    ),

                                    const SizedBox(height: 24),
                                    Row(
                                      children: [
                                        Expanded(child: Divider(color: Colors.grey.shade200)),
                                        Padding(
                                          padding: const EdgeInsets.symmetric(horizontal: 12),
                                          child: Text("atau", style: TextStyle(color: Colors.grey.shade400, fontSize: 12)),
                                        ),
                                        Expanded(child: Divider(color: Colors.grey.shade200)),
                                      ],
                                    ),
                                    const SizedBox(height: 20),

                                    // ── GOOGLE BUTTON ASLI ──
                                    SizedBox(
                                      width: double.infinity,
                                      height: 50,
                                      child: OutlinedButton.icon(
                                        onPressed: () {},
                                        icon: const Icon(Icons.g_mobiledata_rounded, color: Colors.red, size: 35),
                                        label: const Text("Lanjutkan dengan Google", style: TextStyle(color: Colors.black87)),
                                        style: OutlinedButton.styleFrom(
                                          side: BorderSide(color: Colors.grey.shade300),
                                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
                                        ),
                                      ),
                                    ),

                                    const SizedBox(height: 20),

                                    // ── GUEST BUTTON ASLI ──
                                    Center(
                                      child: TextButton.icon(
                                        onPressed: () => Navigator.pushAndRemoveUntil(
                                          context,
                                          MaterialPageRoute(builder: (_) => const HomeScreen()),
                                          (route) => false,
                                        ),
                                        icon: const Icon(Icons.visibility_outlined, color: Colors.grey, size: 18),
                                        label: const Text("Masuk sebagai Tamu", style: TextStyle(color: Colors.grey, decoration: TextDecoration.underline)),
                                      ),
                                    ),
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
          // ── AKTIF: SIGN IN ──
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
                child: Text("Sign in", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 14)),
              ),
            ),
          ),
          // ── TIDAK AKTIF: SIGN UP ──
          Expanded(
            child: GestureDetector(
              onTap: _goToRegister,
              child: const Center(
                child: Text("Sign up", style: TextStyle(color: Colors.grey, fontWeight: FontWeight.w600, fontSize: 14)),
              ),
            ),
          ),
        ],
      ),
    );
  }
}