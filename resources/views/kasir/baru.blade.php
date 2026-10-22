@extends('layouts.app')
@section('judul', 'Pesanan Tunai')
@section('isi')
<h5>Input pesanan tunai</h5>
<form method="POST" action="{{ route('kasir.simpan') }}">@csrf
    <div class="mb-2"><label class="form-label">Meja</label>
        <select name="id_meja" class="form-select" required>
            @foreach($meja as $m)<option value="{{ $m->id_meja }}" @selected(old('id_meja') == $m->id_meja)>{{ $m->nomor_meja }} ({{ $m->area }})</option>@endforeach
        </select></div>
    @include('partials.menu_inputs')
    <div class="mt-3 mb-2"><label class="form-label">Nama pemesan (opsional)</label>
        <input name="nama_pemesan" value="{{ old('nama_pemesan') }}" class="form-control" maxlength="100"></div>
    <div class="mb-3"><label class="form-label">Catatan (opsional)</label>
        <textarea name="catatan" class="form-control" rows="2">{{ old('catatan') }}</textarea></div>
    <button class="btn btn-dark">Simpan dan Kirim ke Dapur</button>
</form>
@endsection
