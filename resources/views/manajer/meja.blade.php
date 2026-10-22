@extends('layouts.app')
@section('judul', 'Kelola Meja')
@section('isi')
<h5>Tambah meja</h5>
<form method="POST" action="{{ route('meja.store') }}" class="row g-2 mb-4">@csrf
    <div class="col-md-3"><input name="nomor_meja" class="form-control" placeholder="Nomor meja (mis. A4)" required maxlength="10"></div>
    <div class="col-md-2"><input name="kapasitas" type="number" min="1" class="form-control" placeholder="Kursi" required></div>
    <div class="col-md-3"><select name="area" class="form-select">@foreach(['indoor','merokok','sofa','outdoor'] as $a)<option>{{ $a }}</option>@endforeach</select></div>
    <div class="col-md-2"><button class="btn btn-dark w-100">Tambah</button></div>
</form>

<h5>Daftar meja</h5>
<div class="bg-white border rounded">
@foreach($meja as $m)
    <div class="d-flex align-items-center gap-2 p-2 border-bottom">
        <form method="POST" action="{{ route('meja.update', $m->id_meja) }}" class="d-flex flex-grow-1 align-items-center gap-2">@csrf @method('PUT')
            <input name="nomor_meja" value="{{ $m->nomor_meja }}" class="form-control form-control-sm" style="width:90px" required>
            <input name="kapasitas" type="number" min="1" value="{{ $m->kapasitas }}" class="form-control form-control-sm" style="width:80px" required>
            <select name="area" class="form-select form-select-sm" style="width:130px">@foreach(['indoor','merokok','sofa','outdoor'] as $a)<option @selected($a === $m->area)>{{ $a }}</option>@endforeach</select>
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
