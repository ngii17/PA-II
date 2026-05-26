@extends('dashboard.layouts.app')
@section('title', 'Tambah Kamar Baru')
@section('content')

<div class="container-fluid px-4">
    <div class="mb-4">
        <a href="{{ route('dashboard.hotel.kamar.index') }}" class="text-decoration-none small text-muted">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
        <h4 class="fw-bold mt-2 text-dark">Tambah Unit Kamar Baru</h4>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius:16px;">
        <div class="card-body p-4">
            <form action="{{ route('dashboard.hotel.kamar.store') }}" method="POST">
                @csrf

                <div class="row">
                    <!-- Input Nomor Kamar -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">NOMOR KAMAR</label>
                        <input type="text" name="nomor_kamar" class="form-control @error('nomor_kamar') is-invalid @enderror"
                               placeholder="Contoh: 101" value="{{ old('nomor_kamar') }}" required>
                        @error('nomor_kamar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Pilih Tipe Kamar -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">TIPE KAMAR</label>
                        <select name="tipe_kamar_id" class="form-select" required>
                            <option value="">-- Pilih Tipe --</option>
                            @foreach($tipeKamar as $tipe)
                                <option value="{{ $tipe->id }}" {{ old('tipe_kamar_id') == $tipe->id ? 'selected' : '' }}>
                                    {{ $tipe->nama_tipe }} - Rp {{ number_format($tipe->harga) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Pilih Status Awal -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">STATUS AWAL</label>
                        <select name="status_kamar_id" class="form-select" required>
                            @foreach($statusKamar as $status)
                                <option value="{{ $status->id }}" {{ old('status_kamar_id') == $status->id ? 'selected' : '' }}>
                                    {{ $status->nama_status }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-4 border-top pt-4">
                    <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm" style="border-radius:10px;">
                        <i class="fas fa-save me-1"></i> Simpan Data Kamar
                    </button>
                    <a href="{{ route('dashboard.hotel.kamar.index') }}" class="btn btn-light px-4 border ms-2" style="border-radius:10px;">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
