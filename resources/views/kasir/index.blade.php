@extends('layouts.app')
@section('judul', 'Kasir')
@section('isi')
<div class="page-heading">
    <div><span class="eyebrow">Operasional</span><h1>Kasir</h1><p>Pantau pembayaran masuk dan pesanan aktif.</p></div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <span class="badge text-bg-light">{{ $menunggu->count() }} menunggu pembayaran</span>
    </div>
</div>
<h5 class="mb-3">Pesanan menunggu pembayaran</h5>
@forelse($menunggu as $p)
    <div class="card mb-2"><div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div><strong>{{ $p->nomor_pesanan }}</strong> - Meja {{ $p->meja->nomor_meja }}
            <div class="small">Pemesan: {{ $p->nama_pemesan_tampilan ?: 'Tidak dicantumkan' }}</div>
            @if($p->reservasi)
                <div class="small text-muted">{{ $p->reservasi->status_reservasi === 'menunggu_pembayaran' ? 'Menunggu pembayaran · slot ditahan' : 'Booking terkonfirmasi' }} · {{ $p->reservasi->waktu_mulai->format('d/m/Y H:i') }}–{{ $p->reservasi->waktu_selesai->format('H:i') }} · {{ $p->reservasi->durasi_jam }} jam</div>
                <div class="small" data-booking-timer data-start="{{ $p->reservasi->waktu_mulai->toIso8601String() }}" data-end="{{ $p->reservasi->waktu_selesai->toIso8601String() }}" data-checked-in="{{ $p->reservasi->waktu_diterima ? 'true' : 'false' }}" aria-live="polite"></div>
            @endif
            <div class="small text-muted">{{ strtoupper($p->pembayaran->metode) }} · Rp {{ number_format($p->total_harga, 0, ',', '.') }} | {{ $p->waktu_pesan->format('H:i') }}</div></div>
        <div class="d-flex gap-2">
            @if($p->pembayaran->metode === 'qris')
                <a class="btn btn-outline-success btn-sm" href="{{ route('pelanggan.pembayaran', $p->nomor_pesanan) }}">Lihat QRIS</a>
            @endif
            <form method="POST" action="{{ route('kasir.konfirmasi', $p->id_pesanan) }}">@csrf
                <button class="btn btn-success btn-sm">Konfirmasi lunas</button></form>
            @include('kasir._batal', ['p' => $p])
        </div>
    </div></div>
@empty
    <p class="text-muted">Tidak ada pembayaran yang menunggu konfirmasi.</p>
@endforelse

<h5 class="mt-4 mb-3">Pesanan aktif</h5>
@forelse($aktif as $p)
    <div class="card mb-2"><div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div><strong>{{ $p->nomor_pesanan }}</strong> - Meja {{ $p->meja->nomor_meja }}
            <div class="small">Pemesan: {{ $p->nama_pemesan_tampilan ?: 'Tidak dicantumkan' }}</div>
            @if($p->reservasi)
                <div class="small text-muted">Booking {{ $p->reservasi->waktu_mulai->format('d/m/Y H:i') }}–{{ $p->reservasi->waktu_selesai->format('H:i') }} · {{ $p->reservasi->durasi_jam }} jam</div>
                <div class="small" data-booking-timer data-start="{{ $p->reservasi->waktu_mulai->toIso8601String() }}" data-end="{{ $p->reservasi->waktu_selesai->toIso8601String() }}" data-checked-in="{{ $p->reservasi->waktu_diterima ? 'true' : 'false' }}" aria-live="polite"></div>
            @endif
            <span class="badge bg-secondary">{{ $p->status_pesanan }}</span>
            @if($p->pembayaran)<span class="badge text-bg-light">{{ $p->pembayaran->metode === 'qris' ? 'QRIS · cashless' : 'Tunai' }} · {{ $p->pembayaran->status_bayar === 'berhasil' ? 'Lunas' : 'Belum lunas' }}</span>@endif
        </div>
        @include('kasir._batal', ['p' => $p])
        @if($p->reservasi && !$p->reservasi->waktu_checkin && $p->reservasi->waktu_mulai->lte(now()) && $p->reservasi->waktu_selesai->gt(now()) && $p->pembayaran?->status_bayar === 'berhasil')
            <form method="POST" action="{{ route('kasir.reservasi.mulai', $p->reservasi->id_reservasi) }}" class="w-100 mt-2">
                @csrf
                <button class="btn btn-sm btn-outline-success">Catat kedatangan pelanggan</button>
            </form>
        @elseif($p->reservasi?->waktu_checkin)
            <div class="small text-success mt-2">Pelanggan check-in {{ $p->reservasi->waktu_checkin->format('H:i') }} · menunggu pesanan diterima</div>
        @endif
        @if($p->reservasi && $p->reservasi->id_member === null && $p->status_pesanan === 'siap' && $p->pembayaran?->status_bayar === 'berhasil' && !$p->reservasi->waktu_diterima && $p->reservasi->waktu_mulai->lte(now()) && $p->reservasi->waktu_selesai->gt(now()))
            <form method="POST" action="{{ route('kasir.reservasi.pesanan.diterima', $p->id_pesanan) }}" class="w-100 mt-2"
                data-confirm="Pastikan pesanan booking sudah benar-benar diterima pelanggan."
                data-confirm-title="Catat pesanan diterima?"
                data-confirm-button="Ya, pesanan diterima">
                @csrf
                <button class="btn btn-sm btn-success">Catat pesanan diterima pelanggan</button>
            </form>
        @endif
    </div></div>
