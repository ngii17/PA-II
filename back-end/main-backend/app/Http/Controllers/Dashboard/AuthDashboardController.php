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
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        try {
            $response = Http::post(env('MIKRO_URL') . '/api/login', [
                'email'    => $request->email,
                'password' => $request->password,
            ]);

            $data = $response->json();

            if ($response->successful() && isset($data['access_token'])) {
                $user    = $data['user'];
                $roleMap = [1 => 'admin', 3 => 'staff_hotel', 4 => 'staff_restoran'];
                $role    = $roleMap[$user['role_id']] ?? null;

                if (!$role) {
                    return back()->withErrors(['email' => 'Anda tidak memiliki akses ke dashboard.'])->withInput();
                }

                session(['user' => [
                    'id'    => $user['id'],
                    'name'  => $user['full_name'],
                    'role'  => $role,
                    'token' => $data['access_token'],
                ]]);

                return redirect('/dashboard');
            }

            // Baca error_type dari response auth-service
            $errorType = $data['error_type'] ?? 'email';
            $errorMsg  = $data['message'] ?? 'Terjadi kesalahan, coba lagi.';

            return back()->withErrors([$errorType => $errorMsg])->withInput();

        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Gagal terhubung ke Auth-Service.'])->withInput();
        }
    }
    public function logout()
    {
        session()->forget('user');
        return redirect('/dashboard/login');
    }
}
