@extends('layouts.app')
@section('judul', 'Pesanan Dapur')
@section('isi')
<div class="page-heading">
    <div><span class="eyebrow">Operasional dapur</span><h1>Pesanan diproses</h1><p>Pesanan lunas langsung diproses. Tandai siap saat pesanan selesai dibuat.</p></div>
    <span class="badge text-bg-light"><span class="text-success">●</span> Diperbarui otomatis</span>
</div>
<div id="antrean">@include('dapur._antrean')</div>

<h5 class="mt-4 mb-3">Ketersediaan menu</h5>
<div class="list-group">
    @foreach($menu as $m)
        <div class="list-group-item d-flex justify-content-between align-items-center">
            <span>{{ $m->nama_menu }}</span>
            <form method="POST" action="{{ route('dapur.ketersediaan', $m->id_menu) }}">@csrf
                <button class="btn btn-sm {{ $m->tersedia ? 'btn-outline-success' : 'btn-outline-danger' }}">{{ $m->tersedia ? 'Tersedia' : 'Habis' }}</button>
            </form>
        </div>
    @endforeach
</div>
@endsection
@push('scripts')
<script>
setInterval(async () => {
    try {
        const r = await fetch('{{ route('dapur.index') }}?partial=1', {headers: {'X-Requested-With': 'XMLHttpRequest'}});
        if (r.ok) document.getElementById('antrean').innerHTML = await r.text();
    } catch (e) {}
}, 8000);
</script>
@endpush
