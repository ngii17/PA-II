import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'room_type_screen.dart'; 
import '../../services/api_services.dart';
import 'package:intl/intl.dart';
import 'package:url_launcher/url_launcher.dart'; 
import 'waiting_payment_screen.dart'; 
import '../event/event_header.dart';

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
  
  // --- BARU: CONTROLLER PROMO ---
  final TextEditingController _promoController = TextEditingController();
  double _discountAmount = 0; // Potongan dalam Rupiah
  String _appliedPromoName = ""; 

  String _selectedPayment = "Transfer Bank"; 
  bool _isLoading = false;

  int _calculateNights() {
    if (_checkInDate == null || _checkOutDate == null) return 0;
    int days = _checkOutDate!.difference(_checkInDate!).inDays;
    return days < 1 ? 1 : days; 
  }

  // --- BARU: FUNGSI CEK PROMO ---
  void _handleCheckPromo() async {
    if (_promoController.text.isEmpty) return;

    final result = await ApiServices.checkPromoCode(_promoController.text, 'hotel');

    if (result['success'] == true) {
      setState(() {
        _appliedPromoName = result['data']['nama_promo'];
        double potongan = double.parse(result['data']['nominal_potongan'].toString());
        
        // Jika tipenya persen, kita hitung dari harga total
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
    final SharedPreferences prefs = await SharedPreferences.getInstance();
    int? userId = prefs.getInt('user_id');

    double finalTotal = (_calculateNights() * widget.room.hargaAkhir) - _discountAmount;

    Map<String, dynamic> data = {
      "user_id": userId ?? 0,
      "tipe_kamar_id": widget.room.id,
      "tgl_checkin": DateFormat('yyyy-MM-dd').format(_checkInDate!),
      "tgl_checkout": DateFormat('yyyy-MM-dd').format(_checkOutDate!),
      "total_malam": _calculateNights(),
      "total_harga": finalTotal, // Kirim harga yang sudah dipotong diskon voucher
      "metode_pembayaran": _selectedPayment,
      "nama_tamu": _guestNameController.text,
      "nik_identitas": _guestNikController.text,
      "jumlah_tamu": int.parse(_guestCountController.text),
    };

    final result = await ApiServices.storeReservation(data);
    setState(() => _isLoading = false);

    if (result['success'] == true) {
      final Uri url = Uri.parse(result['redirect_url']);
      await launchUrl(url, mode: LaunchMode.externalApplication);
      if (!mounted) return;
      Navigator.push(context, MaterialPageRoute(builder: (context) => WaitingPaymentScreen(reservasiId: result['reservasi_id'])));
    }
  }

  void _showSnackBar(String msg, Color color) {
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(msg), backgroundColor: color));
  }

  @override
  Widget build(BuildContext context) {
    final primaryColor = Theme.of(context).primaryColor;
    int nights = _calculateNights();
    double subTotal = nights * widget.room.hargaAkhir;
    double totalBayar = subTotal - _discountAmount;

    return Scaffold(
      appBar: AppBar(title: const Text("Konfirmasi Pesanan"), backgroundColor: primaryColor, foregroundColor: Colors.white),
      body: SingleChildScrollView(
        child: Column(
          children: [
            const EventHeader(),
            Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _buildSectionTitle("Data Tamu"),
                  const SizedBox(height: 10),
                  _buildTextField(_guestNameController, "Nama Sesuai KTP", Icons.person, primaryColor),
                  const SizedBox(height: 10),
                  _buildTextField(_guestNikController, "NIK / KTP", Icons.badge, primaryColor),
                  const SizedBox(height: 20),

                  _buildSectionTitle("Jadwal"),
                  ListTile(
                    tileColor: Colors.grey[100],
                    leading: Icon(Icons.date_range, color: primaryColor),
                    title: Text(_checkInDate == null ? "Pilih Tanggal" : "${DateFormat('dd MMM').format(_checkInDate!)} - ${DateFormat('dd MMM yyyy').format(_checkOutDate!)}"),
                    onTap: () async {
                      DateTimeRange? picked = await showDateRangePicker(context: context, firstDate: DateTime.now(), lastDate: DateTime.now().add(const Duration(days: 365)));
                      if (picked != null) setState(() { _checkInDate = picked.start; _checkOutDate = picked.end; });
                    },
                  ),
                  const SizedBox(height: 25),

                  // --- BOX INPUT PROMO (BARU) ---
                  _buildSectionTitle("Punya Kode Voucher?"),
                  const SizedBox(height: 10),
                  Row(
                    children: [
                      Expanded(
                        child: TextField(
                          controller: _promoController,
                          decoration: InputDecoration(
                            hintText: "Masukkan kode (Contoh: HOTELMURAH)",
                            border: OutlineInputBorder(borderRadius: BorderRadius.circular(10)),
                          ),
                        ),
                      ),
                      const SizedBox(width: 10),
                      ElevatedButton(
                        onPressed: _handleCheckPromo,
                        style: ElevatedButton.styleFrom(backgroundColor: primaryColor, padding: const EdgeInsets.all(15)),
                        child: const Text("CEK"),
                      ),
                    ],
                  ),
                  if (_appliedPromoName.isNotEmpty)
                    Padding(
                      padding: const EdgeInsets.only(top: 8, left: 5),
                      child: Text("✓ Promo '$_appliedPromoName' digunakan", style: const TextStyle(color: Colors.green, fontSize: 12, fontWeight: FontWeight.bold)),
                    ),

                  const SizedBox(height: 30),
                  _buildSectionTitle("Rincian Biaya"),
                  const SizedBox(height: 10),
                  Container(
                    padding: const EdgeInsets.all(15),
                    decoration: BoxDecoration(color: primaryColor.withOpacity(0.05), borderRadius: BorderRadius.circular(10), border: Border.all(color: primaryColor.withOpacity(0.2))),
                    child: Column(
                      children: [
                        _buildPriceRow("Harga ($nights Malam)", "Rp ${subTotal.toStringAsFixed(0)}"),
                        if (_discountAmount > 0)
                          _buildPriceRow("Voucher Diskon", "- Rp ${_discountAmount.toStringAsFixed(0)}", color: Colors.red),
                        const Divider(),
                        _buildPriceRow("Total Bayar", "Rp ${totalBayar.toStringAsFixed(0)}", isBold: true, color: primaryColor),
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
        child: _isLoading ? const Center(child: CircularProgressIndicator()) : ElevatedButton(
          onPressed: _handleBooking,
          style: ElevatedButton.styleFrom(backgroundColor: primaryColor, padding: const EdgeInsets.all(18)),
          child: const Text("KONFIRMASI & BAYAR", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
        ),
      ),
    );
  }

  Widget _buildSectionTitle(String title) => Text(title, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold));
  
  Widget _buildTextField(TextEditingController controller, String label, IconData icon, Color color, {bool isNumber = false}) {
    return TextField(
      controller: controller,
      keyboardType: isNumber ? TextInputType.number : TextInputType.text,
      decoration: InputDecoration(
        labelText: label,
        prefixIcon: Icon(icon, color: color),
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(10)),
      ),
    );
  }

  Widget _buildPriceRow(String label, String value, {bool isBold = false, Color color = Colors.black}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label, style: TextStyle(color: isBold ? Colors.black : Colors.grey)),
          Text(value, style: TextStyle(fontWeight: isBold ? FontWeight.bold : FontWeight.normal, fontSize: isBold ? 18 : 14, color: color)),
        ],
      ),
    );
  }
}