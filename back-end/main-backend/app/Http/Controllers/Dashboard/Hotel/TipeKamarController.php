<?php

namespace App\Http\Controllers\Dashboard\Hotel;

use App\Http\Controllers\Controller;
use App\Models\hotel\TipeKamar;
use Illuminate\Http\Request;

class TipeKamarController extends Controller
{
    // 1. TAMPIL DAFTAR TIPE KAMAR
    public function index()
    {
        $tipe = TipeKamar::orderBy('nama_tipe', 'asc')->get();
        return view('dashboard.hotel.tipe-kamar.index', compact('tipe'));
    }

    // 2. TAMPIL FORM TAMBAH
    public function create()
    {
        return view('dashboard.hotel.tipe-kamar.create');
    }

    // 3. SIMPAN TIPE KAMAR BARU
    public function store(Request $request)
    {
        $request->validate([
            'nama_tipe' => 'required|string|unique:tipe_kamar,nama_tipe',
            'harga'     => 'required|numeric|min:0',
            'kapasitas' => 'required|integer|min:1',
            'fasilitas' => 'nullable|string',
            'deskripsi' => 'nullable|string',
        ]);

        TipeKamar::create($request->all());

        return redirect()->route('dashboard.hotel.tipe-kamar.index')->with('success', 'Tipe kamar baru berhasil ditambahkan!');
    }

    // 4. FORM EDIT (INI YANG TADI ERROR)
    public function edit($id)
    {
        $tipe = TipeKamar::findOrFail($id);
        return view('dashboard.hotel.tipe-kamar.edit', compact('tipe'));
    }

    // 5. PROSES UPDATE DATA
    public function update(Request $request, $id)
    {
        $tipe = TipeKamar::findOrFail($id);

        $request->validate([
            'nama_tipe' => 'required|string|unique:tipe_kamar,nama_tipe,' . $id,
            'harga'     => 'required|numeric|min:0',
            'kapasitas' => 'required|integer|min:1',
            'fasilitas' => 'nullable|string',
            'deskripsi' => 'nullable|string',
        ]);

        $tipe->update($request->all());

        return redirect()->route('dashboard.hotel.tipe-kamar.index')->with('success', 'Data tipe kamar berhasil diperbarui!');
    }

    // 6. HAPUS (SOFT DELETE)
    public function destroy($id)
    {
        $tipe = TipeKamar::findOrFail($id);

        // Validasi: Cek apakah masih ada unit kamar di bawah tipe ini
        if ($tipe->kamar()->count() > 0) {
            // Kirim pesan error (Warna Merah)
            return redirect()->back()->with('error', 'Gagal menghapus! Tipe kamar "' . $tipe->nama_tipe . '" masih memiliki unit kamar aktif di dalamnya. Hapus unit kamarnya terlebih dahulu.');
        }

        $tipe->delete();
        return redirect()->back()->with('success', 'Tipe kamar berhasil dinonaktifkan.');
    }
}
