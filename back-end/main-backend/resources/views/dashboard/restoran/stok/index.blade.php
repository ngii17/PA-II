@extends('dashboard.layouts.app')
@section('title', 'Manajemen Stok')
@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h4 class="fw-bold mb-1">📊 Manajemen Stok Menu</h4>
        <p class="text-muted small">Kelola ketersediaan menu secara akurat.</p>
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
                        <th class="text-center">Stok Saat Ini</th>
                        <th class="text-center">Update Stok</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($menu as $i => $m)
                    @php
                        $kat = strtolower($m->kategori->nama_kategori ?? '');
                        $satuan = str_contains($kat, 'minuman') ? 'Gelas' : (str_contains($kat, 'makanan') ? 'Porsi' : 'Pcs');
                    @endphp
                    <tr>
                        <td class="px-4 text-muted small">{{ $i + 1 }}</td>
                        <td class="fw-bold">{{ $m->nama_menu }} <br> <small class="badge bg-light text-dark border fw-normal">{{ $m->kategori->nama_kategori }}</small></td>
                        <td class="text-center">
                            <span class="badge {{ $m->stok > 10 ? 'bg-success' : ($m->stok > 0 ? 'bg-warning' : 'bg-danger') }} px-3 py-2" style="border-radius:10px; font-size:12px;">
                                {{ $m->stok }} {{ $satuan }}
                            </span>
                        </td>
                        <td class="text-center" style="width: 200px;">
                            <form action="{{ route('dashboard.restoran.stok.update', $m->id) }}" method="POST" class="d-flex gap-1 justify-content-center">
                                @csrf @method('PUT')
                                <input type="number" name="stok" value="{{ $m->stok }}" min="0" class="form-control form-control-sm text-center" style="width:70px; border-radius:8px;">
                                <button type="submit" class="btn btn-sm btn-primary" title="Simpan Cepat"><i class="fas fa-save"></i></button>
                            </form>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                {{-- Tombol Lihat Detail (Popup) --}}
                                <button type="button" class="btn btn-sm btn-info text-white" onclick="showDetail({{ $m->id }})">
                                    <i class="fas fa-eye"></i>
                                </button>

                                {{-- Tombol Edit (Arahkan ke MenuController Edit) --}}
                                <a href="{{ route('dashboard.restoran.stok.edit', $m->id) }}" class="btn btn-sm btn-warning text-white">
                                    <i class="fas fa-edit"></i>
                                </a>

                                {{-- Tombol Hapus (Reset ke 0 & Soft Delete) --}}
                                <form action="{{ route('dashboard.restoran.stok.destroy', $m->id) }}" method="POST" onsubmit="return confirm('Stok akan direset ke 0. Apakah Anda yakin?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-power-off"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-5 text-muted">Data menu tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL DETAIL --}}
<div class="modal fade" id="modalDetailStok" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;">
            <div class="modal-header bg-info text-white">
                <h6 class="modal-title fw-bold">🔍 Detail Informasi Menu</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="detailContent">
                <!-- Content via AJAX/JS -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function showDetail(id) {
        fetch(`/dashboard/restoran/stok/${id}`)
            .then(response => response.json())
            .then(data => {
                let content = `
                    <h4 class="fw-bold">${data.nama_menu}</h4>
                    <p class="text-muted">${data.deskripsi || 'Tidak ada deskripsi.'}</p>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6 border-end">
                            <small class="text-muted d-block">HARGA</small>
                            <span class="fw-bold text-success">Rp ${new Intl.NumberFormat('id-ID').format(data.harga)}</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">STOK SAAT INI</small>
                            <span class="fw-bold text-primary">${data.stok}</span>
                        </div>
                    </div>
                `;
                document.getElementById('detailContent').innerHTML = content;
                new bootstrap.Modal(document.getElementById('modalDetailStok')).show();
            });
    }
</script>
@endpush
