<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class AuthServiceHelper
{
    public static function getUserIdFromToken(string $token): int|null
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
            ])->get('http://auth-service:8000/api/auth/me');

            if ($response->successful() && $response->json('success')) {
                return $response->json('user_id');
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
