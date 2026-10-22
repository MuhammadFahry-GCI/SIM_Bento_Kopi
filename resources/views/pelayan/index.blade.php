@extends('layouts.app')
@section('judul', 'Penyajian')
@section('isi')
<h5>Pesanan siap diantar</h5>
@forelse($siap as $p)
    <div class="card mb-2"><div class="card-body d-flex justify-content-between align-items-center">
        <div><span class="fs-4 fw-bold">Meja {{ $p->meja->nomor_meja }}</span> <span class="text-muted">{{ $p->nomor_pesanan }}</span>
            <div class="small">@foreach($p->detail as $d){{ $d->jumlah }} {{ $d->menu->nama_menu }}@if(!$loop->last), @endif @endforeach</div></div>
        <form method="POST" action="{{ route('pelayan.sajikan', $p->id_pesanan) }}">@csrf<button class="btn btn-success">Sudah Disajikan</button></form>
    </div></div>
@empty
    <p class="text-muted">Belum ada pesanan siap.</p>
@endforelse

<h5 class="mt-4">Status meja</h5>
<div class="row g-2">
    @foreach($meja as $m)
        <div class="col-6 col-md-3"><div class="card text-center {{ $m->status_meja === 'terisi' ? 'border-danger' : 'border-success' }}">
            <div class="card-body p-2"><div class="fw-bold">{{ $m->nomor_meja }}</div>
                <div class="small text-muted">{{ $m->area }} | {{ $m->kapasitas }} kursi</div>
                <form method="POST" action="{{ route('pelayan.meja', $m->id_meja) }}">@csrf
                    <button class="btn btn-sm mt-1 {{ $m->status_meja === 'terisi' ? 'btn-danger' : 'btn-success' }}">{{ $m->status_meja }}</button></form>
            </div></div></div>
    @endforeach
</div>
@endsection
