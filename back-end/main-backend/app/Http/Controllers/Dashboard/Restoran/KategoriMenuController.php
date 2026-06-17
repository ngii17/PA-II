<?php

namespace App\Http\Controllers\Dashboard\Restoran;

use App\Http\Controllers\Controller;
use App\Models\Restoran\KategoriMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KategoriMenuController extends Controller
{
    /**
     * 1. TAMPIL DAFTAR KATEGORI
     */
    public function index()
    {
        // Staff Resto melihat kategori berdasarkan abjad
        $kategori = KategoriMenu::orderBy('nama_kategori', 'asc')->get();
        return view('dashboard.restoran.kategori.index', compact('kategori'));
    }

    /**
     * 2. FORM TAMBAH
     */
    public function create()
    {
        return view('dashboard.restoran.kategori.create');
    }

    /**
     * 3. SIMPAN KATEGORI BARU
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|unique:kategori_menu,nama_kategori',
            'deskripsi'     => 'nullable|string',
        ]);

        try {
            KategoriMenu::create($request->all());
            return redirect()->route('dashboard.restoran.kategori.index')
                             ->with('success', 'Kategori baru berhasil ditambahkan!');
        } catch (\Exception $e) {
            Log::error("Gagal Simpan Kategori: " . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan kategori.')->withInput();
        }
    }

    /**
     * 4. FORM EDIT
     */
    public function edit($id)
    {
        $kategori = KategoriMenu::findOrFail($id);
        return view('dashboard.restoran.kategori.edit', compact('kategori'));
    }

    /**
     * 5. UPDATE DATA
     */
    public function update(Request $request, $id)
    {
        $kategori = KategoriMenu::findOrFail($id);

        $request->validate([
            // Abaikan pengecekan unik untuk ID kategori yang sedang diedit
            'nama_kategori' => 'required|string|unique:kategori_menu,nama_kategori,' . $id,
            'deskripsi'     => 'nullable|string',
        ]);

        try {
            $kategori->update($request->all());
            return redirect()->route('dashboard.restoran.kategori.index')
                             ->with('success', 'Kategori berhasil diperbarui!');
        } catch (\Exception $e) {
            Log::error("Gagal Update Kategori: " . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui kategori.')->withInput();
        }
    }

    /**
     * 6. HAPUS KATEGORI (SOFT DELETE)
     * Proteksi: Tidak boleh menghapus kategori yang masih memiliki menu.
     */
    public function destroy($id)
    {
        $kategori = KategoriMenu::findOrFail($id);

        // Validasi: Cek relasi ke tabel menu
        // Pastikan di model KategoriMenu sudah ada function menus()
        if ($kategori->menus()->count() > 0) {
            return redirect()->back()->with('error', 'Gagal Menghapus! Kategori "' . $kategori->nama_kategori . '" masih digunakan oleh ' . $kategori->menus()->count() . ' menu. Pindahkan atau hapus menu terlebih dahulu.');
        }

        try {
            $kategori->delete();
            return redirect()->route('dashboard.restoran.kategori.index')
                             ->with('success', 'Kategori berhasil dinonaktifkan.');
        } catch (\Exception $e) {
            Log::error("Gagal Hapus Kategori: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat menghapus.');
        }
    }
}