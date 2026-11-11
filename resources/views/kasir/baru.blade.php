@extends('layouts.app')
@section('judul', 'Pesanan Tunai')
@section('isi')
<style>
    .kasir-table-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: .75rem; }
    .kasir-table-option { position: relative; display: block; min-height: 132px; padding: 1rem; border: 1px solid var(--bento-border); border-radius: 14px; background: var(--bento-surface); cursor: pointer; }
    .kasir-table-option:has(input:checked) { border-color: var(--bento-green-bright); box-shadow: 0 0 0 1px var(--bento-green-bright); }
    .kasir-table-option.available { border-color: rgba(67,197,139,.55); background: rgba(23,134,92,.08); }
    .kasir-table-option.booked { border-color: rgba(239,68,68,.72); background: rgba(185,28,28,.16); cursor: not-allowed; }
    .kasir-table-option.occupied { border-color: rgba(220,95,85,.5); background: rgba(150,53,48,.12); cursor: not-allowed; }
    .kasir-table-option input { position: absolute; top: 1rem; right: 1rem; }
    .kasir-table-option .table-status { display: inline-block; margin-top: .55rem; font-size: .75rem; font-weight: 700; }
    .kasir-table-option .booking-info { margin-top: .45rem; color: var(--bento-muted); font-size: .76rem; line-height: 1.4; }
</style>
<div class="page-heading"><div><span class="eyebrow">Operasional kasir</span><h1>Pesanan baru</h1><p>Pilih menu, metode pembayaran, dan status pembayarannya.</p></div></div>
<div class="card"><div class="card-body">
<form method="POST" action="{{ route('kasir.simpan') }}">@csrf
    <fieldset class="mb-4">
        <legend class="form-label">Pilih meja</legend>
        <p class="small text-muted">Meja booking dan meja terisi dikunci untuk pesanan baru. Lihat nama pemesan dan jadwal booking di bawah.</p>
        @forelse($meja->groupBy('area') as $area => $mejaArea)
            <section class="mb-3">
                <h2 class="h6 text-uppercase text-muted">{{ ucfirst($area) }}</h2>
                <div class="kasir-table-grid">
                    @foreach($mejaArea as $m)
                        @php
                            $statusLabel = $m->status_operasional === 'terisi' ? 'Sedang terisi · terkunci' : ($m->status_operasional === 'dipesan' ? 'Sudah dibooking · terkunci' : 'Tersedia');
                            $booking = $m->booking_aktif;
                            $pemesanBooking = $booking
                                ? ($booking->member?->nama_lengkap ?? $booking->kasir?->nama_lengkap ?? $booking->nama_pemesan)
                                : null;
                        @endphp
                        <label class="kasir-table-option {{ $m->status_operasional === 'terisi' ? 'occupied' : ($m->status_operasional === 'dipesan' ? 'booked' : 'available') }}">
                            @if($m->dapat_dipilih)
                                <input type="radio" name="id_meja" value="{{ $m->id_meja }}" @checked((string) old('id_meja') === (string) $m->id_meja) required>
                            @endif
                            <strong>Meja {{ $m->nomor_meja }}</strong>
                            <div class="small text-muted">{{ $m->kapasitas }} kursi</div>
                            <span class="table-status {{ $m->status_operasional === 'tersedia' ? 'text-success' : 'text-danger' }}">{{ $statusLabel }}</span>
                            @if($booking)
                                <div class="booking-info">
                                    Dibooking oleh <strong>{{ $pemesanBooking }}</strong><br>
                                    {{ $booking->waktu_mulai->format('d/m/Y H:i') }}–{{ $booking->waktu_selesai->format('H:i') }}
                                </div>
                            @elseif($m->status_operasional === 'terisi')
                                <div class="booking-info">Sedang digunakan pelanggan.</div>
                            @endif
                        </label>
                    @endforeach
                </div>
            </section>
        @empty
            <p class="text-muted">Belum ada data meja.</p>
        @endforelse
        @error('id_meja')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
    </fieldset>
    @include('partials.menu_inputs')
    <div class="mt-3 mb-2"><label class="form-label">Nama pemesan</label>
        <input name="nama_pemesan" value="{{ old('nama_pemesan') }}" class="form-control" maxlength="100" required></div>
    <div class="mb-3"><label class="form-label">Catatan (opsional)</label>
        <textarea name="catatan" class="form-control" rows="2">{{ old('catatan') }}</textarea></div>
    @include('partials.payment_method', ['isKasir' => true])
    <fieldset class="mb-3">
        <legend class="form-label mb-2">Status pembayaran</legend>
        <div class="payment-methods">
            <label class="payment-option" for="status_menunggu">
                <input class="form-check-input" type="radio" name="status_bayar" id="status_menunggu" value="menunggu" @checked(old('status_bayar', 'menunggu') === 'menunggu') required>
                <span class="payment-option-copy"><strong>Belum dibayar</strong><small>Pesanan belum masuk dapur sampai kasir mencatat pembayaran diterima.</small></span>
            </label>
            <label class="payment-option" for="status_berhasil">
                <input class="form-check-input" type="radio" name="status_bayar" id="status_berhasil" value="berhasil" @checked(old('status_bayar') === 'berhasil') required>
                <span class="payment-option-copy"><strong>Sudah lunas</strong><small>Pilih setelah uang tunai diterima atau pembayaran QRIS sudah diverifikasi.</small></span>
            </label>
        </div>
    </fieldset>
    <button class="btn btn-dark">Simpan pesanan</button>
</form>
</div></div>
@endsection
