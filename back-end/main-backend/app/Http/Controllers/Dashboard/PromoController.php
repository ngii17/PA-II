<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\hotel\Promo;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    // Tampil Daftar Promo
    public function index()
    {
        $promos = Promo::orderBy('created_at', 'desc')->get();
        return view('dashboard.promo.index', compact('promos'));
    }

    // Tampil Form Tambah
    public function create()
    {
        return view('dashboard.promo.create');
    }

    // Simpan Promo Baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_promo'       => 'required|string|max:255',
            'kode_promo'       => 'required|string|unique:promo,kode_promo',
            'kategori'         => 'required|in:hotel,restoran,global',
            'tipe_diskon'      => 'required|in:nominal,persen',
            'nominal_potongan' => 'required|numeric|min:0',
            'tgl_mulai'        => 'required|date',
            'tgl_selesai'      => 'required|date|after:tgl_mulai',
        ]);

        Promo::create($request->all());

        return redirect()->route('dashboard.promo.index')->with('success', 'Promo berhasil ditambahkan!');
    }

    // Detail Promo (Biasanya pakai modal di index, tapi method ini harus ada)
    public function show($id)
    {
        $promo = Promo::findOrFail($id);
        return view('dashboard.promo.index', compact('promo')); // Atau sesuaikan
    }

    // FORM EDIT (INI YANG TADI ERROR)
    public function edit($id)
    {
        $promo = Promo::findOrFail($id);
        return view('dashboard.promo.edit', compact('promo'));
    }

    // UPDATE DATA PROMO
    public function update(Request $request, $id)
    {
        $promo = Promo::findOrFail($id);

        $request->validate([
            'nama_promo'       => 'required|string|max:255',
            'kode_promo'       => 'required|string|unique:promo,kode_promo,' . $id, // Abaikan unik untuk ID ini sendiri
            'kategori'         => 'required|in:hotel,restoran,global',
            'tipe_diskon'      => 'required|in:nominal,persen',
            'nominal_potongan' => 'required|numeric|min:0',
            'tgl_mulai'        => 'required|date',
            'tgl_selesai'      => 'required|date|after:tgl_mulai',
        ]);

        $promo->update($request->all());

        return redirect()->route('dashboard.promo.index')->with('success', 'Promo berhasil diperbarui!');
    }

    // HAPUS PROMO (Soft Delete)
    public function destroy($id)
    {
        $promo = Promo::findOrFail($id);
        $promo->delete(); // Pastikan di model Promo sudah pakai 'use SoftDeletes'

        return redirect()->route('dashboard.promo.index')->with('success', 'Promo berhasil dinonaktifkan.');
    }
}
