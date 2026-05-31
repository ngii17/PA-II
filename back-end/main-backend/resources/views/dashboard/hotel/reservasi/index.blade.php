@extends('dashboard.layouts.app')
@section('title', 'Reservasi Hotel')
@section('content')

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">📋 Manajemen Reservasi</h4>
            <p class="text-muted small mb-0">Kelola data pemesanan kamar tamu secara real-time.</p>
        </div>
        <a href="{{ route('dashboard.hotel.reservasi.create') }}" class="btn btn-primary px-4 shadow-sm" style="border-radius:10px;">
            <i class="fas fa-plus me-2"></i> Tambah Reservasi
        </a>
    </div>

    {{-- Notifikasi Sukses --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" style="border-radius:12px;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border-radius:15px; border-left: 5px solid #0d6efd;">
                <div class="card-body py-3">
                    <small class="text-muted fw-bold uppercase">TOTAL RESERVASI</small>
                    <h4 class="fw-bold mb-0">{{ $totalReservasi }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border-radius:15px; border-left: 5px solid #198754;">
                <div class="card-body py-3">
                    <small class="text-muted fw-bold uppercase">AKTIF / LUNAS</small>
                    {{-- SINKRONISASI: Menampilkan jumlah Terbayar, Check-in, dan Selesai --}}
                    <h4 class="fw-bold text-success mb-0">{{ $reservasi->whereIn('status_reservasi_id', [2, 3, 4])->count() }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border-radius:15px; border-left: 5px solid #ffc107;">
                <div class="card-body py-3">
                    <small class="text-muted fw-bold uppercase">PENDING</small>
                    <h4 class="fw-bold text-warning mb-0">{{ $pendingCount }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border-radius:15px; border-left: 5px solid #1a1a2e; background: #f8f9fa;">
                <div class="card-body py-3">
                    <small class="text-muted fw-bold uppercase">ESTIMASI PENDAPATAN</small>
                    <h4 class="fw-bold text-primary mb-0">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Data --}}
    <div class="card border-0 shadow-sm" style="border-radius:16px; overflow:hidden;">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr style="background:#1a1a2e;">
                        <th class="px-4 py-3">NO</th>
                        <th>TAMU</th>
                        <th>TIPE KAMAR</th>
                        <th>CHECK IN/OUT</th>
                        <th class="text-end">TOTAL</th>
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
                            <span class="fw-bold">{{ $r->tipeKamar->nama_tipe ?? '-' }}</span><br>
                            @if($r->kamar)
                                <span class="badge bg-success" style="font-size:10px;">Kamar: {{ $r->kamar->nomor_kamar }}</span>
                            @else
                                <span class="badge bg-secondary" style="font-size:10px;">Belum Assign Kamar</span>
                            @endif
                        </td>
                        <td style="font-size:12px;">
                            <span class="text-primary"><i class="far fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($r->tgl_checkin)->format('d/m/Y') }}</span><br>
                            <span class="text-danger"><i class="far fa-calendar-check"></i> {{ \Carbon\Carbon::parse($r->tgl_checkout)->format('d/m/Y') }}</span>
                        </td>
                        <td class="text-end fw-bold">Rp {{ number_format($r->total_harga, 0, ',', '.') }}</td>
                        <td class="text-center">
                            @php
                                // SINKRONISASI LOGIKA STATUS BARU (1-5)
                                $statusId = $r->status_reservasi_id;
                                switch($statusId) {
                                    case 1: $bg = 'warning'; $label = 'PENDING'; break;
                                    case 2: $bg = 'primary'; $label = 'TERBAYAR'; break;
                                    case 3: $bg = 'success'; $label = 'CHECK-IN'; break;
                                    case 4: $bg = 'secondary'; $label = 'SELESAI'; break;
                                    case 5: $bg = 'danger'; $label = 'BATAL'; break;
                                    default: $bg = 'dark'; $label = 'N/A';
                                }
                            @endphp
                            <span class="badge bg-{{ $bg }}" style="font-size:10px; border-radius:8px; padding: 5px 10px;">
                                {{ $label }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('dashboard.hotel.reservasi.show', $r->id) }}" class="btn btn-sm btn-light border shadow-sm" title="Detail">
                                    <i class="fas fa-eye text-info"></i>
                                </a>
                                <a href="{{ route('dashboard.hotel.reservasi.edit', $r->id) }}" class="btn btn-sm btn-light border shadow-sm" title="Edit/Update Status">
                                    <i class="fas fa-edit text-warning"></i>
                                </a>
                                <form action="{{ route('dashboard.hotel.reservasi.destroy', $r->id) }}" method="POST" onsubmit="return confirm('Batalkan/Hapus reservasi ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-light border shadow-sm" title="Hapus">
                                        <i class="fas fa-trash text-danger"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-5 text-muted">Belum ada data reservasi hotel.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .uppercase { text-transform: uppercase; letter-spacing: 0.5px; }
    .table-hover tbody tr:hover { background-color: rgba(0,0,0,.02); }
</style>

@endsection