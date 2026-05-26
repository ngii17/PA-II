import 'package:flutter/material.dart';
import '../services/api_services.dart';
import 'package:flutter/material.dart';

class EventProvider extends ChangeNotifier {
  // 1. Variabel penampung seluruh data tema dari Laravel
  Map<String, dynamic> _activeTheme = {
    'event_code': 'default',
    'primary_color': '#448AFF',   // Biru Standar
    'secondary_color': '#E3F2FD', // Biru Muda
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


  // Tambahkan getter untuk Primary Color
  Color get primaryColor {
    if (activeTheme != null && activeTheme['primary_color'] != null) {
      return _parseHexColor(activeTheme['primary_color']);
    }
    return const Color(0xFF1B5E20); // Fallback warna hijau jika data null
  }

  // Tambahkan getter untuk Secondary Color
  Color get secondaryColor {
    if (activeTheme != null && activeTheme['secondary_color'] != null) {
      return _parseHexColor(activeTheme['secondary_color']);
    }
    return const Color(0xFFFDD835); // Fallback warna kuning jika data null
  }

  // Fungsi pembantu untuk mengubah String Hex ke Color Flutter
  Color _parseHexColor(String hexString) {
    final buffer = StringBuffer();
    if (hexString.length == 6 || hexString.length == 7) buffer.write('ff');
    buffer.write(hexString.replaceFirst('#', ''));
    return Color(int.parse(buffer.toString(), radix: 16));
  }

  
}