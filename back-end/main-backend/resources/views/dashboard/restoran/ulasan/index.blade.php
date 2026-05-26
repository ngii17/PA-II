<div>
    <!-- Live as if you were to die tomorrow. Learn as if you were to live forever. - Mahatma Gandhi -->
</div>
@extends('dashboard.layouts.app')
@section('title', 'Ulasan Hotel')
@section('content')
<div class="container-fluid">
    <h4 class="mb-4 fw-bold">⭐ Ulasan Pelanggan Hotel</h4>

    {{-- Widget Statistik --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm" style="border-left:4px solid #1a1a2e;">
                <div class="card-body py-3">
                    <p class="text-muted small mb-1">TOTAL ULASAN</p>
                    <h4 class="fw-bold text-primary mb-0">{{ $totalUlasan }} Masukan</h4>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm" style="border-left:4px solid #f59e0b;">
                <div class="card-body py-3">
                    <p class="text-muted small mb-1">RATA-RATA RATING</p>
                    <h4 class="fw-bold text-warning mb-0">{{ number_format($rataRating, 1) }} / 5.0 ⭐</h4>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius:16px; overflow:hidden;">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr style="background:#1a1a2e;">
                        <th class="px-4 py-3">NO</th>
                        <th>PELANGGAN</th>
                        <th>TIPE KAMAR</th>
                        <th>RATING</th>
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
                        <td class="px-4">{{ $i + 1 }}</td>
                        <td>
                            <div class="fw-bold" style="font-size:13px;">{{ $user['full_name'] ?? 'Tamu' }}</div>
                            <small class="text-muted" style="font-size:11px;">{{ $user['email'] ?? '-' }}</small>
                        </td>
                        <td style="font-size:13px;">{{ $u->tipeKamar->nama_tipe ?? '-' }}</td>
                        <td>
                            <div class="text-warning">
                                {!! str_repeat('★', $u->rating) !!}{!! str_repeat('☆', 5-$u->rating) !!}
                            </div>
                        </td>
                        <td style="max-width:250px;">
                            <p class="mb-0 text-truncate small">{{ $u->komentar }}</p>
                            {{-- Tombol Lihat Selengkapnya --}}
                            <button class="btn btn-link btn-sm p-0 text-decoration-none"
                                    style="font-size:11px;"
                                    onclick="lihatKomentar('{{ addslashes($u->komentar) }}', '{{ $user['full_name'] ?? 'Pelanggan' }}', {{ $u->rating }})">
                                Lihat selengkapnya
                            </button>
                        </td>
                        <td style="font-size:12px;">{{ $u->created_at->format('d/m/Y') }}</td>
                        <td class="text-center">
                            <span class="badge {{ $u->is_hidden ? 'bg-danger' : 'bg-success' }}" style="font-size:10px; border-radius:8px;">
                                {{ $u->is_hidden ? 'Hidden' : 'Visible' }}
                            </span>
                        </td>
                        @if(session('user.role') === 'admin')
                        <td class="text-center">
                            <form action="{{ route('dashboard.hotel.ulasan.toggle', $u->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $u->is_hidden ? 'btn-outline-success' : 'btn-outline-danger' }}" style="font-size:11px; padding: 4px 10px;">
                                    {{ $u->is_hidden ? 'Tampilkan' : 'Sembunyikan' }}
                                </button>
                            </form>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-5 text-muted">Belum ada ulasan hotel.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL UNTUK MELIHAT ULASAN LENGKAP --}}
<div class="modal fade" id="modalKomentar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px; overflow:hidden;">
            <div class="modal-header border-0 py-3 px-4" style="background:#1a1a2e;">
                <h6 class="modal-title text-white fw-bold">💬 Isi Ulasan Tamu</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold mx-auto mb-3"
                     style="width:50px;height:50px;font-size:18px;" id="k-avatar"></div>
                <h6 class="fw-bold mb-0" id="k-nama"></h6>
                <div class="text-warning my-2" id="k-bintang" style="font-size:1.2rem;"></div>
                <hr class="w-25 mx-auto">
                <p class="mt-3 text-muted" id="k-komentar" style="line-height:1.8; font-style:italic;"></p>
            </div>
            <div class="modal-footer border-0 px-4 pb-4">
                <button type="button" class="btn btn-outline-secondary w-100" style="border-radius:10px;" data-bs-dismiss="modal">Tutup</button>
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
    for(let i = 1; i <= 5; i++) {
        bintang += i <= rating ? '★' : '☆';
    }
    document.getElementById('k-bintang').innerText = bintang;

    new bootstrap.Modal(document.getElementById('modalKomentar')).show();
}
</script>
@endpush
