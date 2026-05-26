<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AuthDashboardController extends Controller
{
    public function showLogin()
    {
        if (session('user')) {
            return redirect('/dashboard');
        }
        return view('dashboard.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            // Panggil API Auth-Service (Port 8000)
            $response = Http::post(env('MIKRO_URL') . '/api/login', [
                'email' => $request->email,
                'password' => $request->password,
            ]);

            $data = $response->json();

            if ($response->successful() && isset($data['access_token'])) {
                $user = $data['user'];

                // Role Mapping
                $roleMap = [1 => 'admin', 3 => 'staff_hotel', 4 => 'staff_restoran'];
                $role = $roleMap[$user['role_id']] ?? null;

                if (!$role) {
                    return back()->withErrors(['email' => 'Anda tidak memiliki akses ke dashboard.']);
                }

                // Simpan Session
                session(['user' => [
                    'id' => $user['id'],
                    'name' => $user['full_name'],
                    'role' => $role,
                    'token' => $data['access_token'],
                ]]);

                return redirect('/dashboard');
            }

            return back()->withErrors(['email' => 'Email atau password salah.']);

        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Gagal terhubung ke Auth-Service.']);
        }
    }

    public function logout()
    {
        session()->forget('user');
        return redirect('/dashboard/login');
    }
}
