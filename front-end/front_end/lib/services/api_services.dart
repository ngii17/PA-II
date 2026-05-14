import 'dart:io';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart'; // Untuk rootBundle (baca file asset)
import 'package:image_picker/image_picker.dart'; // Untuk ambil gambar dari galeri/kamera
import 'package:firebase_messaging/firebase_messaging.dart';
import  'package:flutter_local_notifications/flutter_local_notifications.dart';
import '../notification/notification_service.dart'; // Import service notifikasi untuk ambil token
import '../screens/event/app_theme.dart'; // Import AppTheme untuk konversi warna
import '../screens/notification/notification_screen.dart'; // Import Screen Notifikasi untuk navigas
import 'package:url_launcher/url_launcher.dart'; // Untuk buka URL eksternal (redirect pembayaran) 
import 'package:firebase_messaging/firebase_messaging.dart';





class ApiServices {
  // 1. Alamat Server Auth (Mikroservices - Port 8000)
  static const String _authUrl = "http://10.187.82.132:8000/api";
  
  // 2. Alamat Server Bisnis (Main Backend - Port 8001)
  static const String _hotelUrl = "http://10.187.82.132:8001/api";

  // 3. Alamat Server Notifikasi (Port 8002)
  static const String _notifUrl = "http://10.187.82.132:8002/api";

  // ==========================================
  // FUNGSI KHUSUS HOTEL (Server Port 8001)
  // ==========================================

  static Future<Map<String, dynamic>> getRoomTypes() async {
    try {
      final response = await http.get(
        Uri.parse("$_hotelUrl/room-types"), 
        headers: {'Accept': 'application/json'},
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'success': false, 'message': 'Gagal koneksi ke server hotel: $e'};
    }
  }


  // ==========================================
  // FUNGSI KHUSUS AUTH (Server Port 8000)
  // ==========================================

  static Future<Map<String, dynamic>> register(Map<String, dynamic> data) async {
    try {
      final response = await http.post(
        Uri.parse("$_authUrl/register"), 
        headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
        body: jsonEncode(data),
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'success': false, 'message': 'Koneksi gagal: $e'};
    }
  }

  static Future<Map<String, dynamic>> verifyOtp(String email, String otp) async {
    try {
      final response = await http.post(
        Uri.parse("$_authUrl/verify-otp"), 
        headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
        body: jsonEncode({'email': email, 'otp': otp}),
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'success': false, 'message': 'Gagal verifikasi: $e'};
    }
  }

  static Future<Map<String, dynamic>> forgotPassword(String email) async {
    try {
      final response = await http.post(
        Uri.parse("$_authUrl/forgot-password"), 
        headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
        body: jsonEncode({'email': email}),
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'success': false, 'message': 'Koneksi gagal: $e'};
    }
  }

  static Future<Map<String, dynamic>> resetPassword(String email, String otp, String newPassword, String confirmPassword) async {
    try {
      final response = await http.post(
        Uri.parse("$_authUrl/reset-password"), 
        headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
        body: jsonEncode({
          'email': email,
          'otp': otp,
          'password': newPassword,
          'password_confirmation': confirmPassword,
        }),
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'success': false, 'message': 'Koneksi gagal: $e'};
    }
  }

static Future<Map<String, dynamic>> login(String email, String password) async {
  try {
    String? fcmToken = await FirebaseMessaging.instance.getToken();
    
    final response = await http.post(
      Uri.parse("$_authUrl/login"),
      headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
      body: jsonEncode({
        'email': email,
        'password': password,
        'fcm_token': fcmToken
      }),
    );

    var data = jsonDecode(response.body);
    
    if (data['success'] == true) {
      final SharedPreferences prefs = await SharedPreferences.getInstance();
      // SIMPAN WAKTU LOGIN SEKARANG (dalam milidetik)
      int loginTime = DateTime.now().millisecondsSinceEpoch;
      await prefs.setInt('last_login_time', loginTime);
    }

    return data;
  } catch (e) {
    return {'success': false, 'message': 'Koneksi gagal: $e'};
  }
}

  // 6. FUNGSI SIMPAN RESERVASI (Mengirim ke Main Backend - Port 8001)
  static Future<Map<String, dynamic>> storeReservation(Map<String, dynamic> data) async {
    try {
      final response = await http.post(
        Uri.parse("$_hotelUrl/reservasi"), 
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode(data), 
      );

      return jsonDecode(response.body);
    } catch (e) {
      return {
        'success': false, 
        'message': 'Gagal mengirim data ke server bisnis: $e'
      };
    }
  }

