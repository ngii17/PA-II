<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Hotel\Promo; // Pastikan Model Promo diimport
use App\Services\NotificationClientService; // Import Service Notifikasi
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // <--- FIX: Import HTTP Facade
use Illuminate\Support\Facades\Log;  // <--- FIX: Import Log Facade
use Illuminate\Support\Facades\Validator;

class PromoController extends Controller
{
    protected $notifService;

    /**
     * CONSTRUCTOR: Inject Service Notifikasi
     */
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
            // A. Simpan ke Database Lokal (Port 8001)
            $data = $request->all();
            $data['is_active'] = true;
            $promo = Promo::create($data);

            // B. CEK JIKA ADMIN MENCENTANG OPSI KIRIM NOTIFIKASI
            if ($request->send_notification == "1") {
                
                // 1. Ambil seluruh token HP dari Port 8000 via API Internal
                $urlAuth = env('MIKRO_URL', 'http://10.187.82.132:8000') . '/api/internal/user-tokens';
                $authRes = Http::timeout(10)->get($urlAuth);
                $recipients = $authRes->json('data') ?? [];

                if (!empty($recipients)) {
                    // 2. Siapkan Kalimat Pesan Otomatis
                    $potonganText = ($promo->tipe_diskon == 'persen') 
                        ? $promo->nominal_potongan . "%" 
                        : "Rp " . number_format($promo->nominal_potongan, 0, ',', '.');
                    
                    $title = "🎁 Promo Baru: " . $promo->nama_promo;
                    $body  = "Nikmati diskon " . $potonganText . " untuk layanan " . strtoupper($promo->kategori) . ". ";
                    $body .= $promo->kode_promo ? "Gunakan kode: " . $promo->kode_promo : "Diskon otomatis di aplikasi!";

                    // 3. Tembak ke Port 8002 (Kirim Pop-up massal)
                    $this->notifService->massSend([
                        'recipients' => $recipients,
                        'title'      => $title,
                        'body'       => $body,
                        'type'       => 'promo_broadcast'
                    ]);
                }
            }

            return redirect()->route('dashboard.promo.index')
                ->with('success', 'Promo berhasil dibuat' . ($request->send_notification == "1" ? ' dan notifikasi telah disebarkan!' : '.'));

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
     * 7. TOGGLE STATUS AKTIF (FITUR CEPAT)
     */
    public function toggleStatus($id)
    {
        if (session('user.role') !== 'admin') return abort(403);
        $promo = Promo::findOrFail($id);
        $promo->update(['is_active' => !$promo->is_active]);

        return redirect()->back()->with('success', "Status promo berhasil diubah.");
    }
}