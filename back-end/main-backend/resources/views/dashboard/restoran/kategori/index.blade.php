@extends('dashboard.layouts.app')
@section('title', 'Kategori Menu')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">📂 Kategori Menu</h4>
        <a href="{{ route('dashboard.restoran.kategori.create') }}" class="btn btn-primary">
            + Tambah Kategori
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius:16px; overflow:hidden;">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr style="background:#1a1a2e;">
                        <th class="px-4 py-3">No</th>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th>Jumlah Menu</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kategori as $i => $k)
                    <tr>
                        <td class="px-4 text-muted">{{ $i + 1 }}</td>
                        <td class="fw-bold">{{ $k->nama_kategori }}</td>

                        {{-- Menampilkan Deskripsi (Pastikan nama kolom di DB adalah 'deskripsi') --}}
                        <td class="text-muted small">
                            {{ Str::limit($k->deskripsi, 50) ?? '-' }}
                        </td>

                        <td>
                            <span class="badge bg-info text-white" style="border-radius:8px; padding: 5px 10px;">
                                {{ $k->menus->count() }} menu
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                {{-- TOMBOL LIHAT (POPUP) --}}
                                <button type="button" class="btn btn-sm btn-info text-white px-3"
                                        data-bs-toggle="modal" data-bs-target="#modalDetail{{ $k->id }}">
                                    Lihat
                                </button>

                                <a href="{{ route('dashboard.restoran.kategori.edit', $k->id) }}" class="btn btn-sm btn-warning px-3">
                                    Edit
                                </a>

                                <form action="{{ route('dashboard.restoran.kategori.destroy', $k->id) }}" method="POST" onsubmit="return confirm('Nonaktifkan kategori ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger px-3">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-5 text-muted">Belum ada kategori.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL DETAIL KATEGORI --}}
@foreach($kategori as $k)
<div class="modal fade" id="modalDetail{{ $k->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px; overflow:hidden;">
            <div class="modal-header border-0 py-3 px-4" style="background:#1a1a2e;">
                <h6 class="modal-title text-white fw-bold">📂 Detail Kategori</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4">
                    <label class="text-muted small fw-bold uppercase d-block mb-1">Nama Kategori</label>
                    <h5 class="fw-bold text-dark">{{ $k->nama_kategori }}</h5>
                </div>

                <div class="mb-4">
                    <label class="text-muted small fw-bold uppercase d-block mb-1">Deskripsi</label>
                    <p class="text-secondary" style="line-height:1.6;">{{ $k->deskripsi ?? 'Tidak ada deskripsi untuk kategori ini.' }}</p>
                </div>

                <hr>

                <div class="mt-3">
                    <label class="text-muted small fw-bold uppercase d-block mb-2">Daftar Menu ({{ $k->menus->count() }})</label>
                    <div class="list-group list-group-flush">
                        @forelse($k->menus as $menu)
                            <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span class="small">{{ $menu->nama_menu }}</span>
                                <span class="badge bg-light text-dark border">Rp {{ number_format($menu->harga) }}</span>
                            </div>
                        @empty
                            <p class="text-muted small italic">Belum ada menu dalam kategori ini.</p>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4">
                <button type="button" class="btn btn-outline-secondary w-100" data-bs-dismiss="modal" style="border-radius:10px;">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection
