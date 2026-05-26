@extends('dashboard.layouts.app')
@section('title', 'Edit Pesanan #' . $pesanan->id)
@section('content')

<div class="container-fluid px-4">
    <div class="mb-4">
        <a href="{{ route('dashboard.restoran.pesanan.index') }}" class="text-decoration-none small">← Kembali ke Daftar</a>
        <h4 class="fw-bold mt-2">Ubah Status Pesanan</h4>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <form action="{{ route('dashboard.restoran.pesanan.update', $pesanan->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Info Pelanggan (Read Only) -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">PELANGGAN</label>
                            <input type="text" class="form-control bg-light" value="{{ $pesanan->user_id }}" readonly disabled>
                            <small class="text-muted italic">Nama pelanggan tidak dapat diubah dari sini.</small>
                        </div>

                        <!-- Edit Nomor Meja -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold">NOMOR MEJA</label>
                            <input type="text" name="nomor_meja" class="form-control" value="{{ $pesanan->nomor_meja }}" required>
                        </div>

                        <!-- Edit Status Pesanan (Antrean) -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold">STATUS PESANAN (ANTREAN)</label>
                            <select name="status_pesanan_id" class="form-select" required>
                                @foreach($statusList as $s)
                                    <option value="{{ $s->id }}" {{ $pesanan->status_pesanan_id == $s->id ? 'selected' : '' }}>
                                        {{ $s->nama_status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Edit Status Pembayaran -->
                        <div class="mb-4">
                            <label class="form-label small fw-bold">STATUS PEMBAYARAN</label>
                            <select name="status_pembayaran_id" class="form-select" required>
                                @foreach($paymentStatusList as $p)
                                    <option value="{{ $p->id }}" {{ $pesanan->status_pembayaran_id == $p->id ? 'selected' : '' }}>
                                        {{ $p->nama_status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning fw-bold text-white py-2" style="border-radius: 10px;">
                                <i class="fas fa-save me-1"></i> Perbarui Data Pesanan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Ringkasan Pesanan di Samping --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm" style="border-radius: 16px; background: #f8f9fa;">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Rincian Menu Saat Ini:</h6>
                    <ul class="list-group list-group-flush bg-transparent">
                        @foreach($pesanan->details as $detail)
                            <li class="list-group-item bg-transparent px-0 d-flex justify-content-between">
                                {{-- Gunakan ?? untuk mengantisipasi jika datanya benar-benar kosong --}}
                                <span>
                                    {{ $detail->menu->nama_menu ?? 'Menu Telah Dihapus' }}
                                    <strong>(x{{ $detail->jumlah }})</strong>
                                </span>
                                <span class="fw-bold">Rp {{ number_format($detail->jumlah * $detail->harga_at_porsi, 0, ',', '.') }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold">TOTAL HARGA</span>
                        <h4 class="fw-black text-primary mb-0">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
