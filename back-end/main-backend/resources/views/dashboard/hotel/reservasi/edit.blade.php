@extends('dashboard.layouts.app')
@section('title', 'Edit Reservasi #RES-' . $reservasi->id)

@section('content')
{{-- ================================================================
     EDIT RESERVASI HOTEL — PREMIUM UNIFIED
     ================================================================ --}}

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
/* ============================================================
   ROOT VARIABLES
   ============================================================ */
:root {
    --navy:         #00197D;
    --navy-dark:    #000C3D;
    --navy-mid:     #0025B3;
    --gold:         #D4AF37;
    --indigo:       #6366f1;
    --amber:        #f59e0b;
    --rose:         #e11d48;
    --emerald:      #10b981;
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
   PAGE BACKGROUND
   ============================================================ */
.edit-page-wrapper {
    min-height: 100vh;
    background: linear-gradient(160deg, #f0f4ff 0%, #fafbff 60%, #fffdf0 100%);
    padding: 32px 24px;
    position: relative;
    overflow-x: hidden;
}

.edit-page-wrapper::before,
.edit-page-wrapper::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
}
.edit-page-wrapper::before {
    width: 600px; height: 600px;
    top: -200px; right: -150px;
    background: radial-gradient(circle, rgba(0,25,125,.04) 0%, transparent 70%);
}
.edit-page-wrapper::after {
    width: 400px; height: 400px;
    bottom: -100px; left: -100px;
    background: radial-gradient(circle, rgba(212,175,55,.06) 0%, transparent 70%);
}
.edit-page-wrapper > * { position: relative; z-index: 1; }

/* ============================================================
   HEADER
   ============================================================ */
.edit-header {
    margin-bottom: 32px;
}
.edit-header .back-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: var(--text-muted);
    text-decoration: none;
    font-size: .8rem;
    font-weight: 600;
    transition: var(--transition);
    margin-bottom: 12px;
    padding: 5px 10px;
    border-radius: 10px;
    background: rgba(255,255,255,.7);
}
.edit-header .back-link:hover {
    color: var(--navy);
    transform: translateX(-4px);
    background: #fff;
}
.edit-header h2 {
    font-size: 2rem;
    font-weight: 800;
    color: var(--navy-dark);
    margin: 0;
    letter-spacing: -.03em;
    line-height: 1.1;
}
.edit-header h2 span {
    background: linear-gradient(135deg, var(--gold) 0%, #e8c84a 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.edit-header p {
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
    height: 100%;
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
    padding: 28px;
}

/* ============================================================
   FORM ELEMENTS
   ============================================================ */
.form-label-premium {
    font-size: .7rem;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: var(--text-muted);
    margin-bottom: 8px;
    display: block;
}
.form-control-premium,
.form-select-premium {
    width: 100%;
    padding: 12px 16px;
    border: 1.5px solid var(--border);
    border-radius: 14px;
    font-size: .85rem;
    font-weight: 500;
    color: var(--text-primary);
    background: var(--surface);
    transition: var(--transition);
}
.form-control-premium:focus,
.form-select-premium:focus {
    outline: none;
    border-color: var(--navy);
    box-shadow: 0 0 0 3px rgba(0,25,125,.08);
}
.form-control-premium[readonly],
.form-control-premium:disabled {
    background: var(--surface-2);
    color: var(--text-muted);
    cursor: not-allowed;
}
.form-text-premium {
    font-size: .7rem;
    color: var(--text-muted);
    margin-top: 6px;
    display: flex;
    align-items: center;
    gap: 5px;
}

/* ============================================================
   SUMMARY CARD (RIGHT)
   ============================================================ */
.summary-card {
    background: linear-gradient(145deg, var(--navy-dark) 0%, var(--navy) 100%);
    border-radius: 28px;
    padding: 24px;
    color: white;
    margin-bottom: 20px;
}
.summary-card h6 {
    font-weight: 700;
    font-size: .8rem;
    letter-spacing: 1.5px;
    opacity: 0.8;
    margin-bottom: 20px;
}
.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid rgba(255,255,255,.15);
}
.summary-row.total {
    border-bottom: none;
    margin-top: 8px;
    padding-top: 16px;
}
.total-amount {
    font-size: 1.4rem;
    font-weight: 800;
    color: var(--gold);
}
.note-card {
    background: var(--surface);
    border-radius: 20px;
    padding: 20px;
    border: 1px solid var(--border);
}
.note-card .note-title {
    font-size: .7rem;
    font-weight: 700;
    letter-spacing: 1px;
    color: var(--rose);
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.info-box {
    background: linear-gradient(135deg, var(--surface-2) 0%, #fff9e8 100%);
    border-radius: 20px;
    padding: 20px;
    margin-bottom: 24px;
    border: 1px solid var(--border);
}

/* ============================================================
   BUTTONS
   ============================================================ */
.btn-premium-primary {
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
    color: #fff;
    border: none;
    border-radius: 14px;
    padding: 14px 32px;
    font-weight: 800;
    font-size: .85rem;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    transition: var(--transition);
    cursor: pointer;
}
.btn-premium-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,25,125,.3);
    color: white;
}
.btn-premium-warning {
    background: linear-gradient(135deg, var(--amber) 0%, #d97706 100%);
    color: white;
    border: none;
    border-radius: 14px;
    padding: 14px 32px;
    font-weight: 800;
    font-size: .85rem;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: var(--transition);
    cursor: pointer;
}
.btn-premium-warning:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(245,158,11,.3);
    color: white;
}

