<?php

namespace App\Http\Controllers\Dashboard\Restoran;

use App\Http\Controllers\Controller;
use App\Models\event\Event;
use Illuminate\Http\Request;

class EventRestoranController extends Controller
{
    public function index()
    {
        $events = Event::all();
        return view('dashboard.restoran.event.index', compact('events'));
    }

    // TAMBAHKAN FUNGSI EDIT INI
    public function edit($id)
    {
        $event = Event::findOrFail($id);
        return view('dashboard.restoran.event.edit', compact('event'));
    }

    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        $request->validate([
            'nama_event' => 'required|string',
            'deskripsi'  => 'nullable|string',
            'is_active'  => 'required|boolean',
        ]);

        // Jika event ini diaktifkan, matikan event lainnya (Hanya 1 tema aktif)
        if ($request->is_active == 1) {
            Event::where('id', '!=', $id)->update(['is_active' => false]);
        }

        $event->update($request->all());

        return redirect()->route('dashboard.restoran.event')->with('success', 'Konfigurasi event berhasil diperbarui!');
    }
}
