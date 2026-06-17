<?php

namespace App\Http\Controllers\Dashboard\Restoran;

use App\Http\Controllers\Controller;
use App\Models\Restoran\Menu;
use App\Models\Restoran\StatusMenu;
use App\Models\Restoran\KategoriMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StokMenuController extends Controller
{
    /**
     * 1. TAMPIL DAFTAR STOK
     * Mengambil semua menu, termasuk yang stoknya 0.
     */
    public function index()
    {
        // PENTING: Gunakan withTrashed() jika kamu ingin menu yang di-softdelete tetap muncul di sini
        $menu = Menu::with(['kategori', 'status'])->orderBy('nama_menu', 'asc')->get();
        return view('dashboard.restoran.stok.index', compact('menu'));
    }

    /**
     * 2. DETAIL MENU (Via AJAX untuk Modal)
     */
    public function show($id)
    {
        $menu = Menu::with(['kategori', 'status'])->findOrFail($id);
        return response()->json($menu);
    }

    /**
     * 3. FORM EDIT (Navigasi ke Menu Utama)
     */
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

    /**
     * 4. UPDATE STOK CEPAT
     * SINKRONISASI: ID 1 = Tersedia, ID 2 = Habis
     */
    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);
        $request->validate(['stok' => 'required|integer|min:0']);

        // --- PERBAIKAN: Gunakan ID 2 untuk Habis sesuai Seeder ---
        $statusId = ($request->stok > 0) ? 1 : 2;

        try {
            $menu->update([
                'stok' => $request->stok,
                'status_menu_id' => $statusId
            ]);

            return redirect()->back()->with('success', "Stok {$menu->nama_menu} berhasil diperbarui.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    /**
     * 5. RESET STOK (Fungsi Tombol Power-off)
     */
    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);

        try {
            // Set stok ke 0 dan status ke ID 2 (Habis)
            $menu->update([
                'stok' => 0,
                'status_menu_id' => 2 
            ]);

            return redirect()->back()->with('success', "Stok {$menu->nama_menu} telah dikosongkan.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengosongkan stok.');
        }
    }
}