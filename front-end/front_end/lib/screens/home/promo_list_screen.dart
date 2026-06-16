// screens/restoran/promo_list_screen.dart

import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../services/api_services.dart';
import '../../providers/event_provider.dart';
import '../home/promo_detail_screen.dart';

class PromoListScreen extends StatefulWidget {
  final String kategori; // <-- TETAP REQUIRED, TAPI BERI DEFAULT

  const PromoListScreen({
    super.key,
    this.kategori = 'restoran', // <-- DEFAULT VALUE
  });

  @override
  State<PromoListScreen> createState() => _PromoListScreenState();
}

class _PromoListScreenState extends State<PromoListScreen> {
  List<dynamic> _promos = [];
  bool _isLoading = true;
  String? _errorMessage;

  @override
  void initState() {
    super.initState();
    _fetchPromos();
  }

  Future<void> _fetchPromos() async {
    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    try {
      // Ambil semua promo aktif
      final result = await ApiServices.getActivePromos();

      if (result['success'] == true) {
        final allPromos = result['data'] ?? [];
        
        // FILTER berdasarkan kategori
        final filteredPromos = allPromos.where((promo) {
          final promoKategori = (promo['kategori'] ?? '').toString().toLowerCase();
          final targetKategori = widget.kategori.toLowerCase();
          
          // Jika kategori promo 'semua' atau 'all', tampilkan untuk semua
          if (promoKategori == 'semua' || promoKategori == 'all') {
            return true;
          }
          
          // Filter berdasarkan kategori yang diminta
          return promoKategori == targetKategori;
        }).toList();

        setState(() {
          _promos = filteredPromos;
          _isLoading = false;
        });
      } else {
        setState(() {
          _errorMessage = result['message'] ?? 'Gagal memuat data promo';
          _isLoading = false;
        });
      }
    } catch (e) {
      setState(() {
        _errorMessage = 'Terjadi kesalahan: $e';
        _isLoading = false;
      });
    }
  }

  String _formatRupiah(dynamic value) {
    final num angka = (value is num) ? value : num.tryParse(value.toString()) ?? 0;
    final parts = angka.toStringAsFixed(0).split('.');
    final number = parts[0];
    final formatted = number.replaceAllMapped(
      RegExp(r'(\d)(?=(\d{3})+(?!\d))'),
      (match) => '${match[1]}.',
    );
    return 'Rp $formatted';
  }

  String _formatDate(String? dateStr) {
    if (dateStr == null || dateStr.isEmpty) return '-';
    try {
      final date = DateTime.parse(dateStr);
      final months = [
        'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
        'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
      ];
      return '${date.day} ${months[date.month - 1]} ${date.year}';
    } catch (e) {
      return dateStr;
    }
  }

  Color _getPromoColor(String? kategori) {
    final kat = kategori?.toLowerCase() ?? 'semua';
    if (kat == 'hotel') return const Color(0xFF0C2D6B);
    if (kat == 'restoran') return const Color(0xFFC9A227);
    return Colors.teal;
  }

  IconData _getPromoIcon(String? kategori) {
    final kat = kategori?.toLowerCase() ?? 'semua';
    if (kat == 'hotel') return Icons.hotel_rounded;
    if (kat == 'restoran') return Icons.restaurant_menu_rounded;
    return Icons.card_giftcard_rounded;
  }

  String _getKategoriLabel() {
    final kat = widget.kategori.toLowerCase();
    if (kat == 'hotel') return 'Hotel';
    if (kat == 'restoran') return 'Restoran';
    return 'Semua Layanan';
  }

