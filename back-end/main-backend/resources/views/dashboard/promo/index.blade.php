@extends('dashboard.layouts.app')
@section('title', 'Kelola Promo')
@section('content')

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">🎁 Kelola Promo Layanan</h4>
            <p class="text-muted small mb-0">Atur diskon dan promosi untuk Hotel dan Restoran.</p>
        </div>
        <a href="{{ route('dashboard.promo.create') }}" class="btn btn-primary px-4 shadow-sm" style="border-radius:10px;">
            <i class="fas fa-plus me-2"></i> Tambah Promo
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius:16px; overflow:hidden;">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr style="background:#1a1a2e;">
                        <th class="px-4 py-3">NO</th>
                        <th>NAMA PROMO</th>
                        <th>KODE</th>
                        <th>KATEGORI</th>
                        <th>DISKON</th>
                        <th class="text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($promos as $i => $p)
                    <tr>
                        <td class="px-4 text-muted small">{{ $i + 1 }}</td>
                        <td><div class="fw-bold" style="font-size:13px;">{{ $p->nama_promo }}</div></td>
                        <td><span class="badge bg-light text-dark border fw-bold">{{ $p->kode_promo }}</span></td>
                        <td>
                            <span class="badge {{ $p->kategori == 'hotel' ? 'bg-primary' : 'bg-success' }}" style="font-size:10px;">
                                {{ strtoupper($p->kategori) }}
                            </span>
                        </td>
                        <td class="fw-bold text-success">
                            {{ $p->tipe_diskon == 'persen' ? $p->nominal_potongan.'%' : 'Rp '.number_format($p->nominal_potongan, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                {{-- Tombol Detail --}}
                                <button class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $p->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>

                                <a href="{{ route('dashboard.promo.edit', $p->id) }}" class="btn btn-sm btn-warning text-white">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('dashboard.promo.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Nonaktifkan promo ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    {{-- MODAL DETAIL PROMO --}}
                    <div class="modal fade" id="modalDetail{{ $p->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg" style="border-radius:16px; overflow:hidden;">
                                <div class="modal-header border-0 py-3 px-4" style="background:#1a1a2e;">
                                    <h6 class="modal-title text-white fw-bold">🎁 Rincian Promo</h6>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="text-center mb-4">
                                        <div class="badge bg-primary-subtle text-primary mb-2 px-3 py-2" style="font-size: 12px; border-radius:8px;">
                                            {{ strtoupper($p->kategori) }} PROMO
                                        </div>
                                        <h4 class="fw-bold mb-1">{{ $p->nama_promo }}</h4>
                                        <h2 class="text-success fw-black mt-2">
                                            {{ $p->tipe_diskon == 'persen' ? $p->nominal_potongan.'%' : 'Rp '.number_format($p->nominal_potongan, 0, ',', '.') }}
                                        </h2>
                                    </div>

                                    <div class="bg-light p-3 rounded-3 mb-3">
                                        <div class="row text-center">
                                            <div class="col-6 border-end">
                                                <small class="text-muted d-block small">KODE PROMO</small>
                                                <strong class="text-dark">{{ $p->kode_promo }}</strong>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted d-block small">TIPE DISKON</small>
                                                <strong class="text-dark">{{ ucfirst($p->tipe_diskon) }}</strong>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="border p-2 rounded text-center">
                                                <small class="text-muted d-block" style="font-size: 10px;">MULAI BERLAKU</small>
                                                <span class="fw-bold small">{{ \Carbon\Carbon::parse($p->tgl_mulai)->format('d M Y') }}</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="border p-2 rounded text-center">
                                                <small class="text-muted d-block" style="font-size: 10px;">BERAKHIR PADA</small>
                                                <span class="fw-bold small">{{ \Carbon\Carbon::parse($p->tgl_selesai)->format('d M Y') }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    @php
                                        $isExpired = \Carbon\Carbon::now()->gt(\Carbon\Carbon::parse($p->tgl_selesai));
                                    @endphp
                                    <div class="mt-4 text-center">
                                        <span class="badge {{ $isExpired ? 'bg-danger' : 'bg-success' }} px-4 py-2">
                                            {{ $isExpired ? '🔴 SUDAH BERAKHIR' : '🟢 PROMO SEDANG AKTIF' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 px-4 pb-4">
                                    <button type="button" class="btn btn-outline-secondary w-100" data-bs-dismiss="modal" style="border-radius:10px;">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    @empty
                    <tr><td colspan="6" class="text-center py-5 text-muted">Belum ada promo yang dibuat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
