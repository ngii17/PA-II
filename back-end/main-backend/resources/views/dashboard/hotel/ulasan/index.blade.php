@extends('dashboard.layouts.app')
@section('title', 'Ulasan Hotel')

@push('styles')
<style>
/* ============================================================
   ROOT VARIABLES
   ============================================================ */
:root {
    --navy:         #00197D;
    --navy-dark:    #000C3D;
    --navy-mid:     #0025B3;
    --gold:         #D4AF37;
    --amber:        #f59e0b;
    --rose:         #e11d48;
    --emerald:      #10b981;
    --surface:      #ffffff;
    --surface-2:    #f8fafc;
    --border:       #e9eef8;
    --text-primary: #0f172a;
    --text-muted:   #94a3b8;
    --shadow-card:  0 4px 24px rgba(0,25,125,.07), 0 1px 3px rgba(0,0,0,.04);
    --shadow-hover: 0 16px 40px rgba(0,25,125,.13);
    --font:         'Plus Jakarta Sans', sans-serif;
    --transition:   all .3s cubic-bezier(.34,1.56,.64,1);
}

*, *::before, *::after { box-sizing: border-box; }
body, input, select, textarea, button, label {
    font-family: var(--font) !important;
}

/* ============================================================
   PAGE BACKGROUND
   ============================================================ */
.ulasan-page-wrapper {
    min-height: 100vh;
    background: linear-gradient(160deg, #f0f4ff 0%, #fafbff 60%, #fffdf0 100%);
    padding: 32px 24px;
    position: relative;
    overflow-x: hidden;
}
.ulasan-page-wrapper::before,
.ulasan-page-wrapper::after {
    content: '';
    position: fixed;
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
}
.ulasan-page-wrapper::before {
    width: 600px; height: 600px;
    top: -200px; right: -150px;
    background: radial-gradient(circle, rgba(0,25,125,.04) 0%, transparent 70%);
}
.ulasan-page-wrapper::after {
    width: 400px; height: 400px;
    bottom: -100px; left: -100px;
    background: radial-gradient(circle, rgba(212,175,55,.06) 0%, transparent 70%);
}
.ulasan-page-wrapper > * { position: relative; z-index: 1; }

/* ============================================================
   HEADER
   ============================================================ */
.ulasan-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 36px;
    flex-wrap: wrap;
    gap: 16px;
}
.ulasan-header-left h2 {
    font-size: 2rem;
    font-weight: 800;
    color: var(--navy-dark);
    margin: 0 0 4px;
    letter-spacing: -.03em;
    line-height: 1.1;
}
.ulasan-header-left h2 span {
    background: linear-gradient(135deg, var(--gold) 0%, #e8c84a 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.ulasan-header-left p {
    color: var(--text-muted);
    margin: 0;
    font-size: .875rem;
    font-weight: 500;
}

/* ============================================================
   STATS STRIP
   ============================================================ */
.stats-strip {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 16px;
    margin-bottom: 28px;
}
.stat-card {
    background: var(--surface);
    border-radius: 20px;
    padding: 20px 22px;
    box-shadow: var(--shadow-card);
    display: flex;
    align-items: center;
    gap: 14px;
    border: 1px solid var(--border);
    transition: var(--transition);
    opacity: 0;
    transform: translateY(20px);
}
.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-hover);
}
.stat-icon {
    width: 48px; height: 48px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem;
}
.stat-icon.navy  { background: rgba(0,25,125,.08);   color: var(--navy); }
.stat-icon.gold  { background: rgba(212,175,55,.12); color: var(--gold); }
.stat-icon.green { background: rgba(16,185,129,.1);  color: var(--emerald); }
.stat-icon.rose  { background: rgba(225,29,72,.1);   color: var(--rose); }

.stat-info .stat-number {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--text-primary);
    letter-spacing: -.03em;
    line-height: 1;
    margin-bottom: 3px;
}
.stat-info .stat-label {
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--text-muted);
    font-weight: 600;
}

/* ============================================================
   ALERT SUCCESS
   ============================================================ */
