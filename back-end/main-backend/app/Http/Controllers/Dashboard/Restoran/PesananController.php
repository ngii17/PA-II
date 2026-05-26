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

class PesananController extends Controller
{
    // 1. DAFTAR PESANAN
    public function index()
    {
        $token = session('user.token');
        $response = Http::withToken($token)->get(env('MIKRO_URL') . '/api/users');

        // PENTING: Harus pakai keyBy('id') agar bisa dipanggil $users[$id] di Blade
        $users = collect($response->json('data') ?? [])->keyBy('id');

        $pesanan = PesananMenu::with(['details.menu', 'statusPesanan', 'statusPembayaran'])
            ->orderBy('created_at', 'desc')->get();

        return view('dashboard.restoran.pesanan.index', compact('pesanan', 'users'));
    }

    // --- 2. TAMPIL DETAIL (Penting agar struk tidak error) ---
    public function show($id)
    {
        $token = session('user.token');
        $response = Http::withToken($token)->get(env('MIKRO_URL') . '/api/users');
        $users = collect($response->json('data') ?? [])->keyBy('id');

        // Tambahkan withTrashed() agar menu yang sudah dihapus tetap muncul namanya
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

    // 4. SIMPAN DATA
    // --- 4. SIMPAN DATA (STORE) ---
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'nomor_meja' => 'required|string', // Pastikan ini 'nomor_meja'
            'menu_ids' => 'required|array',
            'jumlah' => 'required|array',
        ]);

        $pesanan = PesananMenu::create([
            'user_id'           => $request->user_id,
            'nomor_meja'        => $request->nomor_meja, // FIX: Kembalikan ke nomor_meja
            'total_harga'       => 0,
            'metode_pembayaran' => $request->metode_pembayaran ?? 'Tunai',
            'status_pembayaran_id' => 1,
            'status_pesanan_id'    => 1,
        ]);

        // ... (logika looping detail_pesanan tetap sama) ...
        $total = 0;
        foreach ($request->menu_ids as $key => $mId) {
            $menu = Menu::find($mId);
            $qty = $request->jumlah[$key];
            \App\Models\restoran\DetailPesanan::create([
                'pesanan_menu_id' => $pesanan->id,
                'menu_id' => $mId,
                'jumlah' => $qty,
                'harga_at_porsi' => $menu->harga,
                'status_pesanan_id' => 1
            ]);
            $total += ($menu->harga * $qty);
        }
        $pesanan->update(['total_harga' => $total]);

        return redirect()->route('dashboard.restoran.pesanan.index')->with('success', 'Pesanan Meja ' . $request->nomor_meja . ' berhasil dibuat!');
    }

    // 5. FORM EDIT
        public function edit($id)
    {
        // Tambahkan withTrashed() agar menu yang sudah dihapus tetap muncul namanya
        $pesanan = PesananMenu::with(['details.menu' => function($q) {
            $q->withTrashed();
        }])->findOrFail($id);

        $statusList = StatusPesanan::all();
        $paymentStatusList = StatusPembayaran::all();

        return view('dashboard.restoran.pesanan.edit', compact('pesanan', 'statusList', 'paymentStatusList'));
    }

    // 6. UPDATE
        // --- 6. UPDATE DATA ---
    public function update(Request $request, $id)
    {
        $pesanan = PesananMenu::findOrFail($id);

        $request->validate([
            'nomor_meja' => 'required|string', // FIX: Pastikan ini nomor_meja
            'status_pesanan_id' => 'required|exists:status_pesanan,id',
            'status_pembayaran_id' => 'required|exists:status_pembayaran,id',
        ]);

        // FIX: Gunakan $request->nomor_meja
        $pesanan->update([
            'nomor_meja' => $request->nomor_meja,
            'status_pesanan_id' => $request->status_pesanan_id,
            'status_pembayaran_id' => $request->status_pembayaran_id,
        ]);

        return redirect()->route('dashboard.restoran.pesanan.index')->with('success', 'Data pesanan Meja ' . $request->nomor_meja . ' berhasil diperbarui!');
    }

    // 7. HAPUS / BATALKAN
    public function destroy($id)
    {
        $pesanan = PesananMenu::findOrFail($id);

        // Tentukan kalimat pesan berdasarkan status sebelum dihapus
        if ($pesanan->status_pembayaran_id == 2) {
            $pesan = 'Pesanan yang telah lunas berhasil diarsipkan (Soft Delete).';
        } else {
            $pesan = 'Pesanan berhasil dibatalkan dan dipindahkan ke arsip.';
        }

        // Jalankan Soft Delete (Data tetap aman di database)
        $pesanan->delete();

        return redirect()->back()->with('success', $pesan);
    }
}
