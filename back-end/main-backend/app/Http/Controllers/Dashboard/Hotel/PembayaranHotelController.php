<?php
namespace App\Http\Controllers\Dashboard\Hotel;
use App\Http\Controllers\Controller;
use App\Models\hotel\Reservasi;
use Illuminate\Support\Facades\Http;

class PembayaranHotelController extends Controller {
    public function index() {
        if (session('user.role') === 'staff_restoran') return abort(403);
        $token = session('user.token');
        $users = collect(Http::withToken($token)->get(env('MIKRO_URL').'/api/users')->json('data'))->keyBy('id');
        $reservasi = Reservasi::with(['tipeKamar', 'kamar', 'statusReservasi'])->whereIn('status_reservasi_id', [2, 3])->get();
        $totalPendapatanHotel = $reservasi->sum('total_harga');
        $totalTransaksiHotel = $reservasi->count();
        return view('dashboard.hotel.pembayaran.index', compact('reservasi', 'users', 'totalPendapatanHotel', 'totalTransaksiHotel'));
    }
}
