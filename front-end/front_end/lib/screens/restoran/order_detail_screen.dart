import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../services/api_services.dart';
import '../event/event_header.dart';

class OrderDetailScreen extends StatefulWidget {
  final Map<String, dynamic> order;

  const OrderDetailScreen({super.key, required this.order});

  @override
  State<OrderDetailScreen> createState() => _OrderDetailScreenState();
}

class _OrderDetailScreenState extends State<OrderDetailScreen> {
  late Map<String, dynamic> _currentOrder;

  @override
  void initState() {
    super.initState();
    _currentOrder = widget.order;
  }

  // --- 1. FUNGSI UNTUK REFRESH DATA ---
  void _triggerParentRefresh() {
    ApiServices.getRestaurantOrderHistory(_currentOrder['user_id'].toString())
        .then((value) {
      if (value['success'] == true && mounted) {
        List<dynamic> history = value['data'];
        setState(() {
          _currentOrder = history.firstWhere(
              (element) => element['id'] == _currentOrder['id'],
              orElse: () => _currentOrder);
        });
      }
    });
  }

  // --- 2. FUNGSI DIALOG ULASAN (SIMPAN/EDIT) ---
  void _showRestoReviewDialog(BuildContext context, int menuId, String menuName,
      {bool isEdit = false, int? reviewId, Map<String, dynamic>? existingData}) {
    final TextEditingController commentController = TextEditingController(
        text: isEdit ? (existingData?['komentar'] ?? "") : "");
    int selectedRating = isEdit ? (existingData?['rating'] ?? 5) : 5;
    bool isAnonymous = isEdit ? (existingData?['is_anonymous'] ?? false) : false;
    bool isSending = false;

    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => StatefulBuilder(
        builder: (context, setStateDialog) => AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
          title: Text(isEdit ? "Edit Ulasan $menuName" : "Ulas $menuName"),
          content: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                const Text("Bagaimana rasa makanan ini?"),
                const SizedBox(height: 10),
                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: List.generate(5, (index) {
                    return IconButton(
                      icon: Icon(
                        index < selectedRating ? Icons.star : Icons.star_border,
                        color: Colors.amber,
                      ),
                      onPressed: isSending ? null : () => setStateDialog(() => selectedRating = index + 1),
                    );
                  }),
                ),
                TextField(
                  controller: commentController,
                  maxLines: 3,
                  enabled: !isSending,
                  decoration: const InputDecoration(
                      hintText: "Tulis ulasan...", border: OutlineInputBorder()),
                ),
                SwitchListTile(
                  title: const Text("Ulas sebagai Anonim", style: TextStyle(fontSize: 12)),
                  value: isAnonymous,
                  activeColor: Theme.of(context).primaryColor,
                  onChanged: isSending ? null : (val) => setStateDialog(() => isAnonymous = val),
                ),
              ],
            ),
          ),
          actions: [
            TextButton(onPressed: () => Navigator.pop(context), child: const Text("Batal")),
            ElevatedButton(
              onPressed: isSending ? null : () async {
                setStateDialog(() => isSending = true);
                final SharedPreferences prefs = await SharedPreferences.getInstance();
                int userId = prefs.getInt('user_id') ?? 0;

                // ============================================================
                // --- PERBAIKAN: SERTAKAN ID TRANSAKSI (SINKRON DENGAN DB) ---
                // ============================================================
                Map<String, dynamic> data = {
                  "user_id": userId,
                  "menu_id": menuId,
                  "pesanan_menu_id": _currentOrder['id'], // <--- KUNCI: Harus kirim ID Nota
                  "rating": selectedRating,
                  "komentar": commentController.text,
                  "is_anonymous": isAnonymous,
                };
                // ============================================================

                Map<String, dynamic> result = isEdit && reviewId != null
                    ? await ApiServices.updateRestoReview(reviewId, data)
                    : await ApiServices.storeRestoReview(data);

                if (mounted) {
                  Navigator.pop(context);
                  _triggerParentRefresh();
                  ScaffoldMessenger.of(context).showSnackBar(SnackBar(
                      content: Text(result['message'] ?? "Selesai"),
                      backgroundColor: result['success'] == true ? Colors.green : Colors.red));
                }
              },
              child: isSending 
                ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                : Text(isEdit ? "Simpan" : "Kirim"),
            ),
          ],
        ),
      ),
    );
  }
  // --- 3. FUNGSI HAPUS ---
  void _confirmDeleteReview(int reviewId) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text("Hapus Ulasan?"),
        content: const Text("Tindakan ini tidak bisa dibatalkan."),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context), child: const Text("Batal")),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
            onPressed: () async {
              Navigator.pop(context);
              final SharedPreferences prefs = await SharedPreferences.getInstance();
              int userId = prefs.getInt('user_id') ?? 0;
              final result = await ApiServices.deleteRestoReview(reviewId, userId);
              if (mounted) {
                ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(result['message'] ?? "Dihapus")));
                _triggerParentRefresh();
              }
            },
            child: const Text("Hapus", style: TextStyle(color: Colors.white)),
          ),
        ],
      ),
    );
  }

  // --- 4. WIDGET TOMBOL DINAMIS (SOLUSI ERROR TERBESAR KAMU) ---
  Widget _buildTrailingWidget(Map<String, dynamic> item, bool isPaid, Color primaryColor) {
    bool isReviewed = (item['is_reviewed'] as bool?) ?? false;

    // 1. Jika Belum Lunas (Masih Pending)
    if (!isPaid) {
      double subPrice = double.tryParse(item['harga_at_porsi'].toString()) ?? 0;
      int qty = int.tryParse(item['jumlah'].toString()) ?? 0;
      return Text("Rp ${(subPrice * qty).toStringAsFixed(0)}",
          style: TextStyle(fontWeight: FontWeight.bold, color: primaryColor));
    }

    // 2. Jika SUDAH Lunas TAPI SUDAH Diulas (Tombol Mati/Abu-abu)
    if (isReviewed) {
      return Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          const Text("SUDAH DIULAS ", style: TextStyle(color: Colors.grey, fontSize: 10, fontWeight: FontWeight.bold)),
          IconButton(
            icon: const Icon(Icons.edit_note, color: Colors.blue), // Edit tetap boleh
            onPressed: () => _showRestoReviewDialog(
              context, item['menu_id'], item['menu']['nama_menu'],
              isEdit: true,
              reviewId: item['review_id'],
              existingData: item['existing_review'],
            ),
          ),
          IconButton(
            icon: const Icon(Icons.delete_outline, color: Colors.red), // Hapus tetap boleh
            onPressed: () => _confirmDeleteReview(item['review_id']),
          ),
        ],
      );
    } 
    
    // 3. Jika SUDAH Lunas DAN BELUM Diulas (Tombol Hijau Aktif)
    else {
      return ElevatedButton(
        style: ElevatedButton.styleFrom(
          backgroundColor: primaryColor,
          minimumSize: const Size(60, 30),
        ),
        onPressed: () => _showRestoReviewDialog(context, item['menu_id'], item['menu']['nama_menu']),
        child: const Text("ULAS", style: TextStyle(fontSize: 10, color: Colors.white)),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final primaryColor = Theme.of(context).primaryColor;
    final List<dynamic> details = _currentOrder['details'] ?? [];

    bool isPaid = _currentOrder['status_pembayaran_id'].toString() == '2';
    Color statusColor = isPaid ? Colors.green : Colors.orange;
    String statusText = isPaid ? "SUDAH DIBAYAR" : "MENUNGGU PEMBAYARAN";

    String deliveryType = _currentOrder['tipe_pengantaran'] ?? "Meja";
    String locationNum = _currentOrder['nomor_lokasi'] ?? "-";
    IconData locationIcon = deliveryType == "Kamar" ? Icons.bed : Icons.table_restaurant;

    return Scaffold(
      appBar: AppBar(
        title: const Text("Detail Pesanan Resto"),
        backgroundColor: primaryColor,
        foregroundColor: Colors.white,
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
                        color: primaryColor.withOpacity(0.05),
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(color: primaryColor.withOpacity(0.3))),
                    child: Column(
                      children: [
                        Icon(Icons.restaurant, size: 40, color: primaryColor),
                        const SizedBox(height: 10),
                        Text("Nota #RS-${_currentOrder['id']}", style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                        Text(statusText, style: TextStyle(color: statusColor, fontWeight: FontWeight.bold)),
                        const Divider(height: 30),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Icon(locationIcon, size: 20, color: primaryColor),
                            const SizedBox(width: 8),
                            Text("Lokasi Antar: $deliveryType $locationNum", style: const TextStyle(fontWeight: FontWeight.w600)),
                          ],
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 25),
                  const Text("Pesanan Anda:", style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                  const SizedBox(height: 10),
                  Card(
                    elevation: 2,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                    child: ListView.separated(
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
                      itemCount: details.length,
                      separatorBuilder: (context, index) => const Divider(indent: 15, endIndent: 15),
                      itemBuilder: (context, index) {
                        final item = details[index];
                        return ListTile(
                          title: Text(item['menu']['nama_menu'] ?? "Menu", style: const TextStyle(fontWeight: FontWeight.w600)),
                          subtitle: Text("${item['jumlah']} porsi x Rp ${double.parse(item['harga_at_porsi'].toString()).toStringAsFixed(0)}"),
                          // --- MENGGUNAKAN WIDGET HELPER ---
                          trailing: _buildTrailingWidget(item, isPaid, primaryColor),
                        );
                      },
                    ),
                  ),
                  const SizedBox(height: 25),
                  Container(
                    padding: const EdgeInsets.all(15),
                    decoration: BoxDecoration(color: Colors.grey[50], borderRadius: BorderRadius.circular(10), border: Border.all(color: Colors.grey[200]!)),
                    child: Column(
                      children: [
                        _buildInfoRow("Metode Pembayaran", _currentOrder['metode_pembayaran'] ?? "-"),
                        _buildInfoRow("Waktu Pesan", _currentOrder['created_at'].toString().substring(0, 16).replaceAll('T', ' ')),
                        const Divider(),
                        _buildInfoRow("Total Bayar", "Rp ${double.parse(_currentOrder['total_harga'].toString()).toStringAsFixed(0)}", isBold: true, color: primaryColor),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildInfoRow(String label, String value, {bool isBold = false, Color color = Colors.black}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 5),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label, style: const TextStyle(color: Colors.grey, fontSize: 13)),
          Text(value, style: TextStyle(fontWeight: isBold ? FontWeight.bold : FontWeight.w600, fontSize: isBold ? 18 : 13, color: color)),
        ],
      ),
    );
  }
}