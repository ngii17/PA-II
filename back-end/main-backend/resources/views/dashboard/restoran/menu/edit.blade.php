@extends('dashboard.layouts.app')
@section('title', 'Edit Informasi Menu')
@section('content')

@php
    // cek asal halaman
    $isDariStok = isset($from) && $from == 'stok';

    $routeKembali = $isDariStok
        ? route('dashboard.restoran.stok')
        : route('dashboard.restoran.menu.index');

    $labelKembali = $isDariStok
        ? 'Manajemen Stok'
        : 'Daftar Menu';
@endphp

<div class="container-fluid px-4">

    {{-- Header --}}
    <div class="mb-4">
        <a href="{{ $routeKembali }}" class="text-decoration-none small text-muted">
            <i class="fas fa-arrow-left me-1"></i>
            Kembali ke {{ $labelKembali }}
        </a>

        <h4 class="fw-bold mt-2 text-dark">
            Edit Menu: {{ $menu->nama_menu }}
        </h4>
    </div>

    {{-- Card --}}
    <div class="card border-0 shadow-sm" style="border-radius:16px;">
        <div class="card-body p-4">

            <form action="{{ route('dashboard.restoran.menu.update', $menu->id) }}?from={{ $from }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Hidden input agar controller tahu asal halaman --}}
                @if($isDariStok)
                    <input type="hidden" name="dari_halaman_stok" value="true">
                @endif

                <div class="row">

                    {{-- Nama Menu --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">
                            Nama Menu
                        </label>

                        <input
                            type="text"
                            name="nama_menu"
                            class="form-control"
                            value="{{ old('nama_menu', $menu->nama_menu) }}"
                            required
                        >
                    </div>

                    {{-- Harga --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">
                            Harga Jual (Rp)
                        </label>

                        <input
                            type="number"
                            name="harga"
                            class="form-control"
                            value="{{ old('harga', (int) $menu->harga) }}"
                            required
                        >
                    </div>

                    {{-- Kategori --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">
                            Kategori Menu
                        </label>

                        <select name="kategori_menu_id" class="form-select" required>
                            @foreach($kategori as $k)
                                <option
                                    value="{{ $k->id }}"
                                    {{ $menu->kategori_menu_id == $k->id ? 'selected' : '' }}
                                >
                                    {{ $k->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">
                            Status Ketersediaan
                        </label>

                        <select name="status_menu_id" class="form-select" required>
                            @foreach($status as $s)
                                <option
                                    value="{{ $s->id }}"
                                    {{ $menu->status_menu_id == $s->id ? 'selected' : '' }}
                                >
                                    {{ $s->nama_status }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Stok --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">
                            Stok Menu
                        </label>

                        <input
                            type="number"
                            name="stok"
                            class="form-control"
                            value="{{ old('stok', $menu->stok) }}"
                            min="0"
                            required
                        >
                    </div>
                    {{-- Deskripsi --}}
                    <div class="col-12 mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase">
                            Deskripsi
                        </label>

                        <textarea
                            name="deskripsi"
                            class="form-control"
                            rows="4"
                        >{{ old('deskripsi', $menu->deskripsi) }}</textarea>
                    </div>

                </div>

                {{-- Button --}}
                <div class="mt-2 border-top pt-4 d-flex gap-2">

                    <button
                        type="submit"
                        class="btn btn-warning px-5 fw-bold text-white shadow-sm"
                        style="border-radius:10px;"
                    >
                        <i class="fas fa-save me-1"></i>
                        Simpan Perubahan
                    </button>

                    <a
                        href="{{ $routeKembali }}"
                        class="btn btn-light px-4 border shadow-sm"
                        style="border-radius:10px;"
                    >
                        Batal
                    </a>

                </div>

            </form>

        </div>
    </div>
</div>

@endsection
