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
                @method('PUT') {{-- WAJIB UNTUK PROSES UPDATE --}}

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Nama Promo</label>
                        <input type="text" name="nama_promo" class="form-control" value="{{ $promo->nama_promo }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Kode Promo</label>
                        <input type="text" name="kode_promo" class="form-control" value="{{ $promo->kode_promo }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label small fw-bold">Kategori</label>
                        <select name="kategori" class="form-select" required>
                            <option value="hotel" {{ $promo->kategori == 'hotel' ? 'selected' : '' }}>Hotel</option>
                            <option value="restoran" {{ $promo->kategori == 'restoran' ? 'selected' : '' }}>Restoran</option>
                            <option value="global" {{ $promo->kategori == 'global' ? 'selected' : '' }}>Global (Semua)</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label small fw-bold">Tipe Diskon</label>
                        <select name="tipe_diskon" class="form-select" required>
                            <option value="nominal" {{ $promo->tipe_diskon == 'nominal' ? 'selected' : '' }}>Potongan Harga (Rp)</option>
                            <option value="persen" {{ $promo->tipe_diskon == 'persen' ? 'selected' : '' }}>Persentase (%)</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label small fw-bold">Nominal Potongan</label>
                        <input type="number" name="nominal_potongan" class="form-control" value="{{ (int)$promo->nominal_potongan }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Tanggal Mulai</label>
                        <input type="date" name="tgl_mulai" class="form-control" value="{{ $promo->tgl_mulai }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Tanggal Selesai</label>
                        <input type="date" name="tgl_selesai" class="form-control" value="{{ $promo->tgl_selesai }}" required>
                    </div>
                </div>
                <div class="mt-4 border-top pt-3">
                    <button type="submit" class="btn btn-warning px-5 fw-bold text-white">Update Promo</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
