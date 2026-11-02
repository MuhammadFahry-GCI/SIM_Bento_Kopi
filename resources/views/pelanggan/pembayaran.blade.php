@extends('layouts.app')
@section('judul', 'Pembayaran ' . $pesanan->nomor_pesanan)
@section('isi')
<div class="page-heading">
    <div><span class="eyebrow">Pembayaran pesanan</span><h1>{{ $pesanan->nomor_pesanan }}</h1><p>Meja {{ $pesanan->meja->nomor_meja }} · {{ $pesanan->waktu_pesan->format('d/m/Y H:i') }}</p></div>
    <span class="badge {{ $pesanan->pembayaran->status_bayar === 'berhasil' ? 'text-bg-success' : ($pesanan->status_pesanan === 'dibatalkan' ? 'text-bg-danger' : 'text-bg-warning') }}">
        {{ $pesanan->status_pesanan === 'dibatalkan' ? 'Pesanan dibatalkan' : ($pesanan->pembayaran->status_bayar === 'berhasil' ? 'Lunas' : 'Menunggu pembayaran') }}
    </span>
</div>
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card h-100"><div class="card-body">
            <h2 class="h5 mb-3">Rincian pesanan</h2>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Menu</th><th>Jumlah</th><th class="text-end">Subtotal</th></tr></thead>
                    <tbody>
                    @foreach($pesanan->detail as $detail)
                        <tr>
                            <td>{{ $detail->menu->nama_menu }}</td>
                            <td>{{ $detail->jumlah }}</td>
                            <td class="text-end">Rp {{ number_format($detail->jumlah * $detail->harga_satuan, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot><tr><th colspan="2">Total pembayaran</th><th class="text-end">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</th></tr></tfoot>
                </table>
            </div>
            <p class="small text-muted mt-3 mb-0">Metode: {{ $pesanan->pembayaran->metode === 'qris' ? 'QRIS' : 'Tunai di kasir' }}</p>
        </div></div>
    </div>
    <div class="col-lg-5">
        <div class="card h-100"><div class="card-body text-center">
            @if($pesanan->status_pesanan === 'dibatalkan')
                <div class="eyebrow">Pesanan dibatalkan</div>
                <h2 class="h5">Pembayaran tidak dilanjutkan</h2>
                <p class="text-muted">Silakan hubungi kasir jika pembayaran sudah sempat dilakukan.</p>
            @elseif($pesanan->pembayaran->status_bayar === 'berhasil')
                <div class="eyebrow">Pembayaran berhasil</div>
                <h2 class="h5">Pesanan sudah masuk antrean dapur</h2>
                <p class="text-muted">Terima kasih. Status pesanan akan diperbarui otomatis.</p>
            @elseif($pesanan->pembayaran->metode === 'qris')
                <div class="eyebrow">Bayar dengan QRIS</div>
                <h2 class="h5">Scan QR untuk membayar</h2>
                <p class="text-muted">Bayar tepat sebesar <strong class="text-white">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</strong>, lalu tunjukkan bukti pembayaran kepada kasir.</p>
                <img class="payment-qr img-fluid rounded mb-3" src="{{ asset('qris.jpeg') }}" alt="QRIS Bento Kopi untuk pembayaran pesanan">
                <div class="alert alert-warning text-start mb-0" role="status">
                    QRIS ini adalah QR statis. Pembayaran belum terverifikasi otomatis. Kasir harus mengonfirmasi dana diterima sebelum pesanan masuk dapur.
                </div>
            @else
                <div class="eyebrow">Bayar tunai</div>
                <h2 class="h5">Lakukan pembayaran di kasir</h2>
                <p class="text-muted">Bayar sebesar <strong class="text-white">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</strong> dan berikan nomor pesanan ini kepada kasir.</p>
                <div class="payment-order-number">{{ $pesanan->nomor_pesanan }}</div>
                <p class="small text-muted mt-3 mb-0">Pesanan baru diteruskan ke dapur setelah kasir mengonfirmasi pembayaran.</p>
            @endif
            <a class="btn btn-outline-success w-100 mt-3" href="{{ route('pelanggan.status', $pesanan->nomor_pesanan) }}">Lihat status pesanan</a>
        </div></div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    @if($pesanan->pembayaran->status_bayar === 'menunggu' && $pesanan->status_pesanan !== 'dibatalkan')
        setTimeout(() => location.reload(), 15000);
    @endif
</script>
@endpush
