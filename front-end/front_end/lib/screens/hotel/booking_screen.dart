import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:intl/intl.dart';
import 'package:vibration/vibration.dart';

// Services & Providers
import '../../services/api_services.dart';
import '../../notification/notification_service.dart'; // Port Notifikasi

// Screens
import 'room_type_screen.dart'; 
import 'waiting_payment_screen.dart'; 
import '../midtrans_webview/midtrans_webview_screen.dart';
import '../event/event_header.dart';

// Widgets & Constants
import '../../colors/login_constants.dart';
import '../../widgets/login_widgets.dart';

class BookingScreen extends StatefulWidget {
  final RoomType room;
  const BookingScreen({super.key, required this.room});

  @override
  State<BookingScreen> createState() => _BookingScreenState();
}

class _BookingScreenState extends State<BookingScreen> {
  // Controller
  final TextEditingController _guestNameController = TextEditingController();
  final TextEditingController _guestNikController = TextEditingController();
  final TextEditingController _guestCountController = TextEditingController(text: "1");
  final TextEditingController _promoController = TextEditingController();
  
  // State Data
  DateTime? _checkInDate;
  DateTime? _checkOutDate;
  double _discountAmount = 0; 
  String _appliedPromoName = ""; 
  String _selectedPayment = "Transfer Bank"; 
  bool _isLoading = false;

  // State Error Validasi
  String? _nameError, _nikError, _dateError;

  // --- LOGIKA HITUNG MALAM ---
  int _calculateNights() {
    if (_checkInDate == null || _checkOutDate == null) return 0;
    int days = _checkOutDate!.difference(_checkInDate!).inDays;
    return days < 1 ? 1 : days; 
  }

  // --- LOGIKA CEK PROMO (Port 8000) ---
  void _handleCheckPromo() async {
    if (_promoController.text.isEmpty) return;
    final result = await ApiServices.checkPromoCode(_promoController.text, 'hotel');

    if (result['success'] == true) {
      setState(() {
        _appliedPromoName = result['data']['nama_promo'];
        double potongan = double.parse(result['data']['nominal_potongan'].toString());
        if (result['data']['tipe_diskon'] == 'persen') {
          double currentTotal = _calculateNights() * widget.room.hargaAkhir;
          _discountAmount = currentTotal * (potongan / 100);
        } else {
          _discountAmount = potongan;
        }
      });
      ModernNotify.show(context, "Voucher '$_appliedPromoName' berhasil dipasang!", isError: false);
    } else {
      setState(() { _discountAmount = 0; _appliedPromoName = ""; });
      ModernNotify.show(context, result['message'] ?? "Kode promo tidak valid");
    }
  }

  // --- LOGIKA RESERVASI & NOTIFIKASI FCM ---
  void _handleBooking() async {
    // 1. Validasi Input Lokal
    setState(() {
      _nameError = _guestNameController.text.isEmpty ? "Nama wajib diisi" : null;
      _nikError = _guestNikController.text.length < 16 ? "NIK minimal 16 digit" : null;
      _dateError = _checkInDate == null ? "Pilih tanggal inap" : null;
    });

    if (_nameError != null || _nikError != null || _dateError != null) {
      // Efek Getar saat gagal validasi
      Vibration.hasVibrator().then((has) { if (has == true) Vibration.vibrate(duration: 100); });
      ModernNotify.show(context, "Harap lengkapi data pemesanan Anda dengan benar.");
      return;
    }

    setState(() => _isLoading = true);

    try {
      final SharedPreferences prefs = await SharedPreferences.getInstance();
      int? userId = prefs.getInt('user_id');

      // AMBIL TOKEN HP ASLI UNTUK NOTIFIKASI REAL-TIME
      String? realFcmToken = await PushNotificationService.getDeviceToken();

      double finalTotal = (_calculateNights() * widget.room.hargaAkhir) - _discountAmount;

      // Data Kirim ke Laravel
      Map<String, dynamic> data = {
        "user_id": userId ?? 0,
        "fcm_token": realFcmToken ?? "", 
        "tipe_kamar_id": widget.room.id,
        "tgl_checkin": DateFormat('yyyy-MM-dd').format(_checkInDate!),
        "tgl_checkout": DateFormat('yyyy-MM-dd').format(_checkOutDate!),
        "total_malam": _calculateNights(),
        "total_harga": finalTotal,
        "metode_pembayaran": _selectedPayment,
        "nama_tamu": _guestNameController.text,
        "nik_identitas": _guestNikController.text,
        "jumlah_tamu": int.parse(_guestCountController.text),
      };

      final result = await ApiServices.storeReservation(data);
      setState(() => _isLoading = false);

      if (result['success'] == true) {
        if (!mounted) return;
        // Pindah ke WebView Midtrans untuk bayar
        Navigator.push(context, MaterialPageRoute(
          builder: (context) => MidtransWebViewScreen(
            redirectUrl: result['redirect_url'],
            reservasiId: result['reservasi_id'],
          ),
        ));
      } else {
        ModernNotify.show(context, result['message'] ?? "Gagal memproses reservasi");
      }
    } catch (e) {
      setState(() => _isLoading = false);
      ModernNotify.show(context, "Terjadi kesalahan sistem: $e");
    }
  }

