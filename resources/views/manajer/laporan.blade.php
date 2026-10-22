@extends('layouts.app')
@section('judul', 'Laporan')
@section('isi')
<div class="row g-3 mb-3">
    <div class="col-md-4"><div class="card"><div class="card-body">
        <div class="text-muted small">Waktu penyajian rata-rata</div>
        <div class="fs-3 fw-bold">{{ $rataDetik ? number_format($rataDetik / 60, 1, ',', '.') . ' menit' : 'Belum ada data' }}</div>
    </div></div></div>
    @foreach($perMetode as $r)
        <div class="col-md-4"><div class="card"><div class="card-body">
            <div class="text-muted small">Penjualan {{ strtoupper($r->metode) }} ({{ $r->jumlah }} transaksi)</div>
            <div class="fs-3 fw-bold">Rp {{ number_format($r->total, 0, ',', '.') }}</div>
        </div></div></div>
    @endforeach
</div>

<div class="row g-3">
    <div class="col-md-6"><h6>Penjualan 7 hari terakhir</h6>
        <table class="table table-sm bg-white"><thead><tr><th>Tanggal</th><th>Transaksi</th><th>Total</th></tr></thead><tbody>
        @forelse($perHari as $r)<tr><td>{{ $r->tanggal }}</td><td>{{ $r->jumlah }}</td><td>Rp {{ number_format($r->total, 0, ',', '.') }}</td></tr>
        @empty<tr><td colspan="3" class="text-muted">Belum ada data.</td></tr>@endforelse</tbody></table></div>
    <div class="col-md-6"><h6>Menu terlaris</h6>
        <table class="table table-sm bg-white"><thead><tr><th>Menu</th><th>Terjual</th><th>Omzet</th></tr></thead><tbody>
        @forelse($perMenu as $r)<tr><td>{{ $r->nama_menu }}</td><td>{{ $r->qty }}</td><td>Rp {{ number_format($r->omzet, 0, ',', '.') }}</td></tr>
        @empty<tr><td colspan="3" class="text-muted">Belum ada data.</td></tr>@endforelse</tbody></table></div>
</div>

<h6 class="mt-3">Pesanan dibatalkan dan alasannya</h6>
<table class="table table-sm bg-white"><thead><tr><th>Nomor</th><th>Waktu</th><th>Alasan</th></tr></thead><tbody>
@forelse($dibatalkan as $p)<tr><td>{{ $p->nomor_pesanan }}</td><td>{{ $p->waktu_pesan->format('d/m H:i') }}</td><td>{{ $p->alasan_batal }}</td></tr>
@empty<tr><td colspan="3" class="text-muted">Tidak ada.</td></tr>@endforelse</tbody></table>
@endsection
