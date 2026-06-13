import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:provider/provider.dart';
import '../../services/api_services.dart';
import '../../providers/event_provider.dart';
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
    _fadeController = AnimationController(
        vsync: this, duration: const Duration(milliseconds: 600));
    _fadeAnimation =
        CurvedAnimation(parent: _fadeController, curve: Curves.easeOut);
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
    final eventProvider = context.watch<EventProvider>();

    final bool hasEvent = eventProvider.eventCode != 'default';
    final Color primaryColor = hasEvent ? eventProvider.primaryColor : AppTheme.primaryBlue;
    final Color secondaryColor = hasEvent ? eventProvider.secondaryColor : AppTheme.goldAccent;

    final Color buttonColor = AppTheme.resolveButtonColor(primaryColor, secondaryColor);
    final Color buttonTextColor = AppTheme.resolveButtonTextColor(buttonColor);

    final LinearGradient headerGrad = hasEvent
        ? AppTheme.buildHeaderGradient(primaryColor, secondaryColor)
        : AppTheme.headerGradient;

    final Color onPrimary = AppTheme.contrastColor(primaryColor);
    final Color headerAccent = onPrimary == Colors.white ? secondaryColor : primaryColor;

    return Scaffold(
      backgroundColor: primaryColor,
      body: Stack(
        children: [
          // Dekorasi lingkaran latar
          Positioned(
            top: -60,
            right: -60,
            child: Container(
              width: 220,
              height: 220,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: secondaryColor.withAlpha(20),
              ),
            ),
          ),
          Positioned(
            top: 100,
            right: 40,
            child: Container(
              width: 80,
              height: 80,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: secondaryColor.withAlpha(13),
              ),
            ),
          ),
          Positioned(
            bottom: size.height * 0.35,
            left: -30,
            child: Container(
              width: 100,
              height: 100,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: onPrimary.withAlpha(8),
              ),
            ),
          ),

          Column(
            children: [
              // Header Gradien
              Container(
                height: size.height * 0.32,
                decoration: BoxDecoration(gradient: headerGrad),
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
                          // Logo & Brand
                          Row(
                            children: [
                              Container(
                                padding: const EdgeInsets.all(9),
                                decoration: BoxDecoration(
                                  shape: BoxShape.circle,
                                  border: Border.all(color: headerAccent.withAlpha(160), width: 1.5),
                                  color: onPrimary.withAlpha(18),
                                ),
                                child: Icon(Icons.waves_rounded, color: headerAccent, size: 22),
                              ),
                              const SizedBox(width: 12),
                              Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    hasEvent
                                        ? (eventProvider.activeTheme['nama_hotel'] ?? 'PURNAMA').toString().toUpperCase()
                                        : "PURNAMA",
                                    style: TextStyle(
                                        color: onPrimary,
                                        fontSize: 16,
                                        fontWeight: FontWeight.bold,
                                        letterSpacing: 2),
                                  ),
                                  Text(
                                    "HOTEL & RESTAURANT",
                                    style: TextStyle(
                                        color: onPrimary.withAlpha(115),
                                        fontSize: 9,
                                        letterSpacing: 3),
                                  ),
                                ],
                              ),
                            ],
                          ),
                          const SizedBox(height: 28),
                          Text(
                            "Sign in",
                            style: TextStyle(
                                color: onPrimary,
                                fontSize: 30,
                                fontWeight: FontWeight.bold),
                          ),
                          const SizedBox(height: 6),
                          Text(
                            "Masuk untuk akses penuh layanan kami",
                            style: TextStyle(
                                color: onPrimary.withAlpha(140),
                                fontSize: 13),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
              ),

              // Card putih dengan form
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
                          _buildTabToggle(primaryColor, secondaryColor),
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
                                      accentColor: primaryColor,
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
                                      accentColor: primaryColor,
                                      onSuffixIconPressed: () => setState(
                                          () => _obscurePassword = !_obscurePassword),
                                    ),
                                    Align(
                                      alignment: Alignment.centerRight,
                                      child: TextButton(
                                        onPressed: () => Navigator.push(
                                            context,
                                            MaterialPageRoute(
                                                builder: (_) => const ForgotPasswordScreen())),
                                        child: Text(
                                          "Lupa kata sandi?",
                                          style: TextStyle(
                                              color: secondaryColor,
                                              fontWeight: FontWeight.bold,
                                              fontSize: 12),
                                        ),
                                      ),
                                    ),
                                    const SizedBox(height: 8),
                                    EventAwareButton(
                                      text: "MASUK SEKARANG",
                                      isLoading: _isLoading,
                                      onPressed: _handleLogin,
                                      buttonColor: buttonColor,
                                      textColor: buttonTextColor,
                                    ),
                                    const SizedBox(height: 24),
                                    Row(
                                      children: [
                                        Expanded(child: Divider(color: Colors.grey.shade200)),
                                        Padding(
                                          padding: const EdgeInsets.symmetric(horizontal: 12),
                                          child: Text("atau",
                                              style: TextStyle(color: Colors.grey.shade400, fontSize: 12)),
                                        ),
                                        Expanded(child: Divider(color: Colors.grey.shade200)),
                                      ],
                                    ),
                                    const SizedBox(height: 20),
                                    // Tombol Google (tidak aktif)
                                    SizedBox(
                                      width: double.infinity,
                                      height: 50,
                                      child: OutlinedButton.icon(
                                        onPressed: () {},
                                        icon: const Icon(Icons.g_mobiledata_rounded, color: Colors.red, size: 35),
                                        label: const Text("Lanjutkan dengan Google",
                                            style: TextStyle(color: Colors.black87)),
                                        style: OutlinedButton.styleFrom(
                                          side: BorderSide(color: Colors.grey.shade300),
                                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
                                        ),
                                      ),
                                    ),
                                    const SizedBox(height: 20),
                                    Center(
                                      child: TextButton.icon(
                                        onPressed: () => Navigator.pushAndRemoveUntil(
                                          context,
                                          MaterialPageRoute(builder: (_) => const HomeScreen()),
                                          (route) => false,
                                        ),
                                        icon: Icon(Icons.visibility_outlined, color: Colors.grey.shade400, size: 18),
                                        label: Text(
                                          "Masuk sebagai Tamu",
                                          style: TextStyle(
                                              color: Colors.grey.shade500,
                                              decoration: TextDecoration.underline),
                                        ),
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

  Widget _buildTabToggle(Color primaryColor, Color secondaryColor) {
    return Container(
      margin: const EdgeInsets.fromLTRB(28, 20, 28, 16),
      height: 48,
      decoration: BoxDecoration(
        color: const Color(0xFFF0F4F8),
        borderRadius: BorderRadius.circular(14),
      ),
      child: Row(
        children: [
          // Tab Sign In (aktif)
          Expanded(
            child: Container(
              margin: const EdgeInsets.all(4),
              decoration: BoxDecoration(
                color: primaryColor,
                borderRadius: BorderRadius.circular(11),
                boxShadow: [
                  BoxShadow(
                      color: primaryColor.withAlpha(77),
                      blurRadius: 8,
                      offset: const Offset(0, 3)),
                ],
              ),
              child: Center(
                child: Text(
                  "Sign in",
                  style: TextStyle(
                      color: AppTheme.contrastColor(primaryColor),
                      fontWeight: FontWeight.bold,
                      fontSize: 14),
                ),
              ),
            ),
          ),
          // Tab Sign Up (tidak aktif)
          Expanded(
            child: GestureDetector(
              onTap: _goToRegister,
              child: const Center(
                child: Text("Sign up",
                    style: TextStyle(
                        color: Colors.grey,
                        fontWeight: FontWeight.w600,
                        fontSize: 14)),
              ),
            ),
          ),
        ],
      ),
    );
  }
}