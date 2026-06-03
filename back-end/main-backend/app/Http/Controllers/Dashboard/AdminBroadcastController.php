<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\BroadcastNotification;
use App\Services\NotificationClientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdminBroadcastController extends Controller
{
    protected $notifService;

    public function __construct(NotificationClientService $notifService)
    {
        $this->notifService = $notifService;
    }

    public function index()
    {
        $broadcasts = BroadcastNotification::orderBy('created_at', 'desc')->get();
        return view('dashboard.broadcast.index', compact('broadcasts'));
    }

    public function create()
    {
        return view('dashboard.broadcast.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'      => 'required|string|max:150',
            'body'       => 'required',
            'start_date' => 'required|date', // SINKRON DENGAN DB
            'end_date'   => 'required|date|after_or_equal:start_date', // SINKRON DENGAN DB
        ]);

        // Laravel akan otomatis mencocokkan nama input dengan kolom DB
        BroadcastNotification::create($request->all());

        return redirect()->route('dashboard.admin.broadcast.index')
                         ->with('success', 'Draft pengumuman berhasil disimpan.');
    }

    /**
     * FUNGSI KIRIM MASSAL KE SELURUH HP USER
     */
    public function send($id)
    {
        $broadcast = BroadcastNotification::findOrFail($id);

        try {
            // 1. Ambil seluruh token HP dari Auth-Service (Port 8000)
            $urlAuth = env('MIKRO_URL', 'http://10.187.82.132:8000') . '/api/internal/user-tokens';
            $authRes = Http::timeout(10)->get($urlAuth);
            
            if (!$authRes->successful()) {
                return back()->with('error', 'Gagal mengambil daftar token dari Server Auth.');
            }

            $recipients = $authRes->json('data');

            if (empty($recipients)) {
                return back()->with('error', 'Tidak ada pengguna terdaftar yang memiliki alamat HP (Token).');
            }

            // 2. Tembak ke Notification-Service (Port 8002)
            $this->notifService->massSend([
                'recipients' => $recipients,
                'title'      => $broadcast->title,
                'body'       => $broadcast->body,
                'type'       => 'broadcast_admin' // Tipe khusus agar ikon di HP muncul ikon TOA/Info
            ]);

            // 3. Update Status
            $broadcast->update(['status' => 'sent']);

            return redirect()->route('dashboard.admin.broadcast.index')
                             ->with('success', 'Notifikasi sedang dikirim ke ' . count($recipients) . ' pelanggan!');

        } catch (\Exception $e) {
            Log::error("Broadcast Error: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem pengiriman.');
        }
    }

    public function destroy($id)
    {
        BroadcastNotification::findOrFail($id)->delete();
        return back()->with('success', 'Data pengumuman dihapus.');
    }
}