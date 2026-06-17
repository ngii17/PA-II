<?php

namespace App\Http\Controllers\Dashboard\Restoran;

use App\Http\Controllers\Controller;
use App\Models\Restoran\PesananMenu;
use App\Models\Restoran\DetailPesanan;
use App\Models\Restoran\Menu;
use App\Models\Restoran\StatusPesanan;
use App\Models\Restoran\StatusPembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\NotificationClientService;

class PesananController extends Controller
{
    protected $notifService;

    public function __construct(NotificationClientService $notifService)
    {
        $this->notifService = $notifService;
    }

    // 1. DAFTAR PESANAN
    public function index()
    {
        $token = session('user.token');
        $response = Http::withToken($token)->get(env('MIKRO_URL') . '/api/users');
        $users = collect($response->json('data') ?? [])->keyBy('id');

        $pesanan = PesananMenu::with(['details.menu', 'statusPesanan', 'statusPembayaran'])
            ->orderBy('created_at', 'desc')->get();

        return view('dashboard.restoran.pesanan.index', compact('pesanan', 'users'));
    }

    // 2. TAMPIL DETAIL
    public function show($id)
    {
        $token = session('user.token');
        $response = Http::withToken($token)->get(env('MIKRO_URL') . '/api/users');
        $users = collect($response->json('data') ?? [])->keyBy('id');

        $pesanan = PesananMenu::with(['details.menu' => function($q) {
            $q->withTrashed();
        }, 'statusPesanan', 'statusPembayaran'])->findOrFail($id);

        return view('dashboard.restoran.pesanan.show', compact('pesanan', 'users'));
    }

    // 3. FORM TAMBAH
    public function create()
    {
        $token = session('user.token');
        $response = Http::withToken($token)->get(env('MIKRO_URL') . '/api/users');
        $customers = collect($response->json('data') ?? [])->filter(fn($u) => $u['role_id'] == 2);
        $menus = Menu::where('stok', '>', 0)->get();

        return view('dashboard.restoran.pesanan.create', compact('customers', 'menus'));
    }

    // 4. SIMPAN DATA (STORE) + SINKRONISASI STOK
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'nomor_meja' => 'required|string',
            'menu_ids' => 'required|array',
            'jumlah' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            $pesanan = PesananMenu::create([
                'user_id'           => $request->user_id,
                'nomor_lokasi'      => $request->nomor_meja,
                'total_harga'       => 0,
                'metode_pembayaran' => $request->metode_pembayaran ?? 'Tunai',
                'status_pembayaran_id' => 1,
                'status_pesanan_id'    => 1,
                'tipe_pengantaran'  => 'Meja',
                'fcm_token'         => null, 
            ]);

            $total = 0;
            foreach ($request->menu_ids as $key => $mId) {
                $menu = Menu::find($mId);
                $qty = $request->jumlah[$key];

                // --- SINKRONISASI STOK: Kurangi stok di database ---
                if ($menu->stok < $qty) {
                    throw new \Exception("Stok {$menu->nama_menu} tidak mencukupi.");
                }
                $menu->decrement('stok', $qty);

                DetailPesanan::create([
                    'pesanan_menu_id' => $pesanan->id,
                    'menu_id' => $mId,
                    'jumlah' => $qty,
                    'harga_at_porsi' => $menu->harga,
                    'status_pesanan_id' => 1
                ]);
                $total += ($menu->harga * $qty);
            }
            $pesanan->update(['total_harga' => $total]);

            DB::commit();
            return redirect()->route('dashboard.restoran.pesanan.index')->with('success', 'Pesanan berhasil dibuat & Stok telah dipotong.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    // 5. FORM EDIT
    public function edit($id)
    {
        $pesanan = PesananMenu::with(['details.menu' => function($q) {
            $q->withTrashed();
        }])->findOrFail($id);

        $statusList = StatusPesanan::all();
        $paymentStatusList = StatusPembayaran::all();

        return view('dashboard.restoran.pesanan.edit', compact('pesanan', 'statusList', 'paymentStatusList'));
    }

    // 6. UPDATE DATA + SINKRONISASI NOTIFIKASI
    public function update(Request $request, $id)
    {
        $pesanan = PesananMenu::findOrFail($id);
        
        $statusPesananLama = $pesanan->status_pesanan_id;
        $statusBayarLama   = $pesanan->status_pembayaran_id;

        $request->validate([
            'nomor_meja' => 'required|string',
            'status_pesanan_id' => 'required|exists:status_pesanan,id',
            'status_pembayaran_id' => 'required|exists:status_pembayaran,id',
        ]);

        $pesanan->update([
            'nomor_lokasi'         => $request->nomor_meja,
            'status_pesanan_id'    => $request->status_pesanan_id,
            'status_pembayaran_id' => $request->status_pembayaran_id,
        ]);

        // ============================================================
        // --- LOGIKA PEMICU NOTIFIKASI SINKRON HP ---
        // ============================================================
        
        // A. JIKA STATUS BERUBAH JADI "DISAJIKAN" (ID 3)
        if ($statusPesananLama != 3 && $request->status_pesanan_id == 3) {
             try {
                 $this->notifService->orderReady($pesanan->fcm_token ?? 'no_token', $pesanan->user_id, $pesanan->id);
             } catch (\Exception $e) { Log::error("Notif Error: " . $e->getMessage()); }
        }

        // B. JIKA STATUS BAYAR BERUBAH JADI "LUNAS" (ID 2)
        if ($statusBayarLama != 2 && $request->status_pembayaran_id == 2) {
            try {
                $this->notifService->orderConfirmed(
                    $pesanan->fcm_token ?? 'no_token', 
                    $pesanan->user_id, 
                    $pesanan->id, 
                    (float)$pesanan->total_harga
                );
            } catch (\Exception $e) { Log::error("Notif Error: " . $e->getMessage()); }
        }
        
        return redirect()->route('dashboard.restoran.pesanan.index')->with('success', 'Status Pesanan & Notifikasi Berhasil Diperbarui!');
    }

    // 7. HAPUS / BATALKAN
    public function destroy($id)
    {
        $pesanan = PesananMenu::findOrFail($id);
        $pesan = ($pesanan->status_pembayaran_id == 2) 
            ? 'Pesanan lunas telah diarsipkan.' 
            : 'Pesanan berhasil dibatalkan.';

        $pesanan->delete();
        return redirect()->back()->with('success', $pesan);
    }
}