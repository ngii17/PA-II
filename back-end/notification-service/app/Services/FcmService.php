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

    /**
     * Fungsi untuk mendapatkan Access Token dari Google secara otomatis (v1)
     */
    private function getAccessToken()
    {
        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];
        $creds = new ServiceAccountCredentials($scopes, $this->keyFile);
        $token = $creds->fetchAuthToken(HttpHandlerFactory::build($this->client));
        return $token['access_token'];
    }

    /**
     * Method send() versi HTTP v1
     */
    public function send($fcmToken, $title, $body, $data = [])
    {
        try {
            $accessToken = $this->getAccessToken();
            $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

            // Struktur JSON v1 berbeda dengan Legacy (Wajib dibungkus objek 'message')
            $payload = [
                'message' => [
                    'token' => $fcmToken,
                    'notification' => [
                        'title' => $title,
                        'body'  => $body,
                    ],
                    'data' => array_map('strval', $data), // Semua data di v1 wajib String
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