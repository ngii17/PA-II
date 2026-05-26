import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';

// --- WAJIB: Fungsi ini harus di luar class (Top Level) agar Background Notif Jalan ---
@pragma('vm:entry-point')
Future<void> _firebaseMessagingBackgroundHandler(RemoteMessage message) async {
  await Firebase.initializeApp();
  print("Menangani pesan latar belakang: ${message.messageId}");
}

class PushNotificationService {
  static final FirebaseMessaging _fcm = FirebaseMessaging.instance;
  static final FlutterLocalNotificationsPlugin _localNotifications = FlutterLocalNotificationsPlugin();

  // 1. DEFINISI CHANNEL TINGKAT TINGGI (Kunci agar Notif muncul dari atas/Heads-up)
  static const AndroidNotificationChannel _purnamaChannel = AndroidNotificationChannel(
    'purnama_high_importance_channel', // ID unik
    'Notifikasi Transaksi Purnama',      // Nama di pengaturan HP
    description: 'Notifikasi konfirmasi booking dan pesanan restoran',
    importance: Importance.max,         // WAJIB: Max agar melayang (Swipe-in)
    playSound: true,
    enableVibration: true,
  );

  // 2. Inisialisasi Utama
  static Future<void> initialize() async {
    // Minta Izin penuh ke User (Android 13+ & iOS)
    NotificationSettings settings = await _fcm.requestPermission(
      alert: true,
      badge: true,
      sound: true,
    );

    if (settings.authorizationStatus == AuthorizationStatus.authorized) {
      print('Izin Notifikasi Diberikan');
    }

    // Daftarkan Background Handler
    FirebaseMessaging.onBackgroundMessage(_firebaseMessagingBackgroundHandler);

    // Daftarkan Channel ke sistem Android
    await _localNotifications
        .resolvePlatformSpecificImplementation<AndroidFlutterLocalNotificationsPlugin>()
        ?.createNotificationChannel(_purnamaChannel);

    // Konfigurasi Ikon & Settings Inisialisasi
    const AndroidInitializationSettings androidSettings = 
        AndroidInitializationSettings('@mipmap/ic_launcher');
    const DarwinInitializationSettings iosSettings = DarwinInitializationSettings();

    const InitializationSettings initSettings = InitializationSettings(
      android: androidSettings,
      iOS: iosSettings,
    );

    // Initialisasi Plugin
    await _localNotifications.initialize(
      initSettings,
      onDidReceiveNotificationResponse: (NotificationResponse response) {
        if (response.payload != null) {
          print("Notif diklik: ${response.payload}");
          // Di sini Anda bisa menambahkan navigasi ke halaman NotificationScreen
        }
      },
    );

    // --- HANDLING FOREGROUND (Saat aplikasi sedang dibuka) ---
    FirebaseMessaging.onMessage.listen((RemoteMessage message) {
      print("Pesan masuk saat aplikasi terbuka!");
      _showLocalNotification(message);
    });

    // --- HANDLING BACKGROUND CLICK (Saat notif diklik dari tray) ---
    FirebaseMessaging.onMessageOpenedApp.listen((RemoteMessage message) {
      print("Aplikasi terbuka karena notif diklik: ${message.data}");
    });
  }

  // 3. Fungsi Ambil Token FCM (Kirim ini ke Laravel Database)
  static Future<String?> getDeviceToken() async {
    return await _fcm.getToken();
  }

  // 4. Fungsi Menampilkan Notifikasi (Popup/Heads-up)
  static void _showLocalNotification(RemoteMessage message) {
    RemoteNotification? notification = message.notification;

    if (notification != null) {
      _localNotifications.show(
        notification.hashCode, // ID unik notif
        notification.title,    // Judul
        notification.body,     // Isi
        NotificationDetails(
          android: AndroidNotificationDetails(
            _purnamaChannel.id,
            _purnamaChannel.name,
            channelDescription: _purnamaChannel.description,
            importance: Importance.max,
            priority: Priority.high,
            ticker: 'ticker',
            icon: '@mipmap/ic_launcher',
            // Menambah efek visual premium
            enableLights: true,
            color: const Color(0xFF00197D), // Warna Navy Hotel Purnama
          ),
          iOS: const DarwinNotificationDetails(
            presentAlert: true,
            presentBadge: true,
            presentSound: true,
          ),
        ),
        payload: jsonEncode(message.data),
      );
    }
  }
}