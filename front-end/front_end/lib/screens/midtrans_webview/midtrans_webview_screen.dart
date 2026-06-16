// screens/payment/midtrans_webview_screen.dart

import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:webview_flutter/webview_flutter.dart';
import '../hotel/waiting_payment_screen.dart';
import '../../providers/event_provider.dart';
import '../notification/notification_screen.dart';

class MidtransWebViewScreen extends StatefulWidget {
  final String redirectUrl;
  final int reservasiId;

  const MidtransWebViewScreen({
    super.key,
    required this.redirectUrl,
    required this.reservasiId,
  });

  @override
  State<MidtransWebViewScreen> createState() => _MidtransWebViewScreenState();
}

class _MidtransWebViewScreenState extends State<MidtransWebViewScreen> {
  late final WebViewController _controller;
  bool _isLoading = true;
  double _progress = 0;

  @override
  void initState() {
    super.initState();
    _controller = WebViewController()
      ..setJavaScriptMode(JavaScriptMode.unrestricted)
      ..setNavigationDelegate(
        NavigationDelegate(
          onProgress: (progress) {
            if (mounted) {
              setState(() {
                _progress = progress / 100;
              });
            }
          },
          onPageStarted: (url) {
            if (mounted) setState(() => _isLoading = true);
          },
          onPageFinished: (url) {
            if (mounted) setState(() => _isLoading = false);
          },
          onNavigationRequest: (request) {
            final url = request.url.toLowerCase();

            if (url.contains('status_code=200') ||
                url.contains('status_code=201') ||
                url.contains('finish') ||
                url.contains('callback')) {
              _handlePaymentCompletion();
              return NavigationDecision.prevent;
            }

            if (url.contains('unfinish') || url.contains('error')) {
              _handlePaymentIncomplete();
              return NavigationDecision.prevent;
            }

            return NavigationDecision.navigate;
          },
        ),
      )
      ..loadRequest(Uri.parse(widget.redirectUrl));
  }

  void _handlePaymentCompletion() {
    if (!mounted) return;
    Navigator.pushAndRemoveUntil(
      context,
      MaterialPageRoute(
        builder: (context) => WaitingPaymentScreen(reservasiId: widget.reservasiId),
      ),
      (route) => false,
    );
  }

