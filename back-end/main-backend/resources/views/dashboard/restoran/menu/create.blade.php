@extends('dashboard.layouts.app')
@section('title', 'Tambah Menu Baru')
@section('content')

<div class="container-fluid px-4">
    {{-- ============================================================ --}}
    {{-- --- BAGIAN BARU: NOTIFIKASI ERROR VALIDASI --- --}}
    {{-- ============================================================ --}}
    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius:12px;">
            <div class="fw-bold mb-2"><i class="fas fa-exclamation-triangle me-2"></i> Terjadi Kesalahan Input:</div>
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius:12px;">
            <i class="fas fa-times-circle me-2"></i> {{ session('error') }}
        </div>
    @endif
    {{-- ============================================================ --}}

    <div class="mb-4">
        <a href="{{ route('dashboard.restoran.menu.index') }}" class="text-decoration-none small text-muted">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
        <h4 class="fw-bold mt-2">Buat Menu Restoran Baru</h4>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius:16px;">
        <div class="card-body p-4">
            {{-- PERBAIKAN: Menambahkan enctype agar bisa kirim file gambar --}}
            <form action="{{ route('dashboard.restoran.menu.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <!-- Nama Menu -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Nama Menu</label>
                        <input type="text" name="nama_menu" class="form-control" placeholder="Contoh: Ayam Bakar Madu" value="{{ old('nama_menu') }}" required>
                    </div>

                    <!-- Harga -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Harga Jual (Rp)</label>
                        <input type="number" name="harga" class="form-control" placeholder="0" value="{{ old('harga') }}" required>
                    </div>

                    <!-- Kategori -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Pilih Kategori</label>
                        <select name="kategori_menu_id" class="form-select" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategori as $k)
                                <option value="{{ $k->id }}" {{ old('kategori_menu_id') == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Awal -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Status Awal</label>
                        <select name="status_menu_id" class="form-select" required>
                            @foreach($status as $s)
                                <option value="{{ $s->id }}" {{ old('status_menu_id') == $s->id ? 'selected' : '' }}>
                                    {{ $s->nama_status }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- FOTO MENU -->
                    <div class="col-md-12 mb-3">
                        <label class="form-label small fw-bold text-primary">FOTO PRODUK MAKANAN</label>
                        <input type="file" name="foto_menu" class="form-control" accept="image/*" onchange="previewImage(this)">
                        <div class="form-text" style="font-size: 10px;">Format: JPG, PNG, atau JPEG. Maksimal 2MB.</div>
                        
                        {{-- Tempat Preview Gambar --}}
                        <div class="mt-2">
                            <img id="img-preview" src="#" alt="Preview" style="display:none; width: 150px; height: 150px; object-fit: cover; border-radius: 10px; border: 2px solid #ddd;">
                        </div>
                    </div>

                    <!-- Deskripsi -->
                    <div class="col-12 mb-3">
                        <label class="form-label small fw-bold">Deskripsi Menu</label>
                        <textarea name="deskripsi" class="form-control" rows="3" placeholder="Jelaskan rincian bahan atau rasa menu ini...">{{ old('deskripsi') }}</textarea>
                    </div>
                </div>

                <div class="alert alert-info py-2" style="border-radius: 10px; border: none; background: #eef2ff;">
                    <p class="mb-0 small text-primary">
                        <i class="fas fa-info-circle me-1"></i> <b>Info Stok:</b> Menu baru otomatis memiliki stok <b>0</b>. Silakan masuk ke menu <b>Stok</b> untuk menambah porsi.
                    </p>
                </div>

                <div class="mt-4 border-top pt-3 text-end">
                    <button type="submit" class="btn btn-primary px-5 fw-bold" style="border-radius: 10px;">
                        <i class="fas fa-save me-1"></i> Simpan Menu Baru
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Script untuk Preview Gambar --}}
<script>
    function previewImage(input) {
        const preview = document.getElementById('img-preview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

@endsection