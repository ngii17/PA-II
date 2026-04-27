import 'package:flutter/material.dart';
import '../services/api_services.dart';

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
}