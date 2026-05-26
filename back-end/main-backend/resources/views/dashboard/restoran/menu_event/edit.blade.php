@extends('dashboard.layouts.app')
@section('title', 'Edit Menu Event')
@section('content')

<div class="container-fluid px-4">
    <div class="mb-4">
        <a href="{{ route('dashboard.restoran.menu-event.index') }}" class="text-decoration-none small text-muted">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
        <h4 class="fw-bold mt-2 text-dark">Edit Harga Khusus Menu Event</h4>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius:16px;">
        <div class="card-body p-4">
            <form action="{{ route('dashboard.restoran.menu-event.update', $menuEvent->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Info Event (Read Only) -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-muted uppercase">NAMA EVENT</label>
                        <input type="text" class="form-control bg-light" value="{{ $menuEvent->nama_event }}" readonly>
                    </div>

                    <!-- Info Menu (Read Only) -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-muted uppercase">NAMA MENU</label>
                        <input type="text" class="form-control bg-light" value="{{ $menuEvent->nama_menu }}" readonly>
                    </div>

                    <!-- Input Harga Khusus -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-primary uppercase">HARGA KHUSUS EVENT (RP)</label>
                        <input type="number" name="harga_khusus" class="form-control fw-bold"
                               value="{{ (int)$menuEvent->harga_khusus }}" required>
                        <small class="text-muted">Tentukan harga baru untuk menu ini selama event.</small>
                    </div>

                    <!-- Input Status Aktif -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-muted uppercase">STATUS AKTIF</label>
                        <select name="is_active" class="form-select" required>
                            <option value="1" {{ $menuEvent->is_active ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ !$menuEvent->is_active ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4 border-top pt-4">
                    <button type="submit" class="btn btn-warning px-5 fw-bold text-white shadow-sm" style="border-radius:10px;">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('dashboard.restoran.menu-event.index') }}" class="btn btn-light px-4 border ms-2" style="border-radius:10px;">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
