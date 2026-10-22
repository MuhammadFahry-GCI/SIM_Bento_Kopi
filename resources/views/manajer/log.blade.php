@extends('layouts.app')
@section('judul', 'Log Aktivitas')
@section('isi')
<h5>Log aktivitas (200 terbaru)</h5>
<div class="table-responsive"><table class="table table-sm bg-white">
    <thead><tr><th>Waktu</th><th>Pengguna</th><th>Aksi</th><th>Entitas</th><th>ID</th><th>Keterangan</th></tr></thead>
    <tbody>
    @foreach($log as $l)
        <tr><td>{{ $l->waktu->format('d/m/Y H:i:s') }}</td><td>{{ $l->pengguna->username ?? 'pelanggan' }}</td>
            <td>{{ $l->aksi }}</td><td>{{ $l->entitas }}</td><td>{{ $l->id_entitas }}</td><td>{{ $l->keterangan }}</td></tr>
    @endforeach
    </tbody>
</table></div>
@endsection
