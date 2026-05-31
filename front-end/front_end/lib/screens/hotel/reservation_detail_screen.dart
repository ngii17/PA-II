import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../services/api_services.dart';
import '../event/event_header.dart';

class ReservationDetailScreen extends StatefulWidget {
  final Map<String, dynamic> reservation;

  const ReservationDetailScreen({super.key, required this.reservation});

  @override
  State<ReservationDetailScreen> createState() => _ReservationDetailScreenState();
}

class _ReservationDetailScreenState extends State<ReservationDetailScreen> {
  late Map<String, dynamic> _currentRes;

  @override
  void initState() {
    super.initState();
    _currentRes = widget.reservation;
  }

  // --- 1. REFRESH DATA AGAR TOMBOL UPDATE ---
  void _triggerRefresh() {
    ApiServices.getReservationHistory(_currentRes['user_id'].toString()).then((value) {
      if (value['success'] == true && mounted) {
        List<dynamic> history = value['data'];
        setState(() {
          _currentRes = history.firstWhere(
            (element) => element['id'] == _currentRes['id'],
            orElse: () => _currentRes,
          );
        });
      }
    });
  }

  // --- 2. DIALOG ULASAN (PERSIS SEPERTI RESTORAN) ---
  void _showReviewDialog(BuildContext context, {bool isEdit = false, int? reviewId, Map<String, dynamic>? existingData}) {
    
    // DEBUG: Cek apakah data ulasan lama benar-benar sampai ke sini
    print("DEBUG_EXISTING_DATA: $existingData");

    // Gunakan pengecekan manual untuk mengisi text
    final TextEditingController commentController = TextEditingController();
    if (isEdit && existingData != null) {
      commentController.text = existingData['komentar'] ?? "";
    }

    int selectedRating = isEdit ? (existingData?['rating'] ?? 5) : 5;
    bool isAnonymous = isEdit ? (existingData?['is_anonymous'] ?? false) : false;
    bool isSending = false;
    final primaryColor = Theme.of(context).primaryColor;

    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => StatefulBuilder(
        builder: (context, setStateDialog) => AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
          title: Text(isEdit ? "Edit Ulasan Kamar" : "Beri Ulasan Kamar"),
          content: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                const Text("Bagaimana pengalaman menginap Anda?", textAlign: TextAlign.center),
                const SizedBox(height: 15),
                // Bintang Rating
                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: List.generate(5, (index) {
                    return IconButton(
                      icon: Icon(
                        index < selectedRating ? Icons.star : Icons.star_border,
                        color: Colors.amber, size: 30,
                      ),
                      onPressed: isSending ? null : () => setStateDialog(() => selectedRating = index + 1),
                    );
                  }),
                ),
                // Input Komentar
                TextField(
                  controller: commentController,
                  maxLines: 3,
                  enabled: !isSending,
                  decoration: const InputDecoration(
                    hintText: "Tulis komentar...", 
                    border: OutlineInputBorder()
                  ),
                ),
                const SizedBox(height: 10),
                SwitchListTile(
                  contentPadding: EdgeInsets.zero,
                  title: const Text("Ulasan Anonim", style: TextStyle(fontSize: 14)),
                  value: isAnonymous,
                  activeColor: primaryColor,
                  onChanged: isSending ? null : (val) => setStateDialog(() => isAnonymous = val),
                ),
              ],
            ),
          ),
          actions: [
            TextButton(onPressed: isSending ? null : () => Navigator.pop(context), child: const Text("Batal")),
            ElevatedButton(
              onPressed: isSending ? null : () async {
                if (commentController.text.trim().length < 5) {
                  ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text("Komentar minimal 5 huruf")));
                  return;
                }
                setStateDialog(() => isSending = true);
                final SharedPreferences prefs = await SharedPreferences.getInstance();
                int userId = prefs.getInt('user_id') ?? 0;

                Map<String, dynamic> data = {
                  "user_id": userId,
                  "tipe_kamar_id": _currentRes['tipe_kamar_id'], 
                  "reservasi_id": _currentRes['id'],
                  "rating": selectedRating,
                  "komentar": commentController.text,
                  "is_anonymous": isAnonymous,
                };

                Map<String, dynamic> result;
                if (isEdit && reviewId != null) {
                  result = await ApiServices.updateHotelReview(reviewId, data);
                } else {
                  result = await ApiServices.storeHotelReview(data);
                }

                if (mounted) {
                  Navigator.pop(context);
                  _triggerRefresh();
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(content: Text(result['message'] ?? "Selesai"), backgroundColor: result['success'] ? Colors.green : Colors.red),
                  );
                }
              },
              child: Text(isEdit ? "Simpan Perubahan" : "Kirim"),
            ),
          ],
        ),
      ),
    );
  }

  // --- 3. KONFIRMASI HAPUS ---
  void _confirmDeleteReview(int? reviewId) {
    if (reviewId == null) return;
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text("Hapus Ulasan?"),
        content: const Text("Ulasan Anda akan dihapus secara permanen dari riwayat hotel."),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context), child: const Text("Batal")),
          TextButton(
            onPressed: () async {
              Navigator.pop(context);
              final SharedPreferences prefs = await SharedPreferences.getInstance();
              int userId = prefs.getInt('user_id') ?? 0;
              final result = await ApiServices.deleteHotelReview(reviewId, userId);
              if (mounted) {
                ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(result['message'] ?? "Dihapus")));
                _triggerRefresh();
              }
            },
            child: const Text("Hapus", style: TextStyle(color: Colors.red)),
          ),
        ],
      ),
    );
  }

  // --- 4. WIDGET TOMBOL (SINKRON DENGAN LOGIKA RESTORAN) ---
  Widget _buildReviewButton(int statusId, bool isReviewed, Color primaryColor) {
    if (isReviewed) {
      return Row(
        children: [
          Expanded(
            child: OutlinedButton.icon(
              onPressed: () => _showReviewDialog(
                context, 
                isEdit: true, 
                reviewId: _currentRes['review_id'], 
                existingData: _currentRes['existing_review']
              ),
              icon: const Icon(Icons.edit_note),
              label: const Text("EDIT ULASAN"),
              style: OutlinedButton.styleFrom(
                foregroundColor: Colors.blue, 
                side: const BorderSide(color: Colors.blue)
              ),
            ),
          ),
          const SizedBox(width: 10),
          IconButton(
            onPressed: () => _confirmDeleteReview(_currentRes['review_id']),
            icon: const Icon(Icons.delete_outline, color: Colors.red),
          ),
        ],
      );
    }

    // Hanya aktif jika status sudah SELESAI (4)
    bool canClick = statusId == 4;
    return SizedBox(
      width: double.infinity,
      child: ElevatedButton.icon(
        onPressed: canClick ? () => _showReviewDialog(context) : null,
        icon: const Icon(Icons.rate_review, color: Colors.white),
        label: Text(canClick ? "BERI ULASAN PENGALAMAN" : "ULAS SETELAH CHECK-OUT"),
        style: ElevatedButton.styleFrom(
          backgroundColor: canClick ? primaryColor : Colors.grey[400],
          padding: const EdgeInsets.all(15),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final primaryColor = Theme.of(context).primaryColor;
    int statusId = int.parse(_currentRes['status_reservasi_id']?.toString() ?? "1");
    bool isReviewed = _currentRes['is_reviewed'] == true;

    // ... (Bagian build UI lainnya seperti Box Status dan Info Tamu tetap sama) ...
    return Scaffold(
      appBar: AppBar(title: const Text("Detail Reservasi"), backgroundColor: primaryColor, foregroundColor: Colors.white),
      body: SingleChildScrollView(
        child: Column(
          children: [
            const EventHeader(),
            Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Box Status (Gunakan _currentRes agar reaktif)
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(color: Colors.grey[100], borderRadius: BorderRadius.circular(12)),
                    child: Column(
                      children: [
                        Text(_currentRes['nama_tipe'] ?? "Kamar", style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
                        const SizedBox(height: 5),
                        Text(statusId == 4 ? "SELESAI" : "AKTIF", style: const TextStyle(color: Colors.grey, fontWeight: FontWeight.bold)),
                      ],
                    ),
                  ),
                  const SizedBox(height: 30),
                  _itemRow("Check-in", _currentRes['tgl_checkin']),
                  _itemRow("Total Bayar", "Rp ${double.parse(_currentRes['total_harga'].toString()).toStringAsFixed(0)}", isBold: true, textColor: primaryColor),
                  const Divider(height: 50),
                  
                  // PANGGIL TOMBOL DINAMIS
                  _buildReviewButton(statusId, isReviewed, primaryColor),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _itemRow(String label, String value, {bool isBold = false, Color? textColor}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label, style: const TextStyle(color: Colors.grey)),
          Text(value, style: TextStyle(fontWeight: isBold ? FontWeight.bold : FontWeight.normal, color: textColor)),
        ],
      ),
    );
  }
}