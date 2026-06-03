@extends('dashboard.layouts.app')
@section('title', 'Dashboard Utama')
@section('content')

<div class="mb-4">
    <h3 class="fw-bold mb-1">Selamat Datang, {{ session('user.name') }}! 👋</h3>
    <p class="text-muted">{{ now()->translatedFormat('l, d F Y') }}</p>
</div>

{{-- ========================================== --}}
{{-- 🛡️ VIEW KHUSUS ADMIN (Management Global)   --}}
{{-- ========================================== --}}
@if(session('user.role') === 'admin')
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm text-white" style="border-radius:16px; background:#0d6efd;">
            <div class="card-body p-4">
                <small class="opacity-75 fw-bold">PENGGUNA</small>
                <h1 class="fw-black mb-0" style="font-size: 35px;">{{ $data['total_pengguna'] }}</h1>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm text-white" style="border-radius:16px; background:#6f42c1;">
            <div class="card-body p-4">
                <small class="opacity-75 fw-bold">PROMO AKTIF</small>
                <h1 class="fw-black mb-0" style="font-size: 35px;">{{ $data['total_promo'] }}</h1>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm text-white" style="border-radius:16px; background:#f59e0b;">
            <div class="card-body p-4">
                <small class="opacity-75 fw-bold">ULASAN</small>
                <h1 class="fw-black mb-0" style="font-size: 35px;">{{ $data['total_ulasan'] }}</h1>
            </div>
        </div>
    </div>
    {{-- STATISTIK BARU: BROADCAST --}}
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm text-white" style="border-radius:16px; background:#dc3545;">
            <div class="card-body p-4">
                <small class="opacity-75 fw-bold text-uppercase">Broadcast</small>
                <h1 class="fw-black mb-0" style="font-size: 35px;">{{ \App\Models\BroadcastNotification::where('status','sent')->count() }}</h1>
            </div>
        </div>
    </div>
</div>

{{-- Row Pendapatan Global --}}
<div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; background: #198754; color: white;">
    <div class="card-body p-4 d-flex justify-content-between align-items-center">
        <div>
            <small class="opacity-75 fw-bold text-uppercase">Total Pendapatan Terverifikasi (Global)</small>
            <h2 class="fw-bold mb-0">Rp {{ number_format($data['total_pendapatan'], 0, ',', '.') }}</h2>
        </div>
        <i class="fas fa-wallet fa-3x opacity-25"></i>
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
{{-- 🏨 VIEW KHUSUS STAFF HOTEL (Operational)   --}}
{{-- ========================================== --}}
@elseif(session('user.role') === 'staff hotel')
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm text-white" style="border-radius:16px; background:#0d6efd;">
            <div class="card-body py-3">
                <small class="fw-bold opacity-75">CHECK-IN HARI INI</small>
                <h1 class="fw-black mb-0">{{ $data['checkin_today'] }}</h1>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm text-white" style="border-radius:16px; background:#dc3545;">
            <div class="card-body py-3">
                <small class="fw-bold opacity-75">CHECK-OUT HARI INI</small>
                <h1 class="fw-black mb-0">{{ $data['checkout_today'] }}</h1>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm text-white" style="border-radius:16px; background:#198754;">
            <div class="card-body py-3">
                <small class="fw-bold opacity-75">KAMAR TERSEDIA</small>
                <h1 class="fw-black mb-0">{{ $data['kamar_tersedia'] }}</h1>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
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
                <tr><th class="px-4 small">TAMU</th><th class="small">TIPE</th><th class="small">NO. KAMAR</th><th class="small">STATUS</th></tr>
            </thead>
            <tbody>
                @forelse($data['arrival_today'] as $arrival)
                    @php $u = $data['users'][$arrival->user_id] ?? null; @endphp
                    <tr>
                        <td class="px-4 py-3"><strong>{{ $u['full_name'] ?? 'Tamu' }}</strong></td>
                        <td><small>{{ $arrival->tipeKamar->nama_tipe }}</small></td>
                        <td><span class="badge bg-primary">{{ $arrival->kamar->nomor_kamar ?? 'N/A' }}</span></td>
                        <td>
                            @php $sId = $arrival->status_reservasi_id; @endphp
                            <span class="badge bg-{{ $sId == 3 ? 'success' : ($sId == 2 ? 'primary' : 'warning') }}">
                                {{ $sId == 3 ? 'CHECK-IN' : ($sId == 2 ? 'PAID' : 'PENDING') }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center py-5 text-muted">Tidak ada kedatangan terjadwal hari ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ========================================== --}}
{{-- 🍽️ VIEW KHUSUS STAFF RESTORAN (Operational) --}}
{{-- ========================================== --}}
@elseif(session('user.role') === 'staff restoran')
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm text-white" style="border-radius:16px; background:#fd7e14;">
            <div class="card-body py-3">
                <small class="fw-bold opacity-75">PESANAN HARI INI</small>
                <h1 class="fw-black mb-0">{{ $data['total_pesanan'] }}</h1>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm text-white" style="border-radius:16px; background:#6f42c1;">
            <div class="card-body py-3">
                <small class="fw-bold opacity-75">PENDING BAYAR</small>
                <h1 class="fw-black mb-0">{{ $data['pesanan_pending'] }}</h1>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm text-white" style="border-radius:16px; background:#20c997;">
            <div class="card-body py-3">
                <small class="fw-bold opacity-75">STOK HABIS</small>
                <h1 class="fw-black mb-0">{{ $data['menu_habis'] }}</h1>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
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
        <h5 class="fw-bold mb-0">📦 5 Pesanan Terbaru</h5>
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
                        <td>
                            <span class="badge bg-{{ $p->status_pembayaran_id == 2 ? 'success' : 'warning' }}">
                                {{ $p->status_pembayaran_id == 2 ? 'LUNAS' : 'PENDING' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center py-5 text-muted">Belum ada pesanan masuk.</td></tr>
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
    Chart.defaults.font.size = 12;

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
        options: { 
            responsive: true, 
            maintainAspectRatio: false,
            plugins: { legend: { position: 'top' } }
        }
    });

    // Doughnut Chart Status (SINKRON 5 STATUS)
    new Chart(document.getElementById('chartStatus'), {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Terbayar', 'Check-in', 'Selesai', 'Batal'],
            datasets: [{
                data: {!! json_encode($data['status_reservasi'] ?? [0,0,0,0,0]) !!},
                backgroundColor: ['#ffc107','#0d6efd','#198754','#6c757d','#dc3545']
            }]
        },
        options: { 
            responsive: true, 
            plugins: { legend: { position: 'bottom' } }, 
            cutout: '70%' 
        }
    });
@endif
</script>
@endpush