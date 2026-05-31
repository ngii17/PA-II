<?php

namespace App\Http\Controllers\Dashboard\Hotel;

use App\Http\Controllers\Controller;
use App\Models\hotel\Reservasi;
use App\Models\hotel\StatusReservasi;
use App\Models\hotel\TipeKamar;
use App\Models\hotel\Kamar;
use App\Models\hotel\Promo; // Tambahkan untuk sinkronisasi harga
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\NotificationClientService;

class ReservasiHotelController extends Controller
{
    protected $notifService;

    public function __construct(NotificationClientService $notifService)
    {
        $this->notifService = $notifService;
    }

    // 1. TAMPIL DAFTAR
    public function index()
    {
        $token = session('user.token');
        $response = Http::withToken($token)->get(env('MIKRO_URL') . '/api/users');
        $users = collect($response->json('data') ?? [])->keyBy('id');

        $reservasi = Reservasi::with(['tipeKamar', 'statusReservasi', 'kamar'])
            ->orderBy('created_at', 'desc')->get();

        $totalReservasi = $reservasi->count();
        $terbayarCount = $reservasi->whereIn('status_reservasi_id', [2, 3])->count();
        $pendingCount = $reservasi->where('status_reservasi_id', 1)->count();
        $totalPendapatan = $reservasi->whereIn('status_reservasi_id', [2, 3, 4])->sum('total_harga');

        return view('dashboard.hotel.reservasi.index', compact(
            'reservasi', 'users', 'totalReservasi', 'terbayarCount', 'pendingCount', 'totalPendapatan'
        ));
    }

    // 2. TAMPIL DETAIL
    public function show($id)
    {
        $token = session('user.token');
        $response = Http::withToken($token)->get(env('MIKRO_URL') . '/api/users');
        $users = collect($response->json('data') ?? [])->keyBy('id');

        $reservasi = Reservasi::with(['tipeKamar', 'statusReservasi', 'kamar'])->withTrashed()->findOrFail($id);

        return view('dashboard.hotel.reservasi.show', compact('reservasi', 'users'));
    }

    // 3. FORM TAMBAH
    public function create()
    {
        $token = session('user.token');
        $response = Http::withToken($token)->get(env('MIKRO_URL') . '/api/users');
        $customers = collect($response->json('data') ?? [])->filter(fn($u) => $u['role_id'] == 2);

        $tipeKamar = TipeKamar::all();
        $kamar = Kamar::with('tipeKamar')->where('status_kamar_id', 1)->get(); 
        $statusList = StatusReservasi::all();

        return view('dashboard.hotel.reservasi.create', compact('customers', 'tipeKamar', 'kamar', 'statusList'));
    }