  void _handlePaymentIncomplete() {
    if (!mounted) return;
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(content: Text("Pembayaran belum diselesaikan.")),
    );
    Navigator.pop(context);
  }

  Future<bool> _onWillPop() async {
    final ep = context.read<EventProvider>();
    final Color primary = ep.eventCode != 'default' ? ep.primaryColor : const Color(0xFF0C2D6B);

    bool? confirm = await showDialog(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: const Text("Batalkan Pembayaran?",
            style: TextStyle(fontWeight: FontWeight.w900, fontSize: 18)),
        content: const Text(
            "Jika Anda keluar sekarang, sistem mungkin memerlukan waktu lebih lama untuk memverifikasi pesanan Anda."),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text("LANJUT BAYAR",
                style: TextStyle(color: Colors.grey, fontWeight: FontWeight.bold)),
          ),
          ElevatedButton(
            style: ElevatedButton.styleFrom(
              backgroundColor: primary,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
            ),
            onPressed: () => Navigator.pop(context, true),
            child: const Text("YA, KELUAR",
                style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
          ),
        ],
      ),
    );
    return confirm ?? false;
  }

  // ============================================================
  // WIDGET PURNAMA LOGO
  // ============================================================
  Widget _buildPurnamaLogo() {
    return Image.asset(
      'assets/icons/icon-purnama.png',
      width: 38,
      height: 38,
      errorBuilder: (_, __, ___) => Container(
        width: 38,
        height: 38,
        decoration: BoxDecoration(
          shape: BoxShape.circle,
          gradient: const LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [Color(0xFF1A4A9E), Color(0xFF0C2D6B)],
          ),
          border: Border.all(color: const Color(0xFFC9A227), width: 2),
        ),
        child: const Center(
          child: Text(
            "P",
            style: TextStyle(color: Color(0xFFC9A227), fontWeight: FontWeight.w900, fontSize: 18),
          ),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final ep = context.watch<EventProvider>();
    final Color primary = ep.eventCode != 'default' ? ep.primaryColor : const Color(0xFF0C2D6B);
    final Color secondary = ep.eventCode != 'default' ? ep.secondaryColor : const Color(0xFFC9A227);
    final topPadding = MediaQuery.of(context).padding.top;

    return PopScope(
      canPop: false,
      onPopInvokedWithResult: (didPop, result) async {
        if (didPop) return;
        final shouldPop = await _onWillPop();
        if (shouldPop && context.mounted) Navigator.pop(context);
      },
      child: Scaffold(
        backgroundColor: Colors.white,
        body: Column(
          children: [
            // ── HEADER MODERN ──
            Container(
              width: double.infinity,
              padding: EdgeInsets.only(top: topPadding + 16, left: 20, right: 20, bottom: 20),
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                  colors: [
                    primary,
                    primary.withOpacity(0.85),
                    secondary.withOpacity(0.7),
                  ],
                ),
                borderRadius: const BorderRadius.only(
                  bottomLeft: Radius.circular(36),
                  bottomRight: Radius.circular(36),
                ),
                boxShadow: [
                  BoxShadow(
                    color: primary.withOpacity(0.35),
                    blurRadius: 16,
                    offset: const Offset(0, 6),
                  ),
                ],
              ),
              child: Column(
                children: [
                  Row(
                    children: [
                      GestureDetector(
                        onTap: () async {
                          if (await _onWillPop()) {
                            if (mounted) Navigator.pop(context);
                          }
                        },
                        child: Container(
                          width: 34,
                          height: 34,
                          decoration: BoxDecoration(
                            color: Colors.white.withOpacity(0.15),
                            shape: BoxShape.circle,
                          ),
                          child: const Icon(
                            Icons.close_rounded,
                            color: Colors.white70,
                            size: 20,
                          ),
                        ),
                      ),
                      const SizedBox(width: 10),
                      _buildPurnamaLogo(),
                      const SizedBox(width: 10),
                      const Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text("Hotel & Restoran",
                              style: TextStyle(color: Colors.white60, fontSize: 9, letterSpacing: 1.2)),
                          Text("PURNAMA BALIGE",
                              style: TextStyle(color: Colors.white, fontSize: 13, fontWeight: FontWeight.w800, letterSpacing: 0.8)),
                        ],
                      ),
                      const Spacer(),
                      GestureDetector(
                        onTap: () => _controller.reload(),
                        child: Container(
                          width: 34,
                          height: 34,
                          decoration: BoxDecoration(
                            color: Colors.white.withOpacity(0.12),
                            shape: BoxShape.circle,
                          ),
                          child: const Icon(Icons.refresh_rounded, color: Colors.white70, size: 18),
                        ),
                      ),
                      const SizedBox(width: 8),
                      GestureDetector(
                        onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const NotificationScreen())),
                        child: Container(
                          width: 34,
                          height: 34,
                          decoration: BoxDecoration(
                            color: Colors.white.withOpacity(0.12),
                            shape: BoxShape.circle,
                          ),
                          child: const Icon(Icons.notifications_none_rounded, color: Colors.white70, size: 18),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 14),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Icon(Icons.payment_rounded, color: secondary, size: 20),
                      const SizedBox(width: 8),
                      const Text(
                        "Gerbang Pembayaran",
                        style: TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.w800, letterSpacing: 0.5),
                      ),
                    ],
                  ),
                ],
              ),
            ),
            // ── WEBVIEW ──
            Expanded(
              child: Stack(
                children: [
                  WebViewWidget(controller: _controller),
                  if (_progress < 1.0)
                    Positioned(
                      top: 0,
                      left: 0,
                      right: 0,
                      child: LinearProgressIndicator(
                        value: _progress,
                        backgroundColor: Colors.transparent,
                        color: secondary,
                        minHeight: 2.5,
                      ),
                    ),
                  if (_isLoading && _progress < 0.1)
                    Container(
                      color: Colors.white,
                      child: Center(
                        child: Column(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            CircularProgressIndicator(
                              valueColor: AlwaysStoppedAnimation<Color>(primary),
                              strokeWidth: 3,
                            ),
                            const SizedBox(height: 20),
                            Text(
                              "Menyiapkan Halaman Pembayaran...",
                              style: TextStyle(
                                color: primary,
                                fontWeight: FontWeight.w700,
                                fontSize: 14,
                              ),
                            ),
                            const SizedBox(height: 8),
                            const Text(
                              "Mohon jangan tutup halaman ini",
                              style: TextStyle(color: Colors.grey, fontSize: 12),
                            ),
                          ],
                        ),
                      ),
                    ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}