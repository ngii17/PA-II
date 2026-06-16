<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hotel\Promo;
use App\Models\promo\PromoUsage;
use App\Services\NotificationClientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PromoController extends Controller
{
    protected $notifService;

    public function __construct(NotificationClientService $notifService)
    {
        $this->notifService = $notifService;
    }

    /**
     * 1. TAMPIL DAFTAR PROMO
     */
    public function index()
    {
        if (session('user.role') !== 'admin') {
            return abort(403, 'Akses dilarang.');
        }

        $promos = Promo::orderBy('created_at', 'desc')->get();
        return view('dashboard.promo.index', compact('promos'));
    }

    /**
     * 2. FORM TAMBAH PROMO
     */
    public function create()
    {
        if (session('user.role') !== 'admin') return abort(403);
        return view('dashboard.promo.create');
    }

    /**
     * 3. SIMPAN PROMO BARU + AUTO BROADCAST KE HP
     */
    public function store(Request $request)
    {
        if (session('user.role') !== 'admin') return abort(403);

        $request->validate([
            'nama_promo'       => 'required|string|max:255',
            'kode_promo'       => 'nullable|string|unique:promo,kode_promo',
            'kategori'         => 'required|in:hotel,restoran,semua',
            'tipe_diskon'      => 'required|in:nominal,persen',
            'nominal_potongan' => 'required|numeric|min:0',
            'tgl_mulai'        => 'required|date',
            'tgl_selesai'      => 'required|date|after_or_equal:tgl_mulai',
        ]);

        try {
            $data = $request->all();
            $data['is_active'] = true;
            $promo = Promo::create($data);

            if ($request->boolean('send_notification')) {
                $urlAuth = env('MIKRO_URL') . '/api/internal/user-tokens';
                $authRes = Http::timeout(10)->get($urlAuth);

                if (!$authRes->successful()) {
                    Log::warning('PromoController@store: Gagal mengambil token dari Port 8000. URL: ' . $urlAuth . ' | Status: ' . $authRes->status());
                }

                $recipients = $authRes->successful() ? ($authRes->json('data') ?? []) : [];

                if (!empty($recipients)) {
                    $potonganText = ($promo->tipe_diskon == 'persen')
                        ? $promo->nominal_potongan . "%"
                        : "Rp " . number_format($promo->nominal_potongan, 0, ',', '.');

                    $title = "🎁 Promo Baru: " . $promo->nama_promo;
                    $body  = "Nikmati diskon " . $potonganText . " untuk layanan " . strtoupper($promo->kategori) . ". ";
                    $body .= $promo->kode_promo ? "Gunakan kode: " . $promo->kode_promo : "Diskon otomatis di aplikasi!";

                    $this->notifService->massSend([
                        'recipients' => $recipients,
                        'title'      => $title,
                        'body'       => $body,
                        'type'       => 'promo_broadcast'
                    ]);
                }
            }

            return redirect()->route('dashboard.promo.index')
                ->with('success', 'Promo berhasil dibuat' . ($request->boolean('send_notification') ? ' dan notifikasi telah disebarkan!' : '.'));

        } catch (\Exception $e) {
            Log::error("Auto-Promo Notif Error: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem saat memproses promo.')->withInput();
        }
    }

    /**
     * 4. FORM EDIT PROMO
     */
    public function edit($id)
    {
        if (session('user.role') !== 'admin') return abort(403);
        $promo = Promo::findOrFail($id);
        return view('dashboard.promo.edit', compact('promo'));
    }

    /**
     * 5. UPDATE DATA PROMO
     */
    public function update(Request $request, $id)
    {
        if (session('user.role') !== 'admin') return abort(403);

        $promo = Promo::findOrFail($id);
        $request->validate([
            'nama_promo'       => 'required|string|max:255',
            'kode_promo'       => 'nullable|string|unique:promo,kode_promo,' . $id,
            'kategori'         => 'required|in:hotel,restoran,semua',
            'tipe_diskon'      => 'required|in:nominal,persen',
            'nominal_potongan' => 'required|numeric|min:0',
            'tgl_mulai'        => 'required|date',
            'tgl_selesai'      => 'required|date|after_or_equal:tgl_mulai',
            'is_active'        => 'required|boolean',
        ]);

        $promo->update($request->all());
        return redirect()->route('dashboard.promo.index')->with('success', 'Data promo berhasil diperbarui.');
    }

    /**
     * 6. HAPUS PROMO
     */
    public function destroy($id)
    {
        if (session('user.role') !== 'admin') return abort(403);
        $promo = Promo::findOrFail($id);
        $promo->delete();

        return redirect()->route('dashboard.promo.index')->with('success', 'Promo berhasil dihapus.');
    }

    /**
     * 7. TOGGLE STATUS AKTIF
     */
    public function toggleStatus($id)
    {
        if (session('user.role') !== 'admin') return abort(403);
        $promo = Promo::findOrFail($id);
        $promo->update(['is_active' => !$promo->is_active]);

        return redirect()->back()->with('success', "Status promo berhasil diubah.");
    }

    /**
     * 8. API — LIST SEMUA PROMO AKTIF (untuk Flutter)
     */
    public function activeForApi()
    {
        $promos = Promo::where('is_active', true)
            ->where('tgl_mulai',   '<=', now())
            ->where('tgl_selesai', '>=', now())
            ->orderBy('created_at', 'desc')
            ->get([
                'id', 'nama_promo', 'kode_promo', 'kategori',
                'tipe_diskon', 'nominal_potongan',
                'tgl_mulai', 'tgl_selesai',
            ]);

        return response()->json([
            'success' => true,
            'data'    => $promos,
        ]);
    }

    /**
     * 9. API — CEK & VALIDASI KODE PROMO (untuk Flutter)
     * POST /api/promo/check
     * Body: { kode_promo, user_id, total_harga, kategori }
     */
    public function checkPromo(Request $request)
    {
        $request->validate([
            'kode_promo'  => 'required|string',
            'user_id'     => 'required|integer',
            'total_harga' => 'required|numeric|min:0',
            'kategori'    => 'required|in:hotel,restoran',
        ]);

        $kode       = strtoupper(trim($request->kode_promo));
        $userId     = $request->user_id;
        $totalHarga = (float) $request->total_harga;
        $kategori   = $request->kategori;

        // 1. Cari promo berdasarkan kode
        $promo = Promo::where('kode_promo', $kode)
            ->where('is_active', true)
            ->where('tgl_mulai',   '<=', now())
            ->where('tgl_selesai', '>=', now())
            ->first();

        if (!$promo) {
            return response()->json([
                'success' => false,
                'message' => 'Kode promo tidak ditemukan atau sudah tidak berlaku.',
            ], 404);
        }

        // 2. Cek kategori promo cocok
        if ($promo->kategori !== 'semua' && $promo->kategori !== $kategori) {
            return response()->json([
                'success' => false,
                'message' => 'Promo ini hanya berlaku untuk kategori ' . strtoupper($promo->kategori) . '.',
            ], 422);
        }

        // 3. Cek apakah user sudah pernah pakai promo ini (1 user 1 kali)
        $sudahDipakai = PromoUsage::where('promo_id', $promo->id)
            ->where('user_id', $userId)
            ->exists();

        if ($sudahDipakai) {
            return response()->json([
                'success' => false,
                'message' => 'Kamu sudah pernah menggunakan promo ini sebelumnya.',
            ], 422);
        }

        // 4. Hitung potongan
        if ($promo->tipe_diskon === 'persen') {
            $potongan = $totalHarga * ($promo->nominal_potongan / 100);
        } else {
            // Jika voucher nominal > total harga, potongan maksimal = total harga (tidak minus)
            $potongan = min((float) $promo->nominal_potongan, $totalHarga);
        }

        $totalSetelahDiskon = $totalHarga - $potongan;

        return response()->json([
            'success' => true,
            'message' => 'Promo berhasil diterapkan!',
            'data'    => [
                'promo_id'             => $promo->id,
                'nama_promo'           => $promo->nama_promo,
                'kode_promo'           => $promo->kode_promo,
                'tipe_diskon'          => $promo->tipe_diskon,
                'nominal_potongan'     => $promo->nominal_potongan,
                'potongan_dihitung'    => $potongan,           // nilai potongan aktual
                'total_setelah_diskon' => $totalSetelahDiskon, // total final yang dibayar
            ],
        ]);
    }
}