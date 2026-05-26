@extends('dashboard.layouts.app')
@section('title', 'Tambah Reservasi Baru')
@section('content')

<div class="container-fluid px-4">
    <div class="mb-4">
        <a href="{{ route('dashboard.hotel.reservasi.index') }}" class="text-decoration-none small text-muted">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
        <h4 class="fw-bold mt-2 text-dark">Buat Reservasi Kamar Baru</h4>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius:12px;">
            <ul class="mb-0 small">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius:16px;">
        <div class="card-body p-4">
            <form action="{{ route('dashboard.hotel.reservasi.store') }}" method="POST">
                @csrf

                <div class="row">
                    <!-- Pilih Tamu (Data dari Microservice) -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-muted uppercase">NAMA TAMU (PELANGGAN)</label>
                        <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Pelanggan Terdaftar --</option>
                            @foreach($customers as $c)
                                <option value="{{ $c['id'] }}" {{ old('user_id') == $c['id'] ? 'selected' : '' }}>
                                    {{ $c['full_name'] }} ({{ $c['email'] }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted" style="font-size: 11px;">Tamu harus sudah memiliki akun pelanggan.</small>
                    </div>

                    <!-- Pilih Tipe Kamar -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-muted uppercase">TIPE KAMAR</label>
                        <select name="tipe_kamar_id" class="form-select" required>
                            <option value="">-- Pilih Kategori Kamar --</option>
                            @foreach($tipeKamar as $t)
                                <option value="{{ $t->id }}" {{ old('tipe_kamar_id') == $t->id ? 'selected' : '' }}>
                                    {{ $t->nama_tipe }} - Rp {{ number_format($t->harga, 0, ',', '.') }} /malam
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Pilih Unit Kamar -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-muted uppercase">NOMOR KAMAR FISIK</label>
                        <select name="kamar_id" class="form-select" required>
                            <option value="">-- Pilih Unit Kamar Tersedia --</option>
                            @foreach($kamar as $k)
                                <option value="{{ $k->id }}" {{ old('kamar_id') == $k->id ? 'selected' : '' }}>
                                    No. {{ $k->nomor_kamar }} - ({{ $k->tipeKamar->nama_tipe ?? 'Umum' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Reservasi -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-muted uppercase">STATUS AWAL</label>
                        <select name="status_reservasi_id" class="form-select" required>
                            @foreach($statusList as $s)
                                <option value="{{ $s->id }}" {{ old('status_reservasi_id', 1) == $s->id ? 'selected' : '' }}>
                                    {{ $s->nama_status }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tanggal Check-in -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-muted uppercase">TANGGAL CHECK-IN</label>
                        <input type="date" name="tgl_checkin" class="form-control" value="{{ old('tgl_checkin', date('Y-m-y')) }}" required>
                    </div>

                    <!-- Tanggal Check-out -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-muted uppercase">TANGGAL CHECK-OUT</label>
                        <input type="date" name="tgl_checkout" class="form-control" value="{{ old('tgl_checkout') }}" required>
                    </div>
                </div>

                <div class="alert alert-info mt-3 border-0 small" style="border-radius: 10px;">
                    <i class="fas fa-info-circle me-1"></i> Sistem akan menghitung otomatis total malam dan harga berdasarkan tipe kamar yang dipilih.
                </div>

                <div class="mt-4 border-top pt-4">
                    <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm" style="border-radius: 10px;">
                        <i class="fas fa-save me-2"></i> Simpan Reservasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .uppercase { text-transform: uppercase; letter-spacing: 0.5px; }
</style>

@endsection
