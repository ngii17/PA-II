@extends('dashboard.layouts.app')
@section('title', 'Tambah Menu ke Event')
@section('content')

<div class="container-fluid px-4">
    <div class="mb-4">
        <a href="{{ route('dashboard.restoran.menu-event.index') }}" class="text-decoration-none small text-muted">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
        <h4 class="fw-bold mt-2 text-dark">Tambah Menu ke Event Restoran</h4>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius:16px;">
        <div class="card-body p-4">
            <form action="{{ route('dashboard.restoran.menu-event.store') }}" method="POST">
                @csrf

                <div class="row">
                    <!-- Pilih Event -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-muted uppercase">PILIH EVENT AKTIF</label>
                        <select name="event_id" class="form-select @error('event_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Event --</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}" {{ old('event_id') == $event->id ? 'selected' : '' }}>
                                    {{ $event->nama_event }}
                                </option>
                            @endforeach
                        </select>
                        @error('event_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Harga Khusus Event -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-muted uppercase">HARGA KHUSUS EVENT (RP)</label>
                        <input type="number" name="harga_khusus" class="form-control" placeholder="Contoh: 15000" value="{{ old('harga_khusus') }}" required>
                        <small class="text-muted">Harga ini akan berlaku untuk semua menu yang dipilih di bawah selama event berlangsung.</small>
                    </div>

                    <!-- Pilih Banyak Menu -->
                    <div class="col-12 mb-4">
                        <label class="form-label small fw-bold text-muted uppercase">PILIH MENU (BISA LEBIH DARI SATU)</label>
                        <div class="row g-3">
                            @foreach($menus as $menu)
                            <div class="col-md-4">
                                <div class="border rounded p-3 h-100 shadow-sm" style="border-radius: 12px !important;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="menu_ids[]" value="{{ $menu->id }}" id="menu{{ $menu->id }}">
                                        <label class="form-check-label fw-bold" for="menu{{ $menu->id }}" style="font-size: 13px;">
                                            {{ $menu->nama_menu }}
                                        </label>
                                        <div class="text-success small">Harga Normal: Rp {{ number_format($menu->harga) }}</div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @error('menu_ids') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mt-4 border-top pt-4">
                    <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm" style="border-radius:10px;">
                        <i class="fas fa-save me-1"></i> Daftarkan Menu ke Event
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
