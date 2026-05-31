<?php

namespace App\Http\Controllers\Dashboard\Hotel;

use App\Http\Controllers\Controller;
use App\Models\hotel\TipeKamar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Tambahan untuk keamanan database

class TipeKamarController extends Controller
{
    /**
     * 1. TAMPIL DAFTAR TIPE KAMAR
     * Menampilkan semua tipe kamar yang akan muncul di Katalog HP Customer.
     */
    public function index()
    {
        // Staff Hotel melihat daftar tipe kamar diurutkan berdasarkan abjad
        $tipe = TipeKamar::orderBy('nama_tipe', 'asc')->get();
        return view('dashboard.hotel.tipe-kamar.index', compact('tipe'));
    }

    /**
     * 2. TAMPIL FORM TAMBAH
     */
    public function create()
    {
        return view('dashboard.hotel.tipe-kamar.create');
    }

    /**
     * 3. SIMPAN TIPE KAMAR BARU
     * Data 'harga' di sini akan menjadi 'harga_asli' di aplikasi Flutter.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_tipe' => 'required|string|unique:tipe_kamar,nama_tipe',
            'harga'     => 'required|numeric|min:0',
            'kapasitas' => 'required|integer|min:1',
            'fasilitas' => 'nullable|string',
            'deskripsi' => 'nullable|string',
        ]);

        try {
            TipeKamar::create($request->all());

            return redirect()->route('dashboard.hotel.tipe-kamar.index')
                             ->with('success', 'Tipe kamar baru berhasil ditambahkan ke katalog!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * 4. FORM EDIT
     */
    public function edit($id)
    {
        $tipe = TipeKamar::findOrFail($id);
        return view('dashboard.hotel.tipe-kamar.edit', compact('tipe'));
    }

    /**
     * 5. PROSES UPDATE DATA
     * Sinkronisasi: Update harga di sini otomatis memperbarui harga coret di HP jika ada promo aktif.
     */
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

        try {
            $tipe->update($request->all());

            return redirect()->route('dashboard.hotel.tipe-kamar.index')
                             ->with('success', 'Data tipe kamar berhasil diperbarui dan disinkronkan ke aplikasi.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * 6. HAPUS (SOFT DELETE)
     * Keamanan: Mencegah penghapusan jika masih ada unit kamar (kamar fisik) yang terdaftar.
     */
    public function destroy($id)
    {
        $tipe = TipeKamar::findOrFail($id);

        // Validasi: Cek apakah masih ada unit kamar di bawah tipe ini
        // Mengandalkan relasi 'kamar()' di model TipeKamar.php
        if ($tipe->kamar()->count() > 0) {
            return redirect()->back()->with('error', 'Gagal menghapus! Tipe kamar "' . $tipe->nama_tipe . '" masih memiliki unit kamar aktif di dalamnya. Hapus unit kamarnya terlebih dahulu.');
        }

        try {
            $tipe->delete();
            return redirect()->back()->with('success', 'Tipe kamar berhasil dinonaktifkan dari katalog.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }
}