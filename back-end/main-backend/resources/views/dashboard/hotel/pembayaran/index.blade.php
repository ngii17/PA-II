@extends('dashboard.layouts.app')
@section('title', 'Pembayaran Hotel')
@section('content')

<div class="container-fluid px-4">
    <div class="mb-4">
        <h4 class="fw-bold mb-1">💳 Data Pembayaran Hotel</h4>
        <p class="text-muted small">Rekapitulasi seluruh transaksi reservasi kamar yang telah <b>Lunas</b>.</p>
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
            <div class="card border-0 shadow-sm" style="border-radius:12px; border-left:4px solid #198754 !important;">
                <div class="card-body py-3">
                    <p class="text-muted mb-1 small uppercase fw-bold">Total Transaksi Lunas</p>
                    <h3 class="fw-bold text-success mb-0">{{ $totalTransaksiHotel }} Transaksi</h3>
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
                        <th class="text-center">STATUS</th>
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
                            <strong>{{ $r->kamar->nomor_kamar ?? 'N/A' }}</strong><br>
                            <small class="text-muted">{{ $r->tipeKamar->nama_tipe ?? '-' }}</small>
                        </td>
                        <td style="font-size:13px;">{{ $r->total_malam }} Malam</td>
                        <td class="fw-bold text-primary" style="font-size:13px;">Rp {{ number_format($r->total_harga, 0, ',', '.') }}</td>
                        <td class="text-center">
                            {{-- SINKRONISASI: Status diganti menjadi LUNAS untuk semua baris yang tampil --}}
                            <span class="badge bg-success" style="font-size:10px; padding: 6px 12px; border-radius:50px;">
                                <i class="fas fa-check-circle me-1"></i> LUNAS
                            </span>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-info shadow-sm"
                                    style="border-radius:8px; padding: 5px 12px;"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalHotel{{ $r->id }}">
                                <i class="fas fa-search-dollar me-1"></i> Rincian
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
                <h6 class="modal-title fw-bold"><i class="fas fa-file-invoice-dollar me-2"></i>Kwitansi Digital Kamar {{ $r->kamar->nomor_kamar ?? 'N/A' }}</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-modal="hide"></button>
            </div>
            <div class="modal-body p-4">
                <div class="p-3 mb-3" style="background:#f8f9fa; border-radius:12px; border: 1px solid #eee;">
                    <h6 class="fw-bold small border-bottom pb-2 mb-2 text-primary uppercase">IDENTITAS PEMBAYAR</h6>
                    <p class="mb-1 small"><strong>Nama Lengkap :</strong> {{ $user['full_name'] ?? '-' }}</p>
                    <p class="mb-1 small"><strong>Username :</strong> {{ $user['username'] ?? '-' }}</p>
                    <p class="mb-0 small"><strong>Email :</strong> {{ $user['email'] ?? '-' }}</p>
                </div>

                <h6 class="fw-bold small border-bottom pb-2 mb-2 text-primary uppercase">RINCIAN TRANSAKSI</h6>
                <table class="table table-sm table-borderless mb-0">
                    <tr class="small"><td>Produk</td><td>: Menginap di {{ $r->tipeKamar->nama_tipe }}</td></tr>
                    <tr class="small"><td>Check-In</td><td>: {{ \Carbon\Carbon::parse($r->tgl_checkin)->format('d M Y') }}</td></tr>
                    <tr class="small"><td>Check-Out</td><td>: {{ \Carbon\Carbon::parse($r->tgl_checkout)->format('d M Y') }}</td></tr>
                    <tr class="small"><td>Durasi</td><td>: {{ $r->total_malam }} Malam</td></tr>
                    <tr class="small"><td>Metode Bayar</td><td>: <span class="text-uppercase">{{ $r->metode_pembayaran }}</span></td></tr>
                </table>

                <div class="mt-4 p-3 text-center" style="background:#f0fff4; border-radius:12px; border: 1px dashed #28a745;">
                    <p class="mb-0 text-muted small uppercase fw-bold">TOTAL DANA DITERIMA</p>
                    <h3 class="fw-bold text-success mb-0">Rp {{ number_format($r->total_harga, 0, ',', '.') }}</h3>
                    <small class="text-success fw-bold" style="font-size: 9px;">TRANSAKSI SAH & TERVERIFIKASI</small>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal" style="border-radius:10px; font-weight: bold;">TUTUP RINCIAN</button>
            </div>
        </div>
    </div>
</div>
@endforeach

<style>
    .uppercase { text-transform: uppercase; letter-spacing: 1px; }
    .table-hover tbody tr:hover { background-color: rgba(0,0,0,.01); }
</style>

@endsection