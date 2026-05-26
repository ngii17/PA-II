<?php

namespace App\Http\Controllers\Dashboard\Restoran;

use App\Http\Controllers\Controller;
use App\Models\restoran\Menu;
use App\Models\restoran\StatusMenu;
use App\Models\restoran\KategoriMenu;
use Illuminate\Http\Request;

class StokMenuController extends Controller
{
    public function index()
    {
        $menu = Menu::with(['kategori', 'status'])->orderBy('nama_menu')->get();
        return view('dashboard.restoran.stok.index', compact('menu'));
    }

    // DETAIL MENU (Untuk melihat rincian stok & deskripsi)
    public function show($id)
    {
        $menu = Menu::with(['kategori', 'status'])->findOrFail($id);
        return response()->json($menu); // Kita gunakan JSON agar bisa tampil di modal tanpa refresh
    }

    public function edit($id)
    {
        $menu = Menu::findOrFail($id);

        $kategori = KategoriMenu::all();
        $status = StatusMenu::all();

        return view('dashboard.restoran.menu.edit', [
            'menu' => $menu,
            'kategori' => $kategori,
            'status' => $status,
            'from' => 'stok'
        ]);
    }

    // UPDATE STOK CEPAT (Fungsi yang sudah ada)
    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);
        $request->validate(['stok' => 'required|integer|min:0']);

        // Jika stok diisi 0, otomatis status jadi "Habis" (ID 3), jika > 0 jadi "Tersedia" (ID 1)
        $statusId = ($request->stok > 0) ? 1 : 3;

        $menu->update([
            'stok' => $request->stok,
            'status_menu_id' => $statusId
        ]);

        return redirect()->back()->with('success', "Stok {$menu->nama_menu} berhasil diperbarui.");
    }

    // HAPUS OTOMATIS RESET STOK 0 (Sesuai Permintaan Anda)
    public function destroy($id)
    {
        // 1. Cari data menu berdasarkan ID
        $menu = \App\Models\restoran\Menu::findOrFail($id);

        // 2. Cari ID status yang mewakili "Habis" atau "Tidak Tersedia"
        // Berdasarkan database kamu sebelumnya, status "Habis" adalah ID 3
        $statusHabisId = 3;

        // 3. Update stok menjadi 0 dan status menjadi Habis (Sesuai BPMN 3.2.1.43)
        // Kita TIDAK memanggil $menu->delete(), jadi data tetap ada di tabel menu
        $menu->update([
            'stok' => 0,
            'status_menu_id' => $statusHabisId
        ]);

        // 4. Kembali ke halaman stok dengan pesan sukses
        return redirect()->back()->with('success', "Stok untuk {$menu->nama_menu} telah dikosongkan (Set ke 0) dan status menjadi Habis.");
    }
}