    // 4. SIMPAN RESERVASI BARU (DENGAN SINKRONISASI PROMO)
    public function store(Request $request)
    {
        $request->validate([
            'user_id'             => 'required',
            'tipe_kamar_id'       => 'required|exists:tipe_kamar,id',
            'kamar_id'            => 'required|exists:kamar,id',
            'tgl_checkin'         => 'required|date',
            'tgl_checkout'        => 'required|date|after:tgl_checkin',
            'status_reservasi_id' => 'required|exists:status_reservasi,id',
        ]);

        try {
            $tipeKamar = TipeKamar::findOrFail($request->tipe_kamar_id);
            
            // --- SINKRONISASI HARGA PROMO ---
            $promoAktif = Promo::where('kategori', 'hotel')->where('is_active', true)
                ->whereNull('kode_promo')->whereDate('tgl_mulai', '<=', now())
                ->whereDate('tgl_selesai', '>=', now())->first();

            $hargaPerMalam = $tipeKamar->harga;
            if ($promoAktif) {
                $potongan = ($promoAktif->tipe_diskon == 'persen') 
                    ? $hargaPerMalam * ($promoAktif->nominal_potongan / 100) 
                    : $promoAktif->nominal_potongan;
                $hargaPerMalam = $hargaPerMalam - $potongan;
            }

            $checkIn   = \Carbon\Carbon::parse($request->tgl_checkin);
            $checkOut  = \Carbon\Carbon::parse($request->tgl_checkout);
            $malam     = $checkIn->diffInDays($checkOut);
            $malam     = ($malam < 1) ? 1 : $malam;
            $totalHarga = $malam * $hargaPerMalam;

            Reservasi::create([
                'user_id'             => $request->user_id,
                'tipe_kamar_id'       => $request->tipe_kamar_id,
                'kamar_id'            => $request->kamar_id,
                'tgl_checkin'         => $request->tgl_checkin,
                'tgl_checkout'        => $request->tgl_checkout,
                'total_malam'         => $malam,
                'total_harga'         => $totalHarga,
                'status_reservasi_id' => $request->status_reservasi_id,
                'metode_pembayaran'   => 'Manual (Resepsionis)',
            ]);

            return redirect()->route('dashboard.hotel.reservasi.index')->with('success', 'Reservasi berhasil dibuat.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal: ' . $e->getMessage()])->withInput();
        }
    }

    // 5. FORM EDIT
    public function edit($id)
    {
        $reservasi  = Reservasi::findOrFail($id);
        $tipeKamar  = TipeKamar::all();
        $kamar      = Kamar::all();
        $statusList = StatusReservasi::all();
        return view('dashboard.hotel.reservasi.edit', compact('reservasi', 'tipeKamar', 'kamar', 'statusList'));
    }

    // 6. UPDATE (LOGIKA NOTIFIKASI & MANAGEMENT KAMAR)
    public function update(Request $request, $id)
    {
        $reservasi = Reservasi::findOrFail($id);
        $statusLama = $reservasi->status_reservasi_id;
        $kamarLamaId = $reservasi->kamar_id;

        DB::beginTransaction();
        try {
            $reservasi->update([
                'tipe_kamar_id'       => $request->tipe_kamar_id,
                'kamar_id'            => $request->kamar_id,
                'tgl_checkin'         => $request->tgl_checkin,
                'tgl_checkout'        => $request->tgl_checkout,
                'status_reservasi_id' => $request->status_reservasi_id,
            ]);

            // --- MANAJEMEN STATUS KAMAR FISIK ---
            
            // A. Jika Status berubah jadi CHECK-IN (ID 3)
            if ($statusLama != 3 && $request->status_reservasi_id == 3) {
                // Update kamar baru jadi Terisi
                Kamar::where('id', $request->kamar_id)->update(['status_kamar_id' => 2]);
                
                // Ambil info nomor kamar terbaru untuk notifikasi
                $kamarData = Kamar::find($request->kamar_id);

                $this->notifService->sendCheckinSuccess(
                    $reservasi->fcm_token ?? 'no_token',
                    $reservasi->user_id,
                    $kamarData->nomor_kamar ?? '?'
                );
            }

            // B. Jika Status berubah jadi SELESAI/CHECK-OUT (ID 4)
            if ($statusLama != 4 && $request->status_reservasi_id == 4) {
                // Kembalikan kamar lama jadi Tersedia
                Kamar::where('id', $kamarLamaId)->update(['status_kamar_id' => 1]);
                
                $this->notifService->sendCheckoutSuccess(
                    $reservasi->fcm_token ?? 'no_token',
                    $reservasi->user_id,
                    $reservasi->id
                );
            }

            DB::commit();
            return redirect()->route('dashboard.hotel.reservasi.index')->with('success', 'Data diperbarui & Notifikasi dikirim.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Hotel Dashboard Error: " . $e->getMessage());
            return back()->with('error', 'Gagal update status.');
        }
    }

    // 7. HAPUS
    public function destroy($id)
    {
        $reservasi = Reservasi::findOrFail($id);
        $reservasi->update(['status_reservasi_id' => 5]); // 5 = Batal
        $reservasi->delete();
        return redirect()->route('dashboard.hotel.reservasi.index')->with('success', 'Reservasi dihapus.');
    }

    public function getAvailableRooms($tipe_id)
{
    // Cari kamar yang tipe_id cocok DAN status_kamar_id = 1 (Tersedia)
    $kamar = \App\Models\hotel\Kamar::where('tipe_kamar_id', $tipe_id)
                ->where('status_kamar_id', 1)
                ->get();

    return response()->json($kamar);
}

}