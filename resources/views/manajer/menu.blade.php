@extends('layouts.app')
@section('judul', 'Kelola Menu')
@section('isi')
<div class="page-heading"><div><span class="eyebrow">Data master</span><h1>Kelola menu</h1><p>Atur kategori, harga, dan ketersediaan menu.</p></div></div>
<div class="card mb-4"><div class="card-body">
<h5 class="mb-3">Tambah menu</h5>
<form method="POST" action="{{ route('menu.store') }}" class="row g-2 mb-4">@csrf
    <div class="col-md-3"><select name="id_kategori" class="form-select" required>
        @foreach($kategori as $k)<option value="{{ $k->id_kategori }}">{{ $k->nama_kategori }}</option>@endforeach</select></div>
    <div class="col-md-4"><input name="nama_menu" class="form-control" placeholder="Nama menu" required></div>
    <div class="col-md-2"><input name="harga" type="number" min="0" step="500" class="form-control" placeholder="Harga" required></div>
    <div class="col-md-1 d-flex align-items-center"><div class="form-check"><input class="form-check-input" type="checkbox" name="tersedia" value="1" checked><label class="form-check-label">Ada</label></div></div>
    <div class="col-md-2"><button class="btn btn-dark w-100">Tambah</button></div>
</form>
</div></div>

<h5 class="mb-3">Daftar menu</h5>
<div class="table-responsive"><table class="table table-sm align-middle bg-white">
    <thead><tr><th>Kategori</th><th>Nama</th><th>Harga</th><th>Tersedia</th><th></th></tr></thead>
    <tbody>
    @foreach($menu as $m)
        <tr>
            <td colspan="5" class="p-0">
                <div class="manager-list-row">
                    <form id="u{{ $m->id_menu }}" method="POST" action="{{ route('menu.update', $m->id_menu) }}" class="manager-inline-form">@csrf @method('PUT')
                        <select name="id_kategori" class="form-select form-select-sm" style="width:130px">
                            @foreach($kategori as $k)<option value="{{ $k->id_kategori }}" @selected($k->id_kategori == $m->id_kategori)>{{ $k->nama_kategori }}</option>@endforeach</select>
                        <input name="nama_menu" value="{{ $m->nama_menu }}" class="form-control form-control-sm" required>
                        <input name="harga" type="number" min="0" step="500" value="{{ (int) $m->harga }}" class="form-control form-control-sm" style="width:110px" required>
                        <label class="small text-nowrap"><input type="checkbox" name="tersedia" value="1" @checked($m->tersedia)> Ada</label>
                        <button class="btn btn-sm btn-primary">Simpan</button>
                    </form>
                    <form method="POST" action="{{ route('menu.destroy', $m->id_menu) }}" data-confirm="Menu ini akan dihapus. Menu yang sudah digunakan pada pesanan tidak dapat dihapus."
                        data-confirm-title="Hapus menu?"
                        data-confirm-button="Ya, hapus menu">@csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">Hapus</button></form>
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table></div>
@endsection
