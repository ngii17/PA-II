<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\event\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventController extends Controller
{
    /**
     * Tampil Daftar Tema/Event (Hanya Admin)
     */
    public function index()
    {
        if (session('user.role') !== 'admin') {
            return abort(403, 'Hanya Admin yang boleh mengganti tema aplikasi.');
        }

        $events = Event::orderBy('id', 'asc')->get();
        return view('dashboard.event.index', compact('events'));
    }

    /**
     * Form Tambah Event Baru
     */
    public function create()
    {
        if (session('user.role') !== 'admin') return abort(403);
        return view('dashboard.event.create');
    }

    /**
     * Simpan Event Baru
     */
    public function store(Request $request)
    {
        if (session('user.role') !== 'admin') return abort(403);

        $request->validate([
            'nama_event'       => 'required|string|max:255',
            'event_code'       => 'required|string|max:100|unique:events,event_code|regex:/^[a-z0-9_]+$/',
            'is_active'        => 'required|boolean',
            'deskripsi'        => 'nullable|string',
            'primary_color'    => 'required|string|max:7',
            'secondary_color'  => 'required|string|max:7',
            'header_image'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'background_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'decoration_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'event_code.unique'      => 'Kode event sudah digunakan, silakan gunakan kode lain.',
            'event_code.regex'       => 'Kode event hanya boleh huruf kecil, angka, dan underscore.',
            'header_image.image'     => 'File header harus berupa gambar.',
            'header_image.max'       => 'Ukuran header image maksimal 2MB.',
            'background_image.image' => 'File background harus berupa gambar.',
            'background_image.max'   => 'Ukuran background image maksimal 2MB.',
            'decoration_image.image' => 'File dekorasi harus berupa gambar.',
            'decoration_image.max'   => 'Ukuran decoration image maksimal 2MB.',
        ]);

        // Jika diaktifkan, matikan semua yang lain
        if ($request->is_active == 1) {
            Event::query()->update(['is_active' => false]);
        }

        // Pastikan folder tujuan ada
        $this->ensureEventsFolderExists();

        Event::create([
            'nama_event'       => $request->nama_event,
            'event_code'       => $request->event_code,
            'is_active'        => $request->is_active,
            'deskripsi'        => $request->deskripsi,
            'primary_color'    => $request->primary_color,
            'secondary_color'  => $request->secondary_color,
            'header_image'     => $this->uploadImage($request, 'header_image'),
            'background_image' => $this->uploadImage($request, 'background_image'),
            'decoration_image' => $this->uploadImage($request, 'decoration_image'),
        ]);

        return redirect()
            ->route('dashboard.event.index')
            ->with('success', 'Event baru berhasil dibuat: ' . $request->nama_event);
    }

    /**
     * Form Edit Event
     */
    public function edit($id)
    {
        if (session('user.role') !== 'admin') return abort(403);

        $event = Event::findOrFail($id);
        return view('dashboard.event.edit', compact('event'));
    }

    /**
     * Update Event
     */
    public function update(Request $request, $id)
    {
        if (session('user.role') !== 'admin') return abort(403);

        $event = Event::findOrFail($id);

        $request->validate([
            'nama_event'       => 'required|string|max:255',
            'is_active'        => 'required|boolean',
            'deskripsi'        => 'nullable|string',
            'primary_color'    => 'required|string|max:7',
            'secondary_color'  => 'required|string|max:7',
            'header_image'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'background_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'decoration_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'header_image.image'     => 'File header harus berupa gambar.',
            'header_image.max'       => 'Ukuran header image maksimal 2MB.',
            'background_image.image' => 'File background harus berupa gambar.',
            'background_image.max'   => 'Ukuran background image maksimal 2MB.',
            'decoration_image.image' => 'File dekorasi harus berupa gambar.',
            'decoration_image.max'   => 'Ukuran decoration image maksimal 2MB.',
        ]);

        // Logika saklar tema
        if ($request->is_active == 1) {
            Event::where('id', '!=', $id)->update(['is_active' => false]);
        }

        // Pastikan folder tujuan ada
        $this->ensureEventsFolderExists();

        $event->update([
            'nama_event'       => $request->nama_event,
            'is_active'        => $request->is_active,
            'deskripsi'        => $request->deskripsi,
            'primary_color'    => $request->primary_color,
            'secondary_color'  => $request->secondary_color,
            'header_image'     => $this->uploadImage($request, 'header_image',     $event->header_image),
            'background_image' => $this->uploadImage($request, 'background_image', $event->background_image),
            'decoration_image' => $this->uploadImage($request, 'decoration_image', $event->decoration_image),
        ]);

        return redirect()
            ->route('dashboard.event.index')
            ->with('success', 'Tema berhasil diperbarui: ' . $event->nama_event);
    }

    /**
     * Hapus Event
     */
    public function destroy($id)
    {
        if (session('user.role') !== 'admin') return abort(403);

        $event = Event::findOrFail($id);

        $this->deleteImage($event->header_image);
        $this->deleteImage($event->background_image);
        $this->deleteImage($event->decoration_image);

        $event->delete();

        return redirect()
            ->route('dashboard.event.index')
            ->with('success', 'Event berhasil dihapus.');
    }

    // ============================================================
    //  HELPER PRIVATE METHODS
    // ============================================================

    /**
     * Pastikan folder public/storage/events/ ada
     */
    private function ensureEventsFolderExists(): void
    {
        $folder = public_path('storage/events');
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }
    }

    /**
     * Upload gambar ke public/storage/events/
     * Hapus file lama jika ada file baru yang diupload
     *
     * @param  Request     $request
     * @param  string      $field     Nama field input file (header_image, background_image, dll)
     * @param  string|null $oldPath   Path lama (events/xxx.jpg) untuk dihapus jika ada upload baru
     * @return string|null            Path baru, atau path lama jika tidak ada upload baru
     */
    private function uploadImage(Request $request, string $field, ?string $oldPath = null): ?string
    {
        // Tidak ada file baru → kembalikan path lama agar tidak ter-null-kan
        if (!$request->hasFile($field) || !$request->file($field)->isValid()) {
            return $oldPath;
        }

        // Hapus file lama dari public/storage/events/ jika ada
        if ($oldPath && !str_starts_with($oldPath, 'http')) {
            $oldFullPath = public_path('storage/' . $oldPath);
            if (file_exists($oldFullPath)) {
                unlink($oldFullPath);
            }
        }

        $file      = $request->file($field); // ✅ pakai $field, bukan hardcode 'header_image'
        $extension = $file->getClientOriginalExtension();
        $filename  = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                     . '.' . $extension;

        // Pindahkan file ke public/storage/events/
        $file->move(public_path('storage/events'), $filename);

        return 'events/' . $filename; // contoh: "events/1720000000_header-imlek.jpg"
    }

    /**
     * Hapus file dari public/storage/events/
     * Skip jika path adalah URL eksternal (http/https)
     */
    private function deleteImage(?string $path): void
    {
        if (!$path || str_starts_with($path, 'http')) return;

        $fullPath = public_path('storage/' . $path);
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
}