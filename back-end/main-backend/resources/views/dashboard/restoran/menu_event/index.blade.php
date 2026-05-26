@extends('dashboard.layouts.app')

@section('title', 'Daftar Menu Event')

@section('content')
<div class="container-fluid px-4">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">🎉 Daftar Menu Event</h3>
            <p class="text-muted small mb-0">Kelola daftar menu promo yang tersedia pada event restoran saat ini.</p>
        </div>
        <a href="{{ route('dashboard.restoran.menu-event.create') }}" class="btn btn-primary shadow-sm px-4" style="border-radius:10px;">
            <i class="fas fa-plus me-2"></i> Tambah Menu Event
        </a>
    </div>

    {{-- Alert Notifikasi --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" style="border-radius:12px;">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Tabel Utama --}}
    <div class="card border-0 shadow-sm" style="border-radius:16px; overflow:hidden;">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr style="background:#1a1a2e;">
                        <th class="px-4 py-3 text-center" style="width:50px;">NO</th>
                        <th>EVENT</th>
                        <th>NAMA MENU</th>
                        <th class="text-end">HARGA NORMAL</th>
                        <th class="text-end">HARGA EVENT</th>
                        <th class="text-center">STATUS</th>
                        <th class="text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($menuEvents as $i => $me)
                    <tr>
                        <td class="px-4 text-muted text-center small">{{ $i + 1 }}</td>
                        <td>
                            {{-- Nama Event --}}
                            <span class="badge bg-primary px-3 py-2" style="border-radius:8px; font-size: 11px;">
                                {{ $me->nama_event }}
                            </span>
                        </td>
                        {{-- Nama Menu --}}
                        <td class="fw-bold text-dark">{{ $me->nama_menu }}</td>

                        {{-- Harga Normal --}}
                        <td class="text-end text-muted small">
                            <span class="text-decoration-line-through text-danger">
                                Rp {{ number_format($me->harga, 0, ',', '.') }}
                            </span>
                        </td>

                        {{-- Harga Event --}}
                        <td class="text-end fw-bold text-primary">
                            Rp {{ number_format($me->harga_khusus, 0, ',', '.') }}
                        </td>

                        <td class="text-center">
                            <span class="badge {{ $me->is_active ? 'bg-success' : 'bg-danger' }} px-3 py-2" style="border-radius:8px; font-size: 10px;">
                                {{ $me->is_active ? 'AKTIF' : 'NONAKTIF' }}
                            </span>
                        </td>

                        {{-- Tombol Aksi tetap sama --}}
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <button type="button" class="btn btn-sm btn-info text-white px-3 shadow-sm"
                                        onclick="tampilDetailEvent({{ $me->id }})" style="border-radius:8px;">
                                    Lihat
                                </button>
                                <a href="{{ route('dashboard.restoran.menu-event.edit', $me->id) }}"
                                class="btn btn-sm btn-warning text-white px-3 shadow-sm" style="border-radius:8px;">
                                    Edit
                                </a>
                                <form action="{{ route('dashboard.restoran.menu-event.destroy', $me->id) }}"
                                    method="POST"
                                    class="m-0"
                                    onsubmit="return confirm('Apakah Anda yakin ingin mengarsipkan menu ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger px-3 shadow-sm" style="border-radius:8px;">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    {{-- bagian empty --}}
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL POPUP DETAIL --}}
<div class="modal fade" id="modalDetailEvent" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:20px; overflow:hidden;">
            <div class="modal-header border-0 py-3 px-4" style="background:#1a1a2e;">
                <h6 class="modal-title text-white fw-bold"><i class="fas fa-search me-2"></i> Rincian Promo Menu</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <span id="det-event" class="badge bg-primary px-3 py-2 mb-2" style="border-radius: 8px; font-size: 12px;"></span>
                    <h3 id="det-menu" class="fw-bold text-dark mb-0"></h3>
                </div>

                <div class="p-3 bg-light rounded-4 mb-4 text-center border">
                    <div class="row">
                        <div class="col-6 border-end">
                            <small class="text-muted d-block mb-1 uppercase fw-bold" style="font-size: 9px;">HARGA NORMAL</small>
                            <span id="det-harga-asli" class="text-decoration-line-through text-danger fw-bold fs-6"></span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block mb-1 uppercase fw-bold" style="font-size: 9px;">HARGA EVENT</small>
                            <span id="det-harga-event" class="text-primary fw-bold fs-5"></span>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="text-muted small fw-bold d-block mb-1 uppercase" style="font-size: 10px;">DESKRIPSI MENU</label>
                    <p id="det-deskripsi" class="small text-secondary" style="line-height: 1.6; background: #fdfdfd; padding: 10px; border-radius: 8px; border: 1px solid #eee;"></p>
                </div>

                <div id="det-status-box" class="mt-4 p-2 text-center rounded-3 bg-light border">
                    <span class="small text-muted">Status Promo: </span>
                    <span id="det-status-label" class="badge"></span>
                </div>
            </div>
            <div class="modal-footer border-0 p-3">
                <button type="button" class="btn btn-outline-secondary w-100 py-2 fw-bold" data-bs-dismiss="modal" style="border-radius:12px;">Tutup Rincian</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
/**
 * Fungsi untuk mengambil data rincian via AJAX dan menampilkannya di Modal
 */
function tampilDetailEvent(id) {
    fetch(`/dashboard/restoran/menu-event/${id}`)
        .then(response => {
            if (!response.ok) throw new Error('Data tidak ditemukan');
            return response.json();
        })
        .then(data => {
            // Mapping data ke elemen Modal
            document.getElementById('det-event').innerText = data.nama_event;
            document.getElementById('det-menu').innerText = data.nama_menu;
            document.getElementById('det-deskripsi').innerText = data.deskripsi || 'Tidak ada keterangan tambahan untuk menu ini.';

            // Format Mata Uang Rupiah
            const rupiah = (number) => new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0
            }).format(number);

            document.getElementById('det-harga-asli').innerText = rupiah(data.harga_asli || data.harga);
            document.getElementById('det-harga-event').innerText = rupiah(data.harga_khusus);

            // Pengaturan Badge Status
            const label = document.getElementById('det-status-label');
            if(data.is_active) {
                label.innerText = 'PROMO AKTIF';
                label.className = 'badge bg-success';
            } else {
                label.innerText = 'PROMO NONAKTIF';
                label.className = 'badge bg-danger';
            }

            // Tampilkan Modal
            new bootstrap.Modal(document.getElementById('modalDetailEvent')).show();
        })
        .catch(error => {
            alert('Gagal memuat rincian: ' + error.message);
        });
}
</script>
@endpush
