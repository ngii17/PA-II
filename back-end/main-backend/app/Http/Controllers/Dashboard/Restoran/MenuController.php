<?php

namespace App\Http\Controllers\Dashboard\Restoran;

use App\Http\Controllers\Controller;
use App\Models\restoran\Menu;
use App\Models\restoran\KategoriMenu;
use App\Models\restoran\StatusMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class MenuController extends Controller
{
    // 1. TAMPIL DAFTAR MENU
    public function index()
    {
        // Ambil SEMUA menu tanpa filter status agar yang baru dibuat (stok 0) tetap muncul
        $menu = Menu::with(['kategori', 'status'])->orderBy('created_at', 'desc')->get();
        return view('dashboard.restoran.menu.index', compact('menu'));
    }

    public function create()
    {
        $kategori = KategoriMenu::all();
        $status   = StatusMenu::all();
        return view('dashboard.restoran.menu.create', compact('kategori', 'status'));
    }

    // 2. SIMPAN MENU (FIX: Validasi & Status Otomatis)
    public function store(Request $request)
    {
        // Validasi diperketat
        $request->validate([
            'nama_menu'        => 'required|string|max:255',
            'harga'            => 'required|numeric|min:0',
            'kategori_menu_id' => 'required|exists:kategori_menu,id',
            'status_menu_id'   => 'required|exists:status_menu,id',
            'deskripsi'        => 'nullable|string',
            'foto_menu'        => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        try {
            $data = $request->all();
            
            // Sesuai aturan: Stok awal 0
            $data['stok'] = 0; 

            // LOGIKA SINKRONISASI: Jika stok 0, status WAJIB 'Habis' (ID 2)
            $data['status_menu_id'] = 2; 

            // Proses Unggah Foto
            if ($request->hasFile('foto_menu')) {
                $file = $request->file('foto_menu');
                $filename = time() . '_' . str_replace(' ', '_', $request->nama_menu) . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/menu', $filename);
                $data['foto_menu'] = url('storage/menu/' . $filename); 
            }

            Menu::create($data);

            // REDIRECT KE INDEX (Bukan Back) agar terlihat hasilnya
            return redirect()->route('dashboard.restoran.menu.index')
                            ->with('success', 'Menu "' . $request->nama_menu . '" berhasil ditambahkan dengan stok 0.');

        } catch (\Exception $e) {
            Log::error("Gagal Simpan Menu: " . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage())->withInput();
        }
    }
    // 4. FORM EDIT MENU
    public function edit(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);
        $kategori = KategoriMenu::all();
        $status = StatusMenu::all();
        $from = $request->query('from'); // Ambil parameter asal navigasi

        return view('dashboard.restoran.menu.edit', compact('menu', 'kategori', 'status', 'from'));
    }

    // 5. PROSES UPDATE DATA (SINKRONISASI FOTO & HARGA)
    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        $request->validate([
            'nama_menu'        => 'required|string|max:255',
            'harga'            => 'required|numeric|min:0',
            'stok'             => 'required|integer|min:0',
            'kategori_menu_id' => 'required|exists:kategori_menu,id',
            'status_menu_id'   => 'required|exists:status_menu,id',
            'deskripsi'        => 'nullable|string',
            'foto_menu'        => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        try {
            $data = $request->all();

            // --- UPDATE FOTO JIKA ADA FILE BARU ---
            if ($request->hasFile('foto_menu')) {
                // Hapus foto lama jika bukan URL internet
                if ($menu->foto_menu && str_contains($menu->foto_menu, 'storage/menu')) {
                    $oldPath = str_replace(url('storage'), 'public', $menu->foto_menu);
                    Storage::delete($oldPath);
                }

                $file = $request->file('foto_menu');
                $filename = time() . '_' . str_replace(' ', '_', $request->nama_menu) . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/menu', $filename);
                $data['foto_menu'] = url('storage/menu/' . $filename);
            }

            $menu->update($data);

            // Redirection logic tetap dipertahankan
            $route = ($request->query('from') == 'stok') ? 'dashboard.restoran.stok' : 'dashboard.restoran.menu.index';
            
            return redirect()->route($route)->with('success', 'Data menu berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui: ' . $e->getMessage());
        }
    }

    // 6. HAPUS (SOFT DELETE)
    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);
        // Jangan lupa ganti status jadi habis sebelum hapus (opsional)
        $menu->delete();
        return redirect()->back()->with('success', 'Menu berhasil dinonaktifkan.');
    }
}