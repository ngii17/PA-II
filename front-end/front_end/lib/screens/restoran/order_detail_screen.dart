import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../services/api_services.dart';
import '../../providers/event_provider.dart';
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

  // ✅ TAMBAH: Method untuk processing URL gambar
  String _processImageUrl(String? imageUrl) {
    String finalImageUrl = imageUrl ?? "";
    
    if (finalImageUrl.contains(RegExp(r'\d+\.\d+\.\d+\.\d+'))) {
      finalImageUrl = finalImageUrl.replaceAll(RegExp(r'\d+\.\d+\.\d+\.\d+'), ApiServices.ipAddress);
    } else if (finalImageUrl.isNotEmpty && !finalImageUrl.startsWith('http')) {
      finalImageUrl = "http://${ApiServices.ipAddress}:8001/storage/$finalImageUrl";
    }
    
    return finalImageUrl;
  }

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
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
          title: Text(
            isEdit ? "Edit Ulasan" : "Ulas $menuName",
            style: const TextStyle(fontWeight: FontWeight.bold),
          ),
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
                const SizedBox(height: 8),
                TextField(
                  controller: commentController,
                  maxLines: 3,
                  enabled: !isSending,
                  decoration: InputDecoration(
                    hintText: "Tulis ulasan...",
                    border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                  ),
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
              style: ElevatedButton.styleFrom(
                backgroundColor: Theme.of(context).primaryColor,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
              ),
              onPressed: isSending ? null : () async {
                setStateDialog(() => isSending = true);
                final SharedPreferences prefs = await SharedPreferences.getInstance();
                int userId = prefs.getInt('user_id') ?? 0;

                Map<String, dynamic> data = {
                  "user_id": userId,
                  "menu_id": menuId,
                  "pesanan_menu_id": _currentOrder['id'],
                  "rating": selectedRating,
                  "komentar": commentController.text,
                  "is_anonymous": isAnonymous,
                };

                Map<String, dynamic> result = isEdit && reviewId != null
                    ? await ApiServices.updateRestoReview(reviewId, data)
                    : await ApiServices.storeRestoReview(data);

                if (mounted) {
                  Navigator.pop(context);
                  _triggerParentRefresh();
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(
                      content: Text(result['message'] ?? "Selesai"),
                      backgroundColor: result['success'] == true ? Colors.green : Colors.red,
                    ),
                  );
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

  void _confirmDeleteReview(int reviewId) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: const Text("Hapus Ulasan?"),
        content: const Text("Tindakan ini tidak bisa dibatalkan."),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context), child: const Text("Batal")),
          ElevatedButton(
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.red,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
            ),
            onPressed: () async {
              Navigator.pop(context);
              final SharedPreferences prefs = await SharedPreferences.getInstance();
              int userId = prefs.getInt('user_id') ?? 0;
              final result = await ApiServices.deleteRestoReview(reviewId, userId);
              if (mounted) {
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(content: Text(result['message'] ?? "Dihapus")),
                );
                _triggerParentRefresh();
              }
            },
            child: const Text("Hapus", style: TextStyle(color: Colors.white)),
          ),
        ],
      ),
    );
  }

  // ============================================================
  // WIDGET TOMBOL DINAMIS (Sesuai permintaan)
  // ============================================================
  Widget _buildTrailingWidget(Map<String, dynamic> item, bool isPaid, Color primaryColor) {
    bool isReviewed = (item['is_reviewed'] as bool?) ?? false;

    // ✅ PERBAIKAN: Cek status pesanan SELESAI (id 4), bukan pembayaran
    int statusPesananId = int.tryParse(item['status_pesanan_id'].toString()) ?? 0;
    bool isCompleted = statusPesananId == 4; // Status SELESAI

    // 1. Pesanan BELUM Selesai -> tampilkan harga subtotal
    if (!isCompleted) {
      double subPrice = double.tryParse(item['harga_at_porsi'].toString()) ?? 0;
      int qty = int.tryParse(item['jumlah'].toString()) ?? 0;
      return Text(
        "Rp ${(subPrice * qty).toStringAsFixed(0)}",
        style: TextStyle(fontWeight: FontWeight.bold, color: primaryColor),
      );
    }

    // 2. Sudah Lunas DAN SUDAH Diulas -> tombol Edit & Hapus
    if (isReviewed) {
      return Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          IconButton(
            icon: const Icon(Icons.edit_note, color: Colors.blue, size: 20),
            tooltip: "Edit ulasan",
            onPressed: () => _showRestoReviewDialog(
              context, item['menu_id'], item['menu']['nama_menu'],
              isEdit: true,
              reviewId: item['review_id'],
              existingData: item['existing_review'],
            ),
          ),
          IconButton(
            icon: const Icon(Icons.delete_outline, color: Colors.red, size: 20),
            tooltip: "Hapus ulasan",
            onPressed: () => _confirmDeleteReview(item['review_id']),
          ),
        ],
      );
    }

    // 3. Sudah Selesai DAN BELUM Diulas -> tombol ULAS (warna primary)
    return ElevatedButton(
      style: ElevatedButton.styleFrom(
        backgroundColor: primaryColor,
        minimumSize: const Size(70, 32),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
      ),
      onPressed: () => _showRestoReviewDialog(context, item['menu_id'], item['menu']['nama_menu']),
      child: const Text("ULAS", style: TextStyle(fontSize: 12, color: Colors.white)),
    );
  }

  @override
  Widget build(BuildContext context) {
    final eventProvider = context.watch<EventProvider>();
    final primaryColor = eventProvider.primaryColor;

    final List<dynamic> details = _currentOrder['details'] ?? [];
    final bool isPaid = _currentOrder['status_pembayaran_id'].toString() == '2';
    final Color statusColor = isPaid ? Colors.green : Colors.orange;
    final String statusText = isPaid ? "LUNAS" : "MENUNGGU PEMBAYARAN";

    final String deliveryType = _currentOrder['tipe_pengantaran'] ?? "Meja";
    final String locationNum = _currentOrder['nomor_lokasi'] ?? "-";
    final IconData locationIcon = deliveryType == "Kamar" ? Icons.bed : Icons.table_restaurant;

    return Scaffold(
      backgroundColor: Colors.grey.shade50,
      appBar: AppBar(
        title: const Text("Detail Pesanan"),
        backgroundColor: primaryColor,
        foregroundColor: Colors.white,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios, size: 20),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: CustomScrollView(
        slivers: [
          const SliverToBoxAdapter(child: EventHeader()),
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Card Header Nota
                  Container(
                    width: double.infinity,
                    decoration: BoxDecoration(
                      gradient: LinearGradient(
                        colors: [primaryColor, primaryColor.withOpacity(0.8)],
                        begin: Alignment.topLeft,
                        end: Alignment.bottomRight,
                      ),
                      borderRadius: BorderRadius.circular(24),
                      boxShadow: [
                        BoxShadow(
                          color: primaryColor.withOpacity(0.3),
                          blurRadius: 12,
                          offset: const Offset(0, 6),
                        ),
                      ],
                    ),
                    child: Padding(
                      padding: const EdgeInsets.all(20),
                      child: Column(
                        children: [
                          const Icon(Icons.receipt_long, size: 48, color: Colors.white),
                          const SizedBox(height: 8),
                          Text(
                            "Nota #RS-${_currentOrder['id']}",
                            style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: Colors.white),
                          ),
                          const SizedBox(height: 6),
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                            decoration: BoxDecoration(
                              color: statusColor.withOpacity(0.2),
                              borderRadius: BorderRadius.circular(20),
                              border: Border.all(color: statusColor),
                            ),
                            child: Text(
                              statusText,
                              style: TextStyle(color: statusColor, fontWeight: FontWeight.bold, fontSize: 12),
                            ),
                          ),
                          const Divider(height: 24, color: Colors.white30),
                          Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Icon(locationIcon, size: 18, color: Colors.white70),
                              const SizedBox(width: 8),
                              Text(
                                "Lokasi Antar: $deliveryType $locationNum",
                                style: const TextStyle(color: Colors.white70, fontWeight: FontWeight.w500),
                              ),
                            ],
                          ),
                        ],
                      ),
                    ),
                  ),
                  const SizedBox(height: 24),
                  // Daftar Pesanan (tanpa harga per item)
                  const Text(
                    "Pesanan Anda",
                    style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 12),
                  Card(
                    elevation: 2,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
                    child: ListView.separated(
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
                      itemCount: details.length,
                      separatorBuilder: (_, __) => const Divider(height: 0, indent: 16, endIndent: 16),
                      itemBuilder: (context, index) {
                        final item = details[index];
                        final menu = item['menu'];
                        final int qty = int.tryParse(item['jumlah'].toString()) ?? 0;
                        final String menuName = menu?['nama_menu'] ?? "Menu tidak ditemukan";
                        final String? fotoMenu = menu?['foto_menu'];
                        // ✅ PERBAIKAN: Process URL gambar sebelum dipakai
                        final String processedImageUrl = _processImageUrl(fotoMenu);

                        return Padding(
                          padding: const EdgeInsets.symmetric(vertical: 8, horizontal: 12),
                          child: Row(
                            children: [
                              // Gambar kecil di kiri
                              ClipRRect(
                                borderRadius: BorderRadius.circular(10),
                                child: fotoMenu != null && fotoMenu.isNotEmpty
                                    ? Image.network(
                                        processedImageUrl,
                                        width: 50,
                                        height: 50,
                                        fit: BoxFit.cover,
                                        errorBuilder: (_, __, ___) => Container(
                                          width: 50,
                                          height: 50,
                                          color: Colors.grey[200],
                                          child: const Icon(Icons.fastfood, size: 25, color: Colors.grey),
                                        ),
                                      )
                                    : Container(
                                        width: 50,
                                        height: 50,
                                        color: Colors.grey[200],
                                        child: const Icon(Icons.fastfood, size: 25, color: Colors.grey),
                                      ),
                              ),
                              const SizedBox(width: 12),
                              // Nama dan jumlah (tanpa harga)
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(
                                      menuName,
                                      style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
                                      maxLines: 2,
                                      overflow: TextOverflow.ellipsis,
                                    ),
                                    const SizedBox(height: 4),
                                    Text(
                                      "$qty porsi",
                                      style: const TextStyle(fontSize: 12, color: Colors.grey),
                                    ),
                                  ],
                                ),
                              ),
                              // Tombol aksi (Ulas / Edit/Hapus) atau harga jika pending
                              _buildTrailingWidget(item, isPaid, primaryColor),
                            ],
                          ),
                        );
                      },
                    ),
                  ),
                  const SizedBox(height: 24),
                  // Ringkasan Pembayaran
                  Container(
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(20),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withOpacity(0.05),
                          blurRadius: 8,
                          offset: const Offset(0, 2),
                        ),
                      ],
                    ),
                    child: Padding(
                      padding: const EdgeInsets.all(16),
                      child: Column(
                        children: [
                          const Row(
                            children: [
                              Icon(Icons.payment, size: 18),
                              SizedBox(width: 8),
                              Text("Detail Pembayaran", style: TextStyle(fontWeight: FontWeight.bold)),
                            ],
                          ),
                          const Divider(height: 20),
                          _buildInfoRow("Metode Pembayaran", _currentOrder['metode_pembayaran'] ?? "-"),
                          _buildInfoRow(
                            "Waktu Pesan",
                            _currentOrder['created_at'].toString().substring(0, 16).replaceAll('T', ' '),
                          ),
                          const Divider(height: 16),
                          _buildInfoRow(
                            "Total Bayar",
                            "Rp ${double.parse(_currentOrder['total_harga'].toString()).toStringAsFixed(0)}",
                            isBold: true,
                            color: primaryColor,
                          ),
                        ],
                      ),
                    ),
                  ),
                  const SizedBox(height: 30),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildInfoRow(String label, String value, {bool isBold = false, Color color = Colors.black}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 6),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label, style: const TextStyle(color: Colors.grey, fontSize: 13)),
          Text(
            value,
            style: TextStyle(
              fontWeight: isBold ? FontWeight.bold : FontWeight.w600,
              fontSize: isBold ? 18 : 13,
              color: color,
            ),
          ),
        ],
      ),
    );
  }
}