@media (max-width: 768px) {
    .edit-page-wrapper { padding: 20px 16px; }
    .edit-header h2 { font-size: 1.5rem; }
    .card-premium-body { padding: 20px; }
    .btn-premium-primary, .btn-premium-warning { width: 100%; justify-content: center; margin-bottom: 8px; }
}
</style>

<div class="edit-page-wrapper">

    {{-- HEADER --}}
    <div class="edit-header">
        <a href="{{ route('dashboard.hotel.reservasi.index') }}" class="back-link">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Reservasi
        </a>
        <h2>Edit <span>Reservasi</span></h2>
        <p><i class="fas fa-edit me-1"></i> Ubah data pemesanan dan kirim notifikasi otomatis ke pelanggan</p>
    </div>

    <div class="row g-4">
        {{-- Kiri: Form Edit --}}
        <div class="col-lg-8">
            <div class="card-premium">
                <div class="card-premium-header">
                    <h6><i class="fas fa-pen-alt"></i> Form Ubah Reservasi</h6>
                </div>
                <div class="card-premium-body">
                    <form action="{{ route('dashboard.hotel.reservasi.update', $reservasi->id) }}" method="POST" id="editReservasiForm">
                        @csrf
                        @method('PUT')

                        {{-- Info Pelanggan --}}
                        <div class="mb-4">
                            <label class="form-label-premium">
                                <i class="fas fa-user me-1"></i> PELANGGAN
                            </label>
                            @php $user = $users[$reservasi->user_id] ?? null; @endphp
                            @if($reservasi->user_id && $user)
                                <input type="text" class="form-control-premium"
                                    value="{{ $user['full_name'] ?? 'Tamu #'.$reservasi->user_id }} ({{ $user['email'] ?? '' }})"
                                    readonly disabled>
                            @else
                                <input type="text" class="form-control-premium"
                                    value="Tamu Walk-in (Tanpa Akun)"
                                    readonly disabled>
                            @endif
                            <div class="form-text-premium">
                                <i class="fas fa-info-circle text-info"></i>
                                @if($reservasi->user_id)
                                    Notifikasi WhatsApp akan dikirim otomatis ke pelanggan jika status diubah.
                                @else
                                    Tamu walk-in — tidak ada akun terdaftar, notifikasi tidak akan dikirim.
                                @endif
                            </div>
                        </div>

                        {{-- Data Tamu --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-8">
                                <label class="form-label-premium"><i class="fas fa-id-card"></i> NAMA TAMU SESUAI KTP</label>
                                <input type="text" name="nama_tamu" id="nama_tamu"
                                    class="form-control-premium @error('nama_tamu') is-invalid @enderror"
                                    placeholder="Contoh: Budi Santoso"
                                    value="{{ old('nama_tamu', $reservasi->details->first()->nama_tamu ?? '') }}" required>
                                @error('nama_tamu') <div class="invalid-feedback-premium"><i class="fas fa-times-circle"></i> {{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-premium"><i class="fas fa-users"></i> JUMLAH TAMU</label>
                                <input type="number" name="jumlah_tamu" id="jumlah_tamu"
                                    class="form-control-premium @error('jumlah_tamu') is-invalid @enderror"
                                    min="1" value="{{ old('jumlah_tamu', $reservasi->details->first()->jumlah_tamu ?? 1) }}" required>
                                @error('jumlah_tamu') <div class="invalid-feedback-premium"><i class="fas fa-times-circle"></i> {{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label-premium"><i class="fas fa-id-badge"></i> NIK / KTP</label>
                                <input type="text" name="nik_identitas" id="nik_identitas"
                                    class="form-control-premium @error('nik_identitas') is-invalid @enderror"
                                    placeholder="16 digit NIK" maxlength="16" inputmode="numeric"
                                    value="{{ old('nik_identitas', session('user.role') === 'admin' 
                                        ? ($reservasi->details->first()->nik_identitas ?? '') 
                                        : (strlen($reservasi->details->first()->nik_identitas ?? '') >= 8 
                                            ? substr($reservasi->details->first()->nik_identitas, 0, 4) . str_repeat('*', strlen($reservasi->details->first()->nik_identitas) - 8) . substr($reservasi->details->first()->nik_identitas, -4)
                                            : str_repeat('*', strlen($reservasi->details->first()->nik_identitas ?? '')))
                                    ) }}"required>
                                @error('nik_identitas') <div class="invalid-feedback-premium"><i class="fas fa-times-circle"></i> {{ $message }}</div> @enderror
                                {{-- TAMBAH INI TEPAT DI SINI ↓ --}}
                                @if(session('user.role') !== 'admin')
                                    <div class="form-text-premium">
                                        <i class="fas fa-lock text-warning"></i> NIK disamarkan. Kosongkan field ini jika tidak ingin mengubah NIK, atau ketik NIK baru untuk menggantinya.
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="row g-3">
                            {{-- Tipe Kamar --}}
                            <div class="col-md-6">
                                <label class="form-label-premium"><i class="fas fa-hotel me-1"></i> TIPE KAMAR</label>
                                <select name="tipe_kamar_id" id="tipe_kamar_id" class="form-select-premium" required>
                                    @foreach($tipeKamar as $t)
                                        <option value="{{ $t->id }}" {{ $reservasi->tipe_kamar_id == $t->id ? 'selected' : '' }}
                                                data-harga="{{ $t->harga }}">
                                            {{ $t->nama_tipe }} (Rp {{ number_format($t->harga, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Unit Kamar --}}
                            <div class="col-md-6">
                                <label class="form-label-premium"><i class="fas fa-door-open me-1"></i> NOMOR KAMAR</label>
                                {{--
                                    FIX: Dropdown kamar awalnya dikosongkan dulu (hanya placeholder),
                                    lalu diisi via AJAX on document ready dengan menyertakan current_kamar_id.
                                    Ini memastikan kamar yang sudah di-assign (status Terisi) tetap muncul
                                    baik saat pertama check-in maupun saat update ke Selesai.
                                --}}
                                <select name="kamar_id" id="kamar_id" class="form-select-premium" required>
                                    <option value="">Memuat data kamar...</option>
                                </select>
                                <div class="form-text-premium">
                                    <i class="fas fa-info-circle"></i> Menampilkan kamar tersedia + kamar yang sedang digunakan reservasi ini
                                </div>
                            </div>

                            {{-- Tanggal --}}
                            <div class="col-md-6">
                                <label class="form-label-premium"><i class="fas fa-calendar-alt me-1"></i> CHECK-IN</label>
                                <input type="date" name="tgl_checkin" id="tgl_checkin" class="form-control-premium" value="{{ $reservasi->tgl_checkin }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-premium"><i class="fas fa-calendar-week me-1"></i> CHECK-OUT</label>
                                <input type="date" name="tgl_checkout" id="tgl_checkout" class="form-control-premium" value="{{ $reservasi->tgl_checkout }}" required>
                            </div>

                            {{-- Status Reservasi --}}
                            <div class="col-12">
                                <label class="form-label-premium"><i class="fas fa-tag me-1"></i> STATUS RESERVASI</label>
                                <select name="status_reservasi_id" id="status_reservasi_id" class="form-select-premium" required>
                                    @foreach($statusList as $s)
                                        <option value="{{ $s->id }}" {{ $reservasi->status_reservasi_id == $s->id ? 'selected' : '' }}>
                                            {{ strtoupper($s->nama_status) }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text-premium" id="statusNotifText">
                                    @if($reservasi->status_reservasi_id == 3)
                                        <i class="fas fa-bell text-warning"></i> Akan mengirim notifikasi NOMOR KAMAR ke WhatsApp pelanggan
                                    @elseif($reservasi->status_reservasi_id == 4)
                                        <i class="fas fa-bell text-warning"></i> Akan mengirim notifikasi UCAPAN TERIMA KASIH
                                    @else
                                        <i class="fas fa-info-circle text-info"></i> Notifikasi akan dikirim sesuai perubahan status
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="mt-4 pt-3 border-top d-flex flex-wrap gap-2">
                            <button type="submit" class="btn-premium-warning">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                            <button type="button" class="btn-premium-primary" onclick="window.location.href='{{ route('dashboard.hotel.reservasi.index') }}'">
                                <i class="fas fa-times"></i> Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Kanan: Ringkasan & Info --}}
        <div class="col-lg-4">
            {{-- Summary Card --}}
            <div class="summary-card">
                <h6><i class="fas fa-chart-line me-1"></i> RINGKASAN SAAT INI</h6>
                <div class="summary-row">
                    <span>ID Reservasi</span>
                    <span class="fw-bold">#RES-{{ $reservasi->id }}</span>
                </div>
                <div class="summary-row">
                    <span>Durasi Menginap</span>
                    <span class="fw-bold" id="summary_durasi">{{ $reservasi->total_malam }} Malam</span>
                </div>
                <div class="summary-row">
                    <span>Kamar Fisik</span>
                    <span class="fw-bold text-success" id="display_room">{{ $reservasi->kamar->nomor_kamar ?? 'Belum Assign' }}</span>
                </div>
                <div class="summary-row total">
                    <span class="fw-bold">TOTAL PEMBAYARAN</span>
                    <span class="total-amount" id="summary_total">Rp {{ number_format($reservasi->total_harga, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- Info Perhitungan Harga --}}
            <div class="card-premium">
                <div class="card-premium-header">
                    <h6><i class="fas fa-calculator me-1"></i> Perhitungan Harga</h6>
                </div>
                <div class="card-premium-body">
                    <div class="info-box">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Harga per Malam</span>
                            <span class="fw-bold" id="hargaPerMalam">Rp {{ number_format($reservasi->tipeKamar->harga ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Jumlah Malam</span>
                            <span class="fw-bold" id="jumlahMalam">{{ $reservasi->total_malam }}</span>
                        </div>
                        <div class="d-flex justify-content-between pt-2 border-top mt-2">
                            <span class="fw-bold">Total</span>
                            <span class="fw-bold text-primary fs-5" id="totalHargaDisplay">Rp {{ number_format($reservasi->total_harga, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Catatan Staff --}}
            <div class="note-card">
                <div class="note-title">
                    <i class="fas fa-exclamation-triangle"></i> Catatan untuk Staff
                </div>
                <ul class="small text-muted mb-0" style="padding-left: 18px;">
                    <li>Pastikan identitas tamu (KTP/SIM) sudah difotokopi</li>
                    <li>Verifikasi pembayaran sebelum mengubah status menjadi <strong class="text-success">CHECK-IN</strong></li>
                    <li>Pilih <strong class="text-primary">CHECK-IN</strong> akan mengirim nomor kamar via WhatsApp</li>
                    <li>Pilih <strong class="text-secondary">SELESAI</strong> akan melepaskan status kamar fisik</li>
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT AJAX & PERHITUNGAN --}}
<script>
// ================================================================
// DATA DARI SERVER — diambil sekali, tidak berubah
// ================================================================
var CURRENT_TIPE_ID   = {{ $reservasi->tipe_kamar_id }};
var CURRENT_KAMAR_ID  = {{ $currentKamarId ?? 'null' }};  // FIX: kamar aktif reservasi ini

$(document).ready(function() {

    // ============================================================
    // FUNGSI: Load kamar via AJAX
    // - tipeId        : id tipe kamar yang diminta
    // - selectedId    : kamar_id yang harus ter-select setelah loaded
    // - currentKamarId: kamar aktif reservasi ini (ikut disertakan
    //                   meski status_kamar_id = 2, agar tidak hilang)
    // ============================================================
    function loadKamar(tipeId, selectedId, currentKamarId) {
        var kamarSelect = $('#kamar_id');
        kamarSelect.empty().append('<option value="">Memuat data kamar...</option>');

        // FIX: URL menyertakan current_kamar_id agar endpoint mengembalikan
        //      kamar tersedia + kamar yang sedang dipakai reservasi ini
        var url = '/dashboard/hotel/get-available-rooms/' + tipeId;
        if (currentKamarId) {
            url += '/' + currentKamarId;
        }

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                kamarSelect.empty();
                kamarSelect.append('<option value="">-- Pilih Nomor Kamar --</option>');
                if (data.length > 0) {
                    $.each(data, function(key, value) {
                        // FIX: Tandai kamar aktif dengan label "(Kamar Saat Ini)"
                        var label = 'Kamar ' + value.nomor_kamar;
                        if (value.id == CURRENT_KAMAR_ID) {
                            label += ' (Kamar Saat Ini)';
                        }
                        var option = $('<option>', {
                            value: value.id,
                            text: label
                        });
                        // FIX: Pilih otomatis kamar yang sudah di-assign
                        if (value.id == selectedId) {
                            option.attr('selected', true);
                        }
                        kamarSelect.append(option);
                    });

                    // Update display_room sesuai kamar yang ter-select
                    var selectedText = kamarSelect.find('option:selected').text();
                    if (selectedText && !selectedText.includes('Pilih')) {
                        $('#display_room').text(selectedText.replace('Kamar ', '').replace(' (Kamar Saat Ini)', ''));
                    }
                } else {
                    kamarSelect.append('<option value="" disabled>⚠️ Kamar tipe ini sedang penuh</option>');
                }
            },
            error: function(xhr) {
                console.error('Error load kamar:', xhr);
                kamarSelect.empty().append('<option value="">Gagal memuat data</option>');
            }
        });
    }

    // ============================================================
    // INISIALISASI HALAMAN
    // FIX: Saat load edit, langsung fetch kamar dengan menyertakan
    //      current_kamar_id agar kamar yang sudah di-assign muncul
    //      dan ter-select otomatis — tidak perlu pilih ulang
    // ============================================================
    if (CURRENT_TIPE_ID) {
        loadKamar(CURRENT_TIPE_ID, CURRENT_KAMAR_ID, CURRENT_KAMAR_ID);
    }

    // ============================================================
    // EVENT: Tipe kamar berubah (staff ganti tipe)
    // Saat ini, current_kamar_id tidak lagi relevan karena tipe berubah
    // ============================================================
    $('#tipe_kamar_id').on('change', function() {
        var tipeId = $(this).val();
        var harga  = $(this).find(':selected').data('harga');
        $('#hargaPerMalam').text('Rp ' + new Intl.NumberFormat('id-ID').format(harga));
        hitungTotal();

        if (tipeId) {
            // Jika tipe berubah dari tipe awal, tidak perlu current_kamar_id
            var isTypeSame = (parseInt(tipeId) === parseInt(CURRENT_TIPE_ID));
            var kamarToSelect = isTypeSame ? CURRENT_KAMAR_ID : null;
            var kamarCurrent  = isTypeSame ? CURRENT_KAMAR_ID : null;
            loadKamar(tipeId, kamarToSelect, kamarCurrent);
        } else {
            $('#kamar_id').empty().append('<option value="">-- Pilih Tipe Terlebih Dahulu --</option>');
        }
    });

    // ============================================================
    // FUNGSI: Hitung total malam dan total harga
    // ============================================================
    function hitungTotal() {
        let checkin  = $('#tgl_checkin').val();
        let checkout = $('#tgl_checkout').val();
        let harga    = parseInt($('#tipe_kamar_id option:selected').data('harga')) || 0;

        if (checkin && checkout) {
            let date1    = new Date(checkin);
            let date2    = new Date(checkout);
            let diffTime = Math.abs(date2 - date1);
            let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            if (diffDays > 0) {
                $('#jumlahMalam').text(diffDays + ' Malam');
                $('#summary_durasi').text(diffDays + ' Malam');
                let total     = harga * diffDays;
                let formatted = new Intl.NumberFormat('id-ID').format(total);
                $('#totalHargaDisplay').text('Rp ' + formatted);
                $('#summary_total').text('Rp ' + formatted);
            } else {
                $('#jumlahMalam').text('0 Malam');
                $('#summary_durasi').text('0 Malam');
                $('#totalHargaDisplay').text('Rp 0');
                $('#summary_total').text('Rp 0');
            }
        }
    }

    // Hitung ulang saat tanggal berubah
    $('#tgl_checkin, #tgl_checkout').on('change', function() {
        hitungTotal();
    });

    // Update notifikasi text berdasarkan status
    $('#status_reservasi_id').on('change', function() {
        var statusId = $(this).val();
        var notifText = '';
        if (statusId == 3) {
            notifText = '<i class="fas fa-bell text-warning"></i> Akan mengirim notifikasi NOMOR KAMAR ke WhatsApp pelanggan';
        } else if (statusId == 4) {
            notifText = '<i class="fas fa-bell text-warning"></i> Akan mengirim notifikasi UCAPAN TERIMA KASIH';
        } else if (statusId == 2) {
            notifText = '<i class="fas fa-bell text-warning"></i> Akan mengirim notifikasi KONFIRMASI PEMBAYARAN';
        } else if (statusId == 5) {
            notifText = '<i class="fas fa-bell text-warning"></i> Akan mengirim notifikasi PEMBATALAN reservasi';
        } else {
            notifText = '<i class="fas fa-info-circle text-info"></i> Notifikasi akan dikirim sesuai perubahan status';
        }
        $('#statusNotifText').html(notifText);
    });

    // Update display room saat kamar dipilih manual
    $('#kamar_id').on('change', function() {
        var selectedText = $(this).find('option:selected').text();
        if (selectedText && !selectedText.includes('Pilih')) {
            $('#display_room').text(
                selectedText.replace('Kamar ', '').replace(' (Kamar Saat Ini)', '')
            );
        } else {
            $('#display_room').text('Belum Assign');
        }
    });

    hitungTotal();
});

// Animasi Cards (tanpa jQuery)
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.card-premium, .summary-card, .note-card');
    cards.forEach((card, idx) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'all 0.5s cubic-bezier(0.34,1.56,0.64,1)';
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 100 + (idx * 80));
    });
    const header = document.querySelector('.edit-header');
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