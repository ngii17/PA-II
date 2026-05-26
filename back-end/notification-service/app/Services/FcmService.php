<?php

namespace App\Services;

use GuzzleHttp\Client;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\HttpHandlerFactory;
use Illuminate\Support\Facades\Log;
use Exception;

class FcmService
{
    protected $client;
    protected $projectId;
    protected $keyFile;

    public function __construct()
    {
        $this->client = new Client();
        $this->projectId = config('services.fcm.project_id');
        $this->keyFile = config('services.fcm.key_file');
    }

    private function getAccessToken()
    {
        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];
        $creds = new ServiceAccountCredentials($scopes, $this->keyFile);
        $token = $creds->fetchAuthToken(HttpHandlerFactory::build($this->client));
        return $token['access_token'];
    }

    public function send($fcmToken, $title, $body, $data = [])
    {
        try {
            $accessToken = $this->getAccessToken();
            $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

            // Tambahkan konfigurasi Android agar Notif melayang (Swipe-in)
            $payload = [
                'message' => [
                    'token' => $fcmToken,
                    'notification' => [
                        'title' => $title,
                        'body'  => $body,
                    ],
                    // --- TAMBAHAN KHUSUS UNTUK NOTIF MELAYANG (HEADS-UP) ---
                    'android' => [
                        'priority' => 'high', // Memberitahu Google ini pesan mendesak
                        'notification' => [
                            'channel_id' => 'purnama_high_importance_channel', // WAJIB: Sama dengan di Flutter
                            'priority' => 'high', // Prioritas di tingkat sistem Android
                            'sound' => 'default',
                        ],
                    ],
                    'apns' => [ // Untuk pengguna iPhone agar tetap konsisten
                        'payload' => [
                            'aps' => [
                                'sound' => 'default',
                                'content-available' => 1,
                            ],
                        ],
                    ],
                    // -------------------------------------------------------
                    'data' => array_map('strval', $data), 
                ]
            ];

            $response = $this->client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type'  => 'application/json',
                ],
                'json' => $payload,
            ]);

            return $response->getStatusCode() === 200;

        } catch (Exception $e) {
            Log::error('FCM_V1_SEND_ERROR: ' . $e->getMessage());
            return false;
        }
    }
}