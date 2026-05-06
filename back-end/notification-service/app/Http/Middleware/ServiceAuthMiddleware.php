<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ServiceAuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Ambil kunci rahasia dari config/app.php (yang kita set di Modul 1)
        $secretKey = config('app.service_secret_key');

        // 2. Ambil kunci dari header request yang masuk
        $requestSecret = $request->header('X-Service-Secret');

        // 3. Validasi: Jika tidak ada atau tidak cocok, tolak dengan status 401
        if (!$requestSecret || $requestSecret !== $secretKey) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated Service: Secret Key mismatch or missing.'
            ], 401);
        }

        return $next($request);
    }
}