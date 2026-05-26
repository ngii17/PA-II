@extends('dashboard.layouts.app')
@section('title', 'Edit Reservasi')
@section('content')

<div class="container-fluid px-4">
    <div class="mb-4">
        <a href="{{ route('dashboard.hotel.reservasi.index') }}" class="text-decoration-none small text-muted">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
        <h4 class="fw-bold mt-2 text-dark">Ubah Data Reservasi #RES-{{ $reservasi->id }}</h4>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm" style="border-radius:16px;">
                <div class="card-body p-4">
                    <form action="{{ route('dashboard.hotel.reservasi.update', $reservasi->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Info Tamu (Read Only) -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label small fw-bold text-muted">PELANGGAN</label>
                                <input type="text" class="form-control bg-light" value="ID Pelanggan: {{ $reservasi->user_id }}" readonly disabled>
                                <small class="text-muted italic">Nama pelanggan tidak dapat diubah dari sini.</small>
                            </div>

                            <!-- Pilih Tipe Kamar -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-muted">TIPE KAMAR</label>
                                <select name="tipe_kamar_id" class="form-select" required>
                                    @foreach($tipeKamar as $t)
                                        <option value="{{ $t->id }}" {{ $reservasi->tipe_kamar_id == $t->id ? 'selected' : '' }}>
                                            {{ $t->nama_tipe }} (Rp {{ number_format($t->harga) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Pilih Unit Kamar -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-muted">NOMOR KAMAR</label>
                                <select name="kamar_id" class="form-select" required>
                                    @foreach($kamar as $k)
                                        <option value="{{ $k->id }}" {{ $reservasi->kamar_id == $k->id ? 'selected' : '' }}>
                                            {{ $k->nomor_kamar }} - {{ $k->tipeKamar->nama_tipe ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Tanggal -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-muted">TANGGAL CHECK-IN</label>
                                <input type="date" name="tgl_checkin" class="form-control" value="{{ $reservasi->tgl_checkin }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-muted">TANGGAL CHECK-OUT</label>
                                <input type="date" name="tgl_checkout" class="form-control" value="{{ $reservasi->tgl_checkout }}" required>
                            </div>

                            <!-- Status -->
                            <div class="col-md-12 mb-4">
                                <label class="form-label small fw-bold text-muted">STATUS RESERVASI</label>
                                <select name="status_reservasi_id" class="form-select" required>
                                    @foreach($statusList as $s)
                                        <option value="{{ $s->id }}" {{ $reservasi->status_reservasi_id == $s->id ? 'selected' : '' }}>
                                            {{ $s->nama_status }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mt-2 border-top pt-4">
                            <button type="submit" class="btn btn-warning px-5 fw-bold text-white shadow-sm" style="border-radius:10px;">
                                <i class="fas fa-save me-1"></i> Perbarui Reservasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sisi Kanan: Ringkasan Biaya --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="border-radius:16px; background:#1a1a2e; color: white;">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Estimasi Biaya Saat Ini:</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Durasi:</span>
                        <span>{{ $reservasi->total_malam }} Malam</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Harga/Malam:</span>
                        <span>Rp {{ number_format($reservasi->tipeKamar->harga ?? 0) }}</span>
                    </div>
                    <hr class="opacity-25">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold">TOTAL:</span>
                        <h4 class="fw-bold mb-0 text-warning">Rp {{ number_format($reservasi->total_harga, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>

            <div class="alert alert-info mt-3 border-0 shadow-sm" style="border-radius: 12px; font-size: 12px;">
                <i class="fas fa-info-circle me-1"></i> Perubahan tanggal akan menghitung ulang jumlah malam dan total harga secara otomatis saat disimpan.
            </div>
        </div>
    </div>
</div>

@endsection
