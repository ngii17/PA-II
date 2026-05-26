@extends('dashboard.layouts.app')
@section('title', 'Menu Restoran')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">🍽️ Menu Restoran</h4>
        <a href="{{ route('dashboard.restoran.menu.create') }}" class="btn btn-primary px-4 shadow-sm" style="border-radius:10px;">
            <i class="fas fa-plus me-2"></i> Tambah Menu
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" style="border-radius:12px;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius:16px; overflow:hidden;">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr style="background:#1a1a2e;">
                        <th class="px-4 py-3">No</th>
                        <th>Nama Menu</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th class="text-center">Stok</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($menu as $i => $m)
                    @php
                        // Logika Satuan
                        $kat = strtolower($m->kategori->nama_kategori ?? '');
                        $satuan = 'Pcs';
                        if(str_contains($kat, 'minuman')) $satuan = 'Gelas';
                        elseif(str_contains($kat, 'makanan') || str_contains($kat, 'main')) $satuan = 'Porsi';
                    @endphp
                    <tr>
                        <td class="px-4 text-muted small">{{ $i + 1 }}</td>
                        <td class="fw-bold" style="font-size:14px;">{{ $m->nama_menu }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $m->kategori->nama_kategori ?? '-' }}</span></td>
                        <td class="fw-bold text-success">Rp {{ number_format($m->harga, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <span class="badge {{ $m->stok > 5 ? 'bg-success' : 'bg-warning' }} rounded-pill px-3">
                                {{ $m->stok }} {{ $satuan }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $m->status_menu_id == 1 ? 'success' : 'danger' }}" style="font-size:10px;">
                                {{ $m->status->nama_status ?? '-' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <button type="button" class="btn btn-sm btn-info text-white px-2" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $m->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="{{ route('dashboard.restoran.menu.edit', $m->id) }}" class="btn btn-sm btn-warning text-white px-2">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('dashboard.restoran.menu.destroy', $m->id) }}" method="POST" onsubmit="return confirm('Nonaktifkan menu ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger px-2"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    {{-- MODAL DETAIL --}}
                    <div class="modal fade" id="modalDetail{{ $m->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg" style="border-radius:16px; overflow:hidden;">
                                <div class="modal-header border-0 py-3 px-4" style="background:#1a1a2e;">
                                    <h6 class="modal-title text-white fw-bold">🍽️ Rincian Menu</h6>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="text-center mb-4">
                                        <h4 class="fw-bold text-dark mb-1">{{ $m->nama_menu }}</h4>
                                        <span class="badge bg-primary px-3">{{ $m->kategori->nama_kategori ?? 'Umum' }}</span>
                                    </div>
                                    <div class="mb-3">
                                        <label class="text-muted small fw-bold uppercase d-block mb-1">Deskripsi</label>
                                        <div class="p-3 bg-light rounded italic" style="font-size: 13px;">
                                            {{ $m->deskripsi ?? 'Tidak ada deskripsi.' }}
                                        </div>
                                    </div>
                                    <div class="row g-3 text-center">
                                        <div class="col-6 border-end">
                                            <label class="text-muted small d-block">Harga</label>
                                            <h5 class="fw-bold text-success">Rp {{ number_format($m->harga) }}</h5>
                                        </div>
                                        <div class="col-6">
                                            <label class="text-muted small d-block">Stok</label>
                                            <h5 class="fw-bold">{{ $m->stok }} {{ $satuan }}</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 p-3">
                                    <button type="button" class="btn btn-outline-secondary w-100" data-bs-dismiss="modal" style="border-radius:10px;">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr><td colspan="7" class="text-center py-5 text-muted">Belum ada menu.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
