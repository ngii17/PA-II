@extends('dashboard.layouts.app')
@section('title', 'Tambah Menu ke Event')

@section('content')
<style>
/* ============================================================
   ROOT VARIABLES
   ============================================================ */
:root {
    --navy:         #00197D;
    --navy-dark:    #000C3D;
    --navy-mid:     #0025B3;
    --gold:         #D4AF37;
    --gold-light:   #F5E6BE;
    --indigo:       #6366f1;
    --amber:        #f59e0b;
    --rose:         #e11d48;
    --emerald:      #10b981;
    --surface:      #ffffff;
    --surface-2:    #f8fafc;
    --border:       #e9eef8;
    --text-primary: #0f172a;
    --text-muted:   #5b6e8c;
    --radius-2xl:   32px;
    --shadow-card:  0 4px 24px rgba(0,25,125,.07), 0 1px 3px rgba(0,0,0,.04);
    --shadow-hover: 0 20px 44px rgba(0,25,125,.14);
    --font: 'Plus Jakarta Sans', sans-serif;
    --transition: all .3s cubic-bezier(.34,1.56,.64,1);
}

*, *::before, *::after { box-sizing: border-box; }
body, input, select, textarea, button, label { font-family: var(--font) !important; }

/* ============================================================
   PAGE BACKGROUND
   ============================================================ */
.create-event-menu-wrapper {
    min-height: 100vh;
    background: linear-gradient(160deg, #f0f4ff 0%, #fafbff 60%, #fffdf0 100%);
    padding: 32px 28px;
    position: relative;
    overflow-x: hidden;
}

.create-event-menu-wrapper::before,
.create-event-menu-wrapper::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
}
.create-event-menu-wrapper::before {
    width: 600px; height: 600px;
    top: -200px; right: -150px;
    background: radial-gradient(circle, rgba(0,25,125,.04) 0%, transparent 70%);
}
.create-event-menu-wrapper::after {
    width: 400px; height: 400px;
    bottom: -100px; left: -100px;
    background: radial-gradient(circle, rgba(212,175,55,.06) 0%, transparent 70%);
}

.create-event-menu-wrapper > * {
    position: relative;
}

/* ============================================================
   HEADER
   ============================================================ */
.create-header {
    margin-bottom: 32px;
}

.create-header .back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--text-muted);
    text-decoration: none;
    font-size: .8rem;
    font-weight: 600;
    transition: var(--transition);
    margin-bottom: 12px;
}

.create-header .back-link:hover {
    color: var(--navy);
    transform: translateX(-4px);
}

.create-header h2 {
    font-size: 2rem;
    font-weight: 800;
    color: var(--navy-dark);
    margin: 0;
    letter-spacing: -.03em;
    line-height: 1.1;
}

.create-header h2 span {
    background: linear-gradient(135deg, var(--gold) 0%, #e8c84a 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.create-header p {
    color: var(--text-muted);
    margin: 6px 0 0;
    font-size: .875rem;
    font-weight: 500;
}

/* ============================================================
   CARD PREMIUM
   ============================================================ */
.card-premium {
    background: var(--surface);
    border-radius: 28px;
    border: 1px solid var(--border);
    box-shadow: var(--shadow-card);
    overflow: hidden;
    transition: var(--transition);
}

.card-premium:hover {
    box-shadow: var(--shadow-hover);
}

.card-premium-header {
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
    padding: 18px 28px;
    border: none;
}

.card-premium-header h6 {
    margin: 0;
    font-weight: 800;
    color: white;
    font-size: .85rem;
    letter-spacing: 1px;
}

.card-premium-header h6 i {
    margin-right: 8px;
}

.card-premium-body {
    padding: 32px;
}

/* Form Styles */
.form-label-premium {
    font-size: .7rem;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: var(--text-muted);
    margin-bottom: 8px;
    display: block;
}

.form-label-premium i {
    margin-right: 6px;
}

.form-label-premium.required::after {
    content: '*';
    color: var(--rose);
    margin-left: 4px;
}

.form-control-premium {
    width: 100%;
    padding: 12px 16px;
    border: 1.5px solid var(--border);
    border-radius: 14px;
    font-size: .85rem;
    font-weight: 500;
    color: var(--text-primary);
    background: var(--surface);
    transition: var(--transition);
    font-family: var(--font);
}

.form-control-premium:focus {
    outline: none;
    border-color: var(--navy);
    box-shadow: 0 0 0 3px rgba(0,25,125,.08);
}

.form-control-premium.is-invalid {
    border-color: var(--rose);
    background-color: rgba(225,29,72,.02);
}

select.form-control-premium {
    cursor: pointer;
}

/* Menu Checkbox Grid */
.menu-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 16px;
    margin-top: 12px;
}

