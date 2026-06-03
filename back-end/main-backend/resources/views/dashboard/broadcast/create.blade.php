@extends('dashboard.layouts.app')
@section('title', 'Buat Pengumuman Baru')
@section('content')

<div class="container-fluid px-4">
    <div class="mb-4">
        <a href="{{ route('dashboard.admin.broadcast.index') }}" class="text-decoration-none small text-muted">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
        <h4 class="fw-bold mt-2">Buat Draft Pengumuman</h4>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm" style="border-radius:16px;">
                <div class="card-body p-4">
                    <form action="{{ route('dashboard.admin.broadcast.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">JUDUL NOTIFIKASI (TITLE)</label>
                            <input type="text" name="title" class="form-control" placeholder="Contoh: Info Menu Baru Hari Ini!" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">ISI PESAN (BODY)</label>
                            <textarea name="body" class="form-control" rows="5" placeholder="Tuliskan deskripsi lengkap pengumuman Anda di sini..." required></textarea>
                        </div>

                        <div class="row">
                            {{-- SINKRONISASI DATABASE: Menggunakan start_date dan end_date --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-muted">TANGGAL MULAI TAMPIL</label>
                                <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-muted">TANGGAL BERAKHIR</label>
                                <input type="date" name="end_date" class="form-control" value="{{ date('Y-m-d', strtotime('+7 days')) }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">URL GAMBAR (OPSIONAL)</label>
                            <input type="url" name="image_url" class="form-control" placeholder="https://example.com/foto.jpg">
                        </div>

                        <div class="mt-4 border-top pt-4 text-end">
                            <button type="submit" class="btn btn-primary px-5 fw-bold" style="border-radius:10px;">
                                <i class="fas fa-save me-2"></i> Simpan Sebagai Draft
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-light" style="border-radius:16px; border: 1px dashed #ccc;">
                <div class="card-body p-4">
                    <h6 class="fw-bold"><i class="fas fa-lightbulb text-warning me-2"></i>Tips Admin</h6>
                    <p class="small text-muted mb-0">
                        Pesan yang Anda simpan tidak akan langsung terkirim ke HP pelanggan. Anda harus menekan tombol <b>"SEBARKAN"</b> pada halaman daftar untuk memicu notifikasi pop-up.
                    </p>
                    <hr>
                    <p class="small text-muted">
                        Pastikan isi pesan singkat dan padat agar terbaca jelas pada layar notifikasi HP.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection