@extends('layouts.app')
@section('judul', 'Penyajian')
@section('isi')
<div class="page-heading"><div><span class="eyebrow">Operasional layanan</span><h1>Penyajian</h1><p>Antarkan pesanan siap. Status meja dikelola kasir setelah pelanggan meninggalkan tempat.</p></div><span class="badge text-bg-light">{{ $siap->count() }} pesanan siap</span></div>
<h5 class="mb-3">Pesanan siap diantar</h5>
@forelse($siap as $p)
    <div class="card mb-2"><div class="card-body d-flex justify-content-between align-items-center">
        <div><span class="fs-4 fw-bold">Meja {{ $p->meja->nomor_meja }}</span> <span class="text-muted">{{ $p->nomor_pesanan }}</span>
            <div class="small">@foreach($p->detail as $d){{ $d->jumlah }} {{ $d->menu->nama_menu }}@if(!$loop->last), @endif @endforeach</div></div>
        <form method="POST" action="{{ route('pelayan.sajikan', $p->id_pesanan) }}">@csrf<button class="btn btn-success">Sudah Disajikan</button></form>
    </div></div>
@empty
    <p class="text-muted">Belum ada pesanan siap.</p>
@endforelse

@endsection
