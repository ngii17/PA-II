import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/event_provider.dart';

class EventHeader extends StatelessWidget {
  const EventHeader({super.key});

  @override
  Widget build(BuildContext context) {
    final eventProvider = Provider.of<EventProvider>(context);
    final theme = eventProvider.activeTheme;
    
    String? headerUrl = theme['header_image'];
    // Ambil warna tema untuk cadangan jika gambar gagal dimuat
    final primaryColor = Theme.of(context).primaryColor;

    // Jika kode event adalah 'default' atau link kosong, jangan tampilkan apa-apa
    if (theme['event_code'] == 'default' || headerUrl == null || headerUrl.isEmpty) {
      return const SizedBox.shrink();
    }

    return Container(
      width: double.infinity,
      height: 160,
      margin: const EdgeInsets.only(bottom: 10),
      child: Stack(
        children: [
          // 1. TAMPILAN GAMBAR DENGAN ERROR HANDLING
          Positioned.fill(
            child: Image.network(
              headerUrl,
              fit: BoxFit.cover,
              // Saat proses download gambar
              loadingBuilder: (context, child, loadingProgress) {
                if (loadingProgress == null) return child;
                return Center(
                  child: CircularProgressIndicator(
                    value: loadingProgress.expectedTotalBytes != null
                        ? loadingProgress.cumulativeBytesLoaded / loadingProgress.expectedTotalBytes!
                        : null,
                  ),
                );
              },
              // JIKA GAMBAR ERROR / LINK PALSU (Ini yang bikin abu-abu tadi)
              errorBuilder: (context, error, stackTrace) {
                return Container(
                  color: primaryColor.withOpacity(0.8),
                  child: const Center(
                    child: Icon(Icons.image_not_supported, color: Colors.white, size: 40),
                  ),
                );
              },
            ),
          ),

          // 2. OVERLAY GRADIENT (Agar teks mudah dibaca)
          Positioned.fill(
            child: Container(
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.topCenter,
                  end: Alignment.bottomCenter,
                  colors: [
                    Colors.black.withOpacity(0.1),
                    Colors.black.withOpacity(0.6),
                  ],
                ),
              ),
            ),
          ),

          // 3. TEKS NAMA EVENT
          Positioned(
            bottom: 15,
            left: 20,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  "Event Spesial:",
                  style: TextStyle(color: Colors.white70, fontSize: 12),
                ),
                Text(
                  theme['nama_event'] ?? "",
                  style: const TextStyle(
                    color: Colors.white,
                    fontWeight: FontWeight.bold,
                    fontSize: 22,
                    letterSpacing: 0.5,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}