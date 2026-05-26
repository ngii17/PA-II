<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DashboardMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Jika tidak ada session 'user', tendang ke halaman login
        if (!session('user')) {
            return redirect('/dashboard/login');
        }

        return $next($request);
    }
}