  @override
  Widget build(BuildContext context) {
    int nights = _calculateNights();
    double subTotal = nights * widget.room.hargaAkhir;
    double totalBayar = subTotal - _discountAmount;

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        title: const Text("Konfirmasi Pesanan", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18)),
        centerTitle: true,
        backgroundColor: AppTheme.primaryBlue,
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: SingleChildScrollView(
        child: Column(
          children: [
            const EventHeader(), // Banner Promo Port 8001
            
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 15),
              child: Column(
                children: [
                  // --- SECTION 1: RINGKASAN KAMAR ---
                  _buildSectionCard(
                    title: "Kamar yang Dipilih",
                    icon: Icons.king_bed_rounded,
                    child: Row(
                      children: [
                        ClipRRect(
                          borderRadius: BorderRadius.circular(12),
                          child: Image.network(widget.room.fotoTipe ?? "", width: 90, height: 70, fit: BoxFit.cover, errorBuilder: (c,e,s) => const Icon(Icons.hotel)),
                        ),
                        const SizedBox(width: 15),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(widget.room.namaTipe, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: AppTheme.primaryBlue)),
                              Text("Rp ${widget.room.hargaAkhir.toStringAsFixed(0)} / malam", style: const TextStyle(color: AppTheme.goldAccent, fontWeight: FontWeight.w600)),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),

                  // --- SECTION 2: DATA IDENTITAS ---
                  _buildSectionCard(
                    title: "Informasi Tamu",
                    icon: Icons.person_add_alt_1_rounded,
                    child: Column(
                      children: [
                        ModernInput(controller: _guestNameController, label: "NAMA LENGKAP (KTP)", hint: "Nama sesuai identitas", icon: Icons.badge_outlined, isRequired: true, errorText: _nameError),
                        const SizedBox(height: 18),
                        ModernInput(controller: _guestNikController, label: "NIK / NO. IDENTITAS", hint: "16 Digit NIK", icon: Icons.fingerprint_rounded, isRequired: true, errorText: _nikError),
                      ],
                    ),
                  ),

                  // --- SECTION 3: TANGGAL INAP ---
                  _buildSectionCard(
                    title: "Waktu Menginap",
                    icon: Icons.date_range_rounded,
                    child: InkWell(
                      onTap: () async {
                        DateTimeRange? picked = await showDateRangePicker(
                          context: context, 
                          firstDate: DateTime.now(), 
                          lastDate: DateTime.now().add(const Duration(days: 365)),
                          builder: (context, child) => Theme(data: Theme.of(context).copyWith(colorScheme: const ColorScheme.light(primary: AppTheme.primaryBlue)), child: child!),
                        );
                        if (picked != null) setState(() { _checkInDate = picked.start; _checkOutDate = picked.end; });
                      },
                      child: Container(
                        padding: const EdgeInsets.all(18),
                        decoration: BoxDecoration(color: const Color(0xFFF3F4F6), borderRadius: BorderRadius.circular(18), border: Border.all(color: _dateError != null ? Colors.red : Colors.grey.shade200)),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(_checkInDate == null ? "Ketuk untuk pilih tanggal" : "${DateFormat('dd MMM').format(_checkInDate!)} - ${DateFormat('dd MMM yyyy').format(_checkOutDate!)}", style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
                                if (nights > 0) Text("$nights Malam Menginap", style: const TextStyle(color: AppTheme.primaryBlue, fontSize: 12, fontWeight: FontWeight.bold)),
                              ],
                            ),
                            const Icon(Icons.calendar_month, color: AppTheme.primaryBlue),
                          ],
                        ),
                      ),
                    ),
                  ),

                  // --- SECTION 4: VOUCHER ---
                  _buildSectionCard(
                    title: "Voucher Diskon",
                    icon: Icons.confirmation_number_outlined,
                    child: Row(
                      children: [
                        Expanded(
                          child: ModernInput(controller: _promoController, label: "KODE PROMO", hint: "Contoh: HEMAT", icon: Icons.local_offer_outlined),
                        ),
                        const SizedBox(width: 12),
                        Padding(
                          padding: const EdgeInsets.only(top: 20),
                          child: SizedBox(
                            height: 55,
                            child: ElevatedButton(
                              onPressed: _handleCheckPromo,
                              style: ElevatedButton.styleFrom(backgroundColor: AppTheme.primaryBlue, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15))),
                              child: const Text("CEK", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),

                  // --- SECTION 5: TOTAL HARGA ---
                  _buildSectionCard(
                    title: "Rincian Pembayaran",
                    icon: Icons.receipt_long_rounded,
                    child: Column(
                      children: [
                        _buildPriceRow("Harga Kamar ($nights malam)", subTotal),
                        if (_discountAmount > 0) _buildPriceRow("Potongan Promo", -_discountAmount, isDiscount: true),
                        const Divider(height: 30),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            const Text("Total Bayar", style: TextStyle(fontWeight: FontWeight.w900, fontSize: 16)),
                            Text("Rp ${totalBayar.toStringAsFixed(0)}", style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 20, color: AppTheme.goldAccent)),
                          ],
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 120),
                ],
              ),
            ),
          ],
        ),
      ),
      // --- STICKY BOTTOM BUTTON ---
      bottomSheet: Container(
        padding: const EdgeInsets.fromLTRB(25, 20, 25, 35),
        decoration: BoxDecoration(color: Colors.white, boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10, offset: const Offset(0, -5))], borderRadius: const BorderRadius.vertical(top: Radius.circular(30))),
        child: _isLoading 
          ? const Center(heightFactor: 1, child: CircularProgressIndicator(color: AppTheme.primaryBlue)) 
          : ModernButton(
              text: "RESERVASI SEKARANG", 
              onPressed: _handleBooking,
            ),
      ),
    );
  }

  // Widget Helper Kartu Seksi
  Widget _buildSectionCard({required String title, required IconData icon, required Widget child}) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(22),
      margin: const EdgeInsets.only(bottom: 20),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(25), boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.02), blurRadius: 15, offset: const Offset(0, 5))]),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(icon, color: AppTheme.goldAccent, size: 20),
              const SizedBox(width: 10),
              Text(title, style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 15, color: AppTheme.primaryBlue, letterSpacing: 0.5)),
            ],
          ),
          const Padding(padding: EdgeInsets.symmetric(vertical: 12), child: Divider(height: 1)),
          child,
        ],
      ),
    );
  }

  Widget _buildPriceRow(String label, double value, {bool isDiscount = false}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 5),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label, style: const TextStyle(color: Colors.grey, fontSize: 13, fontWeight: FontWeight.w500)),
          Text(
            "${value < 0 ? '-' : ''} Rp ${value.abs().toStringAsFixed(0)}", 
            style: TextStyle(fontWeight: FontWeight.bold, color: isDiscount ? Colors.red : Colors.black87),
          ),
        ],
      ),
    );
  }
}