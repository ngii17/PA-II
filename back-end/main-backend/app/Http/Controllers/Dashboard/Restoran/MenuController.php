<?php

namespace App\Http\Controllers\Dashboard\Restoran;

use App\Http\Controllers\Controller;
use App\Models\restoran\Menu;
use App\Models\restoran\KategoriMenu;
use App\Models\restoran\StatusMenu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    // 1. TAMPIL DAFTAR MENU
    public function index()
    {
        $menu = Menu::with(['kategori', 'status'])->orderBy('nama_menu')->get();
        return view('dashboard.restoran.menu.index', compact('menu'));
    }

    // 2. FORM TAMBAH MENU
    public function create()
    {
        $kategori = KategoriMenu::all();
        $status   = StatusMenu::all();
        return view('dashboard.restoran.menu.create', compact('kategori', 'status'));
    }

    // 3. SIMPAN MENU BARU
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'nama_menu'        => 'required|string',
            'harga'            => 'required|numeric',
            'kategori_menu_id' => 'required|exists:kategori_menu,id', // Harus cocok dengan name di Blade
            'status_menu_id'   => 'required|exists:status_menu,id',
            'deskripsi'        => 'nullable|string',
        ]);

        // 2. Ambil semua data dari form
        $data = $request->all();

        // 3. SET STOK OTOMATIS JADI 0 (Sesuai permintaanmu)
        $data['stok'] = 0;

        // 4. Simpan ke Database
        Menu::create($data);

        return redirect()->route('dashboard.restoran.menu.index')
                        ->with('success', 'Menu baru berhasil ditambahkan dengan stok awal 0.');
    }

    // 4. FORM EDIT MENU (INI YANG TADI ERROR)
    public function edit(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);
        $kategori = KategoriMenu::all();
        $status = StatusMenu::all();

        // kirim parameter from ke blade
        $from = $request->from;

        return view('dashboard.restoran.menu.edit', compact(
            'menu',
            'kategori',
            'status',
            'from'
        ));
    }

    // 5. PROSES UPDATE DATA
    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        // VALIDASI
        $request->validate([
        'nama_menu' => 'required|string',
        'harga' => 'required|numeric',
        'stok' => 'required|integer|min:0',
        'kategori_menu_id' => 'required',
        'status_menu_id' => 'required',
        'deskripsi' => 'nullable|string',
    ]);

        // UPDATE DATA
        $menu->update([
        'nama_menu' => $request->nama_menu,
        'harga' => $request->harga,
        'stok' => $request->stok,
        'kategori_menu_id' => $request->kategori_menu_id,
        'status_menu_id' => $request->status_menu_id,
        'deskripsi' => $request->deskripsi,
    ]);

        // CEK PARAMETER URL
        if ($request->query('from') == 'stok') {

            return redirect()
                ->route('dashboard.restoran.stok')
                ->with('success', 'Data menu berhasil diperbarui!');
        }

        // DEFAULT
        return redirect()
            ->route('dashboard.restoran.menu.index')
            ->with('success', 'Data menu berhasil diperbarui!');
    }

    // 6. HAPUS (SOFT DELETE)
    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->delete();
        return redirect()->back()->with('success', 'Menu berhasil dinonaktifkan.');
    }
}
