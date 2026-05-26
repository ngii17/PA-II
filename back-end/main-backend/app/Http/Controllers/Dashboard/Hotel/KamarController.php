<?php

namespace App\Http\Controllers\Dashboard\Hotel;

use App\Http\Controllers\Controller;
use App\Models\hotel\Kamar;
use App\Models\hotel\TipeKamar;
use App\Models\hotel\StatusKamar;
use Illuminate\Http\Request;

class KamarController extends Controller
{
    // 1. TAMPIL DAFTAR KAMAR
    public function index()
    {
        $kamar = Kamar::with(['tipeKamar', 'statusKamar'])->orderBy('nomor_kamar', 'asc')->get();
        return view('dashboard.hotel.kamar.index', compact('kamar'));
    }

    // 2. TAMPIL FORM TAMBAH
    public function create()
    {
        $tipeKamar = TipeKamar::all();
        $statusKamar = StatusKamar::all();
        return view('dashboard.hotel.kamar.create', compact('tipeKamar', 'statusKamar'));
    }

    // 3. SIMPAN KAMAR BARU
    public function store(Request $request)
    {
        $request->validate([
            'nomor_kamar' => 'required|unique:kamar,nomor_kamar',
            'tipe_kamar_id' => 'required|exists:tipe_kamar,id',
            'status_kamar_id' => 'required|exists:status_kamar,id',
        ]);

        Kamar::create($request->all());

        return redirect()->route('dashboard.hotel.kamar.index')->with('success', 'Kamar baru berhasil ditambahkan!');
    }

    // 4. FORM EDIT (INI YANG TADI ERROR)
    public function edit($id)
    {
        $kamar = Kamar::findOrFail($id);
        $tipeKamar = TipeKamar::all();
        $statusKamar = StatusKamar::all();

        return view('dashboard.hotel.kamar.edit', compact('kamar', 'tipeKamar', 'statusKamar'));
    }

    // 5. PROSES UPDATE DATA
    public function update(Request $request, $id)
    {
        $kamar = Kamar::findOrFail($id);

        $request->validate([
            'nomor_kamar' => 'required|unique:kamar,nomor_kamar,' . $id,
            'tipe_kamar_id' => 'required|exists:tipe_kamar,id',
            'status_kamar_id' => 'required|exists:status_kamar,id',
        ]);

        $kamar->update($request->all());

        return redirect()->route('dashboard.hotel.kamar.index')->with('success', 'Data kamar berhasil diperbarui!');
    }

    // 6. HAPUS (SOFT DELETE)
    public function destroy($id)
    {
        $kamar = Kamar::findOrFail($id);
        // Set status ke 'Tidak Tersedia' (ID 3) sebelum hapus
        $kamar->update(['status_kamar_id' => 3]);
        $kamar->delete();

        return redirect()->back()->with('success', 'Kamar berhasil dinonaktifkan.');
    }
}
