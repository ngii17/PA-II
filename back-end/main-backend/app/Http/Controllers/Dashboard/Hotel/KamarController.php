<?php

namespace App\Http\Controllers\Dashboard\Hotel;

use App\Http\Controllers\Controller;
use App\Models\hotel\Kamar;
use App\Models\hotel\TipeKamar;
use App\Models\hotel\StatusKamar;
use App\Models\hotel\Reservasi; // Tambahkan untuk pengecekan keamanan
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KamarController extends Controller
{
    /**
     * 1. TAMPIL DAFTAR KAMAR
     */
    public function index()
    {
        $kamar = Kamar::with(['tipeKamar', 'statusKamar'])->orderBy('nomor_kamar', 'asc')->get();
        return view('dashboard.hotel.kamar.index', compact('kamar'));
    }

    /**
     * 2. TAMPIL FORM TAMBAH
     */
    public function create()
    {
        $tipeKamar = TipeKamar::all();
        $statusKamar = StatusKamar::all();
        return view('dashboard.hotel.kamar.create', compact('tipeKamar', 'statusKamar'));
    }

    /**
     * 3. SIMPAN KAMAR BARU
     */
    public function store(Request $request)
    {
        $request->validate([
            'nomor_kamar' => 'required|unique:kamar,nomor_kamar',
            'tipe_kamar_id' => 'required|exists:tipe_kamar,id',
            'status_kamar_id' => 'required|exists:status_kamar,id',
        ]);

        try {
            Kamar::create($request->all());
            return redirect()->route('dashboard.hotel.kamar.index')
                             ->with('success', 'Unit kamar baru berhasil didaftarkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * 4. FORM EDIT
     */
    public function edit($id)
    {
        $kamar = Kamar::findOrFail($id);
        $tipeKamar = TipeKamar::all();
        $statusKamar = StatusKamar::all();

        return view('dashboard.hotel.kamar.edit', compact('kamar', 'tipeKamar', 'statusKamar'));
    }

    /**
     * 5. PROSES UPDATE DATA (DENGAN VALIDASI KEAMANAN TAMU)
     */
    public function update(Request $request, $id)
    {
        $kamar = Kamar::findOrFail($id);

        $request->validate([
            'nomor_kamar' => 'required|unique:kamar,nomor_kamar,' . $id,
            'tipe_kamar_id' => 'required|exists:tipe_kamar,id',
            'status_kamar_id' => 'required|exists:status_kamar,id',
        ]);

        // ============================================================
        // --- KUNCI KEAMANAN: CEK APAKAH ADA TAMU AKTIF (CHECK-IN) ---
        // ============================================================
        // Cari apakah ada reservasi yang menggunakan kamar ini dan statusnya 3 (Check-in)
        $activeGuest = Reservasi::where('kamar_id', $id)
                                ->where('status_reservasi_id', 3) 
                                ->exists();

        // Jika Staff mencoba mengubah status menjadi 1 (Tersedia) atau 3 (Perbaikan) 
        // padahal masih ada tamu di dalamnya, maka TOLAK.
        if ($activeGuest && $request->status_kamar_id != 2) {
            return back()->with('error', 'Gagal! Kamar ' . $kamar->nomor_kamar . ' tidak bisa diubah statusnya karena saat ini masih dihuni oleh tamu (Status: Check-in). Selesaikan proses Check-out tamu terlebih dahulu.');
        }

        try {
            $kamar->update($request->all());
            return redirect()->route('dashboard.hotel.kamar.index')
                             ->with('success', 'Data unit kamar berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * 6. HAPUS (SOFT DELETE)
     */
    public function destroy($id)
    {
        $kamar = Kamar::findOrFail($id);

        // Keamanan: Cek tamu aktif sebelum hapus
        $activeGuest = Reservasi::where('kamar_id', $id)
                                ->where('status_reservasi_id', 3)
                                ->exists();

        if ($activeGuest) {
            return back()->with('error', 'Gagal menghapus! Kamar masih digunakan oleh tamu yang sedang menginap.');
        }

        try {
            // Set status ke 'Tidak Tersedia' (ID 3) sebelum hapus agar tidak muncul di query sistem
            $kamar->update(['status_kamar_id' => 3]);
            $kamar->delete();

            return redirect()->back()->with('success', 'Kamar berhasil dinonaktifkan dan dihapus dari daftar aktif.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }
}