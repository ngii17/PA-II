@extends('dashboard.layouts.app')
@section('title', 'Tambah Pesanan')
@section('content')
<div class="container-fluid">
    <h4 class="fw-bold mb-4">📝 Tambah Pesanan Baru</h4>
    <div class="card border-0 shadow-sm p-4" style="border-radius:15px;">
        <form action="{{ route('dashboard.restoran.pesanan.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Pilih Pelanggan Terdaftar</label>
                    <select name="user_id" class="form-select" required>
                        <option value="">-- Pilih Pelanggan --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c['id'] }}">{{ $c['full_name'] }} ({{ $c['email'] }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-bold">Nomor Meja</label>
                    <input type="text" name="nomor_meja" class="form-control" placeholder="Contoh: 05" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-bold">Metode Bayar</label>
                    <select name="metode_pembayaran" class="form-select">
                        <option value="Tunai">Tunai</option>
                        <option value="Debit">Debit</option>
                    </select>
                </div>
            </div>

            <hr class="my-4">
            <h6 class="fw-bold mb-3">🍴 Daftar Menu</h6>
            <div id="item-container">
                <div class="row g-2 mb-2 item-row">
                    <div class="col-md-8">
                        <select name="menu_ids[]" class="form-select" required>
                            <option value="">-- Pilih Menu --</option>
                            @foreach($menus as $m)
                                <option value="{{ $m->id }}">{{ $m->nama_menu }} (Rp {{ number_format($m->harga) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="jumlah[]" class="form-control" placeholder="Qty" min="1" required>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger w-100 remove-item">Hapus</button>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-success mt-2" id="add-item">+ Tambah Menu</button>

            <div class="mt-5">
                <button type="submit" class="btn btn-primary px-5">💾 Simpan Pesanan</button>
                <a href="{{ route('dashboard.restoran.pesanan.index') }}" class="btn btn-light ms-2">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('add-item').addEventListener('click', function() {
        let container = document.getElementById('item-container');
        let row = document.querySelector('.item-row').cloneNode(true);
        row.querySelector('select').value = "";
        row.querySelector('input').value = "";
        container.appendChild(row);
    });
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item')) {
            if (document.querySelectorAll('.item-row').length > 1) {
                e.target.closest('.item-row').remove();
            }
        }
    });
</script>
@endsection
