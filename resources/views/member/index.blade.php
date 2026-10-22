@extends('layouts.app')
@section('judul', 'Beranda Member')
@section('isi')
<h5>Halo, {{ auth()->user()->nama_lengkap }}</h5>
<p class="text-muted">Pilih meja Anda untuk mulai memesan (sama seperti memindai kode QR di meja).</p>
<div class="row g-2 mb-4">
    @foreach($meja as $m)
        <div class="col-6 col-md-3">
            <a href="{{ route('pelanggan.form', $m->nomor_meja) }}" class="btn btn-outline-dark w-100">
                Meja {{ $m->nomor_meja }}<br><small class="text-muted">{{ $m->area }} | {{ $m->kapasitas }} kursi</small>
            </a>
        </div>
    @endforeach
</div>

<h5>Daftar menu</h5>
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
