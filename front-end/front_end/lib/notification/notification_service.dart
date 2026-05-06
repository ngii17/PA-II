import 'dart:convert';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';

class PushNotificationService {
  static final FirebaseMessaging _fcm = FirebaseMessaging.instance;
  static final FlutterLocalNotificationsPlugin _localNotifications = FlutterLocalNotificationsPlugin();

  // 1. Inisialisasi Firebase & Notifikasi Lokal
  static Future<void> initialize() async {
    // Minta izin ke user (untuk Android 13+)
    await _fcm.requestPermission();

    // Konfigurasi Ikon Android
    const AndroidInitializationSettings androidSettings = 
        AndroidInitializationSettings('@mipmap/ic_launcher');
    
    // Konfigurasi iOS/Darwin
    const DarwinInitializationSettings iosSettings = DarwinInitializationSettings();

    const InitializationSettings initSettings = InitializationSettings(
      android: androidSettings,
      iOS: iosSettings,
    );

    // --- PERBAIKAN: Menggunakan label 'settings' sesuai permintaan VS Code kamu ---
    await _localNotifications.initialize(
      settings: initSettings,
      onDidReceiveNotificationResponse: (NotificationResponse response) {
        if (response.payload != null) {
          Map<String, dynamic> data = jsonDecode(response.payload!);
          print("Notif diklik dengan data: $data");
        }
      },
    );

    // Mendengarkan pesan saat aplikasi sedang dibuka (Foreground)
    FirebaseMessaging.onMessage.listen((RemoteMessage message) {
      _showLocalNotification(message);
    });
  }

  // 2. Fungsi untuk mendapatkan Token HP yang ASLI
  static Future<String?> getDeviceToken() async {
    return await _fcm.getToken();
  }

  // 3. Menampilkan Pesan Melayang (Pop-up)
  static void _showLocalNotification(RemoteMessage message) {
    const AndroidNotificationDetails androidDetail = AndroidNotificationDetails(
      'purnama_channel', 
      'Purnama Notif',
      importance: Importance.max,
      priority: Priority.high,
      playSound: true,
    );

    const NotificationDetails platformDetail = NotificationDetails(
      android: androidDetail,
    );

    // --- PERBAIKAN: Menggunakan label lengkap agar tidak error 'positional arguments' ---
    _localNotifications.show(
      id: DateTime.now().millisecond, 
      title: message.notification?.title ?? "Purnama Hotel",
      body: message.notification?.body ?? "",
      notificationDetails: platformDetail,
      payload: jsonEncode(message.data),
    );
  }
}