.menu-card-checkbox {
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 16px;
    padding: 14px 16px;
    transition: var(--transition);
    cursor: pointer;
}

.menu-card-checkbox:hover {
    border-color: var(--navy);
    background: white;
    transform: translateY(-2px);
    box-shadow: var(--shadow-card);
}

.menu-card-checkbox.selected {
    border-color: var(--emerald);
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
}

.menu-checkbox {
    width: 18px;
    height: 18px;
    margin-right: 12px;
    cursor: pointer;
    accent-color: var(--emerald);
}

.menu-name {
    font-weight: 800;
    font-size: .85rem;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    flex-wrap: wrap;
}

.menu-price {
    font-size: .7rem;
    color: var(--text-muted);
    margin-top: 6px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.menu-price strong {
    color: var(--emerald);
    font-weight: 800;
}

/* Info Box */
.info-box-premium {
    background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 100%);
    border-radius: 16px;
    padding: 14px 18px;
    margin-bottom: 24px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.info-box-premium i {
    color: var(--navy);
    font-size: 1rem;
    margin-top: 2px;
}

.info-box-premium .info-text {
    font-size: .75rem;
    color: var(--navy-dark);
    line-height: 1.5;
    font-weight: 500;
}

.info-box-premium .info-text strong {
    color: var(--navy);
}

/* Alert Error */
.alert-error-premium {
    background: linear-gradient(135deg, #fff5f5 0%, #fee2e2 100%);
    border-left: 4px solid var(--rose);
    border-radius: 16px;
    padding: 16px 20px;
    margin-bottom: 24px;
    animation: slideInDown .5s ease;
}

.alert-error-premium ul {
    margin: 0;
    padding-left: 20px;
    color: #991b1b;
    font-weight: 500;
    font-size: .8rem;
}

@keyframes slideInDown {
    from { opacity: 0; transform: translateY(-10px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* Invalid Feedback */
.invalid-feedback-premium {
    font-size: .7rem;
    color: var(--rose);
    margin-top: 6px;
    display: flex;
    align-items: center;
    gap: 5px;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 12px;
    margin-top: 24px;
    padding-top: 24px;
    border-top: 1px solid var(--border);
}

.btn-premium-primary {
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
    color: white;
    border: none;
    border-radius: 14px;
    padding: 12px 32px;
    font-weight: 800;
    font-size: .85rem;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: var(--transition);
    cursor: pointer;
    text-decoration: none;
}

.btn-premium-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,25,125,.3);
    color: white;
}

.btn-premium-secondary {
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 14px;
    padding: 12px 28px;
    font-weight: 700;
    font-size: .85rem;
    color: var(--text-primary);
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    transition: var(--transition);
    cursor: pointer;
}

.btn-premium-secondary:hover {
    background: var(--border);
    transform: translateY(-2px);
}

/* Summary Card */
.summary-card {
    background: linear-gradient(145deg, var(--navy-dark) 0%, var(--navy) 100%);
    border-radius: 24px;
    padding: 24px;
    color: white;
    margin-bottom: 20px;
}

.summary-card h6 {
    font-weight: 700;
    font-size: .75rem;
    letter-spacing: 1.5px;
    opacity: 0.8;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid rgba(255,255,255,.15);
}

.summary-row:last-child {
    border-bottom: none;
    padding-top: 12px;
    margin-top: 4px;
}

.summary-label {
    font-size: .75rem;
    opacity: 0.8;
}

.summary-value {
    font-weight: 700;
    font-size: .85rem;
}

.summary-value.highlight {
    color: var(--gold);
    font-size: 1rem;
}

/* Tips Card */
.tips-card {
    background: var(--surface-2);
    border-radius: 20px;
    padding: 20px;
    border: 1px solid var(--border);
}

.tips-card h6 {
    font-size: .7rem;
    font-weight: 800;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: var(--text-muted);
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.tips-card ul {
    padding-left: 18px;
    margin: 0;
}

.tips-card li {
    font-size: .75rem;
    color: var(--text-primary);
    margin-bottom: 8px;
}

.tips-card li:last-child {
    margin-bottom: 0;
}

/* Responsive */
@media (max-width: 768px) {
    .create-event-menu-wrapper {
        padding: 20px 16px;
    }
    .create-header h2 {
        font-size: 1.5rem;
    }
    .card-premium-body {
        padding: 24px;
    }
    .menu-grid {
        grid-template-columns: 1fr;
    }
    .action-buttons {
        flex-direction: column;
    }
    .btn-premium-primary,
    .btn-premium-secondary {
        width: 100%;
        justify-content: center;
    }
}
</style>

<!-- ================================================
     MARKUP (menggunakan data dan field dari kode asli)
     ================================================ -->
<div class="create-event-menu-wrapper">

    <!-- Header -->
    <div class="create-header">
        <a href="{{ route('dashboard.restoran.menu-event.index') }}" class="back-link">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
        </a>
        <h2>Tambah Menu ke <span>Event</span></h2>
        <p><i class="fas fa-plus-circle me-1"></i> Daftarkan menu dengan harga khusus untuk event restoran</p>
    </div>

    <div class="row g-4">
        <!-- Kiri: Form Tambah -->
        <div class="col-lg-8">
            <div class="card-premium">
                <div class="card-premium-header">
                    <h6><i class="fas fa-plus-circle"></i> Form Tambah Menu Event</h6>
                </div>
                <div class="card-premium-body">

                    <!-- Error Alerts (dari validasi Laravel) -->
                    @if($errors->any())
                    <div class="alert-error-premium">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li><i class="fas fa-exclamation-circle me-1"></i> {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <!-- Info Box -->
                    <div class="info-box-premium">
                        <i class="fas fa-info-circle"></i>
                        <div class="info-text">
                            Pilih event, tentukan harga khusus, dan pilih menu yang ingin dimasukkan ke dalam promo event.
                            <strong>Harga khusus akan menggantikan harga normal selama event berlangsung.</strong>
                        </div>
                    </div>

                    <form action="{{ route('dashboard.restoran.menu-event.store') }}" method="POST" id="createForm">
                        @csrf

                        <div class="row g-3">
                            <!-- Pilih Event (sama persis kode asli) -->
                            <div class="col-md-6">
                                <label class="form-label-premium required">
                                    <i class="fas fa-calendar-alt"></i> PILIH EVENT AKTIF
                                </label>
                                <select name="event_id" id="event_id" class="form-control-premium @error('event_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Event --</option>
                                    @foreach($events as $event)
                                        <option value="{{ $event->id }}" {{ old('event_id') == $event->id ? 'selected' : '' }}>
                                            {{ $event->nama_event }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('event_id')
                                    <div class="invalid-feedback-premium"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Harga Khusus Event (sama persis kode asli) -->
                            <div class="col-md-6">
                                <label class="form-label-premium required">
                                    <i class="fas fa-tag"></i> HARGA KHUSUS EVENT (RP)
                                </label>
                                <input type="number" 
                                       name="harga_khusus" 
                                       id="harga_khusus"
                                       class="form-control-premium @error('harga_khusus') is-invalid @enderror" 
                                       placeholder="Contoh: 15000" 
                                       value="{{ old('harga_khusus') }}" 
                                       min="0"
                                       required>
                                <div class="form-text-premium" style="margin-top: 6px;">
                                    <i class="fas fa-info-circle"></i> Harga ini akan berlaku untuk semua menu yang dipilih di bawah selama event berlangsung.
                                </div>
                                @error('harga_khusus')
                                    <div class="invalid-feedback-premium"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Pilih Menu (Bisa Lebih Dari Satu) - sama persis kode asli dengan tampilan grid -->
                            <div class="col-12">
                                <label class="form-label-premium required">
                                    <i class="fas fa-utensils"></i> PILIH MENU (BISA LEBIH DARI SATU)
                                </label>
                                <div class="menu-grid" id="menuGrid">
                                    @foreach($menus as $menu)
                                    <div class="menu-card-checkbox" data-menu-id="{{ $menu->id }}">
                                        <div class="d-flex align-items-start">
                                            <input type="checkbox" 
                                                   name="menu_ids[]" 
                                                   value="{{ $menu->id }}" 
                                                   id="menu{{ $menu->id }}"
                                                   class="menu-checkbox"
                                                   {{ in_array($menu->id, old('menu_ids', [])) ? 'checked' : '' }}>
                                            <div class="ms-2 flex-grow-1">
                                                <div class="menu-name">{{ $menu->nama_menu }}</div>
                                                <div class="menu-price">
                                                    Harga Normal: 
                                                    <strong>Rp {{ number_format($menu->harga, 0, ',', '.') }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @error('menu_ids')
                                    <div class="invalid-feedback-premium mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <button type="submit" class="btn-premium-primary" id="submitBtn">
                                <i class="fas fa-save"></i> Daftarkan Menu ke Event
                            </button>
                            <a href="{{ route('dashboard.restoran.menu-event.index') }}" class="btn-premium-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <!-- Kanan: Ringkasan & Tips (hanya untuk UI, tidak mengganggu data) -->
        <div class="col-lg-4">
            <!-- Summary Card -->
            <div class="summary-card">
                <h6>
                    <i class="fas fa-chart-line"></i>
                    Ringkasan Pilihan
                </h6>
                <div class="summary-row">
                    <span class="summary-label">Event Terpilih</span>
                    <span class="summary-value" id="selectedEvent">Belum dipilih</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Harga Khusus</span>
                    <span class="summary-value highlight" id="selectedPrice">Rp 0</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Menu Terpilih</span>
                    <span class="summary-value" id="selectedCount">0 menu</span>
                </div>
            </div>

            <!-- Tips Card -->
            <div class="tips-card">
                <h6>
                    <i class="fas fa-lightbulb"></i>
                    Tips Mengelola Menu Event
                </h6>
                <ul>
                    <li><i class="fas fa-check-circle text-success me-1"></i> Pastikan event yang dipilih sedang aktif</li>
                    <li><i class="fas fa-check-circle text-success me-1"></i> Harga event harus lebih rendah dari harga normal</li>
                    <li><i class="fas fa-check-circle text-success me-1"></i> Pilih menu yang sesuai dengan tema event</li>
                    <li><i class="fas fa-check-circle text-success me-1"></i> Nonaktifkan promo jika event sudah berakhir</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- ================================================
     JAVASCRIPT (preview ringkasan & validasi minimal satu menu)
     ================================================ -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animasi Cards
    const cards = document.querySelectorAll('.card-premium, .summary-card, .tips-card');
    cards.forEach((card, idx) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'all 0.5s cubic-bezier(0.34,1.56,0.64,1)';
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 100 + (idx * 80));
    });
    
    const header = document.querySelector('.create-header');
    if (header) {
        header.style.opacity = '0';
        header.style.transform = 'translateY(-10px)';
        header.style.transition = 'all 0.4s ease';
        setTimeout(() => {
            header.style.opacity = '1';
            header.style.transform = 'translateY(0)';
        }, 50);
    }

    // Elements untuk preview
    const eventSelect = document.getElementById('event_id');
    const hargaInput = document.getElementById('harga_khusus');
    const selectedEventSpan = document.getElementById('selectedEvent');
    const selectedPriceSpan = document.getElementById('selectedPrice');
    const selectedCountSpan = document.getElementById('selectedCount');
    const checkboxes = document.querySelectorAll('.menu-checkbox');
    const menuCards = document.querySelectorAll('.menu-card-checkbox');

    // Update Event Name
    if (eventSelect) {
        eventSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            selectedEventSpan.textContent = selectedOption.value ? selectedOption.text : 'Belum dipilih';
        });
        if (eventSelect.value) {
            const selectedOption = eventSelect.options[eventSelect.selectedIndex];
            selectedEventSpan.textContent = selectedOption.text;
        }
    }

    // Update Harga Preview
    if (hargaInput) {
        hargaInput.addEventListener('input', function() {
            const value = parseInt(this.value) || 0;
            selectedPriceSpan.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
        });
        if (hargaInput.value) {
            selectedPriceSpan.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(parseInt(hargaInput.value));
        }
    }

    // Update Menu Count & Card Highlight
    function updateMenuCount() {
        const checkedCount = document.querySelectorAll('.menu-checkbox:checked').length;
        selectedCountSpan.textContent = checkedCount + (checkedCount === 1 ? ' menu' : ' menu');
        
        menuCards.forEach(card => {
            const checkbox = card.querySelector('.menu-checkbox');
            if (checkbox && checkbox.checked) {
                card.classList.add('selected');
            } else {
                card.classList.remove('selected');
            }
        });
    }

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateMenuCount);
    });
    updateMenuCount();

    // Make entire card clickable (toggle checkbox)
    menuCards.forEach(card => {
        card.addEventListener('click', function(e) {
            if (e.target.type !== 'checkbox') {
                const checkbox = this.querySelector('.menu-checkbox');
                if (checkbox) {
                    checkbox.checked = !checkbox.checked;
                    updateMenuCount();
                }
            }
        });
    });

    // Validasi sebelum submit: minimal satu menu dipilih (sama seperti kode asli)
    const form = document.getElementById('createForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const checkedMenus = document.querySelectorAll('.menu-checkbox:checked');
            if (checkedMenus.length === 0) {
                e.preventDefault();
                alert('Pilih minimal satu menu untuk didaftarkan ke event!');
            }
        });
    }
});

// Additional style untuk form-text premium
const style = document.createElement('style');
style.textContent = `
    .form-text-premium {
        font-size: .7rem;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 5px;
    }
`;
document.head.appendChild(style);
</script>

@endsection