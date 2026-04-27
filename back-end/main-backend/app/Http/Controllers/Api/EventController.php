<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\event\Event; // Import Model Event kita
use Illuminate\Http\Request;

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
            // Ambil event yang sedang aktif (is_active = true)
            $activeEvent = Event::where('is_active', true)->first();

            // Jika tidak ada yang aktif, paksa kirim data tema default
            if (!$activeEvent) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'event_code'      => 'default',
                        'primary_color'   => '#448AFF',
                        'secondary_color' => '#E3F2FD',
                        'header_image'    => null,
                        'background_image'=> null,
                        'decoration_image'=> null,
                    ]
                ], 200);
            }

            // Kirim SEMUA data visual ke Flutter
            return response()->json([
                'success' => true,
                'message' => 'Tema ' . $activeEvent->nama_event . ' aktif.',
                'data' => [
                    'event_code'      => $activeEvent->event_code,
                    'primary_color'   => $activeEvent->primary_color,
                    'secondary_color' => $activeEvent->secondary_color,
                    'header_image'    => $activeEvent->header_image,
                    'background_image'=> $activeEvent->background_image,
                    'decoration_image'=> $activeEvent->decoration_image,
                    'nama_event'      => $activeEvent->nama_event,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat instruksi tema: ' . $e->getMessage()
            ], 500);
        }
    }
}