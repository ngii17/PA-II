<?php

namespace App\Http\Controllers\Dashboard\Restoran;

use App\Http\Controllers\Controller;
use App\Models\restoran\PesananMenu;
use App\Models\restoran\DetailPesanan;
use App\Models\restoran\Menu;
use App\Models\restoran\StatusPesanan;
use App\Models\restoran\StatusPembayaran;
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

    // 4. SIMPAN DATA (STORE)
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
            // FIX: Gunakan 'nomor_lokasi' (kolom DB) untuk menyimpan input 'nomor_meja'
            $pesanan = PesananMenu::create([
                'user_id'           => $request->user_id,
                'nomor_lokasi'      => $request->nomor_meja, // <--- PERBAIKAN SINKRONISASI
                'total_harga'       => 0,
                'metode_pembayaran' => $request->metode_pembayaran ?? 'Tunai',
                'status_pembayaran_id' => 1,
                'status_pesanan_id'    => 1,
                'tipe_pengantaran'  => 'Meja', // Default jika buat dari dashboard resto
            ]);

            $total = 0;
            foreach ($request->menu_ids as $key => $mId) {
                $menu = Menu::find($mId);
                $qty = $request->jumlah[$key];
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
            return redirect()->route('dashboard.restoran.pesanan.index')->with('success', 'Pesanan Meja ' . $request->nomor_meja . ' berhasil dibuat!');
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

    // 6. UPDATE DATA (DENGAN FIX KOLOM nomor_lokasi)
    public function update(Request $request, $id)
    {
        $pesanan = PesananMenu::findOrFail($id);
        
        // Simpan status lama untuk pemicu notifikasi
        $statusPesananLama = $pesanan->status_pesanan_id;
        $statusBayarLama   = $pesanan->status_pembayaran_id;

        $pesanan->update([
            'nomor_lokasi'         => $request->nomor_meja,
            'status_pesanan_id'    => $request->status_pesanan_id, // <--- SEKARANG INI SUDAH ADA DI DB
            'status_pembayaran_id' => $request->status_pembayaran_id,
        ]);

        // ... (Logika notifikasi ke HP tetap di bawah sini) ...
        if ($statusPesananLama != 3 && $request->status_pesanan_id == 3) {
             $this->notifService->orderReady($pesanan->fcm_token, $pesanan->user_id, $pesanan->id);
        }
        
        return redirect()->route('dashboard.restoran.pesanan.index')->with('success', 'Data diperbarui!');
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