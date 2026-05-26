<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PenggunaController extends Controller
{
    public function index(Request $request) 
    {
        $token = session('user.token');
        $response = Http::withToken($token)->get(env('MIKRO_URL') . '/api/users');

        $users = [];
        if ($response->successful()) {
            $users = $response->json('data') ?? [];

            // Logika Filter
            $roleFilter = $request->query('role');
            if ($roleFilter) {
                $users = collect($users)->where('role_id', $roleFilter)->all();
            }
        }

        return view('dashboard.pengguna.index', compact('users'));
    }
}