  // 7. FUNGSI LOGOUT (Ke Port 8000)
  static Future<Map<String, dynamic>> logout() async {
    try {
      final SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('auth_token');

      final response = await http.post(
        Uri.parse("$_authUrl/logout"),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer $token', 
        },
      );

      return jsonDecode(response.body);
    } catch (e) {
      return {'success': false, 'message': 'Gagal logout: $e'};
    }
  }

  // 8. FUNGSI AMBIL RIWAYAT RESERVASI (Dari Port 8001)
  static Future<Map<String, dynamic>> getReservationHistory(String userId) async {
    try {
      final response = await http.get(
        Uri.parse("$_hotelUrl/reservasi/history?user_id=$userId"),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
      );

      return jsonDecode(response.body);
    } catch (e) {
      return {
        'success': false, 
        'message': 'Gagal mengambil riwayat: $e'
      };
    }
  }

  // 9. FUNGSI CEK STATUS PEMBAYARAN (Polling ke Port 8001)
  // Fungsi ini akan dipanggil oleh WaitingPaymentScreen setiap 3 detik
  static Future<Map<String, dynamic>> checkPaymentStatus(int reservasiId) async {
    try {
      final response = await http.get(
        Uri.parse("$_hotelUrl/reservasi/check-status/$reservasiId"),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
      );

      return jsonDecode(response.body);
    } catch (e) {
      return {
        'success': false, 
        'message': 'Gagal mengecek status: $e'
      };
    }
  }
  // ==========================================
  // FUNGSI KHUSUS RESTORAN (Server Port 8001)
  // ==========================================

  // 10. Fungsi untuk mengambil semua daftar menu makanan & minuman
  static Future<Map<String, dynamic>> getRestaurantMenus() async {
    try {
      final response = await http.get(
        Uri.parse("$_hotelUrl/menus"), // Kita gunakan port 8001
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
      );

      return jsonDecode(response.body);
    } catch (e) {
      return {
        'success': false, 
        'message': 'Gagal koneksi ke server restoran: $e'
      };
    }
  }

  // 10. Fungsi untuk mengirim pesanan makanan (Ke Port 8001)
  static Future<Map<String, dynamic>> placeRestaurantOrder(Map<String, dynamic> data) async {
    try {
      final response = await http.post(
        Uri.parse("$_hotelUrl/menus/order"),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode(data),
      );

      return jsonDecode(response.body);
    } catch (e) {
      return {
        'success': false, 
        'message': 'Gagal mengirim pesanan: $e'
      };
    }
  }


  // 11. Fungsi untuk cek status pembayaran restoran (Polling)
  static Future<Map<String, dynamic>> checkRestoOrderStatus(int orderId) async {
    try {
      final response = await http.get(
        Uri.parse("$_hotelUrl/menus/order/status/$orderId"),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'success': false, 'message': 'Gagal cek status: $e'};
    }
  }

  // 12. Fungsi untuk mengambil riwayat pesanan restoran (Dari Port 8001)
  static Future<Map<String, dynamic>> getRestaurantOrderHistory(String userId) async {
    try {
      final response = await http.get(
        Uri.parse("$_hotelUrl/menus/order/history?user_id=$userId"),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'success': false, 'message': 'Gagal mengambil riwayat resto: $e'};
    }
  }

  // 13. Fungsi untuk mengambil data profil lengkap user (Dari Port 8000)
  static Future<Map<String, dynamic>> getUserProfile() async {
    try {
      // 1. Ambil token dari memori HP
      final SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('auth_token');

      // 2. Kirim permintaan ke server Auth (Port 8000)
      final response = await http.get(
        Uri.parse("$_authUrl/user/profile"),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          // Sertakan Token sebagai bukti identitas yang valid
          'Authorization': 'Bearer $token', 
        },
      );

      return jsonDecode(response.body);
    } catch (e) {
      return {
        'success': false, 
        'message': 'Gagal terhubung ke server identitas: $e'
      };
    }
  }

  // 14. Fungsi untuk Update Profil & Upload Foto (Ke Port 8000)
  static Future<Map<String, dynamic>> updateProfile({
    required String username,
    required String fullName,
    required String phone,
    required String address,
    File? imageFile, // File gambar bersifat opsional (boleh null)
  }) async {
    try {
      final SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('auth_token');

      // 1. Karena ada file, kita gunakan MultipartRequest
      var request = http.MultipartRequest('POST', Uri.parse("$_authUrl/user/update"));

      // 2. Tambahkan Header (Token Akses)
      request.headers.addAll({
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      });

      // 3. Tambahkan Data Teks
      request.fields['username'] = username;
      request.fields['full_name'] = fullName;
      request.fields['phone'] = phone;
      request.fields['address'] = address;

      // 4. Tambahkan File Gambar (Jika user memilih foto baru)
      if (imageFile != null) {
        request.files.add(await http.MultipartFile.fromPath('image', imageFile.path));
      }

      // 5. Kirim data dan ambil respon
      var streamedResponse = await request.send();
      var response = await http.Response.fromStream(streamedResponse);

      return jsonDecode(response.body);
    } catch (e) {
      return {'success': false, 'message': 'Gagal update profil: $e'};
    }
  }

  // 15. Fungsi Hapus Foto Profil (Langkah 2)
  static Future<Map<String, dynamic>> deleteProfilePhoto() async {
    try {
      final SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('auth_token');

      final response = await http.delete(
        Uri.parse("$_authUrl/user/delete-photo"),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
        },
      );

      return jsonDecode(response.body);
    } catch (e) {
      return {'success': false, 'message': 'Terjadi kesalahan: $e'};
    }
  }

  // ==========================================
  // FUNGSI KHUSUS ULASAN (Server Port 8001)
  // ==========================================

  // 13. Kirim Ulasan Hotel
  static Future<Map<String, dynamic>> storeHotelReview(Map<String, dynamic> data) async {
    try {
      final response = await http.post(
        Uri.parse("$_hotelUrl/review/hotel"),
        headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
        body: jsonEncode(data),
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'success': false, 'message': 'Gagal kirim ulasan hotel: $e'};
    }
  }

  // 14. Kirim Ulasan Restoran
  static Future<Map<String, dynamic>> storeRestoReview(Map<String, dynamic> data) async {
    try {
      final response = await http.post(
        Uri.parse("$_hotelUrl/review/restoran"),
        headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
        body: jsonEncode(data),
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'success': false, 'message': 'Gagal kirim ulasan resto: $e'};
    }
  }

  // 15. Ambil Ulasan Hotel (Untuk Umum)
  static Future<Map<String, dynamic>> getHotelReviews(int tipeKamarId) async {
    try {
      final response = await http.get(Uri.parse("$_hotelUrl/review/hotel/$tipeKamarId"));
      return jsonDecode(response.body);
    } catch (e) {
      return {'success': false, 'message': 'Gagal ambil ulasan: $e'};
    }
  }

  // 16. Ambil Ulasan Restoran (Untuk Umum)
  static Future<Map<String, dynamic>> getRestoReviews(int menuId) async {
    try {
      final response = await http.get(Uri.parse("$_hotelUrl/review/restoran/$menuId"));
      return jsonDecode(response.body);
    } catch (e) {
      return {'success': false, 'message': 'Gagal ambil ulasan: $e'};
    }
  }


  // 17. FUNGSI AMBIL TEMA EVENT YANG AKTIF (Port 8001)
  static Future<Map<String, dynamic>> getActiveEvent() async {
    try {
      final response = await http.get(
        Uri.parse("$_hotelUrl/active-event"),
        headers: {'Accept': 'application/json'},
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'success': false, 'event_code': 'default'};
    }
  }



  // 18. FUNGSI CEK KODE PROMO MANUAL (Ke Port 8001)
  static Future<Map<String, dynamic>> checkPromoCode(String code, String category) async {
    try {
      final response = await http.post(
        Uri.parse("$_hotelUrl/promo/check"),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode({
          'kode_promo': code,
          'kategori': category, // Isinya 'hotel' atau 'restoran'
        }),
      );

      return jsonDecode(response.body);
    } catch (e) {
      return {'success': false, 'message': 'Gagal mengecek promo: $e'};
    }
  }


   // 19. FUNGSI AMBIL INBOX NOTIFIKASI (Dari Port 8002)
  static Future<Map<String, dynamic>> getNotificationInbox(String userId) async {
    try {
      final response = await http.get(
        Uri.parse("$_notifUrl/inbox/$userId"),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
      );

      // Kita gunakan handleResponse yang kita buat kemarin untuk debug
      if (response.body.startsWith('<')) {
         print("ERROR_HTML: ${response.body}");
         return {'success': false, 'message': 'Server Notif Error'};
      }

      return jsonDecode(response.body);
    } catch (e) {
      return {
        'success': false, 
        'message': 'Gagal terhubung ke pusat notifikasi: $e'
      };
    }
  }


   // 20. FUNGSI AMBIL PROMO (Gunakan _baseUrl karena ini di Port 8001)
  static Future<Map<String, dynamic>> getActivePromo() async {
    try {
      final response = await http.get(
        Uri.parse("$_hotelUrl/resto/active-promo"), // <-- Sekarang _baseUrl sudah dikenal
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'success': false, 'message': e.toString()};
    }
  }



  // Detail Notifikasi
static Future<Map<String, dynamic>> getNotificationDetail(int id, int userId) async {
  final response = await http.get(Uri.parse("$_notifUrl/notifications/$id?user_id=$userId"));
  return jsonDecode(response.body);
}

// Tandai Dibaca
static Future<void> markNotifAsRead(int id, int userId) async {
  await http.patch(Uri.parse("$_notifUrl/notifications/$id/read?user_id=$userId"));
}

// Hapus Notifikasi
static Future<Map<String, dynamic>> deleteNotification(int id, int userId) async {
  final response = await http.delete(Uri.parse("$_notifUrl/notifications/$id?user_id=$userId"));
  return jsonDecode(response.body);
}



}