@extends('dashboard.layouts.app')
@section('title', 'Profil Saya')
@section('breadcrumb', 'Profil')

@section('content')
<style>
    /* ================================================
       PROFILE PAGE PREMIUM - ENHANCED
    ================================================ */
    .profile-header {
        margin-bottom: 28px;
        animation: fadeInUp 0.5s ease;
    }
    .profile-avatar-large {
        width: 130px;
        height: 130px;
        border-radius: 35px;
        background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3.2rem;
        font-weight: 800;
        color: white;
        box-shadow: 0 15px 30px rgba(0,25,125,.2);
        border: 3px solid var(--gold);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .profile-avatar-large:hover {
        transform: scale(1.02);
        box-shadow: 0 20px 35px rgba(0,25,125,.3);
    }
    .profile-card {
        background: white;
        border-radius: 28px;
        border: 1px solid var(--border);
        overflow: hidden;
        box-shadow: var(--shadow-card);
        transition: all 0.3s ease;
        height: 100%;
    }
    .profile-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-hover);
    }
    .profile-card-header {
        background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
        padding: 20px 28px;
        color: white;
        position: relative;
        overflow: hidden;
    }
    .profile-card-header::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 150%;
        height: 200%;
        background: radial-gradient(circle, rgba(212,175,55,0.1) 0%, transparent 70%);
        pointer-events: none;
    }
    .profile-card-header h5 {
        margin: 0;
        font-weight: 800;
        font-size: 1rem;
        letter-spacing: 1px;
        display: flex;
        align-items: center;
        gap: 10px;
        position: relative;
        z-index: 1;
    }
    .profile-info-row {
        display: flex;
        justify-content: space-between;
        padding: 16px 0;
        border-bottom: 1px solid var(--border);
        transition: background 0.2s;
    }
    .profile-info-row:hover {
        background: var(--surface-2);
        padding-left: 5px;
        padding-right: 5px;
    }
    .profile-info-label {
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--text-muted);
    }
    .profile-info-value {
        font-weight: 600;
        font-size: 0.9rem;
        color: var(--text-primary);
        word-break: break-word;
        text-align: right;
    }
    .profile-stats {
        background: linear-gradient(145deg, var(--surface-2) 0%, #ffffff 100%);
        border-radius: 24px;
        padding: 20px;
        margin-top: 25px;
        border: 1px solid var(--border);
    }
    .stat-item {
        text-align: center;
        padding: 12px 5px;
        transition: transform 0.2s;
    }
    .stat-item:hover {
        transform: translateY(-3px);
    }
    .stat-number {
        font-size: 1.8rem;
        font-weight: 800;
        color: var(--navy);
        line-height: 1.2;
    }
    .stat-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 1px;
        font-weight: 600;
        margin-top: 5px;
    }
    .badge-role {
        background: rgba(212,175,55,0.15);
        color: var(--gold);
        padding: 6px 16px;
        border-radius: 40px;
        font-weight: 700;
        font-size: 0.7rem;
        letter-spacing: 1px;
        border: 1px solid rgba(212,175,55,0.3);
    }
    .info-alert {
        background: linear-gradient(135deg, #eef2ff 0%, #e8edff 100%);
        border: none;
        border-radius: 20px;
        color: var(--navy);
        padding: 15px 20px;
        font-weight: 500;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @media (max-width: 768px) {
        .profile-avatar-large { width: 90px; height: 90px; font-size: 2.2rem; }
        .profile-info-row { flex-direction: column; gap: 6px; }
        .profile-info-value { text-align: left; }
        .stat-number { font-size: 1.4rem; }
    }
</style>

<div class="container-fluid px-4">
    <div class="profile-header">
        <h4 class="fw-bold mb-1">👤 Profil Saya</h4>
        <p class="text-muted small">Informasi lengkap akun dan aktivitas Anda</p>
    </div>

    <div class="row g-4">
        <!-- Kolom Kiri: Avatar & Profil Singkat -->
        <div class="col-lg-4">
            <div class="profile-card text-center p-4" style="animation: fadeInUp 0.5s ease 0.1s both;">
                <div class="profile-avatar-large mx-auto mb-4">
                    {{ strtoupper(substr($user->name ?? session('user.name'), 0, 1)) }}
                </div>
                <h4 class="fw-bold mb-1">{{ $user->name ?? session('user.name') }}</h4>
                <div class="badge-role d-inline-block mt-2">
                    <i class="fas fa-shield-alt me-1"></i> {{ str_replace('_', ' ', $user->role ?? session('user.role')) }}
                </div>
                <div class="profile-stats mt-4">
                    <div class="row">
                        <div class="col-6 stat-item">
                            <div class="stat-number">0</div>
                            <div class="stat-label">Total Pesanan</div>
                        </div>
                        <div class="col-6 stat-item">
                            <div class="stat-number">0</div>
                            <div class="stat-label">Bergabung Sejak</div>
                        </div>
                    </div>
                </div>
                <div class="mt-3 small text-muted">
                    <i class="fas fa-calendar-alt me-1"></i> Member sejak: 
                    {{ \Carbon\Carbon::parse($user->created_at ?? session('user.created_at'))->translatedFormat('d F Y') ?? '-' }}
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Detail Informasi -->
        <div class="col-lg-8">
            <div class="profile-card" style="animation: fadeInUp 0.5s ease 0.2s both;">
                <div class="profile-card-header">
                    <h5><i class="fas fa-user-circle"></i> Informasi Akun</h5>
                </div>
                <div class="card-body p-4">
                    <div class="profile-info-row">
                        <span class="profile-info-label"><i class="fas fa-user me-2"></i> Nama Lengkap</span>
                        <span class="profile-info-value">{{ $user->name ?? session('user.name') }}</span>
                    </div>
                    <div class="profile-info-row">
                        <span class="profile-info-label"><i class="fas fa-envelope me-2"></i> Alamat Email</span>
                        <span class="profile-info-value">{{ $user->email ?? session('user.email') }}</span>
                    </div>
                    <div class="profile-info-row">
                        <span class="profile-info-label"><i class="fas fa-briefcase me-2"></i> Role / Jabatan</span>
                        <span class="profile-info-value">{{ str_replace('_', ' ', $user->role ?? session('user.role')) }}</span>
                    </div>
                    <div class="profile-info-row">
                        <span class="profile-info-label"><i class="fas fa-id-card me-2"></i> ID Pengguna</span>
                        <span class="profile-info-value">#{{ $user->id ?? session('user.id') }}</span>
                    </div>
                    @if(isset($user->phone) || session('user.phone'))
                    <div class="profile-info-row">
                        <span class="profile-info-label"><i class="fas fa-phone-alt me-2"></i> Nomor Telepon</span>
                        <span class="profile-info-value">{{ $user->phone ?? session('user.phone') }}</span>
                    </div>
                    @endif
                    @if(isset($user->address) || session('user.address'))
                    <div class="profile-info-row">
                        <span class="profile-info-label"><i class="fas fa-map-marker-alt me-2"></i> Alamat</span>
                        <span class="profile-info-value">{{ $user->address ?? session('user.address') }}</span>
                    </div>
                    @endif
                    <div class="profile-info-row">
                        <span class="profile-info-label"><i class="fas fa-clock me-2"></i> Terakhir Diupdate</span>
                        <span class="profile-info-value">{{ \Carbon\Carbon::parse($user->updated_at ?? session('user.updated_at'))->diffForHumans() ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Informasi Tambahan / Catatan -->
            <div class="info-alert d-flex align-items-center mt-4" style="animation: fadeInUp 0.5s ease 0.3s both;">
                <i class="fas fa-shield-alt fa-2x me-3" style="opacity: 0.7;"></i>
                <div>
                    <strong class="d-block">Akun Terproteksi</strong>
                    <small>Informasi profil ini diambil dari data akun Anda. Jika ada perubahan, hubungi administrator sistem.</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection