// screens/hotel/reservation_detail_screen.dart

import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../services/api_services.dart';
import '../../providers/event_provider.dart';
import '../event/event_header.dart';
import '../notification/notification_screen.dart';

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
    ApiServices.getReservationHistory().then((value) {
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
        content: const Text("Anda yakin ingin menghapus ulasan ini?"),
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
              icon: const Icon(Icons.edit_note, size: 16),
              label: const Text("EDIT ULASAN"),
              style: OutlinedButton.styleFrom(
                foregroundColor: Colors.blue,
                side: const BorderSide(color: Colors.blue),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                padding: const EdgeInsets.symmetric(vertical: 12),
              ),
            ),
          ),
          const SizedBox(width: 10),
          IconButton(
            onPressed: () => _confirmDeleteReview(_currentRes['review_id']),
            icon: const Icon(Icons.delete_outline, color: Colors.red),
            style: IconButton.styleFrom(
              backgroundColor: Colors.red.shade50,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
            ),
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
          padding: const EdgeInsets.symmetric(vertical: 14),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
        ),
      ),
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
    final eventProvider = context.watch<EventProvider>();
    final primaryColor = eventProvider.primaryColor;
    final secondaryColor = eventProvider.secondaryColor;
    final topPadding = MediaQuery.of(context).padding.top;

    final statusId = int.tryParse(_currentRes['status_reservasi_id'].toString()) ?? 1;
    final isReviewed = _currentRes['is_reviewed'] == true;

    String statusText;
    Color statusColor;
    IconData statusIcon;
    switch (statusId) {
      case 1: 
        statusText = "PENDING"; 
        statusColor = Colors.orange; 
        statusIcon = Icons.hourglass_top_rounded;
        break;
      case 2: 
        statusText = "TERBAYAR"; 
        statusColor = Colors.blue; 
        statusIcon = Icons.payment_rounded;
        break;
      case 3: 
        statusText = "SUDAH CHECK-IN"; 
        statusColor = Colors.green; 
        statusIcon = Icons.login_rounded; // <-- PERBAIKAN: login_rounded
        break;
      case 4: 
        statusText = "SELESAI"; 
        statusColor = Colors.grey; 
        statusIcon = Icons.check_circle_rounded;
        break;
      case 5: 
        statusText = "DIBATALKAN"; 
        statusColor = Colors.red; 
        statusIcon = Icons.cancel_rounded;
        break;
      default: 
        statusText = "UNKNOWN"; 
        statusColor = Colors.black; 
        statusIcon = Icons.help_rounded;
    }

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
                    Icon(Icons.receipt_long_rounded, color: secondaryColor, size: 20),
                    const SizedBox(width: 8),
                    const Text(
                      "Detail Reservasi",
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
                  // ── CARD RESERVASI ──
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(20),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withOpacity(0.05),
                          blurRadius: 12,
                          offset: const Offset(0, 4),
                        ),
                      ],
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            Expanded(
                              child: Text(
                                _currentRes['nama_tipe'] ?? "Kamar",
                                style: const TextStyle(
                                  fontSize: 20,
                                  fontWeight: FontWeight.bold,
                                  color: Color(0xFF1F2937),
                                ),
                              ),
                            ),
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                              decoration: BoxDecoration(
                                color: statusColor.withOpacity(0.1),
                                borderRadius: BorderRadius.circular(20),
                                border: Border.all(color: statusColor.withOpacity(0.3)),
                              ),
                              child: Row(
                                mainAxisSize: MainAxisSize.min,
                                children: [
                                  Icon(statusIcon, color: statusColor, size: 14),
                                  const SizedBox(width: 4),
                                  Text(
                                    statusText,
                                    style: TextStyle(
                                      color: statusColor,
                                      fontWeight: FontWeight.bold,
                                      fontSize: 11,
                                      letterSpacing: 0.5,
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ],
                        ),
                        const Divider(height: 24),
                        _infoRow(
                          Icons.calendar_today_rounded,
                          "Check-in",
                          _currentRes['tgl_checkin'] ?? "-",
                        ),
                        _infoRow(
                          Icons.calendar_today_rounded,
                          "Check-out",
                          _currentRes['tgl_checkout'] ?? "-",
                        ),
                        _infoRow(
                          Icons.nights_stay_rounded,
                          "Total Malam",
                          "${_currentRes['total_malam'] ?? 0} malam",
                        ),
                        _infoRow(
                          Icons.people_rounded,
                          "Jumlah Tamu",
                          "${_currentRes['jumlah_tamu'] ?? 1} orang",
                        ),
                        const Divider(height: 24),
                        _infoRow(
                          Icons.payment_rounded,
                          "Metode Bayar",
                          _currentRes['metode_pembayaran'] ?? "-",
                          valueColor: Colors.grey[700],
                        ),
                        _infoRow(
                          Icons.attach_money_rounded,
                          "Total Bayar",
                          "Rp ${double.parse(_currentRes['total_harga'].toString()).toStringAsFixed(0)}",
                          isBold: true,
                          valueColor: primaryColor,
                          fontSize: 18,
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 24),
                  // ── TOMBOL ULASAN ──
                  _buildReviewButton(statusId, isReviewed, primaryColor),
                  const SizedBox(height: 20),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _infoRow(IconData icon, String label, String value, {
    bool isBold = false,
    Color? valueColor,
    double fontSize = 14,
  }) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 6),
      child: Row(
        children: [
          Icon(icon, size: 16, color: Colors.grey[500]),
          const SizedBox(width: 10),
          Text(
            label,
            style: TextStyle(
              color: Colors.grey[600],
              fontSize: 13,
              fontWeight: FontWeight.w500,
            ),
          ),
          const Spacer(),
          Text(
            value,
            style: TextStyle(
              fontWeight: isBold ? FontWeight.bold : FontWeight.w600,
              fontSize: fontSize,
              color: valueColor ?? Colors.black87,
            ),
          ),
        ],
      ),
    );
  }
}