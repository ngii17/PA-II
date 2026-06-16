<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\event\Event; // Import Model Event kita
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class EventController extends Controller
{
    /**
     * FUNGSI UNTUK MENGAMBIL TEMA YANG SEDANG AKTIF
     */
    /**
     * FUNGSI UNTUK MENGAMBIL TEMA & ASSET VISUAL YANG AKTIF
     */

    public function getActiveEvent()
{
    try {
        $activeEvent = Event::where('is_active', true)->first();

        if (!$activeEvent) {
            return response()->json([
                'success' => true,
                'data' => [
                    'event_code'       => 'default',
                    'primary_color'    => '#0C2D6B',
                    'secondary_color'  => '#C9A227',
                    'header_image'     => null,
                    'background_image' => null,
                    'decoration_image' => null,
                ]
            ], 200);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tema ' . $activeEvent->nama_event . ' aktif.',
            'data' => [
                'event_code'       => $activeEvent->event_code,
                'primary_color'    => $activeEvent->primary_color,
                'secondary_color'  => $activeEvent->secondary_color,
                'header_image'     => $this->resolveImageUrl($activeEvent->header_image),
                'background_image' => $this->resolveImageUrl($activeEvent->background_image),
                'decoration_image' => $this->resolveImageUrl($activeEvent->decoration_image),
                'nama_event'       => $activeEvent->nama_event,
            ]
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Gagal memuat instruksi tema: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Convert path relatif ke URL penuh yang bisa diakses Flutter
 * - null          → null
 * - http://...    → dikembalikan apa adanya (sudah URL)
 * - events/xx.jpg → http://IP:8001/storage/events/xx.jpg
 */
private function resolveImageUrl(?string $path): ?string
{
    if (empty($path)) return null;

    // Sudah URL penuh (http/https) → kembalikan langsung
    if (str_starts_with($path, 'http')) return $path;

    // Path lokal → convert ke URL penuh
    return url('storage/' . $path);
}
}