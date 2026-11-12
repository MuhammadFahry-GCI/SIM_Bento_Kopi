@once
    <style>
        .floor-plan { padding: clamp(1rem, 4vw, 2rem); border: 1px solid var(--bento-border); border-radius: 22px; background: linear-gradient(145deg, #1a211d, #151b17); }
        .floor-stage { width: min(100%, 430px); margin: 0 auto 2rem; padding: .8rem 1rem; border: 1px solid rgba(169,120,75,.55); border-radius: 999px; color: #e8c39c; background: rgba(169,120,75,.09); font-size: .75rem; font-weight: 800; letter-spacing: .16em; text-align: center; text-transform: uppercase; }
        .floor-area { margin-top: 1.75rem; }
        .floor-area-title { display: flex; align-items: center; gap: .75rem; margin-bottom: .85rem; color: var(--bento-muted); font-size: .72rem; font-weight: 800; letter-spacing: .13em; text-transform: uppercase; }
        .floor-area-title:after { height: 1px; flex: 1; background: var(--bento-border); content: ""; }
        .seat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(145px, 1fr)); gap: .85rem; }
        .seat-card { min-height: 145px; padding: .85rem; border: 1px solid var(--bento-border); border-radius: 16px; background: rgba(255,255,255,.025); }
        .seat-card.available { border-color: rgba(67,197,139,.45); background: rgba(23,134,92,.08); }
        .seat-card.booked { border-color: rgba(239,68,68,.72); background: rgba(185,28,28,.2); box-shadow: inset 0 0 0 1px rgba(239,68,68,.12); }
        .seat-card.occupied { border-color: rgba(220,95,85,.45); background: rgba(150,53,48,.09); }
        .seat-top { display: flex; align-items: center; justify-content: space-between; gap: .5rem; }
        .seat-number { font-size: 1rem; font-weight: 800; }
        .seat-icon { display: grid; width: 36px; height: 36px; place-items: center; border: 1px solid currentColor; border-radius: 12px; color: #72d9a8; font-size: .75rem; font-weight: 800; }
        .seat-card.booked .seat-icon { color: #f87171; }
        .seat-card.booked .seat-status { color: #fca5a5; }
        .seat-card.occupied .seat-icon { color: #ed8980; }
        .seat-card .seat-status { display: block; margin-top: .75rem; font-size: .75rem; font-weight: 700; }
        .seat-card .seat-meta { color: var(--bento-muted); font-size: .75rem; }
        .seat-card .btn { margin-top: .65rem; }
        .seat-legend { display: flex; flex-wrap: wrap; gap: .75rem 1.25rem; margin-top: 1.5rem; color: var(--bento-muted); font-size: .8rem; }
        .seat-legend span { display: inline-flex; align-items: center; gap: .45rem; }
        .legend-dot { width: 10px; height: 10px; border-radius: 50%; background: #43c58b; }
        .legend-dot.booked { background: #ef4444; }
        .legend-dot.occupied { background: #dc655c; }
    </style>
@endonce

<div class="floor-plan">
    <div class="floor-stage">Area bar &amp; kasir</div>
    @forelse($meja->groupBy('area') as $area => $mejaArea)
        <section class="floor-area">
            <h3 class="floor-area-title">{{ ucfirst($area) }}</h3>
            <div class="seat-grid">
                @foreach($mejaArea as $m)
                    @php
                        $statusClass = $m->status_layout === 'terisi' ? 'occupied' : (in_array($m->status_layout, ['dipesan', 'menunggu_pembayaran'], true) ? 'booked' : 'available');
                        $statusLabel = $m->status_layout === 'terisi'
                            ? 'Sedang terisi'
                            : ($m->status_layout === 'menunggu_pembayaran'
                                ? 'Menunggu pembayaran · slot ditahan'
                                : ($m->status_layout === 'dipesan' ? 'Booking terkonfirmasi' : 'Tersedia'));
                    @endphp
                    <article class="seat-card {{ $statusClass }}">
                        <div class="seat-top">
                            <span class="seat-number">Meja {{ $m->nomor_meja }}</span>
                            <span class="seat-icon" aria-label="{{ $m->kapasitas }} kursi">{{ $m->kapasitas }} <small>kursi</small></span>
                        </div>
                        <span class="seat-status">{{ $statusLabel }}</span>
                        <div class="seat-meta">{{ ucfirst($m->area) }}</div>
                        @if($m->tersedia_booking)
                            @auth
                                @if(in_array(auth()->user()->peran, ['member', 'kasir'], true))
                                    <a class="btn btn-sm btn-outline-success w-100" href="{{ route('reservasi.form', ['id' => $m->id_meja, 'waktu_mulai' => $waktuMulai->format('Y-m-d\TH:i'), 'durasi_jam' => $durasiJam]) }}">Pilih meja</a>
                                @else
                                    <span class="d-block mt-2 small text-muted">Booking melalui kasir atau akun member</span>
                                @endif
                            @else
                                <a class="btn btn-sm btn-outline-success w-100" href="{{ route('login') }}">Login untuk pilih</a>
                            @endauth
                        @endif
                    </article>
                @endforeach
            </div>
        </section>
    @empty
        <div class="text-center text-muted py-5">Belum ada data meja. Silakan hubungi kasir.</div>
    @endforelse
    <div class="seat-legend">
        <span><i class="legend-dot"></i>Tersedia</span>
        <span><i class="legend-dot booked"></i>Booking atau menunggu pembayaran</span>
        <span><i class="legend-dot occupied"></i>Sedang terisi</span>
    </div>
</div>
