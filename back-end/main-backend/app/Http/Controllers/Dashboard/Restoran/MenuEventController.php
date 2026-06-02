<?php

namespace App\Http\Controllers\Dashboard\Restoran;

use App\Http\Controllers\Controller;
use App\Models\event\Event;
use App\Models\restoran\Menu;
use App\Models\restoran\MenuEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 

class MenuEventController extends Controller
{
    public function index()
    {
        $menuEvents = DB::table('event_menu')
            ->join('events', 'event_menu.event_id', '=', 'events.id')
            ->join('menu', 'event_menu.menu_id', '=', 'menu.id')
            ->whereNull('event_menu.deleted_at') // Filter data terhapus
            ->select(
                'event_menu.id',
                'event_menu.harga_khusus',
                'event_menu.is_active',
                'events.nama_event',   // Ini harus dipanggil
                'menu.nama_menu',     // Ini harus dipanggil
                'menu.harga'          // Ini adalah harga normal
            )
            ->orderBy('event_menu.id', 'desc')
            ->get();

        return view('dashboard.restoran.menu_event.index', compact('menuEvents'));
    }

    // =========================
    // DETAIL MENU EVENT (Fix Error & Untuk Popup)
    // =========================
    public function show($id)
    {
        $menuEvent = DB::table('event_menu')
            ->join('events', 'event_menu.event_id', '=', 'events.id')
            ->join('menu', 'event_menu.menu_id', '=', 'menu.id')
            ->where('event_menu.id', $id)
            ->select(
                'event_menu.*',
                'events.nama_event',
                'menu.nama_menu',
                'menu.deskripsi',
                'menu.harga as harga_asli'
            )
            ->first();

        if (!$menuEvent) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($menuEvent);
    }

    public function create()
    {
        $events = Event::all();
        $menus = Menu::all();
        return view('dashboard.restoran.menu_event.create', compact('events', 'menus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required',
            'menu_ids' => 'required|array',
            'harga_khusus' => 'required|numeric'
        ]);

        foreach ($request->menu_ids as $id) {
            MenuEvent::create([
                'event_id' => $request->event_id,
                'menu_id' => $id,
                'harga_khusus' => $request->harga_khusus,
                'is_active' => true
            ]);
        }
        return redirect()->route('dashboard.restoran.menu-event.index')->with('success', 'Menu Event berhasil ditambahkan!');
    }

    // =========================
    // 5. FORM EDIT (Tadi Error karena ini belum ada)
    // =========================
    public function edit($id)
    {
        $menuEvent = DB::table('event_menu')
            ->join('events', 'event_menu.event_id', '=', 'events.id')
            ->join('menu', 'event_menu.menu_id', '=', 'menu.id')
            ->where('event_menu.id', $id)
            ->select('event_menu.*', 'events.nama_event', 'menu.nama_menu')
            ->first();

        if (!$menuEvent) abort(404);

        return view('dashboard.restoran.menu_event.edit', compact('menuEvent'));
    }

    // =========================
    // 6. PROSES UPDATE
    // =========================
    public function update(Request $request, $id)
    {
        $request->validate([
            'harga_khusus' => 'required|numeric|min:0',
            'is_active'    => 'required|boolean'
        ]);

        DB::table('event_menu')
            ->where('id', $id)
            ->update([
                'harga_khusus' => $request->harga_khusus,
                'is_active'    => $request->is_active,
                'updated_at'   => now(),
            ]);

        return redirect()
            ->route('dashboard.restoran.menu-event.index')
            ->with('success', 'Harga khusus menu event berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $me = MenuEvent::findOrFail($id);
        $me->update(['is_active' => false]);
        $me->delete();
        return redirect()->back()->with('success', 'Menu Event berhasil diarsipkan.');
    }
}
