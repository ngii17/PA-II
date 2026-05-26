<?php

namespace App\Http\Controllers\Dashboard\Hotel;

use App\Http\Controllers\Controller;
use App\Models\hotel\Reservasi;
use App\Models\hotel\StatusReservasi;
use App\Models\hotel\TipeKamar;
use App\Models\hotel\Kamar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ReservasiHotelController extends Controller
{
    // 1. TAMPIL DAFTAR (INDEX)
    public function index()
    {
        $token = session('user.token');
        $response = Http::withToken($token)->get(env('MIKRO_URL') . '/api/users');
        $users = collect($response->json('data') ?? [])->keyBy('id');

        // Ambil data aktif
        $reservasi = Reservasi::with(['tipeKamar', 'statusReservasi', 'kamar'])
            ->orderBy('created_at', 'desc')->get();

        // Statistik untuk Cards
        $totalReservasi = $reservasi->count();
        $terbayarCount = $reservasi->whereIn('status_reservasi_id', [2, 3])->count();
        $pendingCount = $reservasi->where('status_reservasi_id', 1)->count();
        $totalPendapatan = $reservasi->whereIn('status_reservasi_id', [2, 3])->sum('total_harga');

        return view('dashboard.hotel.reservasi.index', compact(
            'reservasi', 'users', 'totalReservasi', 'terbayarCount', 'pendingCount', 'totalPendapatan'
        ));
    }

    // 2. TAMPIL DETAIL (SHOW)
    public function show($id)
    {
        $token = session('user.token');
        $response = Http::withToken($token)->get(env('MIKRO_URL') . '/api/users');
        $users = collect($response->json('data') ?? [])->keyBy('id');

        // Gunakan withTrashed agar data yang diarsipkan tetap bisa dilihat detailnya
        $reservasi = Reservasi::with(['tipeKamar', 'statusReservasi', 'kamar'])->withTrashed()->findOrFail($id);

        return view('dashboard.hotel.reservasi.show', compact('reservasi', 'users'));
    }

    // 3. FORM TAMBAH (CREATE)
    public function create()
    {
        // 1. Ambil data user dari Auth-Service (Port 8000)
        $token = session('user.token');
        $response = Http::withToken($token)->get(env('MIKRO_URL') . '/api/users');

        // Filter hanya yang role-nya pelanggan (ID 2)
        $customers = collect($response->json('data') ?? [])->filter(fn($u) => $u['role_id'] == 2);

        // 2. Ambil data pendukung dari DB lokal
        $tipeKamar = TipeKamar::all();
        $kamar = Kamar::with('tipeKamar')->where('status_kamar_id', 1)->get(); // Hanya kamar 'Tersedia'
        $statusList = StatusReservasi::all();

        // 3. Kirim ke View
        return view('dashboard.hotel.reservasi.create', compact('customers', 'tipeKamar', 'kamar', 'statusList'));
    }

    // 4. SIMPAN (STORE)
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'user_id'             => 'required',
            'tipe_kamar_id'       => 'required|exists:tipe_kamar,id',
            'kamar_id'            => 'required|exists:kamar,id',
            'tgl_checkin'         => 'required|date',
            'tgl_checkout'        => 'required|date|after:tgl_checkin',
            'status_reservasi_id' => 'required|exists:status_reservasi,id',
        ]);

        try {
            // 2. Ambil data tipe kamar untuk mendapatkan harga
            $tipeKamar = TipeKamar::findOrFail($request->tipe_kamar_id);

            // 3. Hitung Durasi Malam
            $checkIn   = \Carbon\Carbon::parse($request->tgl_checkin);
            $checkOut  = \Carbon\Carbon::parse($request->tgl_checkout);
            $malam     = $checkIn->diffInDays($checkOut);

            // Pastikan minimal 1 malam
            $malam = ($malam < 1) ? 1 : $malam;

            // 4. Hitung Total Harga
            $totalHarga = $malam * $tipeKamar->harga;

            // 5. Simpan ke Database
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

            return redirect()->route('dashboard.hotel.reservasi.index')
                            ->with('success', 'Reservasi berhasil dibuat untuk ' . $malam . ' malam.');

        } catch (\Exception $e) {
            // Jika ada error database, tampilkan pesannya
            return back()->withErrors(['error' => 'Gagal menyimpan: ' . $e->getMessage()])->withInput();
        }
    }

    // 5. FORM EDIT (EDIT)
    public function edit($id)
    {
        $reservasi  = Reservasi::findOrFail($id);
        $tipeKamar  = TipeKamar::all();
        $kamar      = Kamar::all();
        $statusList = StatusReservasi::all();
        return view('dashboard.hotel.reservasi.edit', compact('reservasi', 'tipeKamar', 'kamar', 'statusList'));
    }

    // 6. UPDATE
    public function update(Request $request, $id)
    {
        $reservasi = Reservasi::findOrFail($id);
        $tipeKamar = TipeKamar::findOrFail($request->tipe_kamar_id);

        // Hitung ulang jumlah malam
        $checkIn   = \Carbon\Carbon::parse($request->tgl_checkin);
        $checkOut  = \Carbon\Carbon::parse($request->tgl_checkout);
        $malam     = $checkIn->diffInDays($checkOut);

        $reservasi->update([
            'tipe_kamar_id'       => $request->tipe_kamar_id,
            'kamar_id'            => $request->kamar_id,
            'tgl_checkin'         => $request->tgl_checkin,
            'tgl_checkout'        => $request->tgl_checkout,
            'total_malam'         => $malam,
            'total_harga'         => $malam * $tipeKamar->harga, // Update harga otomatis
            'status_reservasi_id' => $request->status_reservasi_id,
        ]);

        return redirect()->route('dashboard.hotel.reservasi.index')->with('success', 'Data reservasi berhasil diperbarui!');
    }

    // 7. HAPUS (DESTROY - SOFT DELETE)
    public function destroy($id)
    {
        $reservasi = Reservasi::findOrFail($id);

        if (in_array($reservasi->status_reservasi_id, [2, 3])) {
            $reservasi->update(['status_reservasi_id' => 4]); // 4 = Arsip
            $msg = 'Reservasi lunas telah diarsipkan.';
        } else {
            $reservasi->update(['status_reservasi_id' => 4]);
            $msg = 'Reservasi pending berhasil dibatalkan.';
        }

        $reservasi->delete();
        return redirect()->route('dashboard.hotel.reservasi.index')->with('success', $msg);
    }
}
