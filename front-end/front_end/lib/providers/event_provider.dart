import 'package:flutter/material.dart';
import '../services/api_services.dart';

class EventProvider extends ChangeNotifier {
  // 1. Variabel penampung seluruh data tema dari Laravel
  // Default menggunakan warna brand Purnama: Navy & Gold Premium
  Map<String, dynamic> _activeTheme = {
    'event_code': 'default',
    'primary_color': '#00197D',   // Navy Purnama
    'secondary_color': '#D4AF37', // Gold Premium
    'header_image': null,
    'decoration_image': null,
  };

  // Getter agar semua halaman bisa ambil data visual ini
  Map<String, dynamic> get activeTheme => _activeTheme;
  String get eventCode => _activeTheme['event_code'] ?? 'default';

  // 2. Fungsi untuk ambil paket desain dari Laravel Port 8001
  Future<void> fetchActiveTheme() async {
    try {
      final result = await ApiServices.getActiveEvent();
      
      if (result['success'] == true) {
        // Kita simpan seluruh isi kotak 'data' dari Laravel ke dalam memori HP
        _activeTheme = result['data'];
        
        print("LOG_THEME: Tema aktif berubah menjadi -> ${_activeTheme['event_code']}");
        
        // TERIAK ke seluruh aplikasi: "AYO GANTI BAJU SEKARANG!"
        notifyListeners(); 
      }
    } catch (e) {
      print("LOG_ERROR_THEME: $e");
    }
  }

  // Tambahkan getter untuk Primary Color (fallback ke Navy)
  Color get primaryColor {
    return _parseHexColor(_activeTheme['primary_color'] ?? '#00197D');
  }

  // Tambahkan getter untuk Secondary Color (fallback ke Gold)
  Color get secondaryColor {
    return _parseHexColor(_activeTheme['secondary_color'] ?? '#D4AF37');
  }

  // Fungsi pembantu untuk mengubah String Hex ke Color Flutter
  Color _parseHexColor(String hexString) {
    final buffer = StringBuffer();
    if (hexString.length == 6 || hexString.length == 7) buffer.write('ff');
    buffer.write(hexString.replaceFirst('#', ''));
    return Color(int.parse(buffer.toString(), radix: 16));
  }
}