@extends('dashboard.layouts.app')
@section('title', 'Tipe Kamar')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">🏷️ Tipe Kamar & Harga</h4>
        <a href="{{ route('dashboard.hotel.tipe-kamar.create') }}" class="btn btn-primary px-4 shadow-sm" style="border-radius:10px;">
            + Tambah Tipe
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('success') }}</div>
    @endif

    {{-- TAMBAHKAN INI AGAR TAHU ALASAN GAGAL HAPUS --}}
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-left: 5px solid red !important;">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
        </div>
    @endif

    <div class="row g-4">
        @foreach($tipe as $t)
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius:20px; transition: transform 0.3s;">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-1">{{ $t->nama_tipe }}</h5>
                    <h4 class="text-success fw-bold mb-3">
                        Rp {{ number_format($t->harga, 0, ',', '.') }} <small class="text-muted" style="font-size:12px;">/malam</small>
                    </h4>
                    <hr class="opacity-50">

                    <div class="mb-3">
                        <p class="small text-muted mb-2"><i class="fas fa-users me-2"></i> Kapasitas: <strong>{{ $t->kapasitas }} Orang</strong></p>
                        <p class="small text-muted mb-0"><i class="fas fa-star me-2"></i> Fasilitas: {{ Str::limit($t->fasilitas, 40) }}</p>
                    </div>

                    {{-- TOMBOL AKSI --}}
                    <div class="row g-2 mt-2">
                        <div class="col-12">
                            <button type="button" class="btn btn-info w-100 text-white fw-bold py-2 mb-2 shadow-sm"
                                    style="border-radius:10px;" data-bs-toggle="modal" data-bs-target="#detailModal{{ $t->id }}">
                                <i class="fas fa-eye me-1"></i> Lihat Detail
                            </button>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('dashboard.hotel.tipe-kamar.edit', $t->id) }}" class="btn btn-warning w-100 text-white fw-bold py-2 shadow-sm" style="border-radius:10px;">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                        </div>
                        <div class="col-6">
                            {{-- Tombol trigger SweetAlert --}}
                            <button type="button" class="btn btn-danger w-100 fw-bold py-2 shadow-sm"
                                    style="border-radius:10px;"
                                    onclick="konfirmasiHapusTipe({{ $t->id }}, '{{ $t->nama_tipe }}')">
                                <i class="fas fa-trash me-1"></i> Hapus
                            </button>

                            {{-- Form Hapus (Disembunyikan, akan dijalankan oleh JavaScript) --}}
                            <form id="form-hapus-{{ $t->id }}" action="{{ route('dashboard.hotel.tipe-kamar.destroy', $t->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL POPUP DETAIL TIPE KAMAR --}}
        <div class="modal fade" id="detailModal{{ $t->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius:25px; overflow:hidden;">
                    <div class="modal-header border-0 py-3 px-4" style="background:#1a1a2e;">
                        <h6 class="modal-title text-white fw-bold"><i class="fas fa-info-circle me-2"></i> Informasi Lengkap Tipe Kamar</h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="text-center mb-4">
                            <h3 class="fw-bold text-dark mb-0">{{ $t->nama_tipe }}</h3>
                            <h2 class="text-success fw-bold mt-2">Rp {{ number_format($t->harga, 0, ',', '.') }}<span class="fs-6 text-muted">/malam</span></h2>
                        </div>

                        <div class="bg-light p-3 rounded-4 mb-4">
                            <h6 class="fw-bold small text-primary mb-3 uppercase"><i class="fas fa-list me-2"></i> Fasilitas Utama</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @php $arrFasilitas = explode(',', $t->fasilitas); @endphp
                                @foreach($arrFasilitas as $f)
                                    <span class="badge bg-white text-dark border px-3 py-2 shadow-sm" style="border-radius:8px;">
                                        <i class="fas fa-check text-success me-1"></i> {{ trim($f) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold small text-primary mb-2 uppercase"><i class="fas fa-align-left me-2"></i> Deskripsi</h6>
                            <p class="text-secondary small" style="line-height: 1.6;">
                                {{ $t->deskripsi ?? 'Tidak ada deskripsi tambahan untuk tipe kamar ini.' }}
                            </p>
                        </div>

                        <div class="row g-2">
                            <div class="col-6">
                                <div class="p-3 border rounded-4 text-center">
                                    <small class="text-muted d-block">Kapasitas</small>
                                    <strong class="fs-5">{{ $t->kapasitas }} Orang</strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 border rounded-4 text-center">
                                    <small class="text-muted d-block">Kamar Aktif</small>
                                    <strong class="fs-5">{{ $t->kamar->count() }} Unit</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4">
                        <button type="button" class="btn btn-outline-secondary w-100 fw-bold py-2" data-bs-dismiss="modal" style="border-radius:12px;">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<style>
    .card:hover {
        transform: translateY(-5px);
    }
    .uppercase { text-transform: uppercase; letter-spacing: 1px; }
</style>
@endsection {{-- Ini batas akhir konten kamu --}}


@push('scripts')
<script>
function konfirmasiHapusTipe(id, nama) {
    Swal.fire({
        title: 'Hapus Tipe Kamar?',
        text: "Anda akan menonaktifkan " + nama + ". Pastikan tidak ada unit kamar aktif di dalamnya!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#1a1a2e', // Warna Navy
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        borderRadius: '15px'
    }).then((result) => {
        if (result.isConfirmed) {
            // Pastikan ID form-nya cocok
            const form = document.getElementById('form-hapus-' + id);
            if (form) {
                form.submit();
            } else {
                console.error("Form tidak ditemukan untuk ID: " + id);
            }
        }
    })
}
</script>
@endpush
