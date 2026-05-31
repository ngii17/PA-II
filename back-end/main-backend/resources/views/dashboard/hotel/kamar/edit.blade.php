@extends('dashboard.layouts.app')
@section('title', 'Edit Kamar')
@section('content')

<div class="container-fluid px-4">
    <div class="mb-4">
        <a href="{{ route('dashboard.hotel.kamar.index') }}" class="text-decoration-none small text-muted">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
        <h4 class="fw-bold mt-2">Edit Unit Kamar: {{ $kamar->nomor_kamar }}</h4>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius:16px;">
        <div class="card-body p-4">
            <form action="{{ route('dashboard.hotel.kamar.update', $kamar->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- NOMOR KAMAR -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">NOMOR UNIT KAMAR</label>
                        <input type="text" name="nomor_kamar" class="form-control @error('nomor_kamar') is-invalid @enderror"
                               value="{{ old('nomor_kamar', $kamar->nomor_kamar) }}" 
                               placeholder="Contoh: 101" required>
                        @error('nomor_kamar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <small class="text-muted" style="font-size: 10px;">Pastikan nomor kamar unik dan tidak duplikat.</small>
                    </div>

                    <!-- TIPE KAMAR -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">TIPE LAYANAN KAMAR</label>
                        <select name="tipe_kamar_id" class="form-select @error('tipe_kamar_id') is-invalid @enderror" required>
                            @foreach($tipeKamar as $tipe)
                                <option value="{{ $tipe->id }}" {{ old('tipe_kamar_id', $kamar->tipe_kamar_id) == $tipe->id ? 'selected' : '' }}>
                                    {{ $tipe->nama_tipe }} - (Rp {{ number_format($tipe->harga) }})
                                </option>
                            @endforeach
                        </select>
                        @error('tipe_kamar_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- STATUS KAMAR -->
                    <div class="col-md-12 mb-3">
                        <label class="form-label small fw-bold">STATUS KETERSEDIAAN</label>
                        <select name="status_kamar_id" class="form-select @error('status_kamar_id') is-invalid @enderror" required>
                            @foreach($statusKamar as $status)
                                <option value="{{ $status->id }}" {{ old('status_kamar_id', $kamar->status_kamar_id) == $status->id ? 'selected' : '' }}>
                                    {{ strtoupper($status->nama_status) }}
                                </option>
                            @endforeach
                        </select>
                        @error('status_kamar_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text" style="font-size: 11px;">
                            * <b>Tersedia:</b> Kamar siap dijual ke pelanggan Flutter. <br>
                            * <b>Terisi:</b> Kamar sedang digunakan oleh tamu.
                        </div>
                    </div>
                </div>

                <div class="mt-4 border-top pt-4">
                    <button type="submit" class="btn btn-warning px-5 fw-bold text-white shadow-sm" style="border-radius:10px;">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan Unit
                    </button>
                    <a href="{{ route('dashboard.hotel.kamar.index') }}" class="btn btn-light px-4 border ms-2" style="border-radius:10px;">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection