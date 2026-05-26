@extends('dashboard.layouts.app')
@section('title', 'Pesanan Restoran')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">📦 Pesanan Restoran</h4>
        <a href="{{ route('dashboard.restoran.pesanan.create') }}" class="btn btn-primary shadow-sm">+ Tambah Pesanan</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius:16px; overflow:hidden;">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr style="background:#1a1a2e;">
                        <th class="px-4 py-3">No</th>
                        <th>Pelanggan</th>
                        <th>Meja</th>
                        <th>Total</th>
                        <th>Status Bayar</th>
                        <th>Status Antrean</th> {{-- Tambahan Kolom --}}
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pesanan as $i => $p)
                    {{-- Pencarian User berdasarkan ID --}}
                    @php $user = $users[$p->user_id] ?? null; @endphp
                    <tr>
                        <td class="px-4 text-muted">{{ $i + 1 }}</td>
                        <td>
                            @if($user)
                                <div class="fw-bold" style="font-size:13px;">{{ $user['full_name'] }}</div>
                                <small class="text-muted">{{ $user['email'] }}</small>
                            @else
                                <div class="fw-bold text-danger">Pelanggan #{{ $p->user_id }}</div>
                                <small class="text-muted">Data tidak sinkron</small>
                            @endif
                        </td>
                        <td><span class="badge bg-secondary">Meja {{ $p->nomor_meja }}</span></td>
                        <td class="fw-bold text-success">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                        <td>
                            @php
                                $payStatus = $p->status_pembayaran_id;
                                $payColor = [1=>'warning', 2=>'success', 3=>'danger'];
                            @endphp
                            <span class="badge bg-{{ $payColor[$payStatus] ?? 'secondary' }}">
                                {{ $p->statusPembayaran->nama_status ?? 'Pending' }}
                            </span>
                        </td>
                        <td>
                            {{-- Menampilkan Status Antrean yang diatur Staf --}}
                            <span class="fw-bold text-primary" style="font-size: 13px;">
                                <i class="fas fa-utensils me-1"></i>
                                {{ $p->statusPesanan->nama_status ?? 'Menunggu' }}
                            </span>
                        </td>
                        <td class="text-center">
                        <div class="d-flex justify-content-center gap-2">
                            <a href="{{ route('dashboard.restoran.pesanan.show', $p->id) }}" class="btn btn-sm btn-info text-white px-3 shadow-sm" style="border-radius:8px;">Detail</a>
                            <a href="{{ route('dashboard.restoran.pesanan.edit', $p->id) }}" class="btn btn-sm btn-warning text-white px-3 shadow-sm" style="border-radius:8px;">Edit</a>

                            <form action="{{ route('dashboard.restoran.pesanan.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesanan ini?')" class="m-0">
                                @csrf
                                @method('DELETE')

                                {{-- LOGIKA TOMBOL DINAMIS --}}
                                @if($p->status_pembayaran_id == 2 || $p->status_pesanan_id == 3)
                                    {{-- Jika Lunas (2) atau Selesai (3) --}}
                                    <button type="submit" class="btn btn-sm btn-danger shadow-sm px-3" style="border-radius:8px;">Hapus</button>
                                @else
                                    {{-- Jika masih Pending atau lainnya --}}
                                    <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm px-3" style="border-radius:8px;">Batal</button>
                                @endif
                            </form>
                        </div>
                    </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-5 text-muted">Belum ada pesanan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
