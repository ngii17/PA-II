@extends('dashboard.layouts.app')
@section('title', 'Tambah Promo')
@section('content')

<div class="container-fluid px-4">
    <div class="mb-4">
        <a href="{{ route('dashboard.promo.index') }}" class="text-decoration-none small">← Kembali ke Daftar</a>
        <h4 class="fw-bold mt-2">Buat Promo Baru</h4>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius:16px;">
        <div class="card-body p-4">
            <form action="{{ route('dashboard.promo.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Nama Promo</label>
                        <input type="text" name="nama_promo" class="form-control" placeholder="Contoh: Promo Idul Adha" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Kode Promo (Kosongkan untuk Promo Pop-up Otomatis)</label>
                        <!-- PERBAIKAN: Atribut 'required' dihapus agar bisa dikosongkan -->
                        <input type="text" name="kode_promo" class="form-control" placeholder="Contoh: DISKON30">
                        <div class="form-text text-muted" style="font-size: 10px;">Jika kosong, promo akan muncul sebagai pop-up saat user membuka aplikasi.</div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label small fw-bold">Kategori</label>
                        <select name="kategori" class="form-select" required>
                            <option value="hotel">Hotel</option>
                            <option value="restoran">Restoran</option>
                            <!-- SINKRONISASI: Menggunakan 'semua' sesuai database -->
                            <option value="semua">Semua Layanan</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label small fw-bold">Tipe Diskon</label>
                        <select name="tipe_diskon" class="form-select" required>
                            <option value="persen">Persentase (%)</option>
                            <option value="nominal">Potongan Harga (Rp)</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label small fw-bold">Nominal Potongan</label>
                        <input type="number" name="nominal_potongan" class="form-control" placeholder="0" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Tanggal Mulai</label>
                        <input type="date" name="tgl_mulai" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Tanggal Selesai</label>
                        <input type="date" name="tgl_selesai" class="form-control" required>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary px-5 fw-bold" style="border-radius: 10px;">Simpan Promo</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection