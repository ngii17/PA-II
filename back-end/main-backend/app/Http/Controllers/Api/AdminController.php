<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NotificationClientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    protected $notifService;
    protected $authServiceUrl;

    public function __construct(NotificationClientService $notifService)
    {
        $this->notifService = $notifService;
        // URL Auth Service kamu
        $this->authServiceUrl = "http://10.223.75.132:8000/api"; 
    }

    /**
     * FITUR BROADCAST: Kirim Notifikasi ke Seluruh User Terdaftar
     */
    public function broadcastToAll(Request $request)
    {
        // 1. Validasi Input Admin
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:150',
            'body'  => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            // 2. Ambil semua token dari Auth-Service (Port 8000) via HTTP
            $authResponse = Http::get($this->authServiceUrl . '/internal/user-tokens');
            
            if (!$authResponse->successful()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Gagal mengambil data user dari Auth Service'
                ], 500);
            }

            $recipients = $authResponse->json()['data'];

            if (empty($recipients)) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Tidak ada user yang memiliki token aktif.'
                ], 404);
            }

            // 3. Kirim daftar token & pesan ke Notification-Service (Port 8002)
            // Kita gunakan method massSend yang akan kita buat di NotificationClientService
            $result = $this->notifService->massSend([
                'recipients' => $recipients,
                'title'      => $request->title,
                'body'       => $request->body,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Broadcast sedang diproses oleh sistem notifikasi.',
                'total_penerima' => count($recipients),
                'detail_service' => $result
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}