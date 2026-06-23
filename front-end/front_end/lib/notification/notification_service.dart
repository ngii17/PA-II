import 'dart:convert';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';

class PushNotificationService {
  static final FirebaseMessaging _fcm = FirebaseMessaging.instance;
  static final FlutterLocalNotificationsPlugin _localNotifications =
      FlutterLocalNotificationsPlugin();

  // 1. Inisialisasi Firebase & Notifikasi Lokal
  static Future<void> initialize() async {
    // Minta izin ke user (untuk Android 13+)
    await _fcm.requestPermission(
      alert: true,
      badge: true,
      sound: true,
    );

    // Konfigurasi Ikon Android
    const AndroidInitializationSettings androidSettings =
        AndroidInitializationSettings('@mipmap/ic_launcher');

    // Konfigurasi iOS/Darwin
    const DarwinInitializationSettings iosSettings =
        DarwinInitializationSettings();

    const InitializationSettings initSettings = InitializationSettings(
      android: androidSettings,
      iOS: iosSettings,
    );

    await _localNotifications.initialize(
      initSettings,
      onDidReceiveNotificationResponse: (NotificationResponse response) {
        if (response.payload != null) {
          Map<String, dynamic> data = jsonDecode(response.payload!);
          print("Notif diklik dengan data: $data");
        }
      },
    );

    // WAJIB: Buat channel notifikasi secara eksplisit di Android
    const AndroidNotificationChannel channel = AndroidNotificationChannel(
      'purnama_channel',
      'Purnama Notif',
      description: 'Notifikasi aplikasi Hotel & Resto Purnama',
      importance: Importance.max,
    );

    final AndroidFlutterLocalNotificationsPlugin? androidPlugin =
        _localNotifications.resolvePlatformSpecificImplementation<AndroidFlutterLocalNotificationsPlugin>();
    await androidPlugin?.createNotificationChannel(channel);

    // Mendengarkan pesan saat aplikasi sedang dibuka (Foreground)
    FirebaseMessaging.onMessage.listen((RemoteMessage message) {
      print("FOREGROUND NOTIF MASUK");
      print(message.notification?.title);
      print(message.notification?.body);
      _showLocalNotification(message);
    });
  }

  // 2. Fungsi untuk mendapatkan Token HP yang ASLI
  static Future<String?> getDeviceToken() async {
    return await _fcm.getToken();
  }

  // 3. Menampilkan Pesan Melayang (Pop-up)
  static void _showLocalNotification(RemoteMessage message) {
    final notification = message.notification;
    final data = message.data;

    final String title =
        notification?.title ?? data['title'] ?? "Purnama Hotel";
    final String body = notification?.body ?? data['body'] ?? "";

    final AndroidNotificationDetails androidDetail =
        AndroidNotificationDetails(
      'purnama_channel',
      'Purnama Notif',
      channelDescription: 'Notifikasi aplikasi Hotel & Resto Purnama',
      importance: Importance.max,
      priority: Priority.high,
      playSound: true,
      icon: '@mipmap/ic_launcher',
    );

    final NotificationDetails platformDetail = NotificationDetails(
      android: androidDetail,
    );

    _localNotifications.show(
      DateTime.now().millisecondsSinceEpoch ~/ 1000,
      title,
      body,
      platformDetail,
      payload: jsonEncode(data),
    );
  }
}