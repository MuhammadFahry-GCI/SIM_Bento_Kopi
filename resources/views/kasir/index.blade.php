@extends('layouts.app')
@section('judul', 'Kasir')
@section('isi')
<div class="page-heading">
    <div><span class="eyebrow">Operasional</span><h1>Kasir</h1><p>Pantau pembayaran masuk dan pesanan aktif.</p></div>
    <span class="badge text-bg-light">{{ $menunggu->count() }} menunggu pembayaran</span>
</div>
<h5 class="mb-3">Pesanan menunggu pembayaran</h5>
@forelse($menunggu as $p)
    <div class="card mb-2"><div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div><strong>{{ $p->nomor_pesanan }}</strong> - Meja {{ $p->meja->nomor_meja }}
            <div class="small text-muted">{{ strtoupper($p->pembayaran->metode) }} · Rp {{ number_format($p->total_harga, 0, ',', '.') }} | {{ $p->waktu_pesan->format('H:i') }}</div></div>
        <div class="d-flex gap-2">
            @if($p->pembayaran->metode === 'qris')
                <a class="btn btn-outline-success btn-sm" href="{{ route('pelanggan.pembayaran', $p->nomor_pesanan) }}">Lihat QRIS</a>
            @endif
            <form method="POST" action="{{ route('kasir.konfirmasi', $p->id_pesanan) }}">@csrf
                <button class="btn btn-success btn-sm">Konfirmasi lunas</button></form>
            @include('kasir._batal', ['p' => $p])
        </div>
    </div></div>
@empty
    <p class="text-muted">Tidak ada pembayaran yang menunggu konfirmasi.</p>
@endforelse

<h5 class="mt-4 mb-3">Pesanan aktif</h5>
@forelse($aktif as $p)
    <div class="card mb-2"><div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div><strong>{{ $p->nomor_pesanan }}</strong> - Meja {{ $p->meja->nomor_meja }}
            <span class="badge bg-secondary">{{ $p->status_pesanan }}</span>
            @if($p->pembayaran)<span class="badge text-bg-light">{{ $p->pembayaran->metode === 'qris' ? 'QRIS · cashless' : 'Tunai' }} · {{ $p->pembayaran->status_bayar === 'berhasil' ? 'Lunas' : 'Belum lunas' }}</span>@endif
        </div>
        @include('kasir._batal', ['p' => $p])
    </div></div>
@empty
    <p class="text-muted">Tidak ada.</p>
@endforelse

<section class="mt-5">
    <div class="page-heading">
        <div><span class="eyebrow">Jejak operasional</span><h2>Aktivitas pesanan</h2><p>20 aktivitas terbaru dari proses pemesanan sampai penyajian.</p></div>
    </div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Waktu</th><th>Aktivitas</th><th>Nomor pesanan</th><th>Pengguna</th><th>Keterangan</th></tr></thead>
            <tbody>
            @forelse($aktivitas as $log)
                <tr>
                    <td class="text-nowrap">{{ $log->waktu->format('d/m/Y H:i') }}</td>
                    <td>{{ str_replace('_', ' ', ucfirst($log->aksi)) }}</td>
                    <td>{{ $nomorPesanan[$log->id_entitas] ?? '#' . $log->id_entitas }}</td>
                    <td>{{ $log->pengguna->nama_lengkap ?? 'Pelanggan' }}</td>
                    <td>{{ $log->keterangan ?: '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">Belum ada aktivitas pesanan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
