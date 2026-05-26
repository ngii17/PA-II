@extends('dashboard.layouts.app')
@section('title', 'Tambah Tipe Kamar')
@section('content')

<div class="container-fluid px-4">
    <div class="mb-4">
        <a href="{{ route('dashboard.hotel.tipe-kamar.index') }}" class="text-decoration-none small text-muted">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
        <h4 class="fw-bold mt-2 text-dark">Buat Tipe Kamar Baru</h4>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius:16px;">
        <div class="card-body p-4">
            <form action="{{ route('dashboard.hotel.tipe-kamar.store') }}" method="POST">
                @csrf

                <div class="row">
                    <!-- Nama Tipe -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">NAMA TIPE KAMAR</label>
                        <input type="text" name="nama_tipe" class="form-control @error('nama_tipe') is-invalid @enderror"
                               placeholder="Contoh: Deluxe Room" value="{{ old('nama_tipe') }}" required>
                        @error('nama_tipe') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Harga per Malam -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">HARGA PER MALAM (RP)</label>
                        <input type="number" name="harga" class="form-control @error('harga') is-invalid @enderror"
                               placeholder="0" value="{{ old('harga') }}" required>
                        @error('harga') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Kapasitas -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">KAPASITAS (ORANG)</label>
                        <input type="number" name="kapasitas" class="form-control"
                               placeholder="Contoh: 2" value="{{ old('kapasitas') }}" required>
                    </div>

                    <!-- Fasilitas Singkat -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">FASILITAS (PISAH DENGAN KOMA)</label>
                        <input type="text" name="fasilitas" class="form-control"
                               placeholder="AC, WiFi, TV, Kamar Mandi Dalam" value="{{ old('fasilitas') }}">
                    </div>

                    <!-- Deskripsi -->
                    <div class="col-12 mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase">DESKRIPSI LENGKAP</label>
                        <textarea name="deskripsi" class="form-control" rows="4"
                                  placeholder="Jelaskan detail tipe kamar ini...">{{ old('deskripsi') }}</textarea>
                    </div>
                </div>

                <div class="mt-4 border-top pt-4">
                    <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm" style="border-radius:10px;">
                        <i class="fas fa-save me-1"></i> Simpan Tipe Kamar
                    </button>
                    <a href="{{ route('dashboard.hotel.tipe-kamar.index') }}" class="btn btn-light px-4 border ms-2" style="border-radius:10px;">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
