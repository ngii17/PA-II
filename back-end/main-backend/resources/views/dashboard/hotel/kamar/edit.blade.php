@extends('dashboard.layouts.app')
@section('title', 'Edit Kamar')
@section('content')

<div class="container-fluid px-4">
    <div class="mb-4">
        <a href="{{ route('dashboard.hotel.kamar.index') }}" class="text-decoration-none small text-muted">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
        <h4 class="fw-bold mt-2">Edit Kamar: {{ $kamar->nomor_kamar }}</h4>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius:16px;">
        <div class="card-body p-4">
            <form action="{{ route('dashboard.hotel.kamar.update', $kamar->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">NOMOR KAMAR</label>
                        <input type="text" name="nomor_kamar" class="form-control @error('nomor_kamar') is-invalid @enderror"
                               value="{{ old('nomor_kamar', $kamar->nomor_kamar) }}" required>
                        @error('nomor_kamar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">TIPE KAMAR</label>
                        <select name="tipe_kamar_id" class="form-select" required>
                            @foreach($tipeKamar as $tipe)
                                <option value="{{ $tipe->id }}" {{ $kamar->tipe_kamar_id == $tipe->id ? 'selected' : '' }}>
                                    {{ $tipe->nama_tipe }} - Rp {{ number_format($tipe->harga) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">STATUS KAMAR</label>
                        <select name="status_kamar_id" class="form-select" required>
                            @foreach($statusKamar as $status)
                                <option value="{{ $status->id }}" {{ $kamar->status_kamar_id == $status->id ? 'selected' : '' }}>
                                    {{ $status->nama_status }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-4 border-top pt-4">
                    <button type="submit" class="btn btn-warning px-5 fw-bold text-white shadow-sm" style="border-radius:10px;">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('dashboard.hotel.kamar.index') }}" class="btn btn-light px-4 border ms-2" style="border-radius:10px;">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
