@extends('dashboard.layouts.app')
@section('title', 'Edit Promo')
@section('content')

<div class="container-fluid px-4">
    <div class="mb-4">
        <a href="{{ route('dashboard.promo.index') }}" class="text-decoration-none small">← Kembali ke Daftar</a>
        <h4 class="fw-bold mt-2">Ubah Data Promo: {{ $promo->nama_promo }}</h4>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius:16px;">
        <div class="card-body p-4">
            <form action="{{ route('dashboard.promo.update', $promo->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Nama Promo</label>
                        <input type="text" name="nama_promo" class="form-control" value="{{ $promo->nama_promo }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Kode Promo (Kosongkan jika ingin Promo Pop-up)</label>
                        <!-- PERBAIKAN: Atribut 'required' dihapus agar bisa dikosongkan saat edit -->
                        <input type="text" name="kode_promo" class="form-control" value="{{ $promo->kode_promo }}" placeholder="Contoh: DISKON30">
                        <div class="form-text text-muted" style="font-size: 10px;">Menghapus kode akan menjadikan promo ini muncul otomatis di HP user.</div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label small fw-bold">Kategori</label>
                        <select name="kategori" class="form-select" required>
                            <option value="hotel" {{ $promo->kategori == 'hotel' ? 'selected' : '' }}>Hotel</option>
                            <option value="restoran" {{ $promo->kategori == 'restoran' ? 'selected' : '' }}>Restoran</option>
                            <!-- SINKRONISASI: Menggunakan 'semua' sesuai database -->
                            <option value="semua" {{ $promo->kategori == 'semua' ? 'selected' : '' }}>Semua Layanan</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label small fw-bold">Tipe Diskon</label>
                        <select name="tipe_diskon" class="form-select" required>
                            <option value="persen" {{ $promo->tipe_diskon == 'persen' ? 'selected' : '' }}>Persentase (%)</option>
                            <option value="nominal" {{ $promo->tipe_diskon == 'nominal' ? 'selected' : '' }}>Potongan Harga (Rp)</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label small fw-bold">Nominal Potongan</label>
                        <input type="number" name="nominal_potongan" class="form-control" value="{{ (int)$promo->nominal_potongan }}" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label small fw-bold">Tanggal Mulai</label>
                        <input type="date" name="tgl_mulai" class="form-control" value="{{ $promo->tgl_mulai }}" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label small fw-bold">Tanggal Selesai</label>
                        <input type="date" name="tgl_selesai" class="form-control" value="{{ $promo->tgl_selesai }}" required>
                    </div>

                    <!-- TAMBAHAN: SAKLAR STATUS AKTIF (Sinkron dengan logic is_active Backend) -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label small fw-bold">Status Promo</label>
                        <select name="is_active" class="form-select" required>
                            <option value="1" {{ $promo->is_active ? 'selected' : '' }}>Aktif (Muncul di HP)</option>
                            <option value="0" {{ !$promo->is_active ? 'selected' : '' }}>Non-Aktif (Sembunyi)</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4 border-top pt-3 text-end">
                    <button type="submit" class="btn btn-warning px-5 fw-bold text-white" style="border-radius: 10px;">Update & Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection