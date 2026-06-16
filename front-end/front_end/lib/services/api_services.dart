import 'dart:io';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:image_picker/image_picker.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import '../notification/notification_service.dart';
import '../screens/event/app_theme.dart';
import '../screens/notification/notification_screen.dart';
import 'package:url_launcher/url_launcher.dart';

class ApiServices {

  static const String ipAddress = String.fromEnvironment(
    'API_IP',
    defaultValue: '10.112.252.132',
  );
  static const String baseUrl = "http://$ipAddress:8001/api";
  static const String _authUrl = "http://$ipAddress:8000/api";
  static const String _hotelUrl = "http://$ipAddress:8001/api";
  static const String _notifUrl = "http://$ipAddress:8002/api";

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
          'fcm_token': fcmToken,
        }),
      );

      var data = jsonDecode(response.body);

      if (data['success'] == true) {
        final SharedPreferences prefs = await SharedPreferences.getInstance();
        int loginTime = DateTime.now().millisecondsSinceEpoch;
        await prefs.setInt('last_login_time', loginTime);
      }

      return data;
    } catch (e) {
      return {'success': false, 'message': 'Koneksi gagal: $e'};
    }
  }

  static Future<Map<String, dynamic>> storeReservation(Map<String, dynamic> data) async {
    try {
      final SharedPreferences prefs = await SharedPreferences.getInstance();
      final String? token = prefs.getString('auth_token'); // ← sama seperti logout

      if (token == null) {
        return {'success': false, 'message': 'Token tidak ditemukan, silakan login ulang'};
      }

      data.remove('user_id'); // buang user_id kalau masih ada di data

      final response = await http.post(
        Uri.parse("$_hotelUrl/reservasi"),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer $token', // ← tambahan ini
        },
        body: jsonEncode(data),
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'success': false, 'message': 'Gagal mengirim data ke server bisnis: $e'};
    }
  }

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

  static Future<Map<String, dynamic>> getReservationHistory() async {
    try {
      final SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('auth_token');

      if (token == null) {
        return {'success': false, 'message': 'Token tidak ditemukan, silakan login ulang'};
      }

      final response = await http.get(
        Uri.parse("$_hotelUrl/reservasi/history"), // ← hapus ?user_id=$userId
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'success': false, 'message': 'Gagal mengambil riwayat: $e'};
    }
  }

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
      return {'success': false, 'message': 'Gagal mengecek status: $e'};
    }
  }

  // ==========================================
  // FUNGSI KHUSUS RESTORAN (Server Port 8001)
  // ==========================================

  static Future<Map<String, dynamic>> getRestaurantMenus() async {
    try {
      final response = await http.get(
        Uri.parse("$_hotelUrl/menus"),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'success': false, 'message': 'Gagal koneksi ke server restoran: $e'};
    }
  }

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
      return {'success': false, 'message': 'Gagal mengirim pesanan: $e'};
    }
  }

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

  static Future<Map<String, dynamic>> getRestaurantOrderHistory(String userId) async {
    try {
      final SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('auth_token');

      final response = await http.get(
        Uri.parse("$_hotelUrl/menus/order/history?user_id=$userId"),
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'success': false, 'message': 'Gagal mengambil riwayat resto: $e'};
    }
  }

  static Future<Map<String, dynamic>> getUserProfile() async {
    try {
      final SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('auth_token');

      final response = await http.get(
        Uri.parse("$_authUrl/user/profile"),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'success': false, 'message': 'Gagal terhubung ke server identitas: $e'};
    }
  }

  static Future<Map<String, dynamic>> updateProfile({
    required String username,
    required String fullName,
    required String phone,
    required String address,
    String? avatar,
  }) async {
    try {
      final SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('auth_token');

      var request = http.MultipartRequest('POST', Uri.parse("$_authUrl/user/update"));
      request.headers.addAll({
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      });

      request.fields['username'] = username;
      request.fields['full_name'] = fullName;
      request.fields['phone'] = phone;
      request.fields['address'] = address;
      if (avatar != null) request.fields['avatar'] = avatar;

      var streamedResponse = await request.send();
      var response = await http.Response.fromStream(streamedResponse);
      return jsonDecode(response.body);
    } catch (e) {
      return {'success': false, 'message': 'Gagal update profil: $e'};
    }
  }

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

  static Future<Map<String, dynamic>> storeHotelReview(Map<String, dynamic> data) async {
    try {
      final SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('access_token');

      final response = await http.post(
        Uri.parse("$_hotelUrl/review/hotel"),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
        body: jsonEncode(data),
      );
      print("LOG_REVIEW_HOTEL: ${response.statusCode} - ${response.body}");
      return jsonDecode(response.body);
    } catch (e) {
      return {'success': false, 'message': 'Gagal kirim ulasan hotel: $e'};
    }
  }

  static Future<Map<String, dynamic>> storeRestoReview(Map<String, dynamic> data) async {
    try {
      final SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('access_token');

      final response = await http.post(
        Uri.parse("$_hotelUrl/review/restoran"),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
        body: jsonEncode(data),
      );
      print("LOG_REVIEW_RESTO: ${response.statusCode} - ${response.body}");
      return jsonDecode(response.body);
    } catch (e) {
      return {'success': false, 'message': 'Gagal kirim ulasan resto: $e'};
    }
  }

  static Future<Map<String, dynamic>> updateRestoReview(int id, Map<String, dynamic> data) async {
    final response = await http.put(
      Uri.parse("$_hotelUrl/review/restoran/$id"),
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode(data),
    );
    return jsonDecode(response.body);
  }

  static Future<Map<String, dynamic>> deleteRestoReview(int id, int userId) async {
    final response = await http.delete(
      Uri.parse("$_hotelUrl/review/restoran/$id?user_id=$userId"),
    );
    return jsonDecode(response.body);
  }

  static Future<Map<String, dynamic>> getHotelReviews(int tipeKamarId) async {
    try {
      final response = await http.get(Uri.parse("$_hotelUrl/review/hotel/$tipeKamarId"));
      return jsonDecode(response.body);
    } catch (e) {
      return {'success': false, 'message': 'Gagal ambil ulasan: $e'};
    }
  }

  static Future<Map<String, dynamic>> getRestoReviews(int menuId) async {
    try {
      final response = await http.get(Uri.parse("$_hotelUrl/review/restoran/$menuId"));
      return jsonDecode(response.body);
    } catch (e) {
      return {'success': false, 'message': 'Gagal ambil ulasan: $e'};
    }
  }

  static Future<Map<String, dynamic>> updateHotelReview(int id, Map<String, dynamic> data) async {
    final response = await http.put(
      Uri.parse("$_hotelUrl/review/hotel/$id"),
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode(data),
    );
    return jsonDecode(response.body);
  }

  static Future<Map<String, dynamic>> deleteHotelReview(int id, int userId) async {
    final response = await http.delete(
      Uri.parse("$_hotelUrl/review/hotel/$id?user_id=$userId"),
    );
    return jsonDecode(response.body);
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
  // Mengirim user_id dan total_harga agar backend yang hitung potongan
  // sehingga kasus voucher nominal > total harga ditangani dengan benar
  static Future<Map<String, dynamic>> checkPromoCode(
    String code,
    String category, {
    required int userId,
    required double totalHarga,
  }) async {
    try {
      final response = await http.post(
        Uri.parse("$_hotelUrl/promo/check"),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode({
          'kode_promo'  : code,
          'kategori'    : category,
          'user_id'     : userId,
          'total_harga' : totalHarga,
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

      if (response.body.startsWith('<')) {
        print("ERROR_HTML: ${response.body}");
        return {'success': false, 'message': 'Server Notif Error'};
      }

      return jsonDecode(response.body);
    } catch (e) {
      return {'success': false, 'message': 'Gagal terhubung ke pusat notifikasi: $e'};
    }
  }

  // 20. FUNGSI AMBIL PROMO AKTIF RESTORAN
  static Future<Map<String, dynamic>> getActivePromo() async {
    try {
      final response = await http.get(
        Uri.parse("$_hotelUrl/resto/active-promo"),
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

  // 21. FUNGSI AMBIL SEMUA PROMO AKTIF (untuk PromoScrollStrip di HomeScreen)
  static Future<Map<String, dynamic>> getActivePromos() async {
    try {
      final response = await http.get(
        Uri.parse("$_hotelUrl/promo/active"),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'success': false, 'data': [], 'message': e.toString()};
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