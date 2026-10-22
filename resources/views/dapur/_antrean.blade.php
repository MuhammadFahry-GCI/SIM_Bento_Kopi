@forelse($pesanan as $p)
    <div class="card mb-2 {{ $p->status_pesanan === 'diproses' ? 'border-warning' : '' }}">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <div><strong>{{ $p->nomor_pesanan }}</strong> - Meja <strong>{{ $p->meja->nomor_meja }}</strong>
                    <span class="badge bg-{{ $p->status_pesanan === 'diproses' ? 'warning text-dark' : 'secondary' }}">{{ $p->status_pesanan }}</span></div>
                <div class="small text-muted">{{ $p->waktu_pesan->format('H:i') }} ({{ (int) $p->waktu_pesan->diffInMinutes(now()) }} menit lalu)</div>
            </div>
            <ul class="mb-2 mt-2">
                @foreach($p->detail as $d)<li>{{ $d->jumlah }} x {{ $d->menu->nama_menu }}@if($d->catatan_item) <em>({{ $d->catatan_item }})</em>@endif</li>@endforeach
            </ul>
            @if($p->catatan)<div class="small text-muted mb-2">Catatan: {{ $p->catatan }}</div>@endif
            <div class="d-flex gap-2">
                @if($p->status_pesanan === 'antre')
                    <form method="POST" action="{{ route('dapur.proses', $p->id_pesanan) }}">@csrf<button class="btn btn-warning btn-sm">Mulai Proses</button></form>
                @endif
                <form method="POST" action="{{ route('dapur.siap', $p->id_pesanan) }}">@csrf<button class="btn btn-success btn-sm">Tandai Siap</button></form>
            </div>
        </div>
    </div>
@empty
    <p class="text-muted">Tidak ada pesanan dalam antrean.</p>
@endforelse
