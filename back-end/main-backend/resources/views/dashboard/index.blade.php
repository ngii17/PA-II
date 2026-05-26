@extends('dashboard.layouts.app')
@section('title', 'Dashboard Utama')
@section('content')

<div class="mb-4">
    <h3 class="fw-bold mb-1">Selamat Datang, {{ session('user.name') }}! 👋</h3>
    <p class="text-muted">{{ now()->translatedFormat('l, d F Y') }}</p>
</div>

{{-- ========================================== --}}
{{-- VIEW KHUSUS ADMIN --}}
{{-- ========================================== --}}
@if(session('user.role') === 'admin')
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white" style="border-radius:16px; background:#0d6efd;">
            <div class="card-body p-4">
                <small class="opacity-75 fw-bold">PENGGUNA TERDAFTAR</small>
                <h1 class="fw-black mb-0" style="font-size: 40px;">{{ $data['total_pengguna'] }}</h1>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white" style="border-radius:16px; background:#6f42c1;">
            <div class="card-body p-4">
                <small class="opacity-75 fw-bold">PROMO AKTIF</small>
                <h1 class="fw-black mb-0" style="font-size: 40px;">{{ $data['total_promo'] }}</h1>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white" style="border-radius:16px; background:#f59e0b;">
            <div class="card-body p-4">
                <small class="opacity-75 fw-bold">TOTAL ULASAN</small>
                <h1 class="fw-black mb-0" style="font-size: 40px;">{{ $data['total_ulasan'] }}</h1>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white" style="border-radius:16px; background:#198754;">
            <div class="card-body p-4 text-center">
                <small class="opacity-75 fw-bold">PENDAPATAN GLOBAL</small>
                <h3 class="fw-bold mb-0 mt-2">Rp {{ number_format($data['total_pendapatan'], 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
</div>

{{-- Grafik Admin --}}
<div class="row g-3">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm" style="border-radius:16px;">
            <div class="card-header bg-white border-0 pt-4 px-4"><h5 class="fw-bold mb-0">📊 Performa Bisnis (6 Bulan)</h5></div>
            <div class="card-body px-4 pb-4" style="height: 350px;"><canvas id="chartUtama"></canvas></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius:16px;">
            <div class="card-header bg-white border-0 pt-4 px-4"><h5 class="fw-bold mb-0">💳 Status Reservasi</h5></div>
            <div class="card-body d-flex align-items-center justify-content-center p-4"><canvas id="chartStatus"></canvas></div>
        </div>
    </div>
</div>

{{-- ========================================== --}}
{{-- VIEW KHUSUS STAFF HOTEL --}}
{{-- ========================================== --}}
@elseif(session('user.role') === 'staff_hotel')
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white" style="border-radius:16px; background:#0d6efd;">
            <div class="card-body py-3">
                <small class="fw-bold opacity-75">CHECK-IN HARI INI</small>
                <h1 class="fw-black mb-0">{{ $data['checkin_today'] }}</h1>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white" style="border-radius:16px; background:#dc3545;">
            <div class="card-body py-3">
                <small class="fw-bold opacity-75">CHECK-OUT HARI INI</small>
                <h1 class="fw-black mb-0">{{ $data['checkout_today'] }}</h1>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white" style="border-radius:16px; background:#198754;">
            <div class="card-body py-3">
                <small class="fw-bold opacity-75">KAMAR TERSEDIA</small>
                <h1 class="fw-black mb-0">{{ $data['kamar_tersedia'] }}</h1>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white" style="border-radius:16px; background:#6c757d;">
            <div class="card-body py-3">
                <small class="fw-bold opacity-75">KAMAR TERISI</small>
                <h1 class="fw-black mb-0">{{ $data['kamar_terisi'] }}</h1>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm" style="border-radius:16px;">
    <div class="card-header bg-white border-0 pt-4 px-4">
        <h5 class="fw-bold mb-0">📅 Kedatangan Tamu Hari Ini</h5>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr><th class="px-4">TAMU</th><th>TIPE KAMAR</th><th>NO. KAMAR</th><th>STATUS</th></tr>
            </thead>
            <tbody>
                @forelse($data['arrival_today'] as $arrival)
                    @php $u = $data['users'][$arrival->user_id] ?? null; @endphp
                    <tr>
                        <td class="px-4 py-3"><strong>{{ $u['full_name'] ?? 'Tamu' }}</strong></td>
                        <td>{{ $arrival->tipeKamar->nama_tipe }}</td>
                        <td><span class="badge bg-primary">{{ $arrival->kamar->nomor_kamar ?? 'N/A' }}</span></td>
                        <td><span class="badge bg-success">LUNAS</span></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center py-5 text-muted">Tidak ada tamu datang hari ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ========================================== --}}
{{-- VIEW KHUSUS STAFF RESTORAN --}}
{{-- ========================================== --}}
@elseif(session('user.role') === 'staff_restoran')
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white" style="border-radius:16px; background:#fd7e14;">
            <div class="card-body py-3">
                <small class="fw-bold opacity-75">PESANAN HARI INI</small>
                <h1 class="fw-black mb-0">{{ $data['total_pesanan'] }}</h1>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white" style="border-radius:16px; background:#6f42c1;">
            <div class="card-body py-3">
                <small class="fw-bold opacity-75">PESANAN PENDING</small>
                <h1 class="fw-black mb-0">{{ $data['pesanan_pending'] }}</h1>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white" style="border-radius:16px; background:#a07bde;">
            <div class="card-body py-3">
                <small class="fw-bold opacity-75">EVENT AKTIF</small>
                <h1 class="fw-black mb-0">{{ $data['event_aktif'] }}</h1>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white" style="border-radius:16px; background:#198754;">
            <div class="card-body py-3">
                <small class="fw-bold opacity-75">PENDAPATAN RESTO</small>
                <h4 class="fw-bold mb-0">Rp {{ number_format($data['total_pendapatan_resto'], 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm" style="border-radius:16px;">
    <div class="card-header bg-white border-0 pt-4 px-4">
        <h5 class="fw-bold mb-0">📦 Pesanan Terbaru</h5>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr><th class="px-4">PELANGGAN</th><th>TOTAL</th><th>STATUS</th></tr>
            </thead>
            <tbody>
                @forelse($data['pesanan_terbaru'] as $p)
                    @php $u = $data['users'][$p->user_id] ?? null; @endphp
                    <tr>
                        <td class="px-4 py-3"><strong>{{ $u['full_name'] ?? 'Pelanggan' }}</strong></td>
                        <td class="fw-bold text-success">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                        <td><span class="badge bg-{{ $p->status_pembayaran_id == 2 ? 'success' : 'warning' }}">{{ $p->statusPembayaran->nama_status ?? 'Pending' }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center py-5 text-muted">Belum ada pesanan terbaru.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
@if(session('user.role') === 'admin')
    Chart.defaults.font.size = 14;
    Chart.defaults.font.weight = 'bold';

    // Bar Chart Performa
    new Chart(document.getElementById('chartUtama'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($data['bulan_labels'] ?? []) !!},
            datasets: [
                { label: 'Hotel', data: {!! json_encode($data['reservasi_per_bulan'] ?? []) !!}, backgroundColor: '#0d6efd', borderRadius: 5 },
                { label: 'Resto', data: {!! json_encode($data['pesanan_per_bulan'] ?? []) !!}, backgroundColor: '#fd7e14', borderRadius: 5 }
            ]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // Doughnut Chart Status
    new Chart(document.getElementById('chartStatus'), {
        type: 'doughnut',
        data: {
            labels: ['Terbayar', 'Pending', 'Selesai', 'Batal'],
            datasets: [{
                data: {!! json_encode($data['status_reservasi'] ?? [0,0,0,0]) !!},
                backgroundColor: ['#198754','#ffc107','#0d6efd','#dc3545']
            }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } }, cutout: '70%' }
    });
@endif
</script>
@endpush
