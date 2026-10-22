@extends('layouts.app')
@section('judul', 'Antrean Dapur')
@section('isi')
<h5>Antrean pesanan (urut waktu masuk) <small class="text-muted fs-6">diperbarui otomatis</small></h5>
<div id="antrean">@include('dapur._antrean')</div>

<h5 class="mt-4">Ketersediaan menu</h5>
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
