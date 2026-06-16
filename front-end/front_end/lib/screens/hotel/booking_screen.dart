// screens/hotel/booking_screen.dart

import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:intl/intl.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../services/api_services.dart';
import '../../providers/event_provider.dart';
import 'room_type_screen.dart';
import 'waiting_payment_screen.dart';
import '../event/event_header.dart';
import '../../notification/notification_service.dart';
import '../event/app_theme.dart';
import '../home/promo_list_screen.dart';
import 'package:flutter/services.dart';
import '../notification/notification_screen.dart';

class BookingScreen extends StatefulWidget {
  final RoomType room;
  const BookingScreen({super.key, required this.room});

  @override
  State<BookingScreen> createState() => _BookingScreenState();
}

class _BookingScreenState extends State<BookingScreen> {
  DateTime? _checkInDate;
  DateTime? _checkOutDate;
  final TextEditingController _guestNameController = TextEditingController();
  final TextEditingController _guestNikController = TextEditingController();
  final TextEditingController _guestCountController = TextEditingController(text: "1");
  final TextEditingController _promoController = TextEditingController();

  double _discountAmount = 0;
  String _appliedPromoName = "";
  int? _appliedPromoId;
  String _selectedPayment = "Transfer Bank";
  bool _isLoading = false;
  String _nikErrorMessage = "";

  @override
  void initState() {
    super.initState();
    _guestNikController.addListener(_validateNIK);
  }

  @override
  void dispose() {
    _guestNikController.removeListener(_validateNIK);
    _guestNameController.dispose();
    _guestNikController.dispose();
    _guestCountController.dispose();
    _promoController.dispose();
    super.dispose();
  }

  void _validateNIK() {
    setState(() {
      if (_guestNikController.text.isEmpty) {
        _nikErrorMessage = "";
      } else if (_guestNikController.text.length < 16) {
        _nikErrorMessage = "NIK harus 16 karakter (${_guestNikController.text.length}/16)";
      } else {
        _nikErrorMessage = "";
      }
    });
  }

  int _calculateNights() {
    if (_checkInDate == null || _checkOutDate == null) return 0;
    return _checkOutDate!.difference(_checkInDate!).inDays;
  }

  double _getSubtotal() => _calculateNights() * widget.room.hargaAkhir;

  void _handleCheckPromo() async {
    if (_promoController.text.isEmpty) return;

    final prefs = await SharedPreferences.getInstance();
    int userId = prefs.getInt('user_id') ?? 0;

    if (userId == 0) {
      _showSnackBar("Silakan login terlebih dahulu", Colors.red);
      return;
    }

    final result = await ApiServices.checkPromoCode(
      _promoController.text,
      'hotel',
      userId: userId,
      totalHarga: _getSubtotal(),
    );

    if (result['success'] == true) {
      setState(() {
        _appliedPromoId   = result['data']['promo_id'];
        _appliedPromoName = result['data']['nama_promo'];
        _discountAmount   = double.parse(result['data']['potongan_dihitung'].toString());
      });
      _showSnackBar("Promo Berhasil Dipasang!", Colors.green);
    } else {
      setState(() {
        _appliedPromoId   = null;
        _discountAmount   = 0;
        _appliedPromoName = "";
      });
      _showSnackBar(result['message'] ?? "Promo tidak valid", Colors.red);
    }
  }