@empty
    <p class="text-muted">Tidak ada.</p>
@endforelse

<section class="mt-5">
    <div class="page-heading">
        <div>
            <span class="eyebrow">Arsip sesi meja</span>
            <h2>Riwayat pesanan sesi yang ditutup</h2>
            <p>Pesanan di bagian ini tidak lagi masuk antrean kasir, dapur, atau pelayan. Status terakhir tetap disimpan untuk riwayat.</p>
        </div>
    </div>
    <div class="d-grid gap-2">
        @forelse($riwayatSesi as $p)
            <article class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                        <div>
                            <strong>{{ $p->nomor_pesanan }}</strong> · Meja {{ $p->meja->nomor_meja }}
                            <div class="small">Pemesan: {{ $p->nama_pemesan_tampilan ?: 'Tidak dicantumkan' }}</div>
                            <div class="small text-muted">{{ $p->waktu_pesan->format('d/m/Y H:i') }} · Rp {{ number_format($p->total_harga, 0, ',', '.') }}</div>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-secondary">Sesi meja ditutup</span>
                            <span class="badge {{ $p->status_pesanan === 'dibatalkan' ? 'bg-danger' : 'bg-success' }}">
                                {{ $p->status_pesanan === 'dibatalkan' ? 'Dibatalkan' : 'Selesai' }}
                            </span>
                            <span class="badge bg-secondary">
                                {{ [
                                    'menunggu_bayar' => 'Status terakhir: menunggu bayar',
                                    'antre' => 'Status terakhir: antre',
                                    'diproses' => 'Status terakhir: diproses',
                                    'siap' => 'Status terakhir: siap',
                                    'disajikan' => 'Sudah disajikan',
                                    'selesai' => 'Selesai',
                                    'dibatalkan' => 'Dibatalkan',
                                ][$p->status_pesanan] ?? $p->status_pesanan }}
                            </span>
                            @if($p->pembayaran)
                                <span class="badge text-bg-light">
                                    {{ strtoupper($p->pembayaran->metode) }} · {{ $p->pembayaran->status_bayar }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="small mt-2">
                        @foreach($p->detail as $detail)
                            {{ $detail->jumlah }}× {{ $detail->menu->nama_menu }}@if(!$loop->last), @endif
                        @endforeach
                    </div>
                    @if($p->status_pesanan !== 'dibatalkan')
                        <p class="small text-muted mb-0 mt-2">Pesanan sesi ini selesai dan tidak dapat dilanjutkan dari antrean aktif. Status pesanan sebelum sesi ditutup: {{ [
                            'menunggu_bayar' => 'menunggu bayar',
                            'antre' => 'antre',
                            'diproses' => 'diproses',
                            'siap' => 'siap disajikan',
                            'disajikan' => 'sudah disajikan',
                            'selesai' => 'selesai',
                        ][$p->status_pesanan] ?? $p->status_pesanan }}.</p>
                    @endif
                    @if($p->pembayaran?->status_bayar === 'gagal')
                        <p class="small text-muted mb-0 mt-2">Pembayaran yang belum dikonfirmasi saat sesi meja ditutup ditandai tidak dilanjutkan.</p>
                    @endif
                </div>
            </article>
        @empty
            <div class="card"><div class="card-body text-muted">Belum ada riwayat sesi meja yang ditutup.</div></div>
        @endforelse
    </div>
    <div class="mt-3">{{ $riwayatSesi->links('pagination::bootstrap-5') }}</div>
</section>

<section class="mt-4">
    <div class="page-heading">
        <div><span class="eyebrow">Kontrol meja</span><h2>Status seluruh meja</h2>
            <p>Denah operasional seluruh meja, status penggunaan, serta nama dan jadwal booking. Kosongkan meja setelah pelanggan pergi.</p>
        </div>
    </div>
    <style>
        .cashier-floor-area { margin-bottom: 1.5rem; }
        .cashier-floor-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: .8rem; }
        .cashier-floor-table { border: 1px solid var(--bento-border); border-radius: 15px; background: var(--bento-surface); }
        .cashier-floor-table.available { border-color: rgba(67,197,139,.45); }
        .cashier-floor-table.booked { border-color: rgba(239,68,68,.72); background: rgba(185,28,28,.1); }
        .cashier-floor-table.occupied { border-color: rgba(220,95,85,.55); background: rgba(150,53,48,.1); }
        .cashier-floor-detail { color: var(--bento-muted); font-size: .79rem; line-height: 1.5; }
    </style>
    @forelse($mejaOutlet->groupBy('area') as $area => $mejaArea)
        <section class="cashier-floor-area">
            <h3 class="h6 text-uppercase text-muted">{{ ucfirst($area) }}</h3>
            <div class="cashier-floor-grid">
                @foreach($mejaArea as $m)
                    @php
                        $booking = $m->booking_aktif;
                        $pemesanBooking = $booking
                            ? ($booking->member?->nama_lengkap ?? $booking->kasir?->nama_lengkap ?? $booking->nama_pemesan)
                            : null;
                        $jumlahPesananSesi = $m->pesanan_sesi->count();
                        $namaPelangganSesi = $m->pesanan_sesi
                            ->pluck('nama_pemesan')
                            ->filter()
                            ->unique()
                            ->implode(', ');
                    @endphp
                    <article class="cashier-floor-table {{ $m->status_operasional === 'terisi' ? 'occupied' : (in_array($m->status_operasional, ['dipesan', 'menunggu_pembayaran'], true) ? 'booked' : 'available') }}">
                        <div class="p-3">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div>
                                    <strong>Meja {{ $m->nomor_meja }}</strong>
                                    <div class="cashier-floor-detail">{{ $m->kapasitas }} kursi</div>
                                </div>
                                <span class="badge {{ $m->status_operasional === 'terisi' ? 'bg-warning text-dark' : (in_array($m->status_operasional, ['dipesan', 'menunggu_pembayaran'], true) ? 'bg-danger' : 'bg-success') }}">
                                {{ $m->status_operasional === 'terisi' ? 'Terisi' : ($m->status_operasional === 'menunggu_pembayaran' ? 'Menunggu pembayaran' : ($m->status_operasional === 'dipesan' ? 'Booking terkonfirmasi' : 'Tersedia')) }}
                                </span>
                            </div>
                            @if($booking)
                                <div class="cashier-floor-detail mt-2">
                                    <strong>{{ $booking->status_reservasi === 'menunggu_pembayaran' ? 'Menunggu pembayaran dari' : 'Dibooking oleh' }} {{ $pemesanBooking }}</strong><br>
                                    {{ $booking->waktu_mulai->format('d/m/Y H:i') }}–{{ $booking->waktu_selesai->format('H:i') }} · {{ $booking->durasi_jam }} jam<br>
                                    Pesanan {{ $booking->pesanan->nomor_pesanan }} · {{ $booking->pesanan->pembayaran?->status_bayar === 'berhasil' ? 'Lunas' : 'Belum lunas' }}
                                </div>
                            @endif
                            @if($m->status_operasional === 'terisi')
                                <div class="cashier-floor-detail mt-2">
                                    {{ $jumlahPesananSesi }} pesanan di sesi ini
                                    @if($namaPelangganSesi)<br>Pemesan: {{ $namaPelangganSesi }}@endif
                                </div>
                                <form method="POST" action="{{ route('kasir.meja.kosongkan', $m->id_meja) }}" class="mt-3"
                                    data-confirm="Pastikan pelanggan sudah meninggalkan meja ini sebelum status diubah."
                                    data-confirm-title="Buka kembali meja?"
                                    data-confirm-button="Ya, tandai tersedia">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-success w-100">Tandai meja tersedia</button>
                                </form>
                            @elseif($booking)
                                <div class="cashier-floor-detail mt-2">{{ $booking->status_reservasi === 'menunggu_pembayaran' ? 'Slot ditahan sampai pembayaran lunas atau pesanan dibatalkan.' : 'Meja dikunci selama jadwal booking; status terisi setelah member menerima pesanan.' }}</div>
                            @else
                                <div class="cashier-floor-detail mt-2">Siap dipilih untuk pesanan baru.</div>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
        @empty
            <div class="card"><div class="card-body text-muted">Belum ada data meja.</div></div>
        @endforelse
