import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/event_provider.dart';
import '../../colors/login_constants.dart';

class EventHeader extends StatelessWidget {
  const EventHeader({super.key});

  // FUNGSI PEMBANTU: Mengubah Hex String (#FFFFFF) menjadi Color Flutter
  Color hexToColor(String hexString) {
    final buffer = StringBuffer();
    if (hexString.length == 6 || hexString.length == 7) buffer.write('ff');
    buffer.write(hexString.replaceFirst('#', ''));
    return Color(int.parse(buffer.toString(), radix: 16));
  }

  @override
  Widget build(BuildContext context) {
    // Memantau perubahan tema dari EventProvider
    final eventProvider = context.watch<EventProvider>();
    final theme = eventProvider.activeTheme;

    // Ambil data dinamis dari Provider
    final String eventCode = theme['event_code'] ?? 'default';
    final Color primaryColor = hexToColor(theme['primary_color'] ?? '#00197D');
    final Color secondaryColor = hexToColor(theme['secondary_color'] ?? '#D4AF37');
    final String? headerImg = theme['header_image']; // Gambar Background Banner
    final String? decoImg = theme['decoration_image']; // Gambar Ikon/Hiasan samping
    
    // Teks dinamis (Pastikan di database Laravel Anda mengirimkan field ini)
    final String title = theme['nama_event'] ?? "Promo Hari Ini";
    final String desc = theme['deskripsi'] ?? "Dapatkan penawaran terbaik di Purnama Balige";

    return Container(
      width: double.infinity,
      margin: const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
      height: 150,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(25),
        boxShadow: [
          BoxShadow(
            color: primaryColor.withOpacity(0.2),
            blurRadius: 15,
            offset: const Offset(0, 8),
          )
        ],
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(25),
        child: Stack(
          children: [
            // 1. BACKGROUND DINAMIS (Gambar atau Gradien Warna)
            Positioned.fill(
              child: headerImg != null && headerImg.isNotEmpty
                  ? Image.network(
                      headerImg,
                      fit: BoxFit.cover,
                      errorBuilder: (c, e, s) => _buildGradientBg(primaryColor, secondaryColor),
                    )
                  : _buildGradientBg(primaryColor, secondaryColor),
            ),

            // 2. OVERLAY GRADIEN GELAP (Agar tulisan admin selalu terbaca)
            Positioned.fill(
              child: Container(
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    begin: Alignment.centerLeft,
                    end: Alignment.centerRight,
                    colors: [
                      Colors.black.withOpacity(0.7),
                      Colors.transparent,
                    ],
                  ),
                ),
              ),
            ),

            // 3. KONTEN TEKS DARI ADMIN
            Padding(
              padding: const EdgeInsets.all(20.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  if (eventCode != 'default')
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                      margin: const EdgeInsets.only(bottom: 8),
                      decoration: BoxDecoration(
                        color: secondaryColor,
                        borderRadius: BorderRadius.circular(5),
                      ),
                      child: Text(
                        "EVENT KHUSUS",
                        style: TextStyle(
                          color: primaryColor,
                          fontSize: 9,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ),
                  Text(
                    title.toUpperCase(),
                    style: const TextStyle(
                      color: Colors.white,
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                      letterSpacing: 1,
                    ),
                  ),
                  const SizedBox(height: 5),
                  SizedBox(
                    width: 220,
                    child: Text(
                      desc,
                      style: const TextStyle(color: Colors.white70, fontSize: 11),
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
                ],
              ),
            ),

            // 4. DEKORASI GAMBAR SAMPING (Dinamis dari Admin)
            if (decoImg != null && decoImg.isNotEmpty)
              Positioned(
                right: 10,
                bottom: 10,
                top: 10,
                child: Opacity(
                  opacity: 0.8,
                  child: Image.network(
                    decoImg,
                    width: 100,
                    fit: BoxFit.contain,
                    errorBuilder: (c, e, s) => const SizedBox(),
                  ),
                ),
              ),
          ],
        ),
      ),
    );
  }

  // Widget Background jika tidak ada gambar
  Widget _buildGradientBg(Color p, Color s) {
    return Container(
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [p, s.withOpacity(0.8)],
        ),
      ),
    );
  }
}