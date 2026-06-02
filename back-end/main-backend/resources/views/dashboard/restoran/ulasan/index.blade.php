@extends('dashboard.layouts.app')
@section('title', 'Ulasan Restoran')
@section('content')

<div class="container-fluid px-4">
    <div class="mb-4">
        {{-- JUDUL KHUSUS RESTORAN --}}
        <h4 class="fw-bold mb-1"><i class="fas fa-utensils text-orange me-2"></i> Ulasan Pelanggan Restoran</h4>
        <p class="text-muted small">Kelola feedback dan rating menu makanan/minuman dari pelanggan.</p>
    </div>

    {{-- Widget Statistik Restoran --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm" style="border-radius:15px; border-left:4px solid #fd7e14;">
                <div class="card-body py-3">
                    <p class="text-muted small mb-1 fw-bold text-uppercase">TOTAL ULASAN MENU</p>
                    <h4 class="fw-bold text-dark mb-0">{{ $totalUlasan }} Masukan</h4>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm" style="border-radius:15px; border-left:4px solid #ffc107;">
                <div class="card-body py-3">
                    <p class="text-muted small mb-1 fw-bold text-uppercase">RATING RESTORAN</p>
                    <h4 class="fw-bold text-warning mb-0">{{ number_format($rataRating, 1) }} / 5.0 ⭐</h4>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius:12px;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius:16px; overflow:hidden;">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr style="background:#2c3e50;">
                        <th class="px-4 py-3">NO</th>
                        <th>PELANGGAN</th>
                        {{-- KUNCI: Harus MENU, bukan Tipe Kamar --}}
                        <th>MENU MAKANAN</th>
                        <th class="text-center">RATING</th>
                        <th>KOMENTAR</th>
                        <th>TANGGAL</th>
                        <th class="text-center">STATUS</th>
                        @if(session('user.role') === 'admin')
                        <th class="text-center">AKSI</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($ulasan as $i => $u)
                    @php $user = $users[$u->user_id] ?? null; @endphp
                    <tr>
                        <td class="px-4 text-muted small">{{ $i + 1 }}</td>
                        <td>
                            <div class="fw-bold" style="font-size:13px;">{{ $user['full_name'] ?? 'Pelanggan #'.$u->user_id }}</div>
                            <small class="text-muted" style="font-size:11px;">{{ $user['username'] ?? '-' }}</small>
                        </td>
                        <td style="font-size:13px;">
                            {{-- SINKRON: Mengambil relasi Menu --}}
                            <span class="fw-bold text-primary">{{ $u->menu->nama_menu ?? 'Menu Dihapus' }}</span>
                        </td>
                        <td class="text-center">
                            <div class="text-warning small">
                                @for($s=1; $s<=5; $s++)
                                    <i class="{{ $s <= $u->rating ? 'fas' : 'far' }} fa-star"></i>
                                @endfor
                            </div>
                        </td>
                        <td style="max-width:220px;">
                            <p class="mb-0 text-truncate small text-dark">{{ $u->komentar }}</p>
                            <button class="btn btn-link btn-sm p-0 text-decoration-none fw-bold"
                                    style="font-size:10px;"
                                    onclick="lihatReviewResto('{{ addslashes($u->komentar) }}', '{{ $user['full_name'] ?? 'Pelanggan' }}', {{ $u->rating }})">
                                Baca Detail
                            </button>
                        </td>
                        <td style="font-size:12px;" class="text-muted">{{ $u->created_at->format('d/m/Y') }}</td>
                        <td class="text-center">
                            @if($u->is_hidden)
                                <span class="badge bg-danger" style="font-size:9px;">HIDDEN</span>
                            @else
                                <span class="badge bg-success" style="font-size:9px;">VISIBLE</span>
                            @endif
                        </td>
                        @if(session('user.role') === 'admin')
                        <td class="text-center">
                            <form action="{{ route('dashboard.restoran.ulasan.toggle', $u->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $u->is_hidden ? 'btn-success' : 'btn-outline-danger' }}" 
                                        style="font-size:10px; border-radius: 8px; width: 80px;">
                                    {{ $u->is_hidden ? 'Tampilkan' : 'Sembunyikan' }}
                                </button>
                            </form>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-5 text-muted">Belum ada ulasan restoran yang masuk.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL DETAIL ULASAN RESTORAN --}}
<div class="modal fade" id="modalReviewResto" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:20px; overflow:hidden;">
            <div class="modal-header border-0 py-3 px-4" style="background:#fd7e14;">
                <h6 class="modal-title text-white fw-bold">🍔 Feedback Pelanggan Resto</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="rounded-circle bg-orange text-white d-flex align-items-center justify-content-center fw-bold mx-auto mb-3"
                     style="width:60px;height:60px;font-size:22px; background: #fd7e14; color: white;" id="r-avatar"></div>
                <h6 class="fw-bold mb-0" id="r-nama"></h6>
                <div class="text-warning my-2" id="r-bintang" style="font-size:1.2rem;"></div>
                <hr class="w-25 mx-auto">
                <div class="p-3 bg-light" style="border-radius:12px; border: 1px solid #eee;">
                    <p class="mt-0 text-dark mb-0" id="r-komentar" style="line-height:1.6; font-style:italic;"></p>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4">
                <button type="button" class="btn btn-secondary w-100" style="border-radius:10px; font-weight:bold;" data-bs-dismiss="modal">TUTUP</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function lihatReviewResto(komentar, nama, rating) {
    document.getElementById('r-nama').innerText = nama;
    document.getElementById('r-avatar').innerText = nama.charAt(0).toUpperCase();
    document.getElementById('r-komentar').innerText = `"${komentar}"`;

    let bintang = '';
    for(let i = 1; i <= 5; i++) {
        bintang += i <= rating ? '★' : '☆';
    }
    document.getElementById('r-bintang').innerText = bintang;

    new bootstrap.Modal(document.getElementById('modalReviewResto')).show();
}
</script>
@endpush

<style>
    .text-orange { color: #fd7e14; }
    .bg-orange { background-color: #fd7e14; }
</style>