.alert-success-premium {
    background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
    border-left: 4px solid #10b981;
    border-radius: 16px;
    padding: 16px 20px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 10px;
    color: #065f46;
    font-weight: 600;
    font-size: .875rem;
    animation: slideInDown .5s ease;
}
@keyframes slideInDown {
    from { opacity: 0; transform: translateY(-10px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ============================================================
   TABLE CARD
   ============================================================ */
.card-premium {
    background: var(--surface);
    border-radius: 28px;
    box-shadow: var(--shadow-card);
    border: 1px solid var(--border);
    overflow: hidden;
}
.table-toolbar {
    padding: 20px 24px 0;
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}
.search-input-wrap {
    position: relative;
    flex: 1;
    min-width: 200px;
}
.search-input-wrap i {
    position: absolute;
    left: 14px; top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: .85rem;
}
.search-input-wrap input {
    width: 100%;
    border: 1.5px solid var(--border);
    border-radius: 12px;
    padding: 10px 14px 10px 36px;
    font-size: .85rem;
    font-weight: 500;
    background: var(--surface-2);
    transition: .25s;
}
.search-input-wrap input:focus {
    border-color: var(--navy);
    background: #fff;
    box-shadow: 0 0 0 4px rgba(0,25,125,.07);
}
.filter-badge {
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 12px;
    padding: 10px 16px;
    font-size: .8rem;
    font-weight: 700;
    color: var(--text-muted);
    cursor: pointer;
    transition: var(--transition);
}
.filter-badge.active-all     { background: var(--navy); color: #fff; border-color: var(--navy); }
.filter-badge.active-visible { background: #d1fae5; color: #065f46; border-color: #6ee7b7; }
.filter-badge.active-hidden  { background: #fee2e2; color: #991b1b; border-color: #fca5a5; }

/* ============================================================
   TABLE STYLES
   ============================================================ */
.table-responsive { overflow-x: auto; }
.table-premium {
    width: 100%;
    border-collapse: collapse;
}
.table-premium thead th {
    background: var(--navy);
    color: rgba(255,255,255,.85);
    padding: 18px 20px;
    font-size: .68rem;
    text-transform: uppercase;
    letter-spacing: 1.8px;
    font-weight: 700;
    white-space: nowrap;
}
.table-premium tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background .2s;
    opacity: 0;
    transform: translateY(12px);
}
.table-premium tbody tr:hover { background: #f7f9ff; }
.table-premium tbody td {
    padding: 16px 20px;
    vertical-align: middle;
}
.td-num {
    font-weight: 800;
    color: var(--text-muted);
    text-align: center;
    width: 52px;
}
.pelanggan-cell {
    display: flex;
    align-items: center;
    gap: 12px;
}
.pelanggan-avatar {
    width: 40px; height: 40px;
    border-radius: 12px;
    background: linear-gradient(135deg, var(--navy), var(--navy-mid));
    color: #fff;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 4px 10px rgba(0,25,125,.18);
}
.pelanggan-name {
    font-weight: 700;
    color: var(--text-primary);
    font-size: .88rem;
    margin-bottom: 2px;
}
.pelanggan-email {
    font-size: .72rem;
    color: var(--text-muted);
}
.tipe-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #dbeafe;
    color: #1e40af;
    border-radius: 8px;
    padding: 5px 11px;
    font-size: .72rem;
    font-weight: 700;
}
.stars {
    color: var(--amber);
    font-size: 1rem;
    letter-spacing: 1px;
}
.rating-num {
    font-size: .68rem;
    color: var(--text-muted);
}
.komentar-text {
    font-size: .8rem;
    color: var(--text-primary);
    max-width: 220px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
    margin-bottom: 4px;
}
.btn-lihat {
    background: none;
    border: none;
    padding: 0;
    font-size: .7rem;
    font-weight: 700;
    color: var(--navy);
    cursor: pointer;
    text-decoration: underline dotted;
}
.btn-lihat:hover { color: #6366f1; }
.tanggal-text {
    font-size: .78rem;
    font-weight: 600;
    color: var(--text-muted);
}
.badge-status {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 11px;
    border-radius: 999px;
    font-size: .65rem;
    font-weight: 700;
}
.badge-status::before {
    content: '';
    width: 6px; height: 6px;
    border-radius: 50%;
}
.status-visible { background: #d1fae5; color: #065f46; }
.status-visible::before { background: #10b981; }
.status-hidden  { background: #fee2e2; color: #991b1b; }
.status-hidden::before  { background: #ef4444; }
.btn-toggle {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    border-radius: 10px;
    font-size: .72rem;
    font-weight: 700;
    cursor: pointer;
    border: 1.5px solid;
    background: transparent;
}
.btn-toggle.show  { color: var(--emerald); border-color: #6ee7b7; }
.btn-toggle.show:hover  { background: var(--emerald); color: #fff; border-color: var(--emerald); transform: translateY(-2px); }
.btn-toggle.hide  { color: var(--rose); border-color: #fca5a5; }
.btn-toggle.hide:hover  { background: var(--rose); color: #fff; border-color: var(--rose); transform: translateY(-2px); }
.empty-state {
    text-align: center;
    padding: 72px 24px;
}
.empty-icon {
    width: 80px; height: 80px;
    background: var(--surface-2);
    border-radius: 24px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: var(--text-muted);
    margin-bottom: 20px;
    border: 2px dashed var(--border);
}
.empty-state h5 { font-weight: 800; margin-bottom: 8px; }

/* ============================================================
   MODAL PREMIUM
   ============================================================ */
.modal-premium .modal-content {
    border-radius: 28px;
    border: none;
    overflow: hidden;
    box-shadow: 0 30px 60px rgba(0,0,0,.2);
}
.modal-premium .modal-header {
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
    padding: 24px 28px;
    border: none;
    position: relative;
}
.modal-premium .modal-header::before {
    content: '';
    position: absolute;
    width: 120px; height: 120px;
    border-radius: 50%;
    background: rgba(255,255,255,.06);
    top: -40px; right: -30px;
}
.modal-premium .modal-title {
    font-weight: 800;
    color: #fff;
}
.modal-ulasan-body {
    padding: 32px 28px 20px;
    text-align: center;
}
.modal-avatar {
    width: 64px; height: 64px;
    border-radius: 18px;
    background: linear-gradient(135deg, var(--navy), var(--navy-mid));
    color: #fff;
    font-size: 1.5rem;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 14px;
}
.modal-pelanggan-name {
    font-size: 1.1rem;
    font-weight: 800;
    color: var(--navy-dark);
    margin-bottom: 6px;
}
.modal-stars {
    font-size: 1.5rem;
    color: var(--amber);
    letter-spacing: 2px;
    display: block;
}
.modal-rating-label {
    font-size: .7rem;
    color: var(--text-muted);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 20px;
    display: block;
}
.modal-komentar-box {
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 16px;
    padding: 20px;
    text-align: left;
    position: relative;
}
.modal-komentar-box::before {
    content: '\201C';
    font-size: 4rem;
    color: var(--gold);
    opacity: .25;
    position: absolute;
    top: 8px; left: 16px;
    font-family: Georgia, serif;
}
.modal-komentar-box p {
    font-size: .9rem;
    color: var(--text-primary);
    font-weight: 500;
    line-height: 1.8;
    font-style: italic;
    margin: 0;
    padding-left: 8px;
}
.modal-footer-custom {
    padding: 16px 24px 24px;
    border: none;
}
.btn-close-modal {
    width: 100%;
    padding: 14px;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 14px;
    font-weight: 700;
    transition: .25s;
}
.btn-close-modal:hover {
    background: var(--navy);
    color: #fff;
    border-color: var(--navy);
    transform: translateY(-1px);
}

@media (max-width: 768px) {
    .ulasan-header h2 { font-size: 1.5rem; }
    .stats-strip { grid-template-columns: repeat(2, 1fr); }
}
</style>
@endpush

@section('content')
<div class="ulasan-page-wrapper">
    <div class="ulasan-header">
        <div class="ulasan-header-left">
            <h2 class="fw-800">Ulasan <span>Pelanggan</span></h2>
            <p>Pantau dan moderasi ulasan tamu Hotel Purnama.</p>
        </div>
    </div>

    {{-- Statistik --}}
    @php
        $totalUlasan = $ulasan->count();
        $rataRating = $totalUlasan > 0 ? $ulasan->avg('rating') : 0;
        $visibleCount = $ulasan->where('is_hidden', false)->count();
        $hiddenCount = $ulasan->where('is_hidden', true)->count();
    @endphp
    <div class="stats-strip">
        <div class="stat-card"><div class="stat-icon navy"><i class="fas fa-comments"></i></div><div class="stat-info"><div class="stat-number">{{ $totalUlasan }}</div><div class="stat-label">Total Ulasan</div></div></div>
        <div class="stat-card"><div class="stat-icon gold"><i class="fas fa-star"></i></div><div class="stat-info"><div class="stat-number">{{ number_format($rataRating, 1) }}</div><div class="stat-label">Rata-rata Rating</div></div></div>
        <div class="stat-card"><div class="stat-icon green"><i class="fas fa-eye"></i></div><div class="stat-info"><div class="stat-number">{{ $visibleCount }}</div><div class="stat-label">Ditampilkan</div></div></div>
        <div class="stat-card"><div class="stat-icon rose"><i class="fas fa-eye-slash"></i></div><div class="stat-info"><div class="stat-number">{{ $hiddenCount }}</div><div class="stat-label">Disembunyikan</div></div></div>
    </div>

    @if(session('success'))
    <div class="alert-success-premium"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif

    <div class="card-premium">
        <div class="table-toolbar">
            <div class="search-input-wrap"><i class="fas fa-search"></i><input type="text" id="ulasanSearch" placeholder="Cari nama pelanggan atau komentar..."></div>
            <div class="filter-badge active-all" data-filter="all">Semua</div>
            <div class="filter-badge" data-filter="visible"><i class="fas fa-eye me-1"></i> Visible</div>
            <div class="filter-badge" data-filter="hidden"><i class="fas fa-eye-slash me-1"></i> Hidden</div>
        </div>
        <div class="table-responsive">
            <table class="table-premium" id="ulasanTable">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Pelanggan</th>
                        <th>Tipe Kamar</th>
                        <th>Rating</th>
                        <th>Komentar</th>
                        <th>Tanggal</th>
                        <th class="text-center">Status</th>
                        @if(session('user.role') === 'admin')
                        <th class="text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($ulasan as $i => $u)
                    @php $user = $users[$u->user_id] ?? null; @endphp
                    <tr data-status="{{ $u->is_hidden ? 'hidden' : 'visible' }}" data-nama="{{ strtolower($user['full_name'] ?? '') }}" data-komentar="{{ strtolower($u->komentar) }}">
                        <td class="td-num">{{ $i+1 }}</td>
                        <td>
                            <div class="pelanggan-cell">
                                <div class="pelanggan-avatar">{{ strtoupper(substr($user['full_name'] ?? 'T',0,1)) }}</div>
                                <div><div class="pelanggan-name">{{ $user['full_name'] ?? 'Tamu' }}</div><div class="pelanggan-email">{{ $user['email'] ?? '-' }}</div></div>
                            </div>
                        </td>
                        <td><span class="tipe-chip"><i class="fas fa-tag"></i> {{ $u->tipeKamar->nama_tipe ?? '-' }}</span></td>
                        <td>
                            <div class="stars">{!! str_repeat('★', $u->rating) . str_repeat('☆', 5 - $u->rating) !!}</div>
                            <div class="rating-num">{{ $u->rating }}/5</div>
                        </td>
                        <td>
                            <span class="komentar-text">{{ $u->komentar }}</span>
                            <button class="btn-lihat" onclick="lihatKomentar('{{ addslashes($u->komentar) }}','{{ addslashes($user['full_name'] ?? 'Pelanggan') }}',{{ $u->rating }})">Lihat selengkapnya →</button>
                        </td>
                        <td><div class="tanggal-text"><i class="far fa-calendar-alt"></i> {{ $u->created_at->format('d M Y') }}</div></td>
                        <td class="text-center"><span class="badge-status {{ $u->is_hidden ? 'status-hidden' : 'status-visible' }}">{{ $u->is_hidden ? 'Hidden' : 'Visible' }}</span></td>
                        @if(session('user.role') === 'admin')
                        <td class="text-center">
                            <form action="{{ route('dashboard.hotel.ulasan.toggle', $u->id) }}" method="POST" style="margin:0;">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-toggle {{ $u->is_hidden ? 'show' : 'hide' }}"><i class="fas {{ $u->is_hidden ? 'fa-eye' : 'fa-eye-slash' }}"></i> {{ $u->is_hidden ? 'Tampilkan' : 'Sembunyikan' }}</button>
                            </form>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr><td colspan="{{ session('user.role') === 'admin' ? 8 : 7 }}"><div class="empty-state"><div class="empty-icon"><i class="fas fa-comments"></i></div><h5>Belum Ada Ulasan</h5><p>Ulasan dari tamu hotel akan muncul di sini.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Detail Komentar --}}
<div class="modal fade modal-premium" id="modalKomentar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-comment-dots"></i> Isi Ulasan Tamu</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-ulasan-body">
                <div class="modal-avatar" id="k-avatar"></div>
                <div class="modal-pelanggan-name" id="k-nama"></div>
                <span class="modal-stars" id="k-bintang"></span>
                <span class="modal-rating-label" id="k-rating-label"></span>
                <div class="modal-komentar-box"><p id="k-komentar"></p></div>
            </div>
            <div class="modal-footer-custom">
                <button type="button" class="btn-close-modal" data-bs-dismiss="modal">Tutup Ulasan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animasi stat card
    document.querySelectorAll('.stat-card').forEach((c, i) => {
        setTimeout(() => { c.style.opacity = '1'; c.style.transform = 'translateY(0)'; }, 100 + i * 80);
    });
    // Animasi baris tabel
    const rows = document.querySelectorAll('.table-premium tbody tr');
    rows.forEach((r, i) => {
        setTimeout(() => {
            r.style.transition = 'opacity .4s ease, transform .4s cubic-bezier(.34,1.56,.64,1)';
            r.style.opacity = '1'; r.style.transform = 'translateY(0)';
        }, 350 + i * 55);
    });
    // Filter & search
    const search = document.getElementById('ulasanSearch');
    let activeFilter = 'all';
    function applyFilter() {
        const q = search.value.toLowerCase().trim();
        rows.forEach(row => {
            const nama = row.dataset.nama || '';
            const komentar = row.dataset.komentar || '';
            const status = row.dataset.status || '';
            const matchSearch = q === '' || nama.includes(q) || komentar.includes(q);
            const matchFilter = activeFilter === 'all' || status === activeFilter;
            row.style.display = (matchSearch && matchFilter) ? '' : 'none';
        });
    }
    search.addEventListener('input', applyFilter);
    document.querySelectorAll('.filter-badge').forEach(btn => {
        btn.addEventListener('click', function() {
            activeFilter = this.dataset.filter;
            document.querySelectorAll('.filter-badge').forEach(b => b.className = 'filter-badge');
            const clsMap = { all:'active-all', visible:'active-visible', hidden:'active-hidden' };
            this.classList.add(clsMap[activeFilter] || 'active-all');
            applyFilter();
        });
    });
    // Counter animasi angka statistik
    document.querySelectorAll('.stat-number').forEach(el => {
        let target = parseFloat(el.textContent);
        if (isNaN(target) || target === 0) return;
        let current = 0;
        let step = target / 20;
        let iv = setInterval(() => {
            current = Math.min(current + step, target);
            el.textContent = el.textContent.includes('.') ? current.toFixed(1) : Math.floor(current);
            if (current >= target) clearInterval(iv);
        }, 40);
    });
});

function lihatKomentar(komentar, nama, rating) {
    document.getElementById('k-nama').textContent = nama;
    document.getElementById('k-avatar').textContent = nama.charAt(0).toUpperCase();
    document.getElementById('k-komentar').textContent = komentar;
    const labels = {1:'Sangat Buruk',2:'Buruk',3:'Cukup',4:'Bagus',5:'Sangat Bagus'};
    document.getElementById('k-rating-label').textContent = labels[rating] || '';
    let bintang = '';
    for(let i=1;i<=5;i++) bintang += i<=rating ? '★' : '☆';
    document.getElementById('k-bintang').textContent = bintang;
    new bootstrap.Modal(document.getElementById('modalKomentar')).show();
}
</script>
@endpush