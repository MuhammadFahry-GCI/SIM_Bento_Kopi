@extends('layouts.app')
@section('judul', 'Status Pesanan')
@section('isi')
@php
    $label = ['menunggu_bayar' => 'Menunggu pembayaran di kasir', 'antre' => 'Dalam antrean', 'diproses' => 'Sedang disiapkan',
              'siap' => 'Siap, akan segera diantar', 'disajikan' => 'Sudah disajikan', 'dibatalkan' => 'Dibatalkan'];
@endphp
<div class="card shadow-sm"><div class="card-body">
    <h4>Pesanan {{ $pesanan->nomor_pesanan }}</h4>
    <p class="mb-1">Meja <strong>{{ $pesanan->meja->nomor_meja }}</strong></p>
    <p><span class="badge bg-dark fs-6">{{ $label[$pesanan->status_pesanan] }}</span></p>
    @if($pesanan->status_pesanan === 'dibatalkan')<p class="text-danger">Alasan: {{ $pesanan->alasan_batal }}</p>@endif
    <ul>
        @foreach($pesanan->detail as $d)
            <li>{{ $d->jumlah }} x {{ $d->menu->nama_menu }} (Rp {{ number_format($d->harga_satuan, 0, ',', '.') }})</li>
        @endforeach
    </ul>
    <p class="fw-bold">Total: Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</p>
    <p class="small text-muted mb-0">Pembayaran: {{ strtoupper($pesanan->pembayaran->metode) }} - {{ $pesanan->pembayaran->status_bayar }}. Halaman ini diperbarui otomatis.</p>
</div></div>
@endsection
@push('scripts')
<script>@if(!in_array($pesanan->status_pesanan, ['disajikan', 'dibatalkan']))setTimeout(() => location.reload(), 15000);@endif</script>
@endpush
