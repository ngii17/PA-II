@extends('dashboard.layouts.app')
@section('title', 'Konfigurasi Tema')
@section('content')

<div class="container-fluid px-4">
    <div class="mb-4">
        {{-- SINKRONISASI: Mengarah ke rute admin yang baru --}}
        <a href="{{ route('dashboard.event.index') }}" class="text-decoration-none small text-muted">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Tema
        </a>
        <h4 class="fw-bold mt-2">⚙️ Konfigurasi Tema Aplikasi: {{ $event->nama_event }}</h4>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius:16px;">
        <div class="card-body p-4">
            {{-- SINKRONISASI: Action mengarah ke rute update milik Admin --}}
            <form action="{{ route('dashboard.event.update', $event->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    {{-- Nama Event/Tema --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Nama Event / Tema</label>
                        <input type="text" name="nama_event" class="form-control" value="{{ old('nama_event', $event->nama_event) }}" required>
                    </div>

                    {{-- Status Aktif (Saklar Utama) --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-primary text-uppercase">Aktivasi Tema</label>
                        <select name="is_active" class="form-select border-primary" required>
                            <option value="1" {{ $event->is_active ? 'selected' : '' }}>✅ Aktifkan Sebagai Tema Utama</option>
                            <option value="0" {{ !$event->is_active ? 'selected' : '' }}>❌ Nonaktifkan Tema Ini</option>
                        </select>
                        <div class="form-text" style="font-size: 10px;">*Mengaktifkan tema ini akan menonaktifkan tema lainnya secara otomatis.</div>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="col-12 mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Deskripsi / Keterangan Visual</label>
                        <textarea name="deskripsi" class="form-control" rows="4" placeholder="Jelaskan suasana atau tujuan tema ini...">{{ old('deskripsi', $event->deskripsi) }}</textarea>
                    </div>
                </div>

                <div class="mt-4 border-top pt-4 text-end">
                    <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm" style="border-radius:10px;">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan Tema
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Informasi Tambahan untuk Admin --}}
    <div class="alert alert-light border-0 shadow-sm mt-4" style="border-radius: 12px; background: #fff;">
        <div class="d-flex gap-3 align-items-center">
            <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="fas fa-info"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-0" style="font-size: 14px;">Informasi Sinkronisasi</h6>
                <p class="mb-0 text-muted" style="font-size: 12px;">Perubahan tema di sini akan langsung mengubah warna primer, warna sekunder, dan banner pada aplikasi HP Customer secara real-time.</p>
            </div>
        </div>
    </div>
</div>

@endsection