<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PenggunaController extends Controller
{
    protected $mikroUrl;

    public function __construct()
    {
        // Ambil URL Port 8000 dari .env
        $this->mikroUrl = env('MIKRO_URL', 'http://10.187.82.132:8000');
    }

    /**
     * 1. DAFTAR PENGGUNA (INDEX)
     */
    public function index(Request $request) 
    {
        // KEAMANAN: Hanya Admin yang boleh mengelola pengguna
        if (session('user.role') !== 'admin') {
            return abort(403, 'Akses dilarang.');
        }

        $token = session('user.token');
        $response = Http::withToken($token)->get($this->mikroUrl . '/api/users');

        $users = [];
        if ($response->successful()) {
            $users = $response->json('data') ?? [];

            // Logika Filter berdasarkan Role
            $roleFilter = $request->query('role');
            if ($roleFilter) {
                $users = collect($users)->where('role_id', $roleFilter)->all();
            }
        }

        return view('dashboard.pengguna.index', compact('users'));
    }

    /**
     * 2. FORM TAMBAH STAFF (CREATE)
     */
    public function create()
    {
        if (session('user.role') !== 'admin') return abort(403);
        
        // Kita hanya kirim pilihan role Staff Hotel (3) dan Staff Restoran (4)
        return view('dashboard.pengguna.create');
    }

    /**
     * 3. SIMPAN STAFF BARU KE PORT 8000 (STORE)
     */
    public function store(Request $request)
    {
        if (session('user.role') !== 'admin') return abort(403);

        $request->validate([
            'username'  => 'required|string|min:3',
            'full_name' => 'required|string',
            'email'     => 'required|email',
            'password'  => 'required|min:8',
            'role_id'   => 'required|in:3,4', // Hanya boleh pilih Staff Hotel/Restoran
            'phone'     => 'required',
            'address'   => 'required',
        ]);

        try {
            // Kita tembak API Register di Port 8000 khusus untuk Staff
            $response = Http::post($this->mikroUrl . '/api/internal/create-staff', [
                'username'    => $request->username,
                'full_name'   => $request->full_name,
                'email'       => $request->email,
                'password'    => $request->password,
                'role_id'     => $request->role_id,
                'phone'       => $request->phone,
                'address'     => $request->address,
                'is_verified' => true // Staff otomatis verified
            ]);

            if ($response->successful()) {
                return redirect()->route('dashboard.pengguna')->with('success', 'Staff baru berhasil didaftarkan!');
            }

            return back()->with('error', 'Gagal mendaftarkan staff: ' . ($response->json('message') ?? 'Error API'))->withInput();

        } catch (\Exception $e) {
            Log::error("Error Store Staff: " . $e->getMessage());
            return back()->with('error', 'Koneksi ke Auth-Service terputus.');
        }
    }

    /**
     * 4. UPDATE DATA PENGGUNA (UPDATE)
     */
    public function update(Request $request, $id)
    {
        if (session('user.role') !== 'admin') return abort(403);

        try {
            $token = session('user.token');
            $response = Http::withToken($token)->put($this->mikroUrl . "/api/users/$id", $request->all());

            if ($response->successful()) {
                return redirect()->route('dashboard.pengguna')->with('success', 'Data pengguna berhasil diperbarui.');
            }

            return back()->with('error', 'Gagal update data.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghubungi server pusat.');
        }
    }

    /**
     * 5. HAPUS PENGGUNA (DESTROY)
     */
    public function destroy($id)
    {
        if (session('user.role') !== 'admin') return abort(403);

        try {
            $token = session('user.token');
            $response = Http::withToken($token)->delete($this->mikroUrl . "/api/users/$id");

            if ($response->successful()) {
                return redirect()->route('dashboard.pengguna')->with('success', 'Pengguna berhasil dihapus.');
            }

            return back()->with('error', 'Gagal menghapus pengguna.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem.');
        }
    }

}