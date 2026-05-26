@extends('dashboard.layouts.app')
@section('title', 'Laporan Sistem')
@section('content')

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">📊 Laporan Keuangan & Statistik</h4>
            <p class="text-muted small mb-0">Pantau performa bisnis hotel dan restoran secara real-time.</p>
        </div>
        <div class="d-flex gap-2">
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-file-export me-1"></i> Export Hotel
                </button>
                <ul class="dropdown-menu border-0 shadow">
                    <li><a class="dropdown-item" href="{{ route('dashboard.laporan.pdf.hotel') }}">📄 Export ke PDF</a></li>
                    <li><a class="dropdown-item" href="{{ route('dashboard.laporan.excel.hotel') }}">📊 Export ke Excel</a></li>
                </ul>
            </div>
            <div class="dropdown">
                <button class="btn btn-success dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-file-export me-1"></i> Export Restoran
                </button>
                <ul class="dropdown-menu border-0 shadow">
                    <li><a class="dropdown-item" href="{{ route('dashboard.laporan.pdf.restoran') }}">📄 Export ke PDF</a></li>
                    <li><a class="dropdown-item" href="{{ route('dashboard.laporan.excel.restoran') }}">📊 Export ke Excel</a></li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Info Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3" style="border-radius:15px; border-left:5px solid #0d6efd !important;">
                <p class="text-muted small mb-1 uppercase fw-bold">Pendapatan Hotel</p>
                <h3 class="fw-bold text-primary">Rp {{ number_format($totalHotel, 0, ',', '.') }}</h3>
                <small class="text-muted">{{ $totalTransaksiHotel }} Transaksi Sukses</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3" style="border-radius:15px; border-left:5px solid #198754 !important;">
                <p class="text-muted small mb-1 uppercase fw-bold">Pendapatan Restoran</p>
                <h3 class="fw-bold text-success">Rp {{ number_format($totalRestoran, 0, ',', '.') }}</h3>
                <small class="text-muted">{{ $totalTransaksiRestoran }} Transaksi Lunas</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3" style="border-radius:15px; background: #1a1a2e; color: white;">
                <p class="opacity-75 small mb-1 uppercase fw-bold">Total Keseluruhan</p>
                <h3 class="fw-bold">Rp {{ number_format($totalHotel + $totalRestoran, 0, ',', '.') }}</h3>
                <small class="opacity-50">Akumulasi Gabungan</small>
            </div>
        </div>
    </div>

    {{-- Grafik --}}
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm" style="border-radius:16px;">
                <div class="card-header bg-white border-0 pt-3"><h6 class="fw-bold"><i class="fas fa-hotel me-2 text-primary"></i>Tren Reservasi Hotel</h6></div>
                <div class="card-body"><canvas id="chartHotel"></canvas></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm" style="border-radius:16px;">
                <div class="card-header bg-white border-0 pt-3"><h6 class="fw-bold"><i class="fas fa-utensils me-2 text-success"></i>Tren Pesanan Restoran</h6></div>
                <div class="card-body"><canvas id="chartResto"></canvas></div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const labels = {!! json_encode($bulanLabels) !!};

    // Data Hotel
    new Chart(document.getElementById('chartHotel'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah Reservasi',
                data: {!! json_encode($bulanLabels->map(fn($b) => $reservasiPerBulan->where('bulan', $b)->first()->total ?? 0)) !!},
                borderColor: '#0d6efd', backgroundColor: 'rgba(13, 110, 253, 0.1)', fill: true, tension: 0.4
            }]
        }
    });

    // Data Restoran
    new Chart(document.getElementById('chartResto'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah Pesanan',
                data: {!! json_encode($bulanLabels->map(fn($b) => $pesananPerBulan->where('bulan', $b)->first()->total ?? 0)) !!},
                borderColor: '#198754', backgroundColor: 'rgba(25, 135, 84, 0.1)', fill: true, tension: 0.4
            }]
        }
    });
</script>
@endpush