  @override
  Widget build(BuildContext context) {
    final ep = context.watch<EventProvider>();
    final primaryColor = ep.primaryColor;
    final secondaryColor = ep.secondaryColor;

    final kategoriLabel = _getKategoriLabel();

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        title: Text(
          'Promo $kategoriLabel',
          style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 18),
        ),
        centerTitle: true,
        backgroundColor: primaryColor,
        foregroundColor: Colors.white,
        elevation: 0,
        actions: [
          IconButton(
            onPressed: _fetchPromos,
            icon: const Icon(Icons.refresh_rounded),
            tooltip: 'Refresh',
          ),
        ],
      ),
      body: Column(
        children: [
          // ── HEADER INFO ──
          Container(
            width: double.infinity,
            padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
            decoration: BoxDecoration(
              color: primaryColor.withOpacity(0.05),
              border: Border(
                bottom: BorderSide(color: primaryColor.withOpacity(0.1)),
              ),
            ),
            child: Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(10),
                  decoration: BoxDecoration(
                    color: secondaryColor.withOpacity(0.15),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Icon(
                    Icons.local_offer_rounded,
                    color: secondaryColor,
                    size: 24,
                  ),
                ),
                const SizedBox(width: 14),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        '${_promos.length} Promo $kategoriLabel Aktif',
                        style: TextStyle(
                          fontWeight: FontWeight.bold,
                          fontSize: 16,
                          color: primaryColor,
                        ),
                      ),
                      Text(
                        'Gunakan kode promo untuk mendapatkan diskon',
                        style: TextStyle(
                          fontSize: 12,
                          color: Colors.grey[600],
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
          // ── LIST PROMO ──
          Expanded(
            child: _isLoading
                ? Center(
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        CircularProgressIndicator(color: primaryColor),
                        const SizedBox(height: 16),
                        Text(
                          'Memuat promo...',
                          style: TextStyle(color: Colors.grey[600]),
                        ),
                      ],
                    ),
                  )
                : _errorMessage != null
                    ? _buildErrorState()
                    : _promos.isEmpty
                        ? _buildEmptyState(kategoriLabel)
                        : ListView.builder(
                            padding: const EdgeInsets.all(16),
                            itemCount: _promos.length,
                            itemBuilder: (context, index) {
                              final promo = _promos[index];
                              return _buildPromoCard(promo, primaryColor, secondaryColor);
                            },
                          ),
          ),
        ],
      ),
    );
  }

  Widget _buildErrorState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(Icons.wifi_off_rounded, size: 64, color: Colors.grey[400]),
          const SizedBox(height: 16),
          Text(
            _errorMessage ?? 'Gagal memuat data',
            style: TextStyle(color: Colors.grey[600]),
          ),
          const SizedBox(height: 12),
          ElevatedButton.icon(
            onPressed: _fetchPromos,
            icon: const Icon(Icons.refresh_rounded),
            label: const Text('Coba Lagi'),
            style: ElevatedButton.styleFrom(
              backgroundColor: const Color(0xFF0C2D6B),
              foregroundColor: Colors.white,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(12),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildEmptyState(String kategoriLabel) {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Container(
            padding: const EdgeInsets.all(30),
            decoration: BoxDecoration(
              color: Colors.white,
              shape: BoxShape.circle,
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withOpacity(0.05),
                  blurRadius: 20,
                ),
              ],
            ),
            child: Icon(
              Icons.local_offer_outlined,
              size: 60,
              color: Colors.grey[400],
            ),
          ),
          const SizedBox(height: 20),
          Text(
            'Belum Ada Promo $kategoriLabel',
            style: const TextStyle(
              fontWeight: FontWeight.bold,
              fontSize: 18,
              color: Colors.black87,
            ),
          ),
          const SizedBox(height: 8),
          Text(
            'Promo $kategoriLabel akan muncul di sini jika tersedia',
            style: TextStyle(
              color: Colors.grey[600],
              fontSize: 14,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildPromoCard(
    Map<String, dynamic> promo,
    Color primaryColor,
    Color secondaryColor,
  ) {
    final isExpired = promo['tanggal_berakhir'] != null &&
        DateTime.parse(promo['tanggal_berakhir'].toString()).isBefore(DateTime.now());

    final nominalText = promo['tipe_diskon'] == 'persen'
        ? "${promo['nominal_potongan']}%"
        : _formatRupiah(promo['nominal_potongan']);

    final kategori = promo['kategori'] ?? 'semua';
    final promoColor = _getPromoColor(kategori);
    final promoIcon = _getPromoIcon(kategori);

    return Container(
      margin: const EdgeInsets.only(bottom: 14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.06),
            blurRadius: 12,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Material(
        color: Colors.transparent,
        borderRadius: BorderRadius.circular(20),
        child: InkWell(
          onTap: isExpired
              ? null
              : () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (_) => PromoDetailScreen(
                        promo: promo,
                        accentColor: secondaryColor,
                      ),
                    ),
                  );
                },
          borderRadius: BorderRadius.circular(20),
          child: Padding(
            padding: const EdgeInsets.all(16),
            child: Row(
              children: [
                // ── ICON ──
                Container(
                  width: 56,
                  height: 56,
                  decoration: BoxDecoration(
                    color: promoColor.withOpacity(0.12),
                    borderRadius: BorderRadius.circular(16),
                  ),
                  child: Icon(
                    promoIcon,
                    color: promoColor,
                    size: 28,
                  ),
                ),
                const SizedBox(width: 14),
                // ── INFO ──
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        children: [
                          Expanded(
                            child: Text(
                              promo['nama_promo'] ?? 'Promo',
                              style: TextStyle(
                                fontWeight: FontWeight.bold,
                                fontSize: 15,
                                color: isExpired ? Colors.grey[500] : Colors.black87,
                              ),
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                            ),
                          ),
                          if (isExpired) ...[
                            const SizedBox(width: 8),
                            Container(
                              padding: const EdgeInsets.symmetric(
                                horizontal: 8,
                                vertical: 2,
                              ),
                              decoration: BoxDecoration(
                                color: Colors.red.withOpacity(0.1),
                                borderRadius: BorderRadius.circular(8),
                              ),
                              child: const Text(
                                'EXPIRED',
                                style: TextStyle(
                                  color: Colors.red,
                                  fontSize: 9,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ),
                          ],
                        ],
                      ),
                      const SizedBox(height: 4),
                      Row(
                        children: [
                          Text(
                            nominalText,
                            style: TextStyle(
                              fontWeight: FontWeight.w900,
                              fontSize: 18,
                              color: isExpired ? Colors.grey[400] : secondaryColor,
                            ),
                          ),
                          const SizedBox(width: 8),
                          Container(
                            padding: const EdgeInsets.symmetric(
                              horizontal: 8,
                              vertical: 2,
                            ),
                            decoration: BoxDecoration(
                              color: Colors.grey[100],
                              borderRadius: BorderRadius.circular(6),
                            ),
                            child: Text(
                              kategori.toString().toUpperCase(),
                              style: TextStyle(
                                fontSize: 9,
                                fontWeight: FontWeight.w600,
                                color: Colors.grey[600],
                              ),
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 6),
                      Row(
                        children: [
                          Icon(
                            Icons.calendar_today_rounded,
                            size: 12,
                            color: Colors.grey[400],
                          ),
                          const SizedBox(width: 4),
                          Text(
                            '${_formatDate(promo['tanggal_mulai'])} - ${_formatDate(promo['tanggal_berakhir'])}',
                            style: TextStyle(
                              fontSize: 11,
                              color: isExpired ? Colors.grey[400] : Colors.grey[500],
                            ),
                          ),
                        ],
                      ),
                      if (promo['kode_promo'] != null &&
                          promo['kode_promo'].toString().isNotEmpty) ...[
                        const SizedBox(height: 6),
                        Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 8,
                            vertical: 2,
                          ),
                          decoration: BoxDecoration(
                            color: secondaryColor.withOpacity(0.1),
                            borderRadius: BorderRadius.circular(6),
                          ),
                          child: Text(
                            'KODE: ${promo['kode_promo']}',
                            style: TextStyle(
                              fontSize: 10,
                              fontWeight: FontWeight.bold,
                              color: secondaryColor,
                              letterSpacing: 1,
                            ),
                          ),
                        ),
                      ],
                    ],
                  ),
                ),
                // ── ARROW ──
                if (!isExpired)
                  Icon(
                    Icons.chevron_right_rounded,
                    color: Colors.grey[400],
                    size: 28,
                  ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}