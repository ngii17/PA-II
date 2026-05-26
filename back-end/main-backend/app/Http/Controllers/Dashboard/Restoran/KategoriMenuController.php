<?php

namespace App\Http\Controllers\Dashboard\Restoran;

use App\Http\Controllers\Controller;
use App\Models\restoran\KategoriMenu;
use Illuminate\Http\Request;

class KategoriMenuController extends Controller
{
    /**
     * Tampil Daftar Kategori (READ)
     */
    public function index()
    {
        // Mengambil data kategori yang belum dihapus (Soft Delete)
        $kategori = KategoriMenu::orderBy('nama_kategori', 'asc')->get();
        return view('dashboard.restoran.kategori.index', compact('kategori'));
    }

    /**
     * Tampil Form Tambah (CREATE) - INI YANG TADI ERROR
     */
    public function create()
    {
        return view('dashboard.restoran.kategori.create');
    }

    /**
     * Simpan Kategori Baru (STORE)
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|unique:kategori_menu,nama_kategori',
            'deskripsi'     => 'nullable|string',
        ]);

        KategoriMenu::create($request->all());

        return redirect()->route('dashboard.restoran.kategori.index')
                         ->with('success', 'Kategori berhasil ditambahkan!');
    }

    /**
     * Tampil Form Edit (EDIT)
     */
    /**
     * Tampil Form Edit
     */
    public function edit($id)
    {
        $kategori = KategoriMenu::findOrFail($id);

        // PASTIKAN ADA KATA 'return' DI SINI
        return view('dashboard.restoran.kategori.edit', compact('kategori'));
    }

    /**
     * Update Data
     */
    public function update(Request $request, $id)
    {
        $kategori = KategoriMenu::findOrFail($id);

        $request->validate([
            'nama_kategori' => 'required|string|unique:kategori_menu,nama_kategori,' . $id,
            'deskripsi'     => 'nullable|string',
        ]);

        $kategori->update($request->all());

        // Gunakan .index sesuai standar resource
        return redirect()->route('dashboard.restoran.kategori.index')
                         ->with('success', 'Kategori berhasil diperbarui!');
    }

    /**
     * Hapus Kategori (DESTROY - Soft Delete)
     */
    public function destroy($id)
    {
        $kategori = KategoriMenu::findOrFail($id);

        // Validasi: Cek apakah ada menu yang masih menggunakan kategori ini
        if ($kategori->menus()->count() > 0) {
            return redirect()->back()->with('error', 'Gagal! Kategori masih digunakan oleh beberapa menu.');
        }

        $kategori->delete(); // Soft Delete (mengisi deleted_at)

        return redirect()->route('dashboard.restoran.kategori.index')
                         ->with('success', 'Kategori berhasil dinonaktifkan.');
    }
}
