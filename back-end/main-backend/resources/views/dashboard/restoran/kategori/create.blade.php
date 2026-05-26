@extends('dashboard.layouts.app')
@section('title', 'Tambah Kategori')
@section('content')
<div class="card shadow-sm" style="max-width:500px;">
    <div class="card-header bg-white py-3"><h6 class="mb-0 fw-bold">📂 Tambah Kategori Menu</h6></div>
    <div class="card-body">
        @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
        <form action="{{ route('dashboard.restoran.kategori.store') }}" method="POST">
            @csrf
            <div class="mb-3"><label class="form-label">Nama Kategori</label><input type="text" name="nama_kategori" class="form-control" value="{{ old('nama_kategori') }}" required></div>
            <div class="mb-3"><label class="form-label">Deskripsi</label><textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi') }}</textarea></div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('dashboard.restoran.kategori.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
