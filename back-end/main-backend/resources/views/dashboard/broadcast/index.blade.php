@extends('dashboard.layouts.app')
@section('title', 'Manajemen Broadcast')
@section('content')

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">📢 Pengumuman & Broadcast</h4>
            <p class="text-muted small mb-0">Kirim notifikasi langsung ke seluruh perangkat HP pelanggan.</p>
        </div>
        <a href="{{ route('dashboard.admin.broadcast.create') }}" class="btn btn-primary px-4 shadow-sm" style="border-radius:10px;">
            <i class="fas fa-plus me-2"></i> Buat Draft Baru
        </a>
    </div>

    {{-- Alert Berhasil --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius:12px;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Alert Gagal --}}
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius:12px;">
            <i class="fas fa-times-circle me-2"></i> {{ session('error') }}
        </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius:16px; overflow:hidden;">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr style="background:#1a1a2e;">
                        <th class="px-4 py-3">NO</th>
                        <th>JUDUL PENGUMUMAN</th>
                        <th>JADWAL TAMPIL</th>
                        <th class="text-center">STATUS</th>
                        <th class="text-center">AKSI SEBARKAN</th>
                        <th class="text-center">KELOLA</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($broadcasts as $i => $b)
                    <tr>
                        <td class="px-4 text-muted small">{{ $i + 1 }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $b->title }}</div>
                            <small class="text-muted">{{ Str::limit($b->body, 50) }}</small>
                        </td>
                        <td>
                            <span class="small"><i class="far fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::parse($b->publish_date)->format('d M Y') }}</span>
                        </td>
                        <td class="text-center">
                            @if($b->status == 'sent')
                                <span class="badge bg-success bg-opacity-10 text-success border border-success" style="font-size: 10px;">SUDAH TERKIRIM</span>
                            @else
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning" style="font-size: 10px;">DRAFT</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($b->status == 'draft')
                                <form action="{{ route('dashboard.admin.broadcast.send', $b->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyebarkan notifikasi ini ke SELURUH HP pengguna sekarang?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success px-3 shadow-sm" style="border-radius: 8px;">
                                        <i class="fas fa-paper-plane me-1"></i> SEBARKAN SEKARANG
                                    </button>
                                </form>
                            @else
                                <span class="text-muted small italic"><i class="fas fa-check-double me-1"></i> Selesai disebar</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <form action="{{ route('dashboard.admin.broadcast.destroy', $b->id) }}" method="POST" onsubmit="return confirm('Hapus draft ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger border-0"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-5 text-muted">Belum ada draft pengumuman.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection