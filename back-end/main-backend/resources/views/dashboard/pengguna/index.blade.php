@extends('dashboard.layouts.app')
@section('title', 'Manajemen Pengguna')
@section('content')

{{-- ================================================================
     MANAJEMEN PENGGUNA — PREMIUM UNIFIED
     ================================================================ --}}

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
/* ============================================================
   ROOT VARIABLES
   ============================================================ */
:root {
    --navy:       #00197D;
    --navy-dark:  #000C3D;
    --gold:       #D4AF37;

    --role-admin-bg:    #FFF1F2; --role-admin-tx:  #E11D48; --role-admin-bd:  #FECDD3;
    --role-cust-bg:     #EFF6FF; --role-cust-tx:   #1D4ED8; --role-cust-bd:   #DBEAFE;
    --role-hotel-bg:    #F0FDF4; --role-hotel-tx:  #15803D; --role-hotel-bd:  #DCFCE7;
    --role-resto-bg:    #FFFBEB; --role-resto-tx:  #B45309; --role-resto-bd:  #FEF3C7;

    --surface:    #ffffff;
    --surface-2:  #f8fafc;
    --border:     #e2e8f0;
    --text-primary: #0f172a;
    --text-mid:     #475569;
    --text-muted:   #94a3b8;

    --shadow-card: 0 4px 24px rgba(0,25,125,.07), 0 1px 3px rgba(0,0,0,.04);
    --shadow-hover: 0 16px 40px rgba(0,25,125,.13);
    --font: 'Plus Jakarta Sans', sans-serif;
    --transition: all .3s cubic-bezier(.34,1.56,.64,1);
}

*, *::before, *::after { box-sizing: border-box; }
body, input, select, button { font-family: var(--font) !important; }
.fw-800 { font-weight: 800 !important; letter-spacing: -.02em; }

/* ============================================================
   PAGE WRAPPER
   ============================================================ */
.pengguna-wrapper {
    min-height: 100vh;
    background: linear-gradient(160deg, #f0f4ff 0%, #fafbff 60%, #fffdf0 100%);
    padding: 32px 24px 64px;
    position: relative;
    overflow-x: hidden;
}
.pengguna-wrapper::before,
.pengguna-wrapper::after {
    content: '';
    position: fixed;
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
}
.pengguna-wrapper::before {
    width: 560px; height: 560px;
    top: -180px; right: -130px;
    background: radial-gradient(circle, rgba(0,25,125,.04) 0%, transparent 70%);
}
.pengguna-wrapper::after {
    width: 380px; height: 380px;
    bottom: -100px; left: -90px;
    background: radial-gradient(circle, rgba(212,175,55,.06) 0%, transparent 70%);
}
.pengguna-wrapper > * { position: relative; z-index: 1; }

/* ============================================================
   HEADER
   ============================================================ */
.pengguna-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 28px;
}
.pengguna-header-left h2 {
    font-size: 2rem;
    font-weight: 800;
    color: var(--navy-dark);
    margin: 0 0 5px;
    letter-spacing: -.03em;
    line-height: 1.1;
}
.pengguna-header-left h2 span {
    background: linear-gradient(135deg, var(--gold) 0%, #e8c84a 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.pengguna-header-left p {
    color: var(--text-muted);
    font-size: .875rem;
    font-weight: 500;
    margin: 0;
}

/* ============================================================
   STATS STRIP
   ============================================================ */
.stats-strip {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 14px;
    margin-bottom: 24px;
}
.stat-card {
    background: var(--surface);
    border-radius: 18px;
    padding: 18px 20px;
    box-shadow: var(--shadow-card);
    display: flex;
    align-items: center;
    gap: 12px;
    border: 1px solid var(--border);
    transition: var(--transition);
    opacity: 0;
    transform: translateY(18px);
}
.stat-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-hover); }

.stat-icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}
.si-navy  { background: rgba(0,25,125,.08);   color: var(--navy); }
.si-rose  { background: rgba(225,29,72,.1);   color: #E11D48; }
.si-blue  { background: rgba(29,78,216,.1);   color: #1D4ED8; }
.si-green { background: rgba(21,128,61,.1);   color: #15803D; }
.si-amber { background: rgba(180,83,9,.1);    color: #B45309; }

.stat-info .stat-number {
    font-size: 1.4rem;
    font-weight: 800;
    color: var(--text-primary);
    letter-spacing: -.03em;
    line-height: 1;
    margin-bottom: 2px;
}
.stat-info .stat-label {
    font-size: .65rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--text-muted);
    font-weight: 600;
}

/* ============================================================
   TOOLBAR
   ============================================================ */
.table-toolbar {
    background: var(--surface);
    border-radius: 20px;
    padding: 16px 20px;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    box-shadow: var(--shadow-card);
    border: 1px solid var(--border);
}

.search-wrap {
    position: relative;
    flex: 1;
    min-width: 200px;
}
.search-wrap i {
    position: absolute;
    left: 13px; top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: .82rem;
    pointer-events: none;
}
.search-wrap input {
    width: 100%;
    border: 1.5px solid var(--border);
    border-radius: 12px;
    padding: 10px 14px 10px 36px;
    font-size: .85rem;
    font-weight: 500;
    color: var(--text-primary);
    background: var(--surface-2);
    outline: none;
    transition: .25s;
    font-family: var(--font) !important;
}
.search-wrap input:focus {
    border-color: var(--navy);
    background: #fff;
    box-shadow: 0 0 0 4px rgba(0,25,125,.07);
}

.role-filter-wrap {
    position: relative;
    min-width: 180px;
}
.role-filter-wrap::after {
    content: '\f078';
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
    position: absolute;
    right: 13px; top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: .65rem;
    pointer-events: none;
}
.role-filter-select {
    width: 100%;
    border: 1.5px solid var(--border);
    border-radius: 12px;
    padding: 10px 34px 10px 14px;
    font-size: .82rem;
    font-weight: 700;
    color: var(--navy);
    background: var(--surface-2);
    outline: none;
    transition: .25s;
    -webkit-appearance: none;
    appearance: none;
    cursor: pointer;
    font-family: var(--font) !important;
}
.role-filter-select:focus {
    border-color: var(--navy);
    background: #fff;
    box-shadow: 0 0 0 4px rgba(0,25,125,.07);
}

.result-count {
    font-size: .72rem;
    font-weight: 700;
    color: var(--text-muted);
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 999px;
    padding: 6px 14px;
    white-space: nowrap;
}
.result-count span { color: var(--navy); font-weight: 800; }

/* ============================================================
   TABLE CARD
   ============================================================ */
.card-premium {
    background: var(--surface);
    border-radius: 24px;
    box-shadow: var(--shadow-card);
    border: 1px solid var(--border);
    overflow: hidden;
}
.table-responsive { overflow-x: auto; }

.p-table {
    width: 100%;
    border-collapse: collapse;
}
.p-table thead tr th {
    background: var(--navy);
    color: rgba(255,255,255,.8);
    padding: 18px 20px;
    font-size: .67rem;
    text-transform: uppercase;
    letter-spacing: 1.8px;
    font-weight: 700;
    white-space: nowrap;
    border: none;
}
.p-table tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background .2s ease;
    opacity: 0;
    transform: translateY(10px);
}
.p-table tbody tr:last-child { border-bottom: none; }
.p-table tbody tr:hover { background: #fdf6e3; }
.p-table tbody td { padding: 16px 20px; vertical-align: middle; }

.avatar-user {
    width: 44px; height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    color: #fff;
    font-size: 1.05rem;
    flex-shrink: 0;
    position: relative;
}
.avatar-user .avatar-ring {
    position: absolute;
    inset: -2px;
    border-radius: 14px;
    border: 2px solid currentColor;
    opacity: .25;
}

.user-fullname {
    font-weight: 800;
    color: var(--text-primary);
    font-size: .92rem;
    margin-bottom: 2px;
}
.user-username {
    font-size: .75rem;
    color: var(--text-muted);
    font-weight: 600;
}

.contact-item {
    font-size: .8rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 3px 6px;
    margin: -3px -6px;
    border-radius: 6px;
    transition: all .2s;
}
.contact-item:hover { color: var(--navy) !important; background: rgba(0,25,125,.05); }
.contact-item i { font-size: .7rem; opacity: .6; }
.email-item { color: var(--navy); }
.phone-item { color: var(--text-muted); }

.role-badge {
    display: inline-block;
    padding: 5px 13px;
    border-radius: 8px;
    font-size: .62rem;
    font-weight: 800;
    letter-spacing: .8px;
    border: 1px solid;
}
.role-admin      { background: var(--role-admin-bg); color: var(--role-admin-tx); border-color: var(--role-admin-bd); }
.role-customer   { background: var(--role-cust-bg);  color: var(--role-cust-tx); border-color: var(--role-cust-bd); }
.role-staff-hotel{ background: var(--role-hotel-bg); color: var(--role-hotel-tx); border-color: var(--role-hotel-bd); }
.role-staff-resto{ background: var(--role-resto-bg); color: var(--role-resto-tx); border-color: var(--role-resto-bd); }

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: .68rem;
    font-weight: 700;
    padding: 5px 12px;
    border-radius: 999px;
}
.status-badge::before {
    content: '';
    width: 6px; height: 6px;
    border-radius: 50%;
    flex-shrink: 0;
}
.status-verified { background: #ecfdf5; color: #065f46; }
.status-verified::before { background: #10b981; }
.status-pending  { background: #f8fafc; color: var(--text-muted); }
.status-pending::before  { background: var(--text-muted); }

.empty-state {
    padding: 72px 24px;
    text-align: center;
}
.empty-icon {
    width: 72px; height: 72px;
    background: var(--surface-2);
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    color: var(--text-muted);
    margin-bottom: 16px;
    border: 2px dashed var(--border);
}

.copy-toast {
    position: fixed;
    bottom: 32px; left: 50%;
    transform: translateX(-50%) translateY(20px);
    background: var(--navy-dark);
    color: #fff;
    padding: 11px 22px;
    border-radius: 999px;
    font-size: .8rem;
    font-weight: 700;
    z-index: 9999;
    pointer-events: none;
    opacity: 0;
    transition: all .35s cubic-bezier(.34,1.56,.64,1);
    box-shadow: 0 8px 24px rgba(0,0,0,.25);
    font-family: var(--font) !important;
}
.copy-toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }

@media (max-width: 768px) {
    .pengguna-header h2 { font-size: 1.5rem; }
    .stats-strip { grid-template-columns: repeat(2, 1fr); }
}
</style>

<div class="copy-toast" id="copyToast"></div>

<div class="pengguna-wrapper">

    {{-- HEADER --}}
    <div class="pengguna-header">
        <div class="pengguna-header-left">
            <h2 class="fw-800">Manajemen <span>Pengguna</span></h2>
            <p>Otoritas akses akun dalam ekosistem <strong style="color:var(--navy);">Purnama</strong>.</p>
        </div>
    </div>

    {{-- STATISTIK --}}
    @php
        $totalUsers   = count($users);
        $totalAdmin   = collect($users)->where('role_id', 1)->count();
        $totalCust    = collect($users)->where('role_id', 2)->count();
        $totalHotel   = collect($users)->where('role_id', 3)->count();
        $totalResto   = collect($users)->where('role_id', 4)->count();
    @endphp
    <div class="stats-strip">
        <div class="stat-card"><div class="stat-icon si-navy"><i class="fas fa-users"></i></div><div class="stat-info"><div class="stat-number">{{ $totalUsers }}</div><div class="stat-label">Total Akun</div></div></div>
        <div class="stat-card"><div class="stat-icon si-rose"><i class="fas fa-user-shield"></i></div><div class="stat-info"><div class="stat-number">{{ $totalAdmin }}</div><div class="stat-label">Administrator</div></div></div>
        <div class="stat-card"><div class="stat-icon si-blue"><i class="fas fa-user"></i></div><div class="stat-info"><div class="stat-number">{{ $totalCust }}</div><div class="stat-label">Pelanggan</div></div></div>
        <div class="stat-card"><div class="stat-icon si-green"><i class="fas fa-hotel"></i></div><div class="stat-info"><div class="stat-number">{{ $totalHotel }}</div><div class="stat-label">Staf Hotel</div></div></div>
        <div class="stat-card"><div class="stat-icon si-amber"><i class="fas fa-utensils"></i></div><div class="stat-info"><div class="stat-number">{{ $totalResto }}</div><div class="stat-label">Staf Restoran</div></div></div>
    </div>

    {{-- TOOLBAR --}}
    <div class="table-toolbar">
        <div class="search-wrap"><i class="fas fa-search"></i><input type="text" id="searchUser" placeholder="Cari nama, username, atau email..."></div>
        <form action="{{ route('dashboard.pengguna') }}" method="GET" id="filterForm">
            <div class="role-filter-wrap">
                <select name="role" id="roleFilter" class="role-filter-select">
                    <option value="">Semua Peran</option>
                    <option value="1" {{ request('role') == '1' ? 'selected' : '' }}>Administrator</option>
                    <option value="2" {{ request('role') == '2' ? 'selected' : '' }}>Pelanggan</option>
                    <option value="3" {{ request('role') == '3' ? 'selected' : '' }}>Staf Hotel</option>
                    <option value="4" {{ request('role') == '4' ? 'selected' : '' }}>Staf Restoran</option>
                </select>
            </div>
        </form>
        <div class="result-count">Menampilkan <span id="visibleCount">{{ $totalUsers }}</span> dari {{ $totalUsers }} pengguna</div>
    </div>

    {{-- TABEL --}}
    <div class="card-premium">
        <div class="table-responsive">
            <table class="p-table" id="userTable">
                <thead>
                    <tr><th class="text-center">#</th><th>Profil Pengguna</th><th>Detail Kontak</th><th class="text-center">Hak Akses</th><th class="text-center">Status</th></tr>
                </thead>
                <tbody>
                    @forelse($users as $i => $u)
                    @php
                        $roleId = $u['role_id'] ?? 0;
                        $roleMap = [
                            1 => ['class' => 'role-admin',       'name' => 'Administrator', 'color' => '#E11D48'],
                            2 => ['class' => 'role-customer',    'name' => 'Pelanggan',     'color' => '#1D4ED8'],
                            3 => ['class' => 'role-staff-hotel', 'name' => 'Staf Hotel',    'color' => '#15803D'],
                            4 => ['class' => 'role-staff-resto', 'name' => 'Staf Restoran', 'color' => '#B45309'],
                        ];
                        $c = $roleMap[$roleId] ?? ['class' => 'role-customer', 'name' => 'Guest', 'color' => '#64748B'];
                    @endphp
                    <tr data-nama="{{ strtolower($u['full_name'] ?? '') }}" data-username="{{ strtolower($u['username'] ?? '') }}" data-email="{{ strtolower($u['email'] ?? '') }}" data-role="{{ $roleId }}">
                        <td class="text-center" style="color:var(--text-muted);font-weight:800;">{{ $i+1 }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:12px;">
                                <div class="avatar-user" style="background-color:{{ $c['color'] }};"><div class="avatar-ring"></div><span style="color:#fff;">{{ strtoupper(substr($u['full_name'] ?? 'U',0,1)) }}</span></div>
                                <div><div class="user-fullname">{{ $u['full_name'] ?? 'Guest User' }}</div><div class="user-username">@ {{ $u['username'] ?? 'username' }}</div></div>
                            </div>
                        </td>
                        <td>
                            <div style="display:flex;flex-direction:column;gap:3px;">
                                <span class="contact-item email-item" onclick="copyText('{{ $u['email'] ?? '' }}', 'Email')"><i class="fas fa-envelope"></i> {{ $u['email'] ?? '-' }}</span>
                                <span class="contact-item phone-item" onclick="copyText('{{ $u['phone'] ?? '' }}', 'Nomor telepon')"><i class="fas fa-phone-alt"></i> {{ $u['phone'] ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="text-center"><span class="role-badge {{ $c['class'] }}">{{ $c['name'] }}</span></td>
                        <td class="text-center">
                            @if($u['is_verified'] ?? false)
                                <span class="status-badge status-verified">Verified</span>
                            @else
                                <span class="status-badge status-pending">Pending</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5"><div class="empty-state"><div class="empty-icon"><i class="fas fa-user-slash"></i></div><h5 style="font-weight:800;">Pengguna Tidak Ditemukan</h5><p style="font-size:.875rem;color:var(--text-muted);">Coba ubah filter atau kata kunci pencarian.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animasi stat card
    document.querySelectorAll('.stat-card').forEach((card, i) => {
        setTimeout(() => { card.style.opacity = '1'; card.style.transform = 'translateY(0)'; }, 80 + i*70);
    });

    // Animasi row tabel
    const rows = document.querySelectorAll('#userTable tbody tr[data-nama]');
    rows.forEach((row, i) => { setTimeout(() => { row.style.opacity = '1'; row.style.transform = 'translateY(0)'; }, 300 + i*50); });

    // Client-side search
    const searchInput = document.getElementById('searchUser');
    const visibleSpan = document.getElementById('visibleCount');
    function filterTable() {
        let q = searchInput.value.toLowerCase().trim();
        let visible = 0;
        rows.forEach(row => {
            let nama = row.dataset.nama || '', user = row.dataset.username || '', email = row.dataset.email || '';
            let match = q === '' || nama.includes(q) || user.includes(q) || email.includes(q);
            row.style.display = match ? '' : 'none';
            if(match) visible++;
        });
        visibleSpan.textContent = visible;
    }
    searchInput.addEventListener('input', filterTable);

    // Role filter (server-side)
    const roleFilter = document.getElementById('roleFilter');
    const filterForm = document.getElementById('filterForm');
    roleFilter.addEventListener('change', () => {
        Swal.fire({ title: '<span style="font-family:Plus Jakarta Sans;font-weight:800;">Menyaring Data...</span>', allowOutsideClick: false, didOpen: () => Swal.showLoading(), showConfirmButton: false, backdrop: 'rgba(0,25,125,.05)' });
        setTimeout(() => filterForm.submit(), 500);
    });

    // Counter animation stat numbers
    document.querySelectorAll('.stat-number').forEach(el => {
        let target = parseInt(el.textContent);
        if(isNaN(target) || target === 0) return;
        let curr = 0, step = Math.ceil(target / 20);
        let iv = setInterval(() => { curr = Math.min(curr+step, target); el.textContent = curr; if(curr>=target) clearInterval(iv); }, 40);
    });

    // Avatar hover
    rows.forEach(row => {
        row.addEventListener('mouseenter', () => { let av = row.querySelector('.avatar-user'); if(av) av.style.transform = 'scale(1.08) rotate(-3deg)'; });
        row.addEventListener('mouseleave', () => { let av = row.querySelector('.avatar-user'); if(av) av.style.transform = ''; });
    });
});

function copyText(text, label) {
    if(!text || text === '-') return;
    navigator.clipboard.writeText(text).then(() => {
        let toast = document.getElementById('copyToast');
        toast.innerHTML = '<i class="fas fa-check me-2"></i>' + label + ' disalin!';
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 2200);
    });
}
</script>

<style>.swal2-popup { border-radius: 20px !important; font-family: 'Plus Jakarta Sans', sans-serif !important; }</style>
@endsection