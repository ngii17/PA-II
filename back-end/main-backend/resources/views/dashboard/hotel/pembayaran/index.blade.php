@extends('dashboard.layouts.app')
@section('title', 'Pembayaran Hotel')
@section('content')

<div class="container-fluid px-4">
    <div class="mb-4">
        <h4 class="fw-bold mb-1">💳 Data Pembayaran Hotel</h4>
        <p class="text-muted small">Rekapitulasi seluruh transaksi reservasi kamar yang telah lunas/selesai.</p>
    </div>

    {{-- Statistik Pembayaran --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm" style="border-radius:12px; border-left:4px solid #1a1a2e !important;">
                <div class="card-body py-3">
                    <p class="text-muted mb-1 small uppercase fw-bold">Total Pendapatan Hotel</p>
                    <h3 class="fw-bold text-primary mb-0">Rp {{ number_format($totalPendapatanHotel, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm" style="border-radius:12px; border-left:4px solid #0d6efd !important;">
                <div class="card-body py-3">
                    <p class="text-muted mb-1 small uppercase fw-bold">Total Reservasi Sukses</p>
                    <h3 class="fw-bold text-info mb-0">{{ $totalTransaksiHotel }} Transaksi</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Pembayaran --}}
    <div class="card border-0 shadow-sm" style="border-radius:16px; overflow:hidden;">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr style="background:#1a1a2e;">
                        <th class="px-4 py-3">NO</th>
                        <th>TAMU</th>
                        <th>KAMAR</th>
                        <th>DURASI</th>
                        <th>TOTAL BAYAR</th>
                        <th>STATUS</th>
                        <th class="text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservasi as $i => $r)
                    @php $user = $users[$r->user_id] ?? null; @endphp
                    <tr>
                        <td class="px-4 text-muted small">{{ $i + 1 }}</td>
                        <td>
                            <div class="fw-bold" style="font-size:13px;">{{ $user['full_name'] ?? 'Tamu #'.$r->user_id }}</div>
                            <small class="text-muted">{{ $user['email'] ?? '-' }}</small>
                        </td>
                        <td style="font-size:13px;">
                            <strong>{{ $r->kamar->nomor_kamar ?? '-' }}</strong><br>
                            <small class="text-muted">{{ $r->tipeKamar->nama_tipe ?? '-' }}</small>
                        </td>
                        <td style="font-size:13px;">{{ $r->total_malam }} Malam</td>
                        <td class="fw-bold text-success" style="font-size:13px;">Rp {{ number_format($r->total_harga, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge" style="background:{{ $r->status_reservasi_id == 3 ? '#0d6efd' : '#198754' }}; font-size:10px; padding: 5px 10px; border-radius:8px;">
                                {{ $r->statusReservasi->nama_status ?? '-' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-info text-white shadow-sm"
                                    style="border-radius:8px; padding: 5px 12px;"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalHotel{{ $r->id }}">
                                🔍 Detail
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-5 text-muted">Belum ada data pembayaran hotel yang lunas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL DETAIL PEMBAYARAN HOTEL --}}
@foreach($reservasi as $r)
@php $user = $users[$r->user_id] ?? null; @endphp
<div class="modal fade" id="modalHotel{{ $r->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:20px; overflow:hidden;">
            <div class="modal-header text-white border-0 py-3 px-4" style="background:#1a1a2e;">
                <h6 class="modal-title fw-bold">🏨 Rincian Pembayaran Kamar {{ $r->kamar->nomor_kamar ?? '' }}</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="p-3 mb-3" style="background:#f8f9fa; border-radius:12px;">
                    <h6 class="fw-bold small border-bottom pb-2 mb-2 text-primary uppercase">DATA TAMU</h6>
                    <p class="mb-1 small"><strong>Nama:</strong> {{ $user['full_name'] ?? '-' }}</p>
                    <p class="mb-0 small"><strong>Email:</strong> {{ $user['email'] ?? '-' }}</p>
                </div>

                <h6 class="fw-bold small border-bottom pb-2 mb-2 text-primary uppercase">RINCIAN MENGINAP</h6>
                <table class="table table-sm table-borderless mb-0">
                    <tr class="small"><td>Tipe Kamar</td><td>: {{ $r->tipeKamar->nama_tipe }}</td></tr>
                    <tr class="small"><td>Harga/Malam</td><td>: Rp {{ number_format($r->tipeKamar->harga, 0, ',', '.') }}</td></tr>
                    <tr class="small"><td>Check-In</td><td>: {{ \Carbon\Carbon::parse($r->tgl_checkin)->format('d M Y') }}</td></tr>
                    <tr class="small"><td>Check-Out</td><td>: {{ \Carbon\Carbon::parse($r->tgl_checkout)->format('d M Y') }}</td></tr>
                    <tr class="small"><td>Total Durasi</td><td>: {{ $r->total_malam }} Malam</td></tr>
                </table>

                <div class="mt-4 p-3 text-center" style="background:#eef2ff; border-radius:12px; border: 1px dashed #0d6efd;">
                    <p class="mb-0 text-muted small uppercase fw-bold">TOTAL YANG DIBAYARKAN</p>
                    <h4 class="fw-bold text-primary mb-0">Rp {{ number_format($r->total_harga, 0, ',', '.') }}</h4>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal" style="border-radius:10px;">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach

<style>
    .uppercase { text-transform: uppercase; letter-spacing: 1px; }
</style>

@endsection
