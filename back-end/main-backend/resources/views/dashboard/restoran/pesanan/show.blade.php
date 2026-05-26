@extends('dashboard.layouts.app')
@section('title', 'Detail Pesanan #' . $pesanan->id)
@section('content')

<div class="container-fluid px-4">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.restoran.pesanan.index') }}" class="text-decoration-none text-muted">Daftar Pesanan</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detail #{{ $pesanan->id }}</li>
                </ol>
            </nav>
            <h3 class="fw-bold mb-0 text-dark">📄 Invoice Digital</h3>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-outline-dark shadow-sm px-4">
                <i class="fas fa-print me-2"></i> Cetak Struk
            </button>
            <a href="{{ route('dashboard.restoran.pesanan.index') }}" class="btn btn-primary shadow-sm px-4">
                <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- Sisi Kiri: Informasi Order & Pelanggan --}}
        <div class="col-lg-4">
            {{-- Card Status --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                <div class="card-body p-4 text-center">
                    <div class="mb-3">
                        @php
                            $statusPay = $pesanan->status_pembayaran_id;
                            $bgPay = ($statusPay == 2) ? 'bg-success' : (($statusPay == 1) ? 'bg-warning' : 'bg-danger');
                        @endphp
                        <span class="badge {{ $bgPay }} py-2 px-3 fs-6" style="border-radius: 10px;">
                            {{ $pesanan->statusPembayaran->nama_status ?? 'Pending' }}
                        </span>
                    </div>
                    <h5 class="fw-bold mb-1">No. Pesanan: ORD-{{ $pesanan->id }}</h5>
                    <p class="text-muted small mb-0">{{ $pesanan->created_at->translatedFormat('d F Y, H:i') }} WIB</p>
                </div>
            </div>

            {{-- Card Pelanggan --}}
            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h6 class="fw-bold mb-0 text-primary uppercase" style="letter-spacing: 1px;">👤 Data Pelanggan</h6>
                </div>
                <div class="card-body p-4">
                    @php $user = $users[$pesanan->user_id] ?? null; @endphp
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-light rounded-circle p-3 me-3 text-primary">
                            <i class="fas fa-user-circle fa-2x"></i>
                        </div>
                        <div>
                            <p class="mb-0 fw-bold text-dark fs-5">{{ $user['full_name'] ?? 'Tamu Umum' }}</p>
                            <small class="text-muted">{{ $user['email'] ?? '-' }}</small>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <label class="text-muted small d-block mb-1">Nomor Meja</label>
                            <span class="badge bg-dark px-3 py-2 fs-6 w-100" style="border-radius: 8px;">
                                Meja {{ $pesanan->nomor_meja }}
                            </span>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small d-block mb-1">Metode Bayar</label>
                            <span class="badge bg-secondary px-3 py-2 fs-6 w-100" style="border-radius: 8px;">
                                {{ $pesanan->metode_pembayaran }}
                            </span>
                        </div>
                    </div>
                    <hr class="my-4">
                    <div class="d-flex justify-content-between text-muted small">
                        <span>Status Antrean:</span>
                        <strong class="text-primary">{{ $pesanan->statusPesanan->nama_status ?? 'Dalam Antrean' }}</strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sisi Kanan: Rincian Menu --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; overflow: hidden;">
                <div class="card-header border-0 py-3 px-4" style="background: #1a1a2e;">
                    <h6 class="modal-title text-white fw-bold mb-0">🍴 Rincian Menu yang Dipesan</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4 py-3 border-0 text-muted small" style="width: 50%;">NAMA ITEM</th>
                                    <th class="py-3 border-0 text-muted small text-center">QTY</th>
                                    <th class="py-3 border-0 text-muted small text-end">HARGA</th>
                                    <th class="px-4 py-3 border-0 text-muted small text-end">SUBTOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pesanan->details as $item)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="fw-bold text-dark" style="font-size: 14px;">{{ $item->menu->nama_menu ?? 'Menu Terhapus' }}</div>
                                        <small class="text-muted">{{ $item->menu->kategori->nama_kategori ?? 'Umum' }}</small>
                                    </td>
                                    <td class="text-center fw-bold">{{ $item->jumlah }}</td>
                                    <td class="text-end">Rp {{ number_format($item->harga_at_porsi, 0, ',', '.') }}</td>
                                    <td class="px-4 text-end fw-bold text-dark">
                                        Rp {{ number_format($item->jumlah * $item->harga_at_porsi, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 p-4">
                    <div class="p-4 shadow-sm" style="background: #f8faff; border-radius: 12px; border: 1px dashed #0d6efd;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted fw-bold">TOTAL ITEM</span>
                            <span class="fw-bold">{{ $pesanan->details->sum('jumlah') }} Item</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="fw-black mb-0" style="color: #1a1a2e;">TOTAL BAYAR</h4>
                            <h2 class="fw-black mb-0 text-primary" style="font-size: 32px;">
                                Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tambahkan style khusus --}}
<style>
    .fw-black { font-weight: 900; }
    .uppercase { text-transform: uppercase; }
    @media print {
        .sidebar, .topbar, .btn, .breadcrumb { display: none !important; }
        .main-content { margin-left: 0 !important; padding: 0 !important; }
        .card { border: 1px solid #ddd !important; box-shadow: none !important; }
    }
</style>

@endsection
