@extends('dashboard.layouts.app')
@section('title', 'Detail Reservasi #RES-' . $reservasi->id)
@section('content')

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0 text-dark">📄 Detail Reservasi</h4>
            <p class="text-muted small">Informasi lengkap pemesanan kamar tamu</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-outline-dark shadow-sm">
                <i class="fas fa-print me-2"></i> Cetak Struk
            </button>
            <a href="{{ route('dashboard.hotel.reservasi.index') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- Sisi Kiri: Ringkasan Status --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        @php
                            $sid = $reservasi->status_reservasi_id;
                            $bg = $sid == 2 ? 'bg-success' : ($sid == 3 ? 'bg-info' : ($sid == 1 ? 'bg-warning' : 'bg-danger'));
                        @endphp
                        <span class="badge {{ $bg }} py-2 px-3 fs-6" style="border-radius: 10px;">
                            {{ strtoupper($reservasi->statusReservasi->nama_status ?? 'N/A') }}
                        </span>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">ID: RES-{{ $reservasi->id }}</h5>
                    <p class="text-muted small">Dibuat pada: {{ $reservasi->created_at->format('d M Y, H:i') }}</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h6 class="fw-bold text-primary mb-0 uppercase" style="letter-spacing: 1px;">👤 Data Pelanggan</h6>
                </div>
                <div class="card-body p-4">
                    @php $user = $users[$reservasi->user_id] ?? null; @endphp
                    <div class="text-center mb-4">
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center fw-bold shadow-sm mb-3"
                            style="width:60px; height:60px; font-size:24px; border: 3px solid #eee;">
                            {{ strtoupper(substr($user['full_name'] ?? 'T', 0, 1)) }}
                        </div>
                        <h5 class="fw-bold text-dark mb-0">{{ $user['full_name'] ?? 'Tamu Umum' }}</h5>
                        <p class="text-muted small">{{ $user['email'] ?? '-' }}</p>
                    </div>
                    <hr>
                    <table class="table table-sm table-borderless small mb-0">
                        <tr><td class="text-muted">No. Handphone</td><td class="text-end fw-bold">{{ $user['phone'] ?? '-' }}</td></tr>
                        <tr><td class="text-muted">Metode Bayar</td><td class="text-end fw-bold text-success">{{ $reservasi->metode_pembayaran }}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- Sisi Kanan: Rincian Menginap --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; overflow: hidden;">
                <div class="card-header border-0 py-3 px-4" style="background:#1a1a2e;">
                    <h6 class="modal-title text-white fw-bold mb-0">🛏️ Rincian Kamar & Biaya</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="small text-muted d-block uppercase mb-1">Tipe Kamar</label>
                            <h5 class="fw-bold text-dark">{{ $reservasi->tipeKamar->nama_tipe ?? '-' }}</h5>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted d-block uppercase mb-1">Nomor Kamar</label>
                            <span class="badge bg-primary fs-6 px-3">
                                {{ $reservasi->kamar->nomor_kamar ?? 'Menunggu Penempatan' }}
                            </span>
                        </div>
                    </div>

                    <div class="p-4 rounded-4 mb-4" style="background: #f8f9ff; border: 1px dashed #0d6efd;">
                        <div class="row align-items-center">
                            <div class="col-md-4 text-center border-end">
                                <small class="text-muted d-block mb-1">CHECK IN</small>
                                <strong class="text-primary fs-5">{{ \Carbon\Carbon::parse($reservasi->tgl_checkin)->format('d M Y') }}</strong>
                            </div>
                            <div class="col-md-4 text-center border-end">
                                <small class="text-muted d-block mb-1">DURASI</small>
                                <strong class="text-dark fs-5">{{ $reservasi->total_malam }} Malam</strong>
                            </div>
                            <div class="col-md-4 text-center">
                                <small class="text-muted d-block mb-1">CHECK OUT</small>
                                <strong class="text-danger fs-5">{{ \Carbon\Carbon::parse($reservasi->tgl_checkout)->format('d M Y') }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr class="fs-6">
                                    <td>Harga per Malam</td>
                                    <td class="text-end">Rp {{ number_format($reservasi->tipeKamar->harga ?? 0, 0, ',', '.') }}</td>
                                </tr>
                                <tr class="fs-6">
                                    <td>Jumlah Malam</td>
                                    <td class="text-end">x {{ $reservasi->total_malam }}</td>
                                </tr>
                                <tr class="border-top fs-4 fw-black">
                                    <td class="pt-3" style="color:#1a1a2e;">TOTAL PEMBAYARAN</td>
                                    <td class="pt-3 text-end text-primary">Rp {{ number_format($reservasi->total_harga, 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light border-0 text-center py-3">
                    <small class="text-muted italic"><i class="fas fa-info-circle me-1"></i> Harap tunjukkan Invoice ini saat melakukan Check-in di resepsionis.</small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .fw-black { font-weight: 900; }
    .uppercase { text-transform: uppercase; letter-spacing: 1px; }
    @media print {
        .sidebar, .topbar, .btn, .breadcrumb { display: none !important; }
        .main-content { margin-left: 0 !important; padding: 0 !important; }
        .card { border: 1px solid #eee !important; box-shadow: none !important; }
    }
</style>
@endsection
