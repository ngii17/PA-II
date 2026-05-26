@extends('dashboard.layouts.app')
@section('title', 'Edit Event')
@section('content')

<div class="container-fluid px-4">
    <div class="mb-4">
        <a href="{{ route('dashboard.restoran.event') }}" class="text-decoration-none small text-muted">← Kembali</a>
        <h4 class="fw-bold mt-2">⚙️ Konfigurasi Event: {{ $event->nama_event }}</h4>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius:16px;">
        <div class="card-body p-4">
            <form action="{{ route('dashboard.restoran.event.update', $event->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">NAMA EVENT</label>
                        <input type="text" name="nama_event" class="form-control" value="{{ $event->nama_event }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">STATUS AKTIF (TEMA)</label>
                        <select name="is_active" class="form-select" required>
                            <option value="1" {{ $event->is_active ? 'selected' : '' }}>✅ Aktifkan Sebagai Tema Utama</option>
                            <option value="0" {{ !$event->is_active ? 'selected' : '' }}>❌ Nonaktifkan</option>
                        </select>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label small fw-bold">DESKRIPSI / KETERANGAN</label>
                        <textarea name="deskripsi" class="form-control" rows="4">{{ $event->deskripsi }}</textarea>
                    </div>
                </div>

                <div class="mt-4 border-top pt-3">
                    <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm" style="border-radius:10px;">
                        Simpan Konfigurasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
