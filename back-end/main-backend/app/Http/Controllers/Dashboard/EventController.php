<?php

namespace App\Http\Controllers\Dashboard; // Namespace diubah ke Dashboard utama

use App\Http\Controllers\Controller;
use App\Models\event\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Tampil Daftar Tema/Event (Hanya Admin)
     */
    public function index()
    {
        // Pastikan hanya admin yang bisa masuk
        if (session('user.role') !== 'admin') {
            return abort(403, 'Hanya Admin yang boleh mengganti tema aplikasi.');
        }

        $events = Event::orderBy('id', 'asc')->get();
        return view('dashboard.event.index', compact('events'));
    }

    public function edit($id)
    {
        if (session('user.role') !== 'admin') return abort(403);
        
        $event = Event::findOrFail($id);
        return view('dashboard.event.edit', compact('event'));
    }

    /**
     * PROSES AKTIFKAN TEMA (GANTI TEMA APLIKASI)
     */
    public function update(Request $request, $id)
    {
        if (session('user.role') !== 'admin') return abort(403);

        $event = Event::findOrFail($id);

        $request->validate([
            'nama_event' => 'required|string',
            'is_active'  => 'required|boolean',
        ]);

        // LOGIKA SAKLAR TEMA: Jika tema ini diaktifkan (true), matikan semua tema lainnya
        if ($request->is_active == 1) {
            Event::where('id', '!=', $id)->update(['is_active' => false]);
        }

        $event->update($request->all());

        return redirect()->route('dashboard.event.index')->with('success', 'Tema aplikasi berhasil diganti ke: ' . $event->nama_event);
    }
}