<?php
namespace App\Http\Controllers\Dashboard\Hotel;
use App\Http\Controllers\Controller;
use App\Models\Hotel\Reservasi;
use App\Models\Hotel\StatusReservasi;
use App\Models\Hotel\TipeKamar;
use App\Models\Hotel\Kamar;
use App\Models\Hotel\Promo;
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

    // 4. SIMPAN RESERVASI BARU
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
            $checkIn    = \Carbon\Carbon::parse($request->tgl_checkin);
            $checkOut   = \Carbon\Carbon::parse($request->tgl_checkout);
            $malam      = $checkIn->diffInDays($checkOut);
            $malam      = ($malam < 1) ? 1 : $malam;
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
        $reservasi      = Reservasi::findOrFail($id);
        $tipeKamar      = TipeKamar::all();
        $kamar          = Kamar::all();
        $statusList     = StatusReservasi::all();
        $currentKamarId = $reservasi->kamar_id;
        return view('dashboard.hotel.reservasi.edit', compact(
            'reservasi', 'tipeKamar', 'kamar', 'statusList', 'currentKamarId'
        ));
    }

    // 6. UPDATE (LOGIKA NOTIFIKASI & MANAGEMENT KAMAR)
    public function update(Request $request, $id)
    {
        $reservasi   = Reservasi::findOrFail($id);
        $statusLama  = $reservasi->status_reservasi_id;
        $kamarLamaId = $reservasi->kamar_id;

        DB::beginTransaction();
        try {
            // ----------------------------------------------------------------
            // FIX DOUBLE-ASSIGN: Saat akan CHECK-IN, pastikan kamar yang dipilih
            // tidak sedang aktif dipakai reservasi LAIN (status_reservasi_id = 3)
            // ----------------------------------------------------------------
            if ($request->status_reservasi_id == 3 && $statusLama != 3) {
                $kamarSudahTerpakai = Reservasi::where('kamar_id', $request->kamar_id)
                    ->where('status_reservasi_id', 3)   // sedang CHECK-IN aktif
                    ->where('id', '!=', $id)             // bukan reservasi ini sendiri
                    ->exists();

                if ($kamarSudahTerpakai) {
                    DB::rollback();
                    return back()->with('error',
                        '⚠️ Kamar ini sedang digunakan oleh tamu lain yang masih CHECK-IN. Silakan pilih kamar lain.'
                    );
                }
            }

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
                Kamar::where('id', $request->kamar_id)->update(['status_kamar_id' => 2]);

                $kamarData = Kamar::find($request->kamar_id);
                $this->notifService->sendCheckinSuccess(
                    $reservasi->fcm_token ?? 'no_token',
                    $reservasi->user_id,
                    $kamarData->nomor_kamar ?? '?'
                );
            }

            // B. Jika Status berubah jadi SELESAI/CHECK-OUT (ID 4)
            if ($statusLama != 4 && $request->status_reservasi_id == 4) {
                // FIX: Gunakan $kamarLamaId (diambil sebelum update) agar selalu benar
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
        $reservasi->update(['status_reservasi_id' => 5]);
        $reservasi->delete();
        return redirect()->route('dashboard.hotel.reservasi.index')->with('success', 'Reservasi dihapus.');
    }

    // 8. GET KAMAR TERSEDIA (AJAX)
    // FIX DOUBLE-ASSIGN: Kamar yang sedang di-checkin reservasi LAIN tidak muncul di dropdown.
    // Hanya kamar milik reservasi yang sedang diedit (current_kamar_id) yang tetap muncul
    // meski status_kamar_id = 2 (Terisi).
    public function getAvailableRooms($tipe_id, $current_kamar_id = null)
    {
        // Kumpulkan kamar_id yang sedang aktif CHECK-IN oleh reservasi LAIN
        $kamarTerpakai = Reservasi::where('status_reservasi_id', 3)
            ->when($current_kamar_id, fn($q) => $q->where('kamar_id', '!=', $current_kamar_id))
            ->pluck('kamar_id');

        $kamar = Kamar::where('tipe_kamar_id', $tipe_id)
            ->where(function ($q) use ($kamarTerpakai, $current_kamar_id) {
                // Kamar tersedia dan tidak dipakai reservasi lain
                $q->where('status_kamar_id', 1)
                  ->whereNotIn('id', $kamarTerpakai);
                // Sertakan kamar aktif reservasi ini sendiri (meski status_kamar_id = 2)
                if ($current_kamar_id) {
                    $q->orWhere('id', $current_kamar_id);
                }
            })
            ->get();

        return response()->json($kamar);
    }
}