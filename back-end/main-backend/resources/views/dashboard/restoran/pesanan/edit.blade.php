@extends('dashboard.layouts.app')
@section('title', 'Edit Pesanan #' . $pesanan->id)
@section('content')

<div class="container-fluid px-4">
    <div class="mb-4">
        <a href="{{ route('dashboard.restoran.pesanan.index') }}" class="text-decoration-none small text-muted">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
        <h4 class="fw-bold mt-2">Ubah Status Pesanan #RS-{{ $pesanan->id }}</h4>
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
                            <label class="form-label small fw-bold text-muted">PELANGGAN (USER ID)</label>
                            <input type="text" class="form-control bg-light" value="{{ $pesanan->user_id }}" readonly disabled>
                            <small class="text-info"><i class="fas fa-info-circle me-1"></i> Notifikasi otomatis akan dikirim ke perangkat user ini.</small>
                        </div>

                        <!-- Edit Nomor Lokasi (Sinkron dengan DB nomor_lokasi) -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold">NOMOR MEJA / LOKASI</label>
                            <input type="text" name="nomor_meja" class="form-control" value="{{ $pesanan->nomor_lokasi }}" required>
                        </div>

                        <!-- Edit Status Pesanan (Antrean) -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-primary">STATUS PESANAN (PROSES DAPUR)</label>
                            <select name="status_pesanan_id" class="form-select border-primary" required>
                                @foreach($statusList as $s)
                                    <option value="{{ $s->id }}" {{ $pesanan->status_pesanan_id == $s->id ? 'selected' : '' }}>
                                        {{ strtoupper($s->nama_status) }} 
                                        {{-- Logika Penambahan Label Panduan Staff --}}
                                        @if($s->id == 3) (Kirim Notif Makanan Siap) @endif
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text text-muted" style="font-size: 10px;">Pilih <b>DISAJIKAN</b> jika makanan sudah siap diantar ke meja/kamar.</div>
                        </div>

                        <!-- Edit Status Pembayaran -->
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-success">STATUS PEMBAYARAN (KASIR)</label>
                            <select name="status_pembayaran_id" class="form-select border-success" required>
                                @foreach($paymentStatusList as $p)
                                    <option value="{{ $p->id }}" {{ $pesanan->status_pembayaran_id == $p->id ? 'selected' : '' }}>
                                        {{ strtoupper($p->nama_status) }}
                                        @if($p->id == 2) (Kirim Notif Pembayaran Sukses) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning fw-bold text-white py-2 shadow-sm" style="border-radius: 10px;">
                                <i class="fas fa-save me-1"></i> Simpan & Update ke HP User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Ringkasan Pesanan di Samping --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm" style="border-radius: 16px; background: #1a1a2e; color: white;">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3 text-warning"><i class="fas fa-utensils me-2"></i>Rincian Pesanan:</h6>
                    <ul class="list-group list-group-flush bg-transparent">
                        @foreach($pesanan->details as $detail)
                            <li class="list-group-item bg-transparent px-0 d-flex justify-content-between border-secondary text-white-50">
                                <span>
                                    {{ $detail->menu->nama_menu ?? 'Menu Tidak Diketahui' }}
                                    <strong class="text-white">(x{{ $detail->jumlah }})</strong>
                                </span>
                                <span class="fw-bold text-white">Rp {{ number_format($detail->jumlah * $detail->harga_at_porsi, 0, ',', '.') }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <hr class="border-secondary">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold opacity-75">TOTAL TAGIHAN</span>
                        <h4 class="fw-black text-warning mb-0">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
            
            <div class="alert alert-light mt-3 border-0 shadow-sm small" style="border-radius: 12px;">
                <i class="fas fa-exclamation-triangle text-warning me-1"></i> 
                Pastikan uang tunai sudah diterima sebelum mengubah status pembayaran menjadi <b>LUNAS</b>.
            </div>
        </div>
    </div>
</div>

@endsection