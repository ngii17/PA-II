@extends('dashboard.layouts.app')
@section('title', 'Seluruh Pembayaran')
@section('content')

<div class="container-fluid px-4">
    {{-- Bagian Header & Statistik tetap sama seperti sebelumnya --}}
    <div class="mb-4">
        <h4 class="fw-bold mb-1">💳 Seluruh Transaksi Pembayaran</h4>
        <p class="text-muted small">Rekapitulasi pendapatan dari seluruh unit bisnis (Hotel & Restoran)</p>
    </div>

    {{-- Statistik Ringkas (Gunakan kode stats yang sudah ada) --}}
    <div class="row g-3 mb-4">
        {{-- ... (tetap gunakan kode statistik sebelumnya) ... --}}
    </div>

    {{-- Navigasi Tab --}}
    <ul class="nav nav-pills mb-4 gap-2" id="pills-tab" role="tablist">
        <li class="nav-item">
            <button class="nav-link active px-4 shadow-sm" id="pills-hotel-tab" data-bs-toggle="pill" data-bs-target="#pills-hotel" type="button" style="border-radius:10px;">🏨 Pembayaran Hotel</button>
        </li>
        <li class="nav-item">
            <button class="nav-link px-4 shadow-sm" id="pills-resto-tab" data-bs-toggle="pill" data-bs-target="#pills-resto" type="button" style="border-radius:10px;">🍽️ Pembayaran Restoran</button>
        </li>
    </ul>

    <div class="tab-content" id="pills-tabContent">
        {{-- PANEL HOTEL --}}
        <div class="tab-pane fade show active" id="pills-hotel" role="tabpanel">
            <div class="card border-0 shadow-sm" style="border-radius:16px; overflow:hidden;">
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background:#1a1a2e; color:white;">
                            <tr>
                                <th class="px-4 py-3">NO</th>
                                <th>TAMU</th>
                                <th>TIPE KAMAR</th>
                                <th>CHECK IN/OUT</th>
                                <th>TOTAL</th>
                                <th class="text-center">AKSI</th> {{-- TAMBAHKAN INI --}}
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pembayaranHotel as $i => $h)
                            @php $user = $users[$h->user_id] ?? null; @endphp
                            <tr>
                                <td class="px-4 text-muted">{{ $i + 1 }}</td>
                                <td>
                                    <div class="fw-bold" style="font-size:13px;">{{ $user['full_name'] ?? 'Tamu #'.$h->user_id }}</div>
                                    <small class="text-muted">{{ $user['email'] ?? '-' }}</small>
                                </td>
                                <td style="font-size:13px;">{{ $h->tipeKamar->nama_tipe ?? '-' }}</td>
                                <td style="font-size:12px;">{{ \Carbon\Carbon::parse($h->tgl_checkin)->format('d/m/y') }} - {{ \Carbon\Carbon::parse($h->tgl_checkout)->format('d/m/y') }}</td>
                                <td class="fw-bold text-primary">Rp {{ number_format($h->total_harga, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#modalHotel{{ $h->id }}">🔍 Detail</button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center py-5 text-muted">Belum ada pembayaran hotel.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- PANEL RESTORAN --}}
        <div class="tab-pane fade" id="pills-resto" role="tabpanel">
            <div class="card border-0 shadow-sm" style="border-radius:16px; overflow:hidden;">
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background:#1a1a2e; color:white;">
                            <tr>
                                <th class="px-4 py-3">NO</th>
                                <th>PELANGGAN</th>
                                <th>NOMOR ORDER</th>
                                <th>TOTAL</th>
                                <th class="text-center">AKSI</th> {{-- TAMBAHKAN INI --}}
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pembayaranResto as $i => $r)
                            @php $user = $users[$r->user_id] ?? null; @endphp
                            <tr>
                                <td class="px-4 text-muted">{{ $i + 1 }}</td>
                                <td>
                                    <div class="fw-bold" style="font-size:13px;">{{ $user['full_name'] ?? 'User #'.$r->user_id }}</div>
                                    <small class="text-muted">{{ $user['email'] ?? '-' }}</small>
                                </td>
                                <td class="fw-bold" style="font-size:13px;">ORD-{{ $r->id }}</td>
                                <td class="fw-bold text-success">Rp {{ number_format($r->total_harga, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#modalResto{{ $r->id }}">🔍 Detail</button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-5 text-muted">Belum ada pembayaran restoran.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL DETAIL HOTEL --}}
@foreach($pembayaranHotel as $h)
@php $user = $users[$h->user_id] ?? null; @endphp
<div class="modal fade" id="modalHotel{{ $h->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h6 class="modal-title fw-bold">🏨 Detail Pembayaran Hotel #{{ $h->id }}</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-borderless">
                    <tr><td width="150">Nama Tamu</td><td>: {{ $user['full_name'] ?? '-' }}</td></tr>
                    <tr><td>Tipe Kamar</td><td>: {{ $h->tipeKamar->nama_tipe ?? '-' }}</td></tr>
                    <tr><td>Check In</td><td>: {{ \Carbon\Carbon::parse($h->tgl_checkin)->format('d F Y') }}</td></tr>
                    <tr><td>Check Out</td><td>: {{ \Carbon\Carbon::parse($h->tgl_checkout)->format('d F Y') }}</td></tr>
                    <tr><td>Durasi</td><td>: {{ $h->total_malam }} Malam</td></tr>
                    <tr class="border-top"><td class="fw-bold">TOTAL BAYAR</td><td class="fw-bold text-primary">: Rp {{ number_format($h->total_harga, 0, ',', '.') }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endforeach

{{-- MODAL DETAIL RESTORAN --}}
@foreach($pembayaranResto as $r)
@php $user = $users[$r->user_id] ?? null; @endphp
<div class="modal fade" id="modalResto{{ $r->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h6 class="modal-title fw-bold">🍽️ Rincian Menu ORD-{{ $r->id }}</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="small mb-2 text-muted">Pelanggan: <strong>{{ $user['full_name'] ?? '-' }}</strong></p>
                <table class="table table-bordered table-sm">
                    <thead class="table-light">
                        <tr><th>Menu</th><th class="text-center">Qty</th><th class="text-end">Harga</th><th class="text-end">Subtotal</th></tr>
                    </thead>
                    <tbody>
                        @foreach($r->details as $item)
                        <tr>
                            <td>{{ $item->menu->nama_menu ?? 'Menu Dihapus' }}</td>
                            <td class="text-center">{{ $item->jumlah }}</td>
                            <td class="text-end">Rp {{ number_format($item->harga_at_porsi, 0, ',', '.') }}</td>
                            <td class="text-end fw-bold">Rp {{ number_format($item->jumlah * $item->harga_at_porsi, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-warning fw-bold">
                        <tr><td colspan="3" class="text-end">TOTAL PEMBAYARAN</td><td class="text-end">Rp {{ number_format($r->total_harga, 0, ',', '.') }}</td></tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endforeach

<style>
    .nav-pills .nav-link.active { background-color: #1a1a2e !important; color: white !important; }
    .nav-pills .nav-link { color: #1a1a2e; background: white; border: 1px solid #ddd; }
</style>

@endsection
