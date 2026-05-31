@extends('dashboard.layouts.app')
@section('title', 'Edit Reservasi')
@section('content')

<div class="container-fluid px-4">
    <div class="mb-4">
        <a href="{{ route('dashboard.hotel.reservasi.index') }}" class="text-decoration-none small text-muted">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
        <h4 class="fw-bold mt-2 text-dark">Ubah Data Reservasi #RES-{{ $reservasi->id }}</h4>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm" style="border-radius:16px;">
                <div class="card-body p-4">
                    <form action="{{ route('dashboard.hotel.reservasi.update', $reservasi->id) }}" method="POST" id="editReservasiForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Info Pelanggan -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label small fw-bold text-muted">PELANGGAN (USER ID)</label>
                                <input type="text" class="form-control bg-light" value="{{ $reservasi->user_id }}" readonly disabled>
                                <small class="text-info"><i class="fas fa-info-circle me-1"></i> Notifikasi akan dikirim otomatis ke HP pelanggan ini jika status diubah.</small>
                            </div>

                            <!-- Pilih Tipe Kamar -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-muted">TIPE KAMAR</label>
                                <select name="tipe_kamar_id" id="tipe_kamar_id" class="form-select" required>
                                    @foreach($tipeKamar as $t)
                                        <option value="{{ $t->id }}" {{ $reservasi->tipe_kamar_id == $t->id ? 'selected' : '' }}>
                                            {{ $t->nama_tipe }} (Rp {{ number_format($t->harga) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Pilih Unit Kamar (SINKRON DENGAN AJAX) -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-muted">ASSIGN NOMOR KAMAR</label>
                                <select name="kamar_id" id="kamar_id" class="form-select" required>
                                    @if($reservasi->kamar)
                                        <option value="{{ $reservasi->kamar_id }}" selected>
                                            Kamar {{ $reservasi->kamar->nomor_kamar }} (Saat Ini)
                                        </option>
                                    @else
                                        <option value="">-- Pilih Kamar Tersedia --</option>
                                    @endif
                                    {{-- Data lainnya akan diisi secara otomatis oleh JavaScript --}}
                                </select>
                                <small class="text-muted" style="font-size: 10px;">*Hanya menampilkan kamar dengan status 'Tersedia'.</small>
                            </div>

                            <!-- Tanggal -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-muted">TANGGAL CHECK-IN</label>
                                <input type="date" name="tgl_checkin" class="form-control" value="{{ $reservasi->tgl_checkin }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-muted">TANGGAL CHECK-OUT</label>
                                <input type="date" name="tgl_checkout" class="form-control" value="{{ $reservasi->tgl_checkout }}" required>
                            </div>

                            <!-- Status (SINKRON DENGAN LOGIKA HP) -->
                            <div class="col-md-12 mb-4">
                                <label class="form-label small fw-bold text-primary">UPDATE STATUS RESERVASI</label>
                                <select name="status_reservasi_id" class="form-select border-primary" required>
                                    @foreach($statusList as $s)
                                        <option value="{{ $s->id }}" {{ $reservasi->status_reservasi_id == $s->id ? 'selected' : '' }}>
                                            {{ strtoupper($s->nama_status) }} 
                                            @if($s->id == 3) (Memicu Notif Masuk Kamar) @endif
                                            @if($s->id == 4) (Memicu Notif Terima Kasih) @endif
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text text-muted" style="font-size: 11px;">
                                    *Pilih <b>CHECK-IN</b> untuk mengirim nomor kamar ke HP user. <br>
                                    *Pilih <b>SELESAI</b> untuk melepaskan status kamar fisik menjadi tersedia kembali.
                                </div>
                            </div>
                        </div>

                        <div class="mt-2 border-top pt-4">
                            <button type="submit" class="btn btn-warning px-5 fw-bold text-white shadow-sm" style="border-radius:10px;">
                                <i class="fas fa-sync-alt me-1"></i> Simpan Perubahan & Kirim Notifikasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sisi Kanan: Ringkasan --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="border-radius:16px; background:#1a1a2e; color: white;">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Ringkasan Saat Ini:</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small opacity-75">Durasi:</span>
                        <span>{{ $reservasi->total_malam }} Malam</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="small opacity-75">Kamar Fisik:</span>
                        <span class="text-success fw-bold" id="display_room">{{ $reservasi->kamar->nomor_kamar ?? 'Belum Ada' }}</span>
                    </div>
                    <hr class="opacity-25">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold">TOTAL BAYAR:</span>
                        <h4 class="fw-bold mb-0 text-warning">Rp {{ number_format($reservasi->total_harga, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-3" style="border-radius:16px;">
                <div class="card-body p-3 small">
                    <b class="text-danger">Catatan untuk Staff:</b><br>
                    Pastikan identitas tamu (KTP) sudah difotokopi dan pembayaran sudah diterima sebelum mengubah status menjadi <b>CHECK-IN</b>.
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- SCRIPT AJAX UNTUK DEPENDEN DROPDOWN KAMAR --}}
{{-- ============================================================ --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Jalankan fungsi saat Tipe Kamar diganti
    $('#tipe_kamar_id').on('change', function() {
        var tipeId = $(this).val();
        var kamarSelect = $('#kamar_id');

        if(tipeId) {
            // Tampilkan status loading
            kamarSelect.empty().append('<option value="">Sedang memuat kamar...</option>');

            $.ajax({
                // Memanggil rute yang kita buat di web.php
                url: '/dashboard/hotel/get-available-rooms/' + tipeId,
                type: "GET",
                dataType: "json",
                success:function(data) {
                    kamarSelect.empty();
                    kamarSelect.append('<option value="">-- Pilih Nomor Kamar --</option>');
                    
                    if(data.length > 0) {
                        $.each(data, function(key, value) {
                            kamarSelect.append('<option value="'+ value.id +'">Kamar '+ value.nomor_kamar +'</option>');
                        });
                    } else {
                        kamarSelect.append('<option value="">Kamar Tipe Ini Sedang Penuh</option>');
                    }
                },
                error: function() {
                    alert('Gagal mengambil data kamar. Pastikan server Port 8001 jalan.');
                }
            });
        } else {
            kamarSelect.empty().append('<option value="">-- Pilih Tipe Terlebih Dahulu --</option>');
        }
    });
});
</script>

@endsection