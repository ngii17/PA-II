import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'room_type_screen.dart'; 
import '../../services/api_services.dart';
import 'package:intl/intl.dart';
import 'package:url_launcher/url_launcher.dart'; // IMPORT BARU
import 'waiting_payment_screen.dart'; // IMPORT HALAMAN POLLING

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
  String _selectedPayment = "Transfer Bank"; 
  bool _isLoading = false;

  int _calculateNights() {
    if (_checkInDate == null || _checkOutDate == null) return 0;
    int days = _checkOutDate!.difference(_checkInDate!).inDays;
    return days < 1 ? 1 : days; 
  }

  void _handleBooking() async {
    // Validasi input
    if (_checkInDate == null || _guestNameController.text.isEmpty || _guestNikController.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text("Harap isi semua data!")));
      return;
    }

    setState(() => _isLoading = true);
    final SharedPreferences prefs = await SharedPreferences.getInstance();
    int? userId = prefs.getInt('user_id');

    // Data dikirim ke Laravel
    Map<String, dynamic> data = {
      "user_id": userId ?? 0,
      "tipe_kamar_id": widget.room.id,
      "tgl_checkin": DateFormat('yyyy-MM-dd').format(_checkInDate!),
      "tgl_checkout": DateFormat('yyyy-MM-dd').format(_checkOutDate!),
      "total_malam": _calculateNights(),
      "total_harga": _calculateNights() * widget.room.hargaAkhir,
      "metode_pembayaran": _selectedPayment,
      "nama_tamu": _guestNameController.text,
      "nik_identitas": _guestNikController.text,
      "jumlah_tamu": int.parse(_guestCountController.text),
    };

    // Panggil API Laravel
    final result = await ApiServices.storeReservation(data);
    setState(() => _isLoading = false);

    if (result['success'] == true) {
      // AMBIL URL PEMBAYARAN DAN ID DARI LARAVEL
      String redirectUrl = result['redirect_url'];
      int reservasiId = result['reservasi_id'];

      // 1. BUKA BROWSER HP (CHROME/SAFARI) - TIDAK PAKAI WEBVIEW LAGI
      final Uri url = Uri.parse(redirectUrl);
      if (await canLaunchUrl(url)) {
        await launchUrl(url, mode: LaunchMode.externalApplication);
        
        if (!mounted) return;

        // 2. PINDAH KE HALAMAN MENUNGGU (POLLING)
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (context) => WaitingPaymentScreen(reservasiId: reservasiId),
          ),
        );
      } else {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text("Tidak bisa membuka browser")));
      }
    } else {
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(result['message'] ?? "Gagal")));
    }
  }

  @override
  Widget build(BuildContext context) {
    int nights = _calculateNights();
    double totalPrice = nights * widget.room.hargaAkhir;

    return Scaffold(
      appBar: AppBar(title: const Text("Form Reservasi"), backgroundColor: Colors.blueAccent),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          children: [
            const Card(
              color: Colors.orangeAccent, 
              child: Padding(
                padding: EdgeInsets.all(10), 
                child: Text("Check-in 14:00 | Check-out 12:00", style: TextStyle(color: Colors.white))
              )
            ),
            const SizedBox(height: 20),
            ListTile(
              tileColor: Colors.grey[100],
              leading: const Icon(Icons.date_range, color: Colors.blueAccent),
              title: Text(_checkInDate == null ? "Pilih Tanggal" : "${DateFormat('dd MMM').format(_checkInDate!)} - ${DateFormat('dd MMM yyyy').format(_checkOutDate!)}"),
              onTap: () async {
                DateTimeRange? picked = await showDateRangePicker(
                  context: context, 
                  firstDate: DateTime.now(), 
                  lastDate: DateTime.now().add(const Duration(days: 365))
                );
                if (picked != null) setState(() { _checkInDate = picked.start; _checkOutDate = picked.end; });
              },
            ),
            const SizedBox(height: 20),
            TextField(controller: _guestNameController, decoration: const InputDecoration(labelText: "Nama Lengkap")),
            TextField(controller: _guestNikController, decoration: const InputDecoration(labelText: "Nomor NIK")),
            TextField(controller: _guestCountController, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: "Jumlah Tamu")),
            const SizedBox(height: 20),
            DropdownButton<String>(
              isExpanded: true,
              value: _selectedPayment,
              items: ["Transfer Bank", "E-Wallet"].map((e) => DropdownMenuItem(value: e, child: Text(e))).toList(),
              onChanged: (v) => setState(() => _selectedPayment = v!),
            ),
            const SizedBox(height: 30),
            Text("Total Biaya: Rp ${totalPrice.toStringAsFixed(0)}", style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: Colors.blueAccent)),
          ],
        ),
      ),
      bottomNavigationBar: Padding(
        padding: const EdgeInsets.all(20),
        child: _isLoading 
          ? const Center(heightFactor: 1, child: CircularProgressIndicator())
          : ElevatedButton(
              onPressed: _handleBooking, 
              style: ElevatedButton.styleFrom(backgroundColor: Colors.blueAccent, padding: const EdgeInsets.all(15)),
              child: const Text("KONFIRMASI & BAYAR", style: TextStyle(color: Colors.white)),
            ),
      ),
    );
  }
}