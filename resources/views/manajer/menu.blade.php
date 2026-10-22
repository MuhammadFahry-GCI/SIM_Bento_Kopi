@extends('layouts.app')
@section('judul', 'Kelola Menu')
@section('isi')
<h5>Tambah menu</h5>
<form method="POST" action="{{ route('menu.store') }}" class="row g-2 mb-4">@csrf
    <div class="col-md-3"><select name="id_kategori" class="form-select" required>
        @foreach($kategori as $k)<option value="{{ $k->id_kategori }}">{{ $k->nama_kategori }}</option>@endforeach</select></div>
    <div class="col-md-4"><input name="nama_menu" class="form-control" placeholder="Nama menu" required></div>
    <div class="col-md-2"><input name="harga" type="number" min="0" step="500" class="form-control" placeholder="Harga" required></div>
    <div class="col-md-1 d-flex align-items-center"><div class="form-check"><input class="form-check-input" type="checkbox" name="tersedia" value="1" checked><label class="form-check-label">Ada</label></div></div>
    <div class="col-md-2"><button class="btn btn-dark w-100">Tambah</button></div>
</form>

<h5>Daftar menu</h5>
<div class="table-responsive"><table class="table table-sm align-middle bg-white">
    <thead><tr><th>Kategori</th><th>Nama</th><th>Harga</th><th>Tersedia</th><th></th></tr></thead>
    <tbody>
    @foreach($menu as $m)
        <tr>
            <td colspan="5" class="p-0">
                <div class="d-flex align-items-center gap-2 p-2">
                    <form id="u{{ $m->id_menu }}" method="POST" action="{{ route('menu.update', $m->id_menu) }}" class="d-flex flex-grow-1 align-items-center gap-2">@csrf @method('PUT')
                        <select name="id_kategori" class="form-select form-select-sm" style="width:130px">
                            @foreach($kategori as $k)<option value="{{ $k->id_kategori }}" @selected($k->id_kategori == $m->id_kategori)>{{ $k->nama_kategori }}</option>@endforeach</select>
                        <input name="nama_menu" value="{{ $m->nama_menu }}" class="form-control form-control-sm" required>
                        <input name="harga" type="number" min="0" step="500" value="{{ (int) $m->harga }}" class="form-control form-control-sm" style="width:110px" required>
                        <label class="small text-nowrap"><input type="checkbox" name="tersedia" value="1" @checked($m->tersedia)> Ada</label>
                        <button class="btn btn-sm btn-primary">Simpan</button>
                    </form>
                    <form method="POST" action="{{ route('menu.destroy', $m->id_menu) }}">@csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus menu ini?')">Hapus</button></form>
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table></div>
@endsection
