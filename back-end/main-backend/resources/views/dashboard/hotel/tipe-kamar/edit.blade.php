@extends('dashboard.layouts.app')
@section('title', 'Edit Tipe Kamar')
@section('content')

<div class="container-fluid px-4">
    <div class="mb-4">
        <a href="{{ route('dashboard.hotel.tipe-kamar.index') }}" class="text-decoration-none small text-muted">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
        <h4 class="fw-bold mt-2 text-dark">Edit Tipe Kamar: {{ $tipe->nama_tipe }}</h4>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius:16px;">
        <div class="card-body p-4">
            <form action="{{ route('dashboard.hotel.tipe-kamar.update', $tipe->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Nama Tipe -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Nama Tipe Kamar</label>
                        <input type="text" name="nama_tipe" class="form-control @error('nama_tipe') is-invalid @enderror"
                               value="{{ old('nama_tipe', $tipe->nama_tipe) }}" required>
                        @error('nama_tipe') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Harga -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Harga per Malam (Rp)</label>
                        <input type="number" name="harga" class="form-control @error('harga') is-invalid @enderror"
                               value="{{ old('harga', (int)$tipe->harga) }}" required>
                        @error('harga') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Kapasitas -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Kapasitas (Orang)</label>
                        <input type="number" name="kapasitas" class="form-control"
                               value="{{ old('kapasitas', $tipe->kapasitas) }}" required>
                    </div>

                    <!-- Fasilitas -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Fasilitas Utama</label>
                        <input type="text" name="fasilitas" class="form-control"
                               placeholder="Contoh: AC, TV, WiFi, Bathtub"
                               value="{{ old('fasilitas', $tipe->fasilitas) }}">
                    </div>

                    <!-- Deskripsi -->
                    <div class="col-12 mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase">Deskripsi Lengkap</label>
                        <textarea name="deskripsi" class="form-control" rows="4">{{ old('deskripsi', $tipe->deskripsi) }}</textarea>
                    </div>
                </div>

                <div class="mt-2 border-top pt-4">
                    <button type="submit" class="btn btn-warning px-5 fw-bold text-white shadow-sm" style="border-radius:10px;">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('dashboard.hotel.tipe-kamar.index') }}" class="btn btn-light px-4 border ms-2" style="border-radius:10px;">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
