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
  String _selectedPayment = "Transfer Bank";
  bool _isLoading = false;

  int _calculateNights() {
    if (_checkInDate == null || _checkOutDate == null) return 0;
    int days = _checkOutDate!.difference(_checkInDate!).inDays;
    return days < 1 ? 1 : days;
  }

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
      _showSnackBar("Promo Berhasil Dipasang!", Colors.green);
    } else {
      setState(() {
        _discountAmount = 0;
        _appliedPromoName = "";
      });
      _showSnackBar(result['message'] ?? "Promo tidak valid", Colors.red);
    }
  }

  void _handleBooking() async {
    if (_checkInDate == null || _guestNameController.text.isEmpty || _guestNikController.text.isEmpty) {
      _showSnackBar("Harap isi semua data!", Colors.red);
      return;
    }
    setState(() => _isLoading = true);
    final prefs = await SharedPreferences.getInstance();
    int? userId = prefs.getInt('user_id');
    String? realFcmToken = await PushNotificationService.getDeviceToken();
    double finalTotal = (_calculateNights() * widget.room.hargaAkhir) - _discountAmount;
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
      if (_selectedPayment != "Bayar di Kasir") {
        String? redirectUrl = result['redirect_url'];
        if (redirectUrl != null) {
          await launchUrl(Uri.parse(redirectUrl), mode: LaunchMode.externalApplication);
          if (!mounted) return;
          Navigator.push(context, MaterialPageRoute(builder: (context) => WaitingPaymentScreen(reservasiId: result['reservasi_id'])));
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
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(msg), backgroundColor: color, behavior: SnackBarBehavior.floating));
  }

  @override
  Widget build(BuildContext context) {
    final eventProvider = context.watch<EventProvider>();
    final primaryColor = eventProvider.primaryColor;
    final secondaryColor = eventProvider.secondaryColor;

    int nights = _calculateNights();
    double subTotal = nights * widget.room.hargaAkhir;
    double totalBayar = subTotal - _discountAmount;

    return Scaffold(
      backgroundColor: Colors.grey.shade50,
      appBar: AppBar(
        title: const Text("Konfirmasi Pesanan", style: TextStyle(fontWeight: FontWeight.bold)),
        backgroundColor: Colors.transparent,
        foregroundColor: Colors.white,
        elevation: 0,
        flexibleSpace: Container(
          decoration: BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
              colors: [primaryColor, secondaryColor.withOpacity(0.85)],
            ),
          ),
        ),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios, size: 20),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: SingleChildScrollView(
        child: Column(
          children: [
            const EventHeader(),
            Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _buildSectionTitle("Data Tamu", primaryColor),
                  const SizedBox(height: 10),
                  _buildTextField(_guestNameController, "Nama Sesuai KTP", Icons.person, primaryColor),
                  const SizedBox(height: 10),
                  _buildTextField(_guestNikController, "NIK / KTP", Icons.badge, primaryColor),
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
                        style: TextStyle(fontWeight: FontWeight.w500),
                      ),
                      trailing: const Icon(Icons.arrow_forward_ios, size: 16, color: Colors.grey),
                      onTap: () async {
                        DateTimeRange? picked = await showDateRangePicker(
                          context: context,
                          firstDate: DateTime.now(),
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
                          setState(() {
                            _checkInDate = picked.start;
                            _checkOutDate = picked.end;
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

                  _buildSectionTitle("Punya Kode Voucher?", primaryColor),
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
                ],
              ),
            ),
          ],
        ),
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

  Widget _buildPriceRow(String label, String value, {bool isBold = false, Color color = Colors.black, double fontSize = 14}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label, style: TextStyle(color: Colors.grey.shade700, fontSize: fontSize * 0.85)),
          Text(
            value,
            style: TextStyle(
              fontWeight: isBold ? FontWeight.bold : FontWeight.w600,
              fontSize: fontSize,
              color: color,
            ),
          ),
        ],
      ),
    );
  }
}