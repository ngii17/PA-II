@extends('dashboard.layouts.app')
@section('title', 'Edit Kategori')
@section('content')

<div class="container-fluid px-4">
    <div class="mb-4">
        <a href="{{ route('dashboard.restoran.kategori.index') }}" class="text-decoration-none small">← Kembali ke Daftar</a>
        <h4 class="fw-bold mt-2">Ubah Kategori: {{ $kategori->nama_kategori }}</h4>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius:16px;">
        <div class="card-body p-4">
            <form action="{{ route('dashboard.restoran.kategori.update', $kategori->id) }}" method="POST">
                @csrf
                @method('PUT') {{-- PENTING: Wajib untuk route put/patch --}}

                <div class="mb-3">
                    <label class="form-label small fw-bold">Nama Kategori</label>
                    <input type="text" name="nama_kategori"
                           class="form-control @error('nama_kategori') is-invalid @enderror"
                           value="{{ old('nama_kategori', $kategori->nama_kategori) }}" required>
                    @error('nama_kategori')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="4"
                              placeholder="Tambahkan penjelasan kategori...">{{ old('deskripsi', $kategori->deskripsi) }}</textarea>
                </div>

                <div class="mt-4 border-top pt-3">
                    <button type="submit" class="btn btn-warning px-5 fw-bold text-white" style="border-radius:10px;">
                        Update Kategori
                    </button>
                    <a href="{{ route('dashboard.restoran.kategori.index') }}" class="btn btn-light px-4 ms-2" style="border-radius:10px;">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
