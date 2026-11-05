@forelse($pesanan as $p)
    <div class="card mb-2 border-warning">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <div><strong>{{ $p->nomor_pesanan }}</strong> - Meja <strong>{{ $p->meja->nomor_meja }}</strong>
                    <span class="badge bg-warning text-dark">Sedang diproses</span></div>
                <div class="small text-muted">{{ $p->waktu_pesan->format('H:i') }} ({{ (int) $p->waktu_pesan->diffInMinutes(now()) }} menit lalu)</div>
            </div>
            <div class="small mt-1">Pemesan: {{ $p->nama_pemesan_tampilan ?: 'Tidak dicantumkan' }}</div>
            <ul class="mb-2 mt-2">
                @foreach($p->detail as $d)<li>{{ $d->jumlah }} x {{ $d->menu->nama_menu }}@if($d->catatan_item) <em>({{ $d->catatan_item }})</em>@endif</li>@endforeach
            </ul>
            @if($p->catatan)<div class="small text-muted mb-2">Catatan: {{ $p->catatan }}</div>@endif
            <div class="d-flex gap-2">
                <form method="POST" action="{{ route('dapur.siap', $p->id_pesanan) }}">@csrf<button class="btn btn-success btn-sm">Tandai Siap</button></form>
            </div>
        </div>
    </div>
@empty
    <p class="text-muted">Tidak ada pesanan yang sedang diproses.</p>
@endforelse
