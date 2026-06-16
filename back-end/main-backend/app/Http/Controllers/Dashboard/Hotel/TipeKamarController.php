<?php

namespace App\Http\Controllers\Dashboard\Hotel;

use App\Http\Controllers\Controller;
use App\Models\hotel\TipeKamar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage; // ✅ Tambahkan ini

class TipeKamarController extends Controller
{
    /**
     * 1. TAMPIL DAFTAR TIPE KAMAR
     */
    public function index()
    {
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
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_tipe' => 'required|string|unique:tipe_kamar,nama_tipe',
            'harga'     => 'required|numeric|min:0',
            'kapasitas' => 'required|integer|min:1',
            'fasilitas' => 'nullable|string',
            'deskripsi' => 'nullable|string',
            'foto'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // ✅ Tambahkan validasi foto
        ]);

        try {
            $data = $request->except('foto'); // ✅ Ambil semua data kecuali foto

            // ✅ Jika ada foto yang diupload, simpan ke storage
            if ($request->hasFile('foto')) {
                $data['foto'] = $request->file('foto')->store('tipe_kamar', 'public');
            }

            TipeKamar::create($data);

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
            'foto'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // ✅ Tambahkan validasi foto
        ]);

        try {
            $data = $request->except('foto'); // ✅ Ambil semua data kecuali foto

            // ✅ Jika ada foto baru yang diupload
            if ($request->hasFile('foto')) {
                // Hapus foto lama dari storage jika bukan URL eksternal (seeder)
                if ($tipe->foto && !str_starts_with($tipe->foto, 'http')) {
                    Storage::disk('public')->delete($tipe->foto);
                }
                // Simpan foto baru
                $data['foto'] = $request->file('foto')->store('tipe_kamar', 'public');
            }
            // Jika tidak ada foto baru, $data['foto'] tidak diset
            // sehingga foto lama di database tetap tidak berubah

            $tipe->update($data);

            return redirect()->route('dashboard.hotel.tipe-kamar.index')
                             ->with('success', 'Data tipe kamar berhasil diperbarui dan disinkronkan ke aplikasi.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * 6. HAPUS (SOFT DELETE)
     */
    public function destroy($id)
    {
        $tipe = TipeKamar::findOrFail($id);

        if ($tipe->kamar()->count() > 0) {
            return redirect()->back()->with('error', 'Gagal menghapus! Tipe kamar "' . $tipe->nama_tipe . '" masih memiliki unit kamar aktif di dalamnya. Hapus unit kamarnya terlebih dahulu.');
        }

        try {
            // ✅ Hapus foto dari storage saat tipe kamar dihapus
            if ($tipe->foto && !str_starts_with($tipe->foto, 'http')) {
                Storage::disk('public')->delete($tipe->foto);
            }

            $tipe->delete();
            return redirect()->back()->with('success', 'Tipe kamar berhasil dinonaktifkan dari katalog.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }
}