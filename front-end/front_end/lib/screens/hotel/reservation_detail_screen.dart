import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../services/api_services.dart';
import '../../providers/event_provider.dart';
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

  void _showReviewDialog(BuildContext context, {bool isEdit = false, int? reviewId, Map<String, dynamic>? existingData}) {
    final TextEditingController commentController = TextEditingController();
    if (isEdit && existingData != null) {
      commentController.text = existingData['komentar'] ?? "";
    }

    int selectedRating = isEdit ? (existingData?['rating'] ?? 5) : 5;
    bool isAnonymous = isEdit ? (existingData?['is_anonymous'] ?? false) : false;
    bool isSending = false;
    final eventProvider = Provider.of<EventProvider>(context, listen: false);
    final primaryColor = eventProvider.primaryColor;

    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => StatefulBuilder(
        builder: (context, setStateDialog) => AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
          title: Text(isEdit ? "Edit Ulasan Kamar" : "Beri Ulasan Kamar"),
          content: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                const Text("Bagaimana pengalaman menginap Anda?", textAlign: TextAlign.center),
                const SizedBox(height: 15),
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
                TextField(
                  controller: commentController,
                  maxLines: 3,
                  enabled: !isSending,
                  decoration: InputDecoration(
                    hintText: "Tulis komentar...", 
                    border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
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
              style: ElevatedButton.styleFrom(backgroundColor: primaryColor),
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

  void _confirmDeleteReview(int? reviewId) {
    if (reviewId == null) return;
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: const Text("Hapus Ulasan?"),
        content: const Text("Ulasan Anda akan dihapus secara permanen."),
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

  Widget _buildReviewButton(int statusId, bool isReviewed, Color primaryColor) {
    if (isReviewed) {
      return Row(
        children: [
          Expanded(
            child: OutlinedButton.icon(
              onPressed: () => _showReviewDialog(context, isEdit: true, reviewId: _currentRes['review_id'], existingData: _currentRes['existing_review']),
              icon: const Icon(Icons.edit_note),
              label: const Text("EDIT ULASAN"),
              style: OutlinedButton.styleFrom(
                foregroundColor: Colors.blue,
                side: const BorderSide(color: Colors.blue),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
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
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final eventProvider = context.watch<EventProvider>();
    final primaryColor = eventProvider.primaryColor;
    final statusId = int.tryParse(_currentRes['status_reservasi_id'].toString()) ?? 1;
    final isReviewed = _currentRes['is_reviewed'] == true;

    String statusText;
    Color statusColor;
    switch (statusId) {
      case 1: statusText = "PENDING"; statusColor = Colors.orange; break;
      case 2: statusText = "TERBAYAR"; statusColor = Colors.blue; break;
      case 3: statusText = "SUDAH CHECK-IN"; statusColor = Colors.green; break;
      case 4: statusText = "SELESAI"; statusColor = Colors.grey; break;
      case 5: statusText = "DIBATALKAN"; statusColor = Colors.red; break;
      default: statusText = "UNKNOWN"; statusColor = Colors.black;
    }

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text("Detail Reservasi"),
        backgroundColor: primaryColor,
        foregroundColor: Colors.white,
        elevation: 0,
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
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(
                      color: Colors.grey.shade50,
                      borderRadius: BorderRadius.circular(20),
                      border: Border.all(color: Colors.grey.shade200),
                    ),
                    child: Column(
                      children: [
                        Text(
                          _currentRes['nama_tipe'] ?? "Kamar",
                          style: const TextStyle(fontSize: 22, fontWeight: FontWeight.bold),
                        ),
                        const SizedBox(height: 8),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                          decoration: BoxDecoration(
                            color: statusColor.withOpacity(0.1),
                            borderRadius: BorderRadius.circular(20),
                            border: Border.all(color: statusColor),
                          ),
                          child: Text(
                            statusText,
                            style: TextStyle(color: statusColor, fontWeight: FontWeight.bold, fontSize: 12),
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 30),
                  _infoRow("Check-in", _currentRes['tgl_checkin']),
                  _infoRow("Total Bayar", "Rp ${double.parse(_currentRes['total_harga'].toString()).toStringAsFixed(0)}", isBold: true, textColor: primaryColor),
                  const SizedBox(height: 30),
                  _buildReviewButton(statusId, isReviewed, primaryColor),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _infoRow(String label, String value, {bool isBold = false, Color? textColor}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label, style: const TextStyle(color: Colors.grey, fontSize: 14)),
          Text(value, style: TextStyle(fontWeight: isBold ? FontWeight.bold : FontWeight.normal, color: textColor, fontSize: 14)),
        ],
      ),
    );
  }
}