<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\hotel\Promo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PromoController extends Controller
{
    /**
     * 1. TAMPIL DAFTAR PROMO
     */
    public function index()
    {
        $promos = Promo::orderBy('created_at', 'desc')->get();
        return view('dashboard.promo.index', compact('promos'));
    }

    /**
     * 2. FORM TAMBAH PROMO
     */
    public function create()
    {
        return view('dashboard.promo.create');
    }

    /**
     * 3. SIMPAN PROMO BARU (DENGAN LOGIKA NULLABLE KODE)
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama_promo'       => 'required|string|max:255',
            'kode_promo'       => 'nullable|string|unique:promo,kode_promo', // NULLABLE: Agar bisa jadi promo otomatis
            'kategori'         => 'required|in:hotel,restoran,semua',
            'tipe_diskon'      => 'required|in:nominal,persen',
            'nominal_potongan' => 'required|numeric|min:0',
            'tgl_mulai'        => 'required|date',
            'tgl_selesai'      => 'required|date|after:tgl_mulai',
        ]);

        // Tambahkan status is_active secara otomatis saat pembuatan
        $data = $request->all();
        $data['is_active'] = true; 

        Promo::create($data);

        return redirect()->route('dashboard.promo.index')
            ->with('success', 'Promo "' . $request->nama_promo . '" berhasil dibuat dan langsung aktif!');
    }

    /**
     * 4. DETAIL PROMO
     */
    public function show($id)
    {
        $promo = Promo::findOrFail($id);
        return view('dashboard.promo.show', compact('promo'));
    }

    /**
     * 5. FORM EDIT PROMO
     */
    public function edit($id)
    {
        $promo = Promo::findOrFail($id);
        return view('dashboard.promo.edit', compact('promo'));
    }

    /**
     * 6. UPDATE DATA PROMO
     */
    public function update(Request $request, $id)
    {
        $promo = Promo::findOrFail($id);

        $request->validate([
            'nama_promo'       => 'required|string|max:255',
            // Unique kecuali untuk ID ini sendiri
            'kode_promo'       => 'nullable|string|unique:promo,kode_promo,' . $id, 
            'kategori'         => 'required|in:hotel,restoran,semua',
            'tipe_diskon'      => 'required|in:nominal,persen',
            'nominal_potongan' => 'required|numeric|min:0',
            'tgl_mulai'        => 'required|date',
            'tgl_selesai'      => 'required|date|after:tgl_mulai',
            'is_active'        => 'required|boolean', // Saklar ON/OFF
        ]);

        $promo->update($request->all());

        return redirect()->route('dashboard.promo.index')
            ->with('success', 'Perubahan promo berhasil disimpan.');
    }

    /**
     * 7. HAPUS PROMO (SOFT DELETE)
     */
    public function destroy($id)
    {
        $promo = Promo::findOrFail($id);
        
        // Sebelum menghapus, kita matikan dulu statusnya
        $promo->update(['is_active' => false]);
        $promo->delete();

        return redirect()->route('dashboard.promo.index')
            ->with('success', 'Promo telah dihapus dan dinonaktifkan.');
    }

    /**
     * 8. FITUR CEPAT: TOGGLE AKTIF/NONAKTIF
     * Digunakan untuk mematikan promo tanpa harus masuk ke form edit
     */
    public function toggleStatus($id)
    {
        $promo = Promo::findOrFail($id);
        $promo->update(['is_active' => !$promo->is_active]);

        $status = $promo->is_active ? 'diaktifkan' : 'dimatikan';
        return redirect()->back()->with('success', "Promo berhasil $status!");
    }
}