@extends('dashboard.layouts.app')
@section('title', 'Tambah Menu Baru')
@section('content')

<div class="container-fluid px-4">
    <div class="mb-4">
        <a href="{{ route('dashboard.restoran.menu.index') }}" class="text-decoration-none small">← Kembali ke Daftar</a>
        <h4 class="fw-bold mt-2">Buat Menu Restoran Baru</h4>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius:16px;">
        <div class="card-body p-4">
            <form action="{{ route('dashboard.restoran.menu.store') }}" method="POST">
                @csrf
                <div class="row">
                    <!-- Nama Menu -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Nama Menu</label>
                        <input type="text" name="nama_menu" class="form-control" placeholder="Contoh: Ayam Bakar" required>
                    </div>

                    <!-- Harga -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Harga (Rp)</label>
                        <input type="number" name="harga" class="form-control" placeholder="0" required>
                    </div>

                    <!-- Kategori (PASTIKAN NAME-NYA kategori_menu_id) -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Pilih Kategori</label>
                        <select name="kategori_menu_id" class="form-select @error('kategori_menu_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategori as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                            @endforeach
                        </select>
                        @error('kategori_menu_id')
                            <div class="invalid-feedback">Kategori wajib dipilih.</div>
                        @enderror
                    </div>

                    <!-- Status Awal -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Status Awal</label>
                        <select name="status_menu_id" class="form-select" required>
                            @foreach($status as $s)
                                <option value="{{ $s->id }}">{{ $s->nama_status }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Deskripsi -->
                    <div class="col-12 mb-3">
                        <label class="form-label small fw-bold">Deskripsi Menu</label>
                        <textarea name="deskripsi" class="form-control" rows="3" placeholder="Jelaskan rincian menu ini..."></textarea>
                    </div>
                </div>

                {{-- INFO: STOK TIDAK DITAMPILKAN KARENA OTOMATIS 0 --}}
                <div class="alert alert-info py-2" style="font-size: 11px;">
                    <i class="fas fa-info-circle me-1"></i> Stok menu baru akan otomatis diset ke <b>0</b>. Anda bisa menambah stok di menu <b>Stok Menu</b>.
                </div>

                <div class="mt-4 border-top pt-3">
                    <button type="submit" class="btn btn-primary px-5 fw-bold">Simpan Menu</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