</section>

<section class="mt-5">
    <div class="page-heading">
        <div><span class="eyebrow">Jejak operasional</span><h2>Aktivitas pesanan</h2><p>20 aktivitas terbaru dari proses pemesanan sampai penyajian.</p></div>
    </div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Waktu</th><th>Aktivitas</th><th>Nomor pesanan</th><th>Pemesan</th><th>Pesanan</th><th>Pengguna</th><th>Keterangan</th></tr></thead>
            <tbody>
            @forelse($aktivitas as $log)
                <tr>
                    <td class="text-nowrap">{{ $log->waktu->format('d/m/Y H:i') }}</td>
                    <td>{{ str_replace('_', ' ', ucfirst($log->aksi)) }}</td>
                    <td>{{ $pesananAktivitas[$log->id_entitas]->nomor_pesanan ?? '#' . $log->id_entitas }}</td>
                    <td>{{ $pesananAktivitas->get($log->id_entitas)?->nama_pemesan_tampilan ?: 'Tidak dicantumkan' }}</td>
                    <td>
                        @if($pesananAktivitas->has($log->id_entitas))
                            @foreach($pesananAktivitas[$log->id_entitas]->detail as $detail)
                                {{ $detail->jumlah }}× {{ $detail->menu?->nama_menu ?? 'Menu dihapus' }}@if(!$loop->last), @endif
                            @endforeach
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $log->pengguna->nama_lengkap ?? 'Pelanggan' }}</td>
                    <td>{{ $log->keterangan ?: '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Belum ada aktivitas pesanan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