  void _handleBooking() async {
    if (_checkInDate == null) {
      _showSnackBar("Harap pilih tanggal check-in!", Colors.red);
      return;
    }

    DateTime tomorrow     = DateTime.now().add(const Duration(days: 1));
    DateTime checkInDate  = DateTime(_checkInDate!.year, _checkInDate!.month, _checkInDate!.day);
    DateTime tomorrowDate = DateTime(tomorrow.year, tomorrow.month, tomorrow.day);

    if (checkInDate.isBefore(tomorrowDate)) {
      _showSnackBar("Booking minimal H+1 hari. Silakan pilih tanggal mulai besok atau lebih.", Colors.red);
      return;
    }

    if (_guestNikController.text.length != 16) {
      _showSnackBar("NIK harus tepat 16 karakter", Colors.red);
      return;
    }

    final prefs   = await SharedPreferences.getInstance();
    int? userId   = prefs.getInt('user_id');

    if (userId == null || userId == 0) {
      showDialog(
        context: context,
        builder: (context) => AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
          contentPadding: const EdgeInsets.fromLTRB(24, 24, 24, 0),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(color: Colors.red.shade50, shape: BoxShape.circle),
                child: Icon(Icons.lock_outline_rounded, color: Colors.red.shade400, size: 40),
              ),
              const SizedBox(height: 16),
              const Text("Login Diperlukan", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16), textAlign: TextAlign.center),
              const SizedBox(height: 8),
              const Text("Silakan login terlebih dahulu untuk melakukan reservasi kamar.", style: TextStyle(color: Colors.grey, fontSize: 13), textAlign: TextAlign.center),
              const SizedBox(height: 24),
            ],
          ),
          actions: [
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppTheme.primaryBlue,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                ),
                onPressed: () => Navigator.pop(context),
                child: const Text("Mengerti", style: TextStyle(color: Colors.white)),
              ),
            ),
          ],
        ),
      );
      return;
    }

    if (_guestNameController.text.isEmpty || _guestNikController.text.isEmpty) {
      _showSnackBar("Harap isi semua data!", Colors.red);
      return;
    }

    setState(() => _isLoading = true);
    String? realFcmToken = await PushNotificationService.getDeviceToken();

    Map<String, dynamic> data = {
      "user_id"          : userId,
      "fcm_token"        : realFcmToken ?? "",
      "tipe_kamar_id"    : widget.room.id,
      "tgl_checkin"      : DateFormat('yyyy-MM-dd').format(_checkInDate!),
      "tgl_checkout"     : DateFormat('yyyy-MM-dd').format(_checkOutDate!),
      "total_malam"      : _calculateNights(),
      "total_harga"      : _getSubtotal(),
      "metode_pembayaran": _selectedPayment,
      "nama_tamu"        : _guestNameController.text,
      "nik_identitas"    : _guestNikController.text,
      "jumlah_tamu"      : int.parse(_guestCountController.text),
      "promo_id"         : _appliedPromoId,
    };

    final result = await ApiServices.storeReservation(data);
    setState(() => _isLoading = false);

    if (result['success'] == true) {
      if (_selectedPayment != "Bayar di Kasir") {
        String? redirectUrl = result['redirect_url'];
        int? reservasiId = result['reservasi_id'];
        if (redirectUrl != null && reservasiId != null) {
          await launchUrl(Uri.parse(redirectUrl), mode: LaunchMode.externalApplication);
          if (!mounted) return;
          Navigator.push(context, MaterialPageRoute(builder: (context) => WaitingPaymentScreen(reservasiId: reservasiId)));
        }
      } else {
        _showSuccessDialog("Booking Berhasil! Silakan selesaikan pembayaran dan proses Check-in di Front Desk saat tiba di hotel.");
      }
    } else {
      _showSnackBar(result['message'] ?? "Gagal memproses booking", Colors.red);
    }
  }

  void _showSuccessDialog(String msg) {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: const Text("Berhasil", style: TextStyle(fontWeight: FontWeight.bold)),
        content: Text(msg),
        actions: [
          TextButton(
            onPressed: () => Navigator.popUntil(context, (route) => route.isFirst),
            child: const Text("OK", style: TextStyle(fontWeight: FontWeight.bold)),
          ),
        ],
      ),
    );
  }

  void _showSnackBar(String msg, Color color) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(msg), backgroundColor: color, behavior: SnackBarBehavior.floating),
    );
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
    final eventProvider  = context.watch<EventProvider>();
    final primaryColor   = eventProvider.primaryColor;
    final secondaryColor = eventProvider.secondaryColor;
    final topPadding = MediaQuery.of(context).padding.top;

    int nights       = _calculateNights();
    double subTotal  = _getSubtotal();
    double totalBayar = subTotal - _discountAmount;

    return Scaffold(
      backgroundColor: Colors.grey.shade50,
      body: Column(
        children: [
          // ── HEADER MODERN DENGAN TOMBOL BACK ──
          Container(
            width: double.infinity,
            padding: EdgeInsets.only(top: topPadding + 16, left: 20, right: 20, bottom: 28),
            decoration: BoxDecoration(
              gradient: LinearGradient(
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
                colors: [
                  primaryColor,
                  primaryColor.withOpacity(0.85),
                  secondaryColor.withOpacity(0.7),
                ],
              ),
              borderRadius: const BorderRadius.only(
                bottomLeft: Radius.circular(36),
                bottomRight: Radius.circular(36),
              ),
              boxShadow: [
                BoxShadow(
                  color: primaryColor.withOpacity(0.35),
                  blurRadius: 16,
                  offset: const Offset(0, 6),
                ),
              ],
            ),
            child: Column(
              children: [
                Row(
                  children: [
                    // ── TOMBOL BACK ──
                    GestureDetector(
                      onTap: () => Navigator.pop(context),
                      child: Container(
                        width: 34,
                        height: 34,
                        decoration: BoxDecoration(
                          color: Colors.white.withOpacity(0.15),
                          shape: BoxShape.circle,
                        ),
                        child: const Icon(
                          Icons.arrow_back_ios_new_rounded,
                          color: Colors.white70,
                          size: 16,
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
                const SizedBox(height: 18),
                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Icon(Icons.hotel_rounded, color: secondaryColor, size: 20),
                    const SizedBox(width: 8),
                    const Text(
                      "Konfirmasi Booking",
                      style: TextStyle(color: Colors.white, fontSize: 20, fontWeight: FontWeight.w800, letterSpacing: 0.5),
                    ),
                  ],
                ),
              ],
            ),
          ),
          const EventHeader(),
          // ── BODY ──
          Expanded(
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _buildSectionTitle("Data Tamu", primaryColor),
                  const SizedBox(height: 10),
                  _buildTextField(_guestNameController, "Nama Sesuai KTP", Icons.person, primaryColor),
                  const SizedBox(height: 10),
                  _buildTextFieldNIK(_guestNikController, "NIK / KTP", Icons.badge, primaryColor),
                  const SizedBox(height: 20),

                  _buildSectionTitle("Jadwal Menginap", primaryColor),
                  const SizedBox(height: 10),
                  Card(
                    elevation: 2,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                    child: ListTile(
                      leading: Icon(Icons.date_range, color: primaryColor),
                      title: Text(
                        _checkInDate == null
                            ? "Klik untuk Pilih Tanggal"
                            : "${DateFormat('dd MMM').format(_checkInDate!)} - ${DateFormat('dd MMM yyyy').format(_checkOutDate!)}",
                        style: const TextStyle(fontWeight: FontWeight.w500),
                      ),
                      trailing: const Icon(Icons.arrow_forward_ios, size: 16, color: Colors.grey),
                      onTap: () async {
                        DateTimeRange? picked = await showDateRangePicker(
                          context: context,
                          firstDate: DateTime.now().add(const Duration(days: 1)),
                          lastDate: DateTime.now().add(const Duration(days: 365)),
                          builder: (context, child) {
                            return Theme(
                              data: Theme.of(context).copyWith(
                                colorScheme: ColorScheme.light(primary: primaryColor),
                              ),
                              child: child!,
                            );
                          },
                        );

                        if (picked != null) {
                          if (!picked.end.isAfter(picked.start)) {
                            if (mounted) {
                              showDialog(
                                context: context,
                                builder: (context) => AlertDialog(
                                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                                  contentPadding: const EdgeInsets.fromLTRB(24, 24, 24, 0),
                                  content: Column(
                                    mainAxisSize: MainAxisSize.min,
                                    children: [
                                      Container(
                                        padding: const EdgeInsets.all(16),
                                        decoration: BoxDecoration(color: Colors.orange.shade50, shape: BoxShape.circle),
                                        child: Icon(Icons.warning_amber_rounded, color: Colors.orange.shade400, size: 40),
                                      ),
                                      const SizedBox(height: 16),
                                      const Text("Tanggal Tidak Valid", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16), textAlign: TextAlign.center),
                                      const SizedBox(height: 8),
                                      const Text("Reservasi minimal harus 1 malam. Silakan pilih tanggal check-out setelah tanggal check-in.", style: TextStyle(color: Colors.grey, fontSize: 13), textAlign: TextAlign.center),
                                      const SizedBox(height: 24),
                                    ],
                                  ),
                                  actions: [
                                    SizedBox(
                                      width: double.infinity,
                                      child: ElevatedButton(
                                        style: ElevatedButton.styleFrom(
                                          backgroundColor: primaryColor,
                                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                                        ),
                                        onPressed: () => Navigator.pop(context),
                                        child: const Text("Mengerti", style: TextStyle(color: Colors.white)),
                                      ),
                                    ),
                                  ],
                                ),
                              );
                            }
                            return;
                          }
                          setState(() {
                            _checkInDate  = picked.start;
                            _checkOutDate = picked.end;
                            _appliedPromoId   = null;
                            _discountAmount   = 0;
                            _appliedPromoName = "";
                            _promoController.clear();
                          });
                        }
                      },
                    ),
                  ),
                  const SizedBox(height: 25),

                  _buildSectionTitle("Metode Pembayaran", primaryColor),
                  const SizedBox(height: 10),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 16),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(16),
                      border: Border.all(color: Colors.grey.shade200),
                    ),
                    child: DropdownButtonHideUnderline(
                      child: DropdownButton<String>(
                        value: _selectedPayment,
                        isExpanded: true,
                        items: ["Transfer Bank", "E-Wallet", "Bayar di Kasir"]
                            .map((v) => DropdownMenuItem(value: v, child: Text(v, style: const TextStyle(fontSize: 14))))
                            .toList(),
                        onChanged: (v) => setState(() => _selectedPayment = v!),
                        dropdownColor: Colors.white,
                        borderRadius: BorderRadius.circular(12),
                      ),
                    ),
                  ),
                  const SizedBox(height: 25),

                  // ── BAGIAN PROMO DENGAN TOMBOL LIHAT PROMO ──
                  Row(
                    children: [
                      const Expanded(
                        child: Text(
                          "Punya Kode Promo?",
                          style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
                        ),
                      ),
                      TextButton(
                        onPressed: () async {
                          final result = await Navigator.push<String>(
                            context,
                            MaterialPageRoute(
                              builder: (_) => const PromoListScreen(
                                kategori: 'hotel',
                              ),
                            ),
                          );
                          
                          if (result != null && result.isNotEmpty) {
                            setState(() {
                              _promoController.text = result;
                            });
                            _handleCheckPromo();
                          }
                        },
                        style: TextButton.styleFrom(
                          foregroundColor: secondaryColor,
                          padding: const EdgeInsets.symmetric(horizontal: 8),
                        ),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Icon(Icons.local_offer_rounded, color: secondaryColor, size: 16),
                            const SizedBox(width: 4),
                            const Text(
                              "Lihat Promo",
                              style: TextStyle(fontWeight: FontWeight.w600, fontSize: 12),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 10),
                  Row(
                    children: [
                      Expanded(
                        child: Container(
                          decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(16),
                            border: Border.all(color: Colors.grey.shade200),
                          ),
                          child: TextField(
                            controller: _promoController,
                            decoration: const InputDecoration(
                              hintText: "Masukkan kode promo",
                              contentPadding: EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                              border: InputBorder.none,
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(width: 10),
                      ElevatedButton(
                        onPressed: _handleCheckPromo,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: secondaryColor,
                          foregroundColor: Colors.white,
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(30)),
                          padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 14),
                        ),
                        child: const Text("CEK", style: TextStyle(fontWeight: FontWeight.bold)),
                      ),
                    ],
                  ),
                  if (_appliedPromoName.isNotEmpty)
                    Padding(
                      padding: const EdgeInsets.only(top: 8),
                      child: Row(
                        children: [
                          const Icon(Icons.check_circle, color: Colors.green, size: 16),
                          const SizedBox(width: 6),
                          Text(
                            _appliedPromoName,
                            style: const TextStyle(color: Colors.green, fontWeight: FontWeight.w600, fontSize: 13),
                          ),
                          const Spacer(),
                          GestureDetector(
                            onTap: () => setState(() {
                              _appliedPromoId   = null;
                              _discountAmount   = 0;
                              _appliedPromoName = "";
                              _promoController.clear();
                            }),
                            child: const Icon(Icons.close, color: Colors.red, size: 16),
                          ),
                        ],
                      ),
                    ),
                  const SizedBox(height: 30),

                  _buildSectionTitle("Rincian Biaya", primaryColor),
                  const SizedBox(height: 10),
                  Container(
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(20),
                      boxShadow: [
                        BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 10, offset: const Offset(0, 4)),
                      ],
                      border: Border.all(color: Colors.grey.shade100),
                    ),
                    child: Column(
                      children: [
                        _buildPriceRow("Harga ($nights Malam)", "Rp ${subTotal.toStringAsFixed(0)}", color: Colors.black87),
                        if (_discountAmount > 0)
                          _buildPriceRow("Voucher Diskon", "- Rp ${_discountAmount.toStringAsFixed(0)}", color: Colors.red),
                        const Divider(height: 30),
                        _buildPriceRow("Total Bayar", "Rp ${totalBayar.toStringAsFixed(0)}", isBold: true, color: primaryColor, fontSize: 20),
                      ],
                    ),
                  ),
                  const SizedBox(height: 30),
                ],
              ),
            ),
          ),
        ],
      ),
      bottomNavigationBar: Container(
        padding: const EdgeInsets.all(20),
        decoration: BoxDecoration(
          color: Colors.white,
          boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 8, offset: const Offset(0, -2))],
        ),
        child: _isLoading
            ? Center(child: CircularProgressIndicator(color: primaryColor))
            : ElevatedButton(
                onPressed: _handleBooking,
                style: ElevatedButton.styleFrom(
                  backgroundColor: primaryColor,
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(vertical: 16),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                  elevation: 4,
                ),
                child: const Text("KONFIRMASI & BOOKING SEKARANG", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
              ),
      ),
    );
  }

  Widget _buildSectionTitle(String title, Color color) {
    return Row(
      children: [
        Container(width: 4, height: 18, color: color, margin: const EdgeInsets.only(right: 8)),
        Text(title, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
      ],
    );
  }

  Widget _buildTextField(TextEditingController controller, String label, IconData icon, Color color) {
    return TextField(
      controller: controller,
      decoration: InputDecoration(
        labelText: label,
        prefixIcon: Icon(icon, color: color),
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(16), borderSide: BorderSide.none),
        filled: true,
        fillColor: Colors.white,
        contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
      ),
    );
  }

  Widget _buildTextFieldNIK(TextEditingController controller, String label, IconData icon, Color color) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        TextField(
          controller: controller,
          keyboardType: TextInputType.number,
          inputFormatters: [
            FilteringTextInputFormatter.digitsOnly,
            LengthLimitingTextInputFormatter(16),
          ],
          decoration: InputDecoration(
            labelText: label,
            prefixIcon: Icon(icon, color: color),
            suffixText: '${controller.text.length}/16',
            suffixStyle: TextStyle(
              color: controller.text.length == 16 ? Colors.green : Colors.grey,
              fontWeight: FontWeight.bold,
            ),
            border: OutlineInputBorder(
              borderRadius: BorderRadius.circular(16),
              borderSide: BorderSide(color: controller.text.length == 16 ? Colors.green : Colors.grey.shade200),
            ),
            enabledBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(16),
              borderSide: BorderSide(
                color: controller.text.length == 16 ? Colors.green : Colors.grey.shade200,
                width: controller.text.length == 16 ? 2 : 1,
              ),
            ),
            focusedBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(16),
              borderSide: BorderSide(color: color, width: 2),
            ),
            filled: true,
            fillColor: Colors.white,
            contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
          ),
        ),
        if (_nikErrorMessage.isNotEmpty)
          Padding(
            padding: const EdgeInsets.only(top: 6, left: 16),
            child: Row(
              children: [
                const Icon(Icons.info_outline, size: 16, color: Colors.red),
                const SizedBox(width: 8),
                Text(_nikErrorMessage, style: const TextStyle(color: Colors.red, fontSize: 12)),
              ],
            ),
          ),
      ],
    );
  }

  Widget _buildPriceRow(String label, String value, {bool isBold = false, Color color = Colors.black, double fontSize = 14}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label, style: TextStyle(color: Colors.grey.shade700, fontSize: fontSize * 0.85)),
          Text(
            value,
            style: TextStyle(fontWeight: isBold ? FontWeight.bold : FontWeight.w600, fontSize: fontSize, color: color),
          ),
        ],
      ),
    );
  }
}