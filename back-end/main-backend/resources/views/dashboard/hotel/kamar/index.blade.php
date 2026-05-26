@extends('dashboard.layouts.app')
@section('title', 'Manajemen Kamar')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">🏨 Manajemen Kamar</h4>
        <a href="{{ route('dashboard.hotel.kamar.create') }}" class="btn btn-primary px-4 shadow-sm" style="border-radius:10px;">
            <i class="fas fa-plus me-2"></i> Tambah Kamar
        </a>
    </div>

    {{-- Letakkan di bawah judul atau di atas daftar card --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" style="border-radius:12px;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" style="border-radius:12px; background-color: #fff5f5; color: #dc3545; border-left: 5px solid #dc3545 !important;">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-3 fs-4"></i>
                <div>
                    <strong class="d-block">Tindakan Ditolak</strong>
                    <span class="small">{{ session('error') }}</span>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius:16px; overflow:hidden;">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr style="background:#1a1a2e;">
                        <th class="px-4 py-3">NO</th>
                        <th>NOMOR KAMAR</th>
                        <th>TIPE KAMAR</th>
                        <th class="text-center">STATUS</th>
                        <th class="text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kamar as $i => $k)
                    <tr>
                        <td class="px-4 text-muted small">{{ $i + 1 }}</td>
                        <td class="fw-bold fs-5 text-primary">{{ $k->nomor_kamar }}</td>
                        <td>{{ $k->tipeKamar->nama_tipe ?? '-' }}</td>
                        <td class="text-center">
                            @php
                                $colors = [1 => 'success', 2 => 'danger', 3 => 'secondary'];
                            @endphp
                            <span class="badge bg-{{ $colors[$k->status_kamar_id] ?? 'dark' }} px-3 py-2" style="border-radius:8px; font-size:11px;">
                                {{ $k->statusKamar->nama_status ?? '-' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                {{-- Tombol Lihat --}}
                                <button type="button" class="btn btn-sm btn-info text-white px-3"
                                        data-bs-toggle="modal" data-bs-target="#modalDetail{{ $k->id }}">
                                    Lihat
                                </button>

                                {{-- Tombol Edit --}}
                                <a href="{{ route('dashboard.hotel.kamar.edit', $k->id) }}" class="btn btn-sm btn-warning px-3 text-white">
                                    Edit
                                </a>

                                {{-- Tombol Hapus (SweetAlert) --}}
                                <button type="button" class="btn btn-sm btn-danger px-3 shadow-sm"
                                        onclick="konfirmasiHapusKamar({{ $k->id }}, '{{ $k->nomor_kamar }}')">
                                    Hapus
                                </button>

                                {{-- Form Hapus Tersembunyi Khusus Kamar --}}
                                <form id="form-hapus-kamar-{{ $k->id }}" action="{{ route('dashboard.hotel.kamar.destroy', $k->id) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>

                    {{-- MODAL DETAIL KAMAR --}}
                    <div class="modal fade" id="modalDetail{{ $k->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg" style="border-radius:20px; overflow:hidden;">
                                <div class="modal-header border-0 py-3 px-4" style="background:#1a1a2e;">
                                    <h6 class="modal-title text-white fw-bold">🛏️ Rincian Kamar {{ $k->nomor_kamar }}</h6>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="text-center mb-4">
                                        <h2 class="fw-bold text-primary mb-1">{{ $k->nomor_kamar }}</h2>
                                        <span class="badge bg-primary-subtle text-primary px-3 py-2" style="border-radius:8px;">
                                            {{ $k->tipeKamar->nama_tipe ?? 'Tipe Tidak Diketahui' }}
                                        </span>
                                    </div>

                                    <div class="bg-light p-3 rounded-3 mb-3">
                                        <div class="row align-items-center">
                                            <div class="col-6 border-end">
                                                <small class="text-muted d-block small">HARGA / MALAM</small>
                                                <strong class="text-success fs-5">Rp {{ number_format($k->tipeKamar->harga ?? 0, 0, ',', '.') }}</strong>
                                            </div>
                                            <div class="col-6 ps-4">
                                                <small class="text-muted d-block small">KAPASITAS</small>
                                                <strong class="text-dark"><i class="fas fa-users me-1"></i> {{ $k->tipeKamar->kapasitas ?? '0' }} Orang</strong>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="text-muted small fw-bold uppercase d-block mb-1">FASILITAS KAMAR</label>
                                        <p class="text-secondary small" style="line-height:1.6;">
                                            {{ $k->tipeKamar->fasilitas ?? 'Fasilitas standar Purnama Hotel.' }}
                                        </p>
                                    </div>

                                    <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                                        <span class="small text-muted">Status Saat Ini:</span>
                                        <span class="badge bg-{{ $colors[$k->status_kamar_id] ?? 'dark' }} px-3 py-2">
                                            {{ $k->statusKamar->nama_status ?? '-' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 px-4 pb-4">
                                    <button type="button" class="btn btn-outline-secondary w-100" data-bs-dismiss="modal" style="border-radius:12px;">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function konfirmasiHapusKamar(id, nomor) {
    Swal.fire({
        title: 'Nonaktifkan Kamar?',
        text: "Kamar nomor " + nomor + " akan dinonaktifkan. Data riwayat reservasi tetap tersimpan.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#1a1a2e',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Nonaktifkan!',
        cancelButtonText: 'Batal',
        borderRadius: '15px'
    }).then((result) => {
        if (result.isConfirmed) {
            // PENTING: Gunakan ID form yang benar
            document.getElementById('form-hapus-kamar-' + id).submit();
        }
    })
}
</script>
@endpush
