@extends('layouts.app')
@section('judul', 'Status Pesanan')
@section('isi')
@php
    $label = ['menunggu_bayar' => 'Menunggu pembayaran di kasir', 'antre' => 'Sedang disiapkan', 'diproses' => 'Sedang disiapkan',
              'siap' => 'Siap, akan segera diantar', 'disajikan' => 'Sudah disajikan', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'];
    if ($pesanan->sesiMejaDitutup() && $pesanan->status_pesanan !== 'dibatalkan') {
        $label[$pesanan->status_pesanan] = 'Selesai';
    }
@endphp
<div class="page-heading"><div><span class="eyebrow">Pembaruan pesanan</span><h1>Status pesanan</h1><p>Nomor {{ $pesanan->nomor_pesanan }} · Pemesan {{ $pesanan->nama_pemesan_tampilan ?: 'Tidak dicantumkan' }} · Meja {{ $pesanan->meja->nomor_meja }}</p></div></div>
<div class="card shadow-sm"><div class="card-body">
    <h4>Detail pesanan</h4>
    <p class="mb-1">Meja <strong>{{ $pesanan->meja->nomor_meja }}</strong></p>
    <p><span class="badge bg-dark fs-6">{{ $label[$pesanan->status_pesanan] }}</span></p>
    @if($pesanan->status_pesanan === 'dibatalkan')<p class="text-danger">Alasan: {{ $pesanan->alasan_batal }}</p>@endif
    <ul>
        @foreach($pesanan->detail as $d)
            <li>{{ $d->jumlah }} x {{ $d->menu->nama_menu }} (Rp {{ number_format($d->harga_satuan, 0, ',', '.') }})</li>
        @endforeach
    </ul>
    <p class="fw-bold">Total: Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</p>
    <div class="mt-3 rounded border p-3">
        <div class="fw-semibold">Pembayaran {{ $pesanan->pembayaran->metode === 'qris' ? 'cashless · QRIS' : 'tunai' }}</div>
        <div class="small text-muted">
            {{ $pesanan->pembayaran->status_bayar === 'berhasil' ? 'Pembayaran berhasil.' : ($pesanan->pembayaran->status_bayar === 'gagal' ? 'Pembayaran dibatalkan.' : 'Pembayaran belum dikonfirmasi kasir.') }}
            Status diperbarui otomatis.
        </div>
        @if($pesanan->pembayaran->status_bayar === 'menunggu')
            <a class="btn btn-sm btn-outline-success mt-3" href="{{ route('pelanggan.pembayaran', $pesanan->nomor_pesanan) }}">Buka halaman pembayaran</a>
        @endif
    </div>
    <div class="d-flex flex-wrap gap-2 mt-3">
        <a class="btn btn-outline-success" href="{{ route('keluhan.form', $pesanan->nomor_pesanan) }}">Sampaikan keluhan</a>
        <a class="btn btn-outline-secondary" href="{{ route('pengetahuan.index') }}">Buka panduan pelanggan</a>
    </div>
</div></div>
@endsection
@push('scripts')
<script>@if(!in_array($pesanan->status_pesanan, ['disajikan', 'selesai', 'dibatalkan']) && !$pesanan->sesiMejaDitutup())setTimeout(() => location.reload(), 15000);@endif</script>
@endpush
