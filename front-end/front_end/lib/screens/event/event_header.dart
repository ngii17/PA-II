import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../../providers/event_provider.dart';

class EventHeader extends StatelessWidget {
  const EventHeader({super.key});

  @override
  Widget build(BuildContext context) {
    final eventProvider = Provider.of<EventProvider>(context);
    final theme = eventProvider.activeTheme;

    final String eventCode = (theme['event_code'] ?? 'default').toString();
    final String title = theme['nama_event']?.toString() ?? 'Promo Spesial';
    final String description = theme['deskripsi']?.toString() ??
        'Dapatkan penawaran terbaik kami sekarang juga.';
    final String? headerUrl = _extractImageUrl(theme['header_image']) ??
        _extractImageUrl(theme['background_image']);
    final String? decoUrl = _extractImageUrl(theme['decoration_image']);

    if (eventCode == 'default') {
      return const SizedBox.shrink();
    }

    final Color primaryColor = eventProvider.primaryColor;
    final Color secondaryColor = eventProvider.secondaryColor;

    return Container(
      width: double.infinity,
      height: 190,
      margin: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(28),
        boxShadow: [
          BoxShadow(
            color: primaryColor.withAlpha(46),
            blurRadius: 24,
            offset: const Offset(0, 12),
          ),
        ],
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(28),
        child: Stack(
          fit: StackFit.expand,
          children: [
            if (headerUrl != null && headerUrl.isNotEmpty)
              Image.network(
                headerUrl,
                fit: BoxFit.cover,
                loadingBuilder: (context, child, loadingProgress) {
                  if (loadingProgress == null) return child;
                  return Container(
                    color: primaryColor.withAlpha(64),
                    child: Center(
                      child: CircularProgressIndicator(
                        value: loadingProgress.expectedTotalBytes != null
                            ? loadingProgress.cumulativeBytesLoaded /
                                loadingProgress.expectedTotalBytes!
                            : null,
                        color: Colors.white,
                      ),
                    ),
                  );
                },
                errorBuilder: (context, error, stackTrace) {
                  return _buildFallbackBackground(primaryColor, secondaryColor);
                },
              )
            else
              _buildFallbackBackground(primaryColor, secondaryColor),
            Container(
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.topCenter,
                  end: Alignment.bottomCenter,
                  colors: [
                    Colors.black.withAlpha(77),
                    Colors.black.withAlpha(191),
                  ],
                ),
              ),
            ),
            if (decoUrl != null && decoUrl.isNotEmpty)
              Positioned(
                right: 0,
                top: 0,
                bottom: 0,
                width: 140,
                child: Opacity(
                  opacity: 0.8,
                  child: Image.network(
                    decoUrl,
                    fit: BoxFit.cover,
                    errorBuilder: (context, error, stackTrace) =>
                        const SizedBox.shrink(),
                  ),
                ),
              ),
            Padding(
              padding: const EdgeInsets.fromLTRB(24, 20, 24, 20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Container(
                    padding:
                        const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                    decoration: BoxDecoration(
                      color: secondaryColor.withAlpha(235),
                      borderRadius: BorderRadius.circular(18),
                    ),
                    child: Text(
                      eventCode.toUpperCase(),
                      style: TextStyle(
                        color: _getContrastText(secondaryColor),
                        fontSize: 11,
                        fontWeight: FontWeight.w700,
                        letterSpacing: 0.8,
                      ),
                    ),
                  ),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        title,
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                        style: const TextStyle(
                          color: Colors.white,
                          fontSize: 24,
                          fontWeight: FontWeight.bold,
                          height: 1.08,
                        ),
                      ),
                      const SizedBox(height: 8),
                      Text(
                        description,
                        maxLines: 3,
                        overflow: TextOverflow.ellipsis,
                        style: TextStyle(
                          color: const Color(0xFFFFFFFF).withAlpha(225),
                          fontSize: 13,
                          height: 1.5,
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildFallbackBackground(Color primary, Color secondary) {
    return Container(
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [primary, secondary.withAlpha(217)],
        ),
      ),
    );
  }

  Color _getContrastText(Color background) {
    return background.computeLuminance() > 0.5 ? Colors.black : Colors.white;
  }

  String? _extractImageUrl(dynamic value) {
    if (value is String && value.isNotEmpty) {
      if (value.startsWith('http')) return value;
      return 'https://purnama-hotel.duckdns.org/storage/$value';
    }
    return null;
  }
}