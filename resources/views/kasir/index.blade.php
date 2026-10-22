@extends('layouts.app')
@section('judul', 'Kasir')
@section('isi')
<h5>Menunggu pembayaran tunai</h5>
@forelse($menunggu as $p)
    <div class="card mb-2"><div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div><strong>{{ $p->nomor_pesanan }}</strong> - Meja {{ $p->meja->nomor_meja }}
            <div class="small text-muted">Rp {{ number_format($p->total_harga, 0, ',', '.') }} | {{ $p->waktu_pesan->format('H:i') }}</div></div>
        <div class="d-flex gap-2">
            <form method="POST" action="{{ route('kasir.konfirmasi', $p->id_pesanan) }}">@csrf
                <button class="btn btn-success btn-sm">Terima Tunai</button></form>
            @include('kasir._batal', ['p' => $p])
        </div>
    </div></div>
@empty
    <p class="text-muted">Tidak ada.</p>
@endforelse

<h5 class="mt-4">Pesanan aktif</h5>
@forelse($aktif as $p)
    <div class="card mb-2"><div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div><strong>{{ $p->nomor_pesanan }}</strong> - Meja {{ $p->meja->nomor_meja }}
            <span class="badge bg-secondary">{{ $p->status_pesanan }}</span></div>
        @include('kasir._batal', ['p' => $p])
    </div></div>
@empty
    <p class="text-muted">Tidak ada.</p>
@endforelse
@endsection
