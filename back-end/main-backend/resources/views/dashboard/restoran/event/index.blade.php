@extends('dashboard.layouts.app')
@section('title', 'Event Restoran')
@section('content')

<div class="container-fluid">
    <div class="mb-4">
        <h4 class="fw-bold mb-1">🏰 Event Restoran</h4>
        <p class="text-muted small">Kelola informasi dan aktifkan tema visual aplikasi</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('success') }}</div>
    @endif

    <div class="row g-4">
        @foreach($events as $event)
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius:20px; overflow:hidden;">

                {{-- WARNA GRADIENT DIAMBIL DARI DATABASE ATAU MAPPING --}}
                @php
                    $gradients = [
                        'imlek'    => 'linear-gradient(135deg, #f09819, #edde5d)',
                        'lebaran'  => 'linear-gradient(135deg, #1d976c, #93f9b9)',
                        'valentine'=> 'linear-gradient(135deg, #f06292, #f8bbd0)',
                        'hut_ri'   => 'linear-gradient(135deg, #ff5252, #f5f5f5)',
                        'natal'    => 'linear-gradient(135deg, #134e5e, #71b280)',
                        'default'  => 'linear-gradient(135deg, #89f7fe, #66a6ff)',
                    ];
                    $bg = $gradients[$event->event_code] ?? 'linear-gradient(135deg, #6c757d, #adb5bd)';
                @endphp

                <div style="height: 150px; background: {{ $bg }}; position: relative;">
                    @if($event->header_image)
                        <img src="{{ $event->header_image }}" style="width:100%; height:100%; object-fit:cover; opacity: 0.2; position: absolute;">
                    @endif
                </div>

                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="fw-bold mb-0">{{ $event->nama_event }}</h6>
                        <span class="badge {{ $event->is_active ? 'bg-success' : 'bg-secondary' }}" style="font-size: 10px;">
                            {{ $event->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    <p class="text-muted small mb-0" style="font-size: 11px; min-height: 35px;">
                        {{ $event->deskripsi ?? '-' }}
                    </p>

                    <div class="mt-3">
                        {{-- Tombol untuk pindah ke halaman Edit --}}
                        <a href="{{ route('dashboard.restoran.event.edit', $event->id) }}"
                           class="btn btn-warning w-100 fw-bold py-2 shadow-sm"
                           style="border-radius:12px; background: #ffc107; border: none; color: #1a1a2e;">
                            ✏️ Edit & Konfigurasi
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

@endsection
