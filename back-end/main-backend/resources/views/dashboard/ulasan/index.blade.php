@extends('dashboard.layouts.app')
@section('title', 'Seluruh Ulasan')
@section('content')

<div class="container-fluid px-4">
    <div class="row mb-4 align-items-end">
        <div class="col-lg-6">
            <h4 class="fw-bold mb-1">⭐ Seluruh Ulasan Pelanggan</h4>
            <p class="text-muted small mb-0">Pantau masukan pelanggan untuk layanan Hotel & Restoran.</p>
        </div>
        <div class="col-lg-6 text-lg-end">
            <div class="d-inline-flex gap-3">
                <div class="bg-white p-2 px-3 shadow-sm rounded-3 border-start border-primary border-4">
                    <small class="text-muted d-block" style="font-size: 10px;">TOTAL ULASAN</small>
                    <span class="fw-bold">{{ $totalUlasan }} Masukan</span>
                </div>
                <div class="bg-white p-2 px-3 shadow-sm rounded-3 border-start border-warning border-4">
                    <small class="text-muted d-block" style="font-size: 10px;">RATA-RATA RATING</small>
                    <span class="fw-bold text-warning">{{ number_format($rataRating, 1) }} / 5.0 ★</span>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('success') }}</div>
    @endif

    {{-- Navigasi Tab --}}
    <ul class="nav nav-pills mb-3 gap-2" id="pills-tab" role="tablist">
        <li class="nav-item">
            <button class="nav-link active px-4 fw-bold" data-bs-toggle="pill" data-bs-target="#hotel" style="border-radius:10px;">🏨 Hotel</button>
        </li>
        <li class="nav-item">
            <button class="nav-link px-4 fw-bold" data-bs-toggle="pill" data-bs-target="#resto" style="border-radius:10px;">🍽️ Restoran</button>
        </li>
    </ul>

    <div class="tab-content">
        {{-- ================= TAB HOTEL ================= --}}
        <div class="tab-pane fade show active" id="hotel">
            <div class="card border-0 shadow-sm" style="border-radius:16px; overflow:hidden;">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background:#1a1a2e; color:white;">
                        <tr>
                            <th class="px-4 py-3">NO</th>
                            <th>PELANGGAN</th>
                            <th>TIPE KAMAR</th>
                            <th class="text-center">RATING</th>
                            <th>KOMENTAR</th>
                            <th class="text-center">STATUS</th>
                            <th class="text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ulasanHotel as $i => $u)
                        @php $user = $users->get((int)$u->user_id); @endphp
                        <tr>
                            <td class="px-4 text-muted small">{{ $i+1 }}</td>
                            <td>
                                <div class="fw-bold" style="font-size: 13px;">{{ $user['full_name'] ?? 'Pelanggan #'.$u->user_id }}</div>
                                <small class="text-muted">{{ $user['email'] ?? 'Akun tidak ditemukan' }}</small>
                            </td>
                            <td><span class="small">{{ $u->tipeKamar->nama_tipe ?? 'Kamar Terhapus' }}</span></td>
                            <td class="text-center text-warning">
                                {!! str_repeat('★', $u->rating) !!}{!! str_repeat('☆', 5-$u->rating) !!}
                            </td>
                            <td style="max-width:200px;">
                                <p class="mb-0 text-truncate small">{{ $u->komentar }}</p>
                                <button class="btn btn-link btn-sm p-0 text-decoration-none fw-bold" style="font-size: 10px;"
                                        onclick="lihatKomentar('{{ addslashes($u->komentar) }}', '{{ $user['full_name'] ?? 'Pelanggan' }}', {{ $u->rating }})">
                                    BACA LENGKAP
                                </button>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $u->is_hidden ? 'bg-danger' : 'bg-success' }}" style="font-size: 10px;">
                                    {{ $u->is_hidden ? 'Hidden' : 'Visible' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <form action="{{ route('dashboard.ulasan.toggle', $u->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $u->is_hidden ? 'btn-outline-success' : 'btn-outline-danger' }}" style="font-size: 11px; width: 90px;">
                                        {{ $u->is_hidden ? 'Tampilkan' : 'Sembunyikan' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ================= TAB RESTO ================= --}}
        <div class="tab-pane fade" id="resto">
            <div class="card border-0 shadow-sm" style="border-radius:16px; overflow:hidden;">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background:#1a1a2e; color:white;">
                        <tr>
                            <th class="px-4 py-3">NO</th>
                            <th>PELANGGAN</th>
                            <th>MENU</th>
                            <th class="text-center">RATING</th>
                            <th>KOMENTAR</th>
                            <th class="text-center">STATUS</th>
                            <th class="text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ulasanRestoran as $i => $u)
                        @php $user = $users->get((int)$u->user_id); @endphp
                        <tr>
                            <td class="px-4 text-muted small">{{ $i+1 }}</td>
                            <td>
                                <div class="fw-bold" style="font-size: 13px;">{{ $user['full_name'] ?? 'Pelanggan #'.$u->user_id }}</div>
                                <small class="text-muted">{{ $user['email'] ?? 'Akun tidak ditemukan' }}</small>
                            </td>
                            <td><span class="small">{{ $u->menu->nama_menu ?? 'Menu Terhapus' }}</span></td>
                            <td class="text-center text-warning">
                                {!! str_repeat('★', $u->rating) !!}{!! str_repeat('☆', 5-$u->rating) !!}
                            </td>
                            <td style="max-width:200px;">
                                <p class="mb-0 text-truncate small">{{ $u->komentar }}</p>
                                <button class="btn btn-link btn-sm p-0 text-decoration-none fw-bold" style="font-size: 10px;"
                                        onclick="lihatKomentar('{{ addslashes($u->komentar) }}', '{{ $user['full_name'] ?? 'Pelanggan' }}', {{ $u->rating }})">
                                    BACA LENGKAP
                                </button>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $u->is_hidden ? 'bg-danger' : 'bg-success' }}" style="font-size: 10px;">
                                    {{ $u->is_hidden ? 'Hidden' : 'Visible' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <form action="{{ route('dashboard.ulasan.toggle', $u->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $u->is_hidden ? 'btn-outline-success' : 'btn-outline-danger' }}" style="font-size: 11px; width: 90px;">
                                        {{ $u->is_hidden ? 'Tampilkan' : 'Sembunyikan' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODAL DETAIL KOMENTAR --}}
<div class="modal fade" id="modalKomentar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header border-0 py-3 px-4 text-white" style="background: #1a1a2e;">
                <h6 class="modal-title fw-bold">💬 Detail Ulasan Lengkap</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold mx-auto mb-3"
                     style="width: 50px; height: 50px; font-size: 20px; border: 3px solid #eee;" id="k-avatar"></div>
                <h6 class="fw-bold mb-0" id="k-nama"></h6>
                <div class="text-warning my-2" id="k-bintang" style="font-size: 1.2rem;"></div>
                <hr class="w-25 mx-auto">
                <div class="p-3 bg-light rounded-3 mt-3">
                    <p class="mb-0 text-dark italic" id="k-komentar" style="line-height: 1.6; font-style: italic;"></p>
                </div>
            </div>
            <div class="modal-footer border-0 p-3">
                <button type="button" class="btn btn-secondary w-100 fw-bold" data-bs-dismiss="modal" style="border-radius: 10px;">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function lihatKomentar(komentar, nama, rating) {
    document.getElementById('k-nama').innerText = nama;
    document.getElementById('k-avatar').innerText = nama.charAt(0).toUpperCase();
    document.getElementById('k-komentar').innerText = `"${komentar}"`;
    let bintang = '';
    for(let i = 1; i <= 5; i++) bintang += i <= rating ? '★' : '☆';
    document.getElementById('k-bintang').innerText = bintang;
    new bootstrap.Modal(document.getElementById('modalKomentar')).show();
}
</script>
@endpush

<style>
    .nav-pills .nav-link.active { background-color: #1a1a2e !important; color: white !important; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    .nav-pills .nav-link { color: #1a1a2e; background: white; border: 1px solid #dee2e6; margin-right: 5px; }
    .italic { font-style: italic; }
</style>
