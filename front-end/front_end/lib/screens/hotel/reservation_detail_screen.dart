import 'package:flutter/material.dart';

class ReservationDetailScreen extends StatelessWidget {
  final Map<String, dynamic> reservation;

  const ReservationDetailScreen({super.key, required this.reservation});

  @override
  Widget build(BuildContext context) {
    // --- PELINDUNG 1: Pastikan 'details' tidak Null ---
    // Jika null, kita anggap List kosong. Jika bukan List, kita jadikan List kosong.
    final List<dynamic> details = (reservation['details'] != null && reservation['details'] is List) 
        ? reservation['details'] 
        : [];

    // --- PELINDUNG 2: Ambil data tamu pertama dengan aman ---
    // Jika list kosong, kita kasih data default agar tidak error merah
    final Map<String, dynamic> detailTamu = details.isNotEmpty 
        ? details[0] as Map<String, dynamic> 
        : {
            "nama_tamu": "Data tidak tersedia",
            "nik_identitas": "-",
            "jumlah_tamu": 0
          };

    // Logika Warna Status
    Color statusColor;
    String statusText;
    switch (reservation['status_reservasi_id'].toString()) { // Pakai toString() agar aman jika tipenya Int/String
      case '2':
        statusColor = Colors.green;
        statusText = "SUDAH DIBAYAR";
        break;
      case '1':
        statusColor = Colors.orange;
        statusText = "MENUNGGU PEMBAYARAN";
        break;
      default:
        statusColor = Colors.red;
        statusText = "BATAL / GAGAL";
    }

    return Scaffold(
      appBar: AppBar(
        title: const Text("Detail Pemesanan"),
        backgroundColor: Colors.blueAccent,
        foregroundColor: Colors.white,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Header Info Kamar
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: statusColor.withOpacity(0.1),
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: statusColor),
              ),
              child: Column(
                children: [
                  Text(
                    reservation['nama_tipe']?.toString() ?? "Kamar",
                    style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 10),
                  Text(
                    statusText,
                    style: TextStyle(color: statusColor, fontWeight: FontWeight.bold),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 25),

            _buildSectionTitle("Informasi Menginap"),
            _buildInfoCard([
              _buildRow("Check-in", reservation['tgl_checkin']?.toString() ?? "-"),
              _buildRow("Check-out", reservation['tgl_checkout']?.toString() ?? "-"),
              _buildRow("Durasi", "${reservation['total_malam'] ?? 0} Malam"),
            ]),

            const SizedBox(height: 20),

            _buildSectionTitle("Detail Tamu"),
            _buildInfoCard([
              _buildRow("Nama Tamu", detailTamu['nama_tamu']?.toString() ?? "-"),
              _buildRow("Nomor NIK", detailTamu['nik_identitas']?.toString() ?? "-"),
              _buildRow("Jumlah Tamu", "${detailTamu['jumlah_tamu'] ?? 0} Orang"),
            ]),

            const SizedBox(height: 20),

            _buildSectionTitle("Rincian Pembayaran"),
            _buildInfoCard([
              _buildRow("Metode", reservation['metode_pembayaran']?.toString() ?? "-"),
              _buildRow(
                "Total Harga", 
                "Rp ${double.parse(reservation['total_harga']?.toString() ?? '0').toStringAsFixed(0)}"
              ),
            ]),
          ],
        ),
      ),
    );
  }

  Widget _buildSectionTitle(String title) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 10, left: 5),
      child: Text(title, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Colors.blueAccent)),
    );
  }

  Widget _buildInfoCard(List<Widget> children) {
    return Card(
      elevation: 2,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
      child: Padding(
        padding: const EdgeInsets.all(15),
        child: Column(children: children),
      ),
    );
  }

  Widget _buildRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label, style: const TextStyle(color: Colors.grey)),
          const SizedBox(width: 10),
          Flexible(
            child: Text(
              value, 
              style: const TextStyle(fontWeight: FontWeight.w600),
              textAlign: TextAlign.right,
            ),
          ),
        ],
      ),
    );
  }
}