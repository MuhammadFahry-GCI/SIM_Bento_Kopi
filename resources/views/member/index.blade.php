@extends('layouts.app')
@section('judul', 'Beranda Member')
@section('isi')
<div class="page-heading"><div><span class="eyebrow">Bento Kopi member</span><h1>Halo, {{ auth()->user()->nama_lengkap }}</h1><p>Pilih meja untuk mulai memesan, atau pindai kode QR di meja.</p></div></div>
<h5 class="mb-3">Pilih meja</h5>
<div class="row g-2 mb-4">
    @foreach($meja as $m)
        <div class="col-6 col-md-3">
            <a href="{{ route('pelanggan.form', $m->nomor_meja) }}" class="btn btn-outline-dark w-100">
                Meja {{ $m->nomor_meja }}<br><small class="text-muted">{{ $m->area }} | {{ $m->kapasitas }} kursi</small>
            </a>
        </div>
    @endforeach
</div>

<h5 class="mb-3">Jelajahi menu</h5>
@foreach($kategori as $k)
    @if($k->menu->count())
        <h6 class="mt-3 text-uppercase text-muted">{{ $k->nama_kategori }}</h6>
        <ul class="list-group">
            @foreach($k->menu as $m)
                <li class="list-group-item d-flex justify-content-between">
                    <span>{{ $m->nama_menu }}</span><span>Rp {{ number_format($m->harga, 0, ',', '.') }}</span>
                </li>
            @endforeach
        </ul>
    @endif
@endforeach
@endsection
