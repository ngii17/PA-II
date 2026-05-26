@extends('dashboard.layouts.app')
@section('title', 'Pembayaran Restoran')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">💳 Data Pembayaran Restoran</h4>
            <p class="text-muted mb-0" style="font-size:13px;">Rekap transaksi pembayaran restoran</p>
        </div>
    </div>

    {{-- Statistik --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm" style="border-radius:12px; border-left:4px solid #198754 !important;">
                <div class="card-body py-3">
                    <p class="text-muted mb-1 small uppercase fw-bold">Total Pendapatan</p>
                    <h4 class="fw-bold text-success mb-0">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm" style="border-radius:12px; border-left:4px solid #0d6efd !important;">
                <div class="card-body py-3">
                    <p class="text-muted mb-1 small uppercase fw-bold">Total Pesanan</p>
                    <h4 class="fw-bold text-primary mb-0">{{ $totalPesanan }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm" style="border-radius:12px; border-left:4px solid #198754 !important;">
                <div class="card-body py-3">
                    <p class="text-muted mb-1 small uppercase fw-bold">Pesanan Lunas</p>
                    <h4 class="fw-bold text-success mb-0">{{ $pesananLunas }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm" style="border-radius:12px; border-left:4px solid #ffc107 !important;">
                <div class="card-body py-3">
                    <p class="text-muted mb-1 small uppercase fw-bold">Pesanan Pending</p>
                    <h4 class="fw-bold text-warning mb-0">{{ $pesananPending }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Utama --}}
    <div class="card border-0 shadow-sm" style="border-radius:16px; overflow:hidden;">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr style="background:#1a1a2e;">
                        <th class="px-4 py-3" style="font-size:12px; background:#1a1a2e !important;">NO</th>
                        <th style="font-size:12px; background:#1a1a2e !important;">PELANGGAN</th>
                        <th style="font-size:12px; background:#1a1a2e !important;">TOTAL</th>
                        <th style="font-size:12px; background:#1a1a2e !important;">METODE</th>
                        <th style="font-size:12px; background:#1a1a2e !important;">STATUS</th>
                        <th style="font-size:12px; background:#1a1a2e !important;">TANGGAL</th>
                        <th class="text-center" style="font-size:12px; background:#1a1a2e !important;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pesanan as $i => $p)
                    @php $user = $users[$p->user_id] ?? null; @endphp
                    <tr>
                        <td class="px-4 text-muted small">{{ $i + 1 }}</td>
                        <td>
                            <div class="fw-bold" style="font-size:13px;">{{ $user['full_name'] ?? 'User #'.$p->user_id }}</div>
                            <div class="text-muted" style="font-size:11px;">{{ $user['email'] ?? '-' }}</div>
                        </td>
                        <td style="font-size:13px; font-weight:600; color:#198754;">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                        <td style="font-size:13px;">{{ $p->metode_pembayaran }}</td>
                        <td>
                            @php
                                $sc = [1=>'#ffc107', 2=>'#198754', 3=>'#0d6efd', 4=>'#dc3545'];
                                $tc = $p->status_pembayaran_id == 1 ? '#000' : '#fff';
                            @endphp
                            <span class="badge" style="background:{{ $sc[$p->status_pembayaran_id] ?? '#6c757d' }}; color:{{ $tc }}; font-size:10px; border-radius:8px; padding:5px 10px;">
                                {{ [1=>'Pending', 2=>'Lunas', 3=>'Proses', 4=>'Batal'][$p->status_pembayaran_id] ?? '-' }}
                            </span>
                        </td>
                        <td style="font-size:13px;">{{ $p->created_at->format('d M Y H:i') }}</td>
                        <td class="text-center">
                            {{-- Tombol Detail memicu Modal --}}
                            <button type="button" class="btn btn-sm btn-info text-white shadow-sm"
                                    style="border-radius:8px; padding: 4px 12px; font-size: 12px;"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalDetail{{ $p->id }}">
                                🔍 Detail
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-5 text-muted">Belum ada data pembayaran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL DETAIL PESANAN --}}
@foreach($pesanan as $p)
@php $user = $users[$p->user_id] ?? null; @endphp
<div class="modal fade" id="modalDetail{{ $p->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:20px; overflow:hidden;">
            <div class="modal-header text-white border-0 py-3 px-4" style="background: linear-gradient(135deg, #1a1a2e, #16213e);">
                <h6 class="modal-title fw-bold">🍽️ Rincian Pesanan #{{ $p->id }}</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row mb-3">
                    <div class="col-md-6 border-end">
                        <h6 class="fw-bold small mb-2 text-primary uppercase">DATA PELANGGAN</h6>
                        <p class="mb-1 small"><strong>Nama:</strong> {{ $user['full_name'] ?? '-' }}</p>
                        <p class="mb-1 small"><strong>Email:</strong> {{ $user['email'] ?? '-' }}</p>
                        <p class="mb-0 small"><strong>HP:</strong> {{ $user['phone'] ?? '-' }}</p>
                    </div>
                    <div class="col-md-6 ps-md-4">
                        <h6 class="fw-bold small mb-2 text-primary uppercase">INFO ORDER</h6>
                        <p class="mb-1 small"><strong>Meja:</strong> {{ $p->nomor_meja ?? '-' }}</p>
                        <p class="mb-1 small"><strong>Metode:</strong> {{ $p->metode_pembayaran }}</p>
                        <p class="mb-0 small"><strong>Status:</strong>
                            <span class="badge" style="background:{{ $sc[$p->status_pembayaran_id] ?? '#6c757d' }}; color:{{ $tc }}; font-size:9px;">
                                {{ [1=>'Pending', 2=>'Lunas', 3=>'Proses', 4=>'Batal'][$p->status_pembayaran_id] ?? '-' }}
                            </span>
                        </p>
                    </div>
                </div>

                <h6 class="fw-bold small mb-2 text-muted uppercase">DAFTAR MENU</h6>
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th style="font-size: 11px;">NAMA MENU</th>
                            <th style="font-size: 11px;">JUMLAH</th>
                            <th style="font-size: 11px;">HARGA/PORSI</th>
                            <th style="font-size: 11px;">SUBTOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($p->details as $item)
                        <tr>
                            <td class="small">
                                {{ $item->menu->nama_menu ?? 'Menu Tidak Ditemukan' }}
                                @if($item->menu && $item->menu->trashed())
                                    <span class="badge bg-secondary" style="font-size: 8px;">Arsip</span>
                                @endif
                            </td>
                            <td class="text-center small">{{ $item->jumlah }}</td>
                            <td class="text-end small">Rp {{ number_format($item->harga_at_porsi, 0, ',', '.') }}</td>
                            <td class="text-end small fw-bold">Rp {{ number_format($item->jumlah * $item->harga_at_porsi, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center small py-3">Rincian menu tidak ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-warning fw-bold">
                        <tr>
                            <td colspan="3" class="text-end small text-dark">TOTAL PEMBAYARAN</td>
                            <td class="text-end text-primary">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal" style="border-radius:10px; font-weight:600;">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach

<style>
    .uppercase { text-transform: uppercase; letter-spacing: 1px; }
</style>
@endsection
