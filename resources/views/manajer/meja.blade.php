@extends('layouts.app')
@section('judul', 'Kelola Meja')
@section('isi')
<div class="page-heading"><div><span class="eyebrow">Data master</span><h1>Kelola meja</h1><p>Atur nomor, kapasitas, dan area meja outlet.</p></div></div>
<div class="card mb-4"><div class="card-body">
<h5 class="mb-3">Tambah meja</h5>
<form method="POST" action="{{ route('meja.store') }}" class="row g-2 mb-4">@csrf
    <div class="col-md-3"><input name="nomor_meja" class="form-control" placeholder="Nomor meja (mis. A4)" required maxlength="10"></div>
    <div class="col-md-2"><input name="kapasitas" type="number" min="1" class="form-control" placeholder="Kursi" required></div>
    <div class="col-md-3"><select name="area" class="form-select">@foreach(['indoor','merokok','sofa','outdoor'] as $a)<option>{{ $a }}</option>@endforeach</select></div>
    <div class="col-md-2"><button class="btn btn-dark w-100">Tambah</button></div>
</form>
</div></div>

<h5 class="mb-3">Daftar meja</h5>
<div class="manager-list">
@foreach($meja as $m)
    <div class="manager-list-row">
        <form method="POST" action="{{ route('meja.update', $m->id_meja) }}" class="manager-inline-form">@csrf @method('PUT')
            <input name="nomor_meja" value="{{ $m->nomor_meja }}" class="form-control form-control-sm" aria-label="Nomor meja" required>
            <input name="kapasitas" type="number" min="1" value="{{ $m->kapasitas }}" class="form-control form-control-sm" aria-label="Kapasitas kursi" required>
            <select name="area" class="form-select form-select-sm" aria-label="Area meja">@foreach(['indoor','merokok','sofa','outdoor'] as $a)<option @selected($a === $m->area)>{{ $a }}</option>@endforeach</select>
            <span class="small text-muted flex-grow-1">{{ $m->status_meja }}</span>
            <button class="btn btn-sm btn-primary">Simpan</button>
        </form>
        <form method="POST" action="{{ route('meja.destroy', $m->id_meja) }}">@csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus meja ini?')">Hapus</button></form>
    </div>
@endforeach
</div>
<p class="small text-muted mt-2">Kode QR meja mengarah ke: <code>{{ url('/meja/NOMOR_MEJA') }}</code></p>
@endsection
