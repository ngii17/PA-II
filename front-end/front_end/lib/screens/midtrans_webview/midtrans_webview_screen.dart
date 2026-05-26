import 'package:flutter/material.dart';
import 'package:webview_flutter/webview_flutter.dart';
import '../hotel/waiting_payment_screen.dart';
import '../../colors/login_constants.dart'; // Import tema warna Anda

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
            setState(() {
              _progress = progress / 100;
            });
          },
          onPageStarted: (url) {
            setState(() => _isLoading = true);
          },
          onPageFinished: (url) {
            setState(() => _isLoading = false);
          },
          onNavigationRequest: (request) {
            // LOGIKA REDIRECT MIDTRANS SNAP
            if (request.url.contains('demo.midtrans.com/callback_url') ||
                request.url.contains('status_code=200') ||
                request.url.contains('status_code=201')) {
              
              // Pembayaran Berhasil/Pending: Arahkan ke WaitingPaymentScreen
              Navigator.pushAndRemoveUntil(
                context,
                MaterialPageRoute(
                  builder: (context) => WaitingPaymentScreen(reservasiId: widget.reservasiId),
                ),
                (route) => false,
              );
              return NavigationDecision.prevent;
            }
            return NavigationDecision.navigate;
          },
        ),
      )
      ..loadRequest(Uri.parse(widget.redirectUrl));
  }

  // --- FUNGSI PROTEKSI: Mencegah User keluar secara tidak sengaja ---
  Future<bool> _onWillPop() async {
    bool? confirm = await showDialog(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: const Text("Batalkan Pembayaran?", style: TextStyle(fontWeight: FontWeight.bold)),
        content: const Text("Jika Anda keluar sekarang, status pembayaran mungkin tidak terupdate secara otomatis."),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context, false), child: const Text("Lanjut Bayar")),
          TextButton(
            onPressed: () => Navigator.pop(context, true), 
            child: const Text("Ya, Keluar", style: TextStyle(color: Colors.red))
          ),
        ],
      ),
    );
    return confirm ?? false;
  }

  @override
  Widget build(BuildContext context) {
    return PopScope(
      canPop: false, // Menahan tombol back fisik HP
      onPopInvokedWithResult: (didPop, result) async {
        if (didPop) return;
        final bool shouldPop = await _onWillPop();
        if (shouldPop && context.mounted) {
          Navigator.pop(context);
        }
      },
      child: Scaffold(
        backgroundColor: Colors.white,
        appBar: AppBar(
          title: const Text("Gerbang Pembayaran", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18)),
          centerTitle: true,
          backgroundColor: AppTheme.primaryBlue,
          foregroundColor: Colors.white,
          elevation: 0,
          leading: IconButton(
            icon: const Icon(Icons.close_rounded),
            onPressed: () async {
              if (await _onWillPop()) Navigator.pop(context);
            },
          ),
        ),
        body: Stack(
          children: [
            // Konten WebView Utama
            WebViewWidget(controller: _controller),

            // Linear Progress Bar (Seperti Browser Modern)
            if (_progress < 1.0)
              LinearProgressIndicator(
                value: _progress,
                backgroundColor: Colors.white,
                color: AppTheme.goldAccent,
                minHeight: 3,
              ),

            // Overlay Loading Tengah (Hanya saat awal load)
            if (_isLoading && _progress < 0.1)
              Container(
                color: Colors.white,
                child: const Center(
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      CircularProgressIndicator(color: AppTheme.primaryBlue),
                      SizedBox(height: 15),
                      Text("Menghubungkan ke Midtrans...", style: TextStyle(color: Colors.grey, fontSize: 13)),
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