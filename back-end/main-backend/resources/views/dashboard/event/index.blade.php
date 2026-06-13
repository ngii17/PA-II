@extends('dashboard.layouts.app')

@section('title', session('user.role') === 'admin' ? 'Manajemen Tema & Event' : 'Event Restoran')

@section('content')
<style>
/* ============================================================
   ROOT VARIABLES (KONSISTEN DENGAN HALAMAN SEBELUMNYA)
   ============================================================ */
:root {
    --navy:         #00197D;
    --navy-dark:    #000C3D;
    --navy-mid:     #0025B3;
    --gold:         #D4AF37;
    --gold-light:   #F5E6BE;
    --surface:      #ffffff;
    --surface-2:    #f8fafc;
    --border:       #e9eef8;
    --text-primary: #0f172a;
    --text-muted:   #5b6e8c;
    --shadow-card:  0 4px 24px rgba(0,25,125,.07), 0 1px 3px rgba(0,0,0,.04);
    --shadow-hover: 0 20px 44px rgba(0,25,125,.14);
    --font:         'Plus Jakarta Sans', sans-serif;
    --transition:   all .3s cubic-bezier(.34,1.56,.64,1);
}

*, *::before, *::after { box-sizing: border-box; }
body, input, select, textarea, button, label { font-family: var(--font) !important; }

/* ============================================================
   PAGE WRAPPER
   ============================================================ */
.event-page-wrapper {
    min-height: 100vh;
    background: linear-gradient(160deg, #f0f4ff 0%, #fafbff 60%, #fffdf0 100%);
    padding: 32px 28px;
    position: relative;
    overflow-x: hidden;
}
.event-page-wrapper::before,
.event-page-wrapper::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
}
.event-page-wrapper::before {
    width: 600px; height: 600px;
    top: -200px; right: -150px;
    background: radial-gradient(circle, rgba(0,25,125,.04) 0%, transparent 70%);
}
.event-page-wrapper::after {
    width: 400px; height: 400px;
    bottom: -100px; left: -100px;
    background: radial-gradient(circle, rgba(212,175,55,.06) 0%, transparent 70%);
}
.event-page-wrapper > * { position: relative; z-index: 1; }

/* ============================================================
   HEADER
   ============================================================ */
.event-header {
    margin-bottom: 36px;
}
.event-header-left h2 {
    font-size: 2rem;
    font-weight: 800;
    color: var(--navy-dark);
    margin: 0 0 4px;
    letter-spacing: -.03em;
    line-height: 1.1;
}
.event-header-left h2 span {
    background: linear-gradient(135deg, var(--gold) 0%, #e8c84a 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.event-header-left p {
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
    margin-bottom: 32px;
}
.stat-card {
    background: var(--surface);
    border-radius: 20px;
    padding: 18px 22px;
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
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}
.stat-icon.navy  { background: rgba(0,25,125,.08); color: var(--navy); }
.stat-icon.gold  { background: rgba(212,175,55,.12); color: var(--gold); }
.stat-icon.green { background: rgba(16,185,129,.1); color: #10b981; }
.stat-icon.amber { background: rgba(245,158,11,.1); color: #f59e0b; }
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
   ALERT
   ============================================================ */
.alert-premium {
    background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
    border-left: 4px solid #10b981;
    border-radius: 16px;
    padding: 14px 20px;
    margin-bottom: 28px;
    display: flex;
    align-items: center;
    gap: 12px;
    color: #065f46;
    font-weight: 600;
    font-size: .85rem;
    animation: slideInDown .5s ease;
}
@keyframes slideInDown {
    from { opacity: 0; transform: translateY(-10px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ============================================================
   EVENT GRID (ENHANCED WITH REALISTIC THEME PREVIEW)
   ============================================================ */
.event-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
    gap: 28px;
}
.event-card {
    background: var(--surface);
    border-radius: 28px;
    border: 1px solid var(--border);
    box-shadow: var(--shadow-card);
    overflow: hidden;
    transition: var(--transition);
    opacity: 0;
    transform: translateY(20px);
}
.event-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-hover);
}
.event-preview {
    height: 180px;
    position: relative;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    transition: transform 0.4s ease;
    overflow: hidden;
}
.event-card:hover .event-preview {
    transform: scale(1.03);
}
/* Overlay gradien premium */
.event-preview::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.65) 100%);
    z-index: 1;
}
.event-preview-content {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 18px 16px;
    z-index: 2;
    color: white;
    text-align: left;
    background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, transparent 100%);
}
.event-preview-icon {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 8px;
}
.event-icon-circle {
    width: 44px;
    height: 44px;
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(10px);
    border-radius: 16px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(255,255,255,0.3);
    transition: all 0.3s ease;
}
.event-card:hover .event-icon-circle {
    background: rgba(212,175,55,0.8);
    border-color: var(--gold);
    transform: scale(1.05);
}
.event-icon-circle i { font-size: 1.3rem; color: white; }
.event-preview-name {
    font-size: 1rem;
    font-weight: 800;
    letter-spacing: -0.3px;
    text-shadow: 0 1px 3px rgba(0,0,0,0.4);
}
.event-preview-date {
    font-size: 0.7rem;
    opacity: 0.85;
    font-weight: 500;
}
.event-body {
    padding: 20px 24px 24px;
}
.event-title {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 14px;
    flex-wrap: wrap;
    gap: 10px;
}
.event-title h5 {
    font-size: 1rem;
    font-weight: 800;
    color: var(--text-primary);
    margin: 0;
}
.badge-status {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 12px;
    border-radius: 30px;
    font-weight: 700;
    font-size: .68rem;
}
.badge-status.aktif { background: #dcfce7; color: #15803d; }
.badge-status.nonaktif { background: #f1f5f9; color: #64748b; }
.event-desc {
    background: var(--surface-2);
    border-radius: 16px;
    padding: 12px 16px;
    margin-bottom: 18px;
    min-height: 70px;
}
.event-desc p {
    font-size: .78rem;
    color: var(--text-muted);
    line-height: 1.5;
    margin: 0;
}
.event-desc p i { margin-right: 8px; color: var(--navy); }
.color-swatches {
    display: flex;
    justify-content: space-around;
    padding: 12px 0;
    border-top: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
    margin-bottom: 18px;
}
.swatch-item { text-align: center; }
.swatch-label {
    font-size: .6rem;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--text-muted);
    margin-bottom: 6px;
    display: block;
}
.swatch-color {
    width: 32px;
    height: 32px;
    border-radius: 10px;
    margin: 0 auto;
    border: 2px solid white;
    box-shadow: 0 2px 8px rgba(0,0,0,.1);
    transition: transform 0.2s;
}
.swatch-color:hover {
    transform: scale(1.1);
}
.swatch-code {
    font-size: .65rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-top: 6px;
    display: block;
}
.btn-event-config {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 12px;
    background: var(--navy);
    border: none;
    border-radius: 16px;
    font-weight: 700;
    font-size: .78rem;
    color: white;
    transition: var(--transition);
    text-decoration: none;
}
.btn-event-config:hover {
    background: var(--navy-mid);
    transform: translateY(-2px);
    color: white;
}
.empty-state-premium {
    grid-column: 1 / -1;
    padding: 70px 20px;
    text-align: center;
}
.empty-icon-circle {
    width: 80px;
    height: 80px;
    background: var(--surface-2);
    border-radius: 30px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: var(--text-muted);
    margin-bottom: 20px;
    border: 2px dashed var(--border);
}

@media (max-width: 768px) {
    .event-page-wrapper { padding: 20px 16px; }
    .event-header-left h2 { font-size: 1.5rem; }
    .event-grid { grid-template-columns: 1fr; }
    .event-preview { height: 150px; }
}
</style>

<div class="event-page-wrapper">

    {{-- HEADER (berdasarkan role) --}}
    <div class="event-header">
        <div class="event-header-left">
            @if(session('user.role') === 'admin')
                <h2>Manajemen <span>Tema & Event</span></h2>
                <p><i class="fas fa-palette me-1"></i> Kelola tema visual global untuk seluruh aplikasi (Event Tahunan, Promo, dll)</p>
            @else
                <h2>Event <span>Restoran</span></h2>
                <p><i class="fas fa-calendar-alt me-1"></i> Kelola tema & event khusus untuk suasana restoran</p>
            @endif
        </div>
    </div>

    {{-- STATISTIK --}}
    @php
        $totalEvents = $events->count();
        $activeEvents = $events->where('is_active', true)->count();
        $totalMenuEvents = 0;
        foreach($events as $ev) {
            if (method_exists($ev, 'menuEvents') && $ev->relationLoaded('menuEvents')) {
                $totalMenuEvents += $ev->menuEvents->count();
            } elseif (method_exists($ev, 'menuEvents')) {
                $totalMenuEvents += $ev->menuEvents()->count();
            }
        }
    @endphp
    <div class="stats-strip">
        <div class="stat-card">
            <div class="stat-icon navy"><i class="fas fa-calendar-alt"></i></div>
            <div class="stat-info">
                <div class="stat-number">{{ $totalEvents }}</div>
                <div class="stat-label">Total Event</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
            <div class="stat-info">
                <div class="stat-number">{{ $activeEvents }}</div>
                <div class="stat-label">Event Aktif</div>
            </div>
        </div>
        @if(session('user.role') !== 'admin')
        <div class="stat-card">
            <div class="stat-icon amber"><i class="fas fa-utensils"></i></div>
            <div class="stat-info">
                <div class="stat-number">{{ $totalMenuEvents }}</div>
                <div class="stat-label">Menu Promo</div>
            </div>
        </div>
        @endif
    </div>

    {{-- ALERT SUKSES --}}
    @if(session('success'))
    <div class="alert-premium">
        <i class="fas fa-check-circle fa-lg"></i> {{ session('success') }}
    </div>
    @endif

    {{-- GRID EVENT --}}
    <div class="event-grid">
        @forelse($events as $event)
        @php
            // Event mapping untuk gambar realistis berkualitas tinggi
            $eventTheme = [
                'imlek'    => ['name' => 'Tahun Baru Imlek', 'bg' => 'https://images.pexels.com/photos/2558197/pexels-photo-2558197.jpeg?auto=compress&cs=tinysrgb&w=800', 'color' => '#C62828', 'icon' => 'fa-dragon'],
                'lebaran'  => ['name' => 'Idul Fitri', 'bg' => 'https://images.pexels.com/photos/3265992/pexels-photo-3265992.jpeg?auto=compress&cs=tinysrgb&w=800', 'color' => '#2E7D32', 'icon' => 'fa-moon'],
                'natal'    => ['name' => 'Natal', 'bg' => 'https://images.pexels.com/photos/899330/pexels-photo-899330.jpeg?auto=compress&cs=tinysrgb&w=800', 'color' => '#0A5F38', 'icon' => 'fa-tree'],
                'valentine'=> ['name' => 'Valentine', 'bg' => 'https://images.pexels.com/photos/1024228/pexels-photo-1024228.jpeg?auto=compress&cs=tinysrgb&w=800', 'color' => '#AD1457', 'icon' => 'fa-heart'],
                'hut_ri'   => ['name' => 'HUT Kemerdekaan', 'bg' => 'https://images.pexels.com/photos/747964/pexels-photo-747964.jpeg?auto=compress&cs=tinysrgb&w=800', 'color' => '#C62828', 'icon' => 'fa-flag'],
                'tahun_baru'=>['name' => 'Tahun Baru', 'bg' => 'https://images.pexels.com/photos/703603/pexels-photo-703603.jpeg?auto=compress&cs=tinysrgb&w=800', 'color' => '#1A237E', 'icon' => 'fa-clock'],
                'default'  => ['name' => 'Event Spesial', 'bg' => 'https://images.pexels.com/photos/260689/pexels-photo-260689.jpeg?auto=compress&cs=tinysrgb&w=800', 'color' => '#00197D', 'icon' => 'fa-calendar'],
            ];
            $code = $event->event_code ?? 'default';
            $theme = $eventTheme[$code] ?? $eventTheme['default'];
            $bgImage = $theme['bg'];
            $primaryColor = $event->primary_color ?? $theme['color'];
            $icon = $theme['icon'];
            
            $isActive = $event->is_active ?? false;
            // Tentukan route edit berdasarkan role
            $editRoute = session('user.role') === 'admin'
                ? route('dashboard.event.edit', $event->id)
                : route('dashboard.restoran.event.edit', $event->id);
        @endphp
        <div class="event-card">
            <div class="event-preview" style="background-image: url('{{ $bgImage }}'); background-size: cover; background-position: center;">
                <div class="event-preview-content">
                    <div class="event-preview-icon">
                        <div class="event-icon-circle"><i class="fas {{ $icon }}"></i></div>
                        <div>
                            <div class="event-preview-name">{{ $theme['name'] }}</div>
                            <div class="event-preview-date">Event {{ \Carbon\Carbon::parse($event->created_at)->format('Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="event-body">
                <div class="event-title">
                    <h5>{{ $event->nama_event }}</h5>
                    <span class="badge-status {{ $isActive ? 'aktif' : 'nonaktif' }}">
                        <i class="fas {{ $isActive ? 'fa-check-circle' : 'fa-ban' }}"></i>
                        {{ $isActive ? 'AKTIF' : 'NONAKTIF' }}
                    </span>
                </div>
                <div class="event-desc">
                    <p><i class="fas fa-info-circle"></i> {{ $event->deskripsi ?? 'Deskripsi event akan tampil di sini.' }}</p>
                </div>
                <div class="color-swatches">
                    <div class="swatch-item">
                        <span class="swatch-label">Primer</span>
                        <div class="swatch-color" style="background: {{ $event->primary_color ?? $primaryColor }};"></div>
                    </div>
                    <div class="swatch-item">
                        <span class="swatch-label">Sekunder</span>
                        <div class="swatch-color" style="background: {{ $event->secondary_color ?? '#D4AF37' }};"></div>
                    </div>
                    <div class="swatch-item">
                        <span class="swatch-label">Kode</span>
                        <span class="swatch-code">{{ strtoupper($code) }}</span>
                    </div>
                </div>
                <a href="{{ $editRoute }}" class="btn-event-config">
                    <i class="fas fa-sliders-h"></i> Konfigurasi Tema
                </a>
            </div>
        </div>
        @empty
        <div class="empty-state-premium">
            <div class="empty-icon-circle"><i class="fas fa-calendar-alt"></i></div>
            <h5>Belum Ada Event</h5>
            <p class="text-muted">Tambahkan event/tema untuk tampilan yang lebih menarik.</p>
        </div>
        @endforelse
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animasi stat cards
    document.querySelectorAll('.stat-card').forEach((card, i) => {
        setTimeout(() => {
            card.style.transition = 'all 0.5s cubic-bezier(0.34,1.56,0.64,1)';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 100 + i * 80);
    });
    // Animasi event cards
    document.querySelectorAll('.event-card').forEach((card, i) => {
        setTimeout(() => {
            card.style.transition = 'opacity 0.45s ease, transform 0.45s cubic-bezier(0.34,1.56,0.64,1)';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 200 + i * 60);
    });
    // Header animation
    const header = document.querySelector('.event-header');
    if (header) {
        header.style.opacity = '0';
        header.style.transform = 'translateY(-10px)';
        header.style.transition = 'all 0.4s ease';
        setTimeout(() => {
            header.style.opacity = '1';
            header.style.transform = 'translateY(0)';
        }, 50);
    }
});
</script>
@endsection