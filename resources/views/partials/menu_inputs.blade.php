@foreach($kategori as $k)
    @if($k->menu->count())
        <h6 class="mt-3 text-uppercase text-muted">{{ $k->nama_kategori }}</h6>
        <div class="list-group">
            @foreach($k->menu as $m)
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-semibold">{{ $m->nama_menu }}</div>
                        <div class="small text-muted">Rp {{ number_format($m->harga, 0, ',', '.') }}</div>
                    </div>
                    <input type="number" name="items[{{ $m->id_menu }}]" value="{{ old('items.' . $m->id_menu, 0) }}"
                           min="0" max="20" class="form-control" style="width:80px">
                </div>
            @endforeach
        </div>
    @endif
@endforeach
