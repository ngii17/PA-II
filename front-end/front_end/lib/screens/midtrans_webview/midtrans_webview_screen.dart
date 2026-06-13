import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:webview_flutter/webview_flutter.dart';
import '../hotel/waiting_payment_screen.dart';
import '../../providers/event_provider.dart';

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

            // Deteksi redirect Midtrans selesai (success)
            if (url.contains('status_code=200') ||
                url.contains('status_code=201') ||
                url.contains('finish') ||
                url.contains('callback')) {
              _handlePaymentCompletion();
              return NavigationDecision.prevent;
            }

            // Deteksi pembatalan / error
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

  @override
  Widget build(BuildContext context) {
    final ep = context.watch<EventProvider>();
    final Color primary = ep.eventCode != 'default' ? ep.primaryColor : const Color(0xFF0C2D6B);
    final Color secondary = ep.eventCode != 'default' ? ep.secondaryColor : const Color(0xFFC9A227);

    return PopScope(
      canPop: false,
      onPopInvokedWithResult: (didPop, result) async {
        if (didPop) return;
        final shouldPop = await _onWillPop();
        if (shouldPop && context.mounted) Navigator.pop(context);
      },
      child: Scaffold(
        backgroundColor: Colors.white,
        appBar: AppBar(
          title: const Text(
            "Gerbang Pembayaran",
            style: TextStyle(
              fontWeight: FontWeight.bold,
              fontSize: 16,
              letterSpacing: 0.5,
            ),
          ),
          centerTitle: true,
          backgroundColor: primary,
          foregroundColor: Colors.white,
          elevation: 0,
          leading: IconButton(
            icon: const Icon(Icons.close_rounded),
            onPressed: () async {
              if (await _onWillPop()) {
                if (mounted) Navigator.pop(context);
              }
            },
          ),
          actions: [
            IconButton(
              icon: const Icon(Icons.refresh_rounded),
              onPressed: () => _controller.reload(),
            ),
          ],
          flexibleSpace: Container(
            decoration: BoxDecoration(
              gradient: LinearGradient(
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
                colors: [primary, secondary.withOpacity(0.85)],
              ),
            ),
          ),
        ),
        body: Stack(
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
    );
  }
}