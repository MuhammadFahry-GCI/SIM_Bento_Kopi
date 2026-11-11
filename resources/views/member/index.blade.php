@extends('layouts.app')
@section('judul', 'Beranda Member')
@section('isi')
<div class="page-heading"><div><span class="eyebrow">Bento Kopi member</span><h1>Halo, {{ auth()->user()->nama_lengkap }}</h1><p>Pilih meja untuk mulai memesan, atau pindai kode QR di meja.</p></div></div>
<div class="card mb-4"><div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div><strong>Ingin datang di waktu tertentu?</strong><div class="small text-muted">Pilih jadwal dan durasi booking 1–6 jam dengan pre-order minimal Rp50.000.</div></div>
    <a class="btn btn-dark" href="{{ route('reservasi.index') }}">Lihat denah &amp; booking meja</a>
</div></div>
<div class="row g-3 mb-4">
    <div class="col-md-6"><div class="card h-100"><div class="card-body">
        <div class="text-muted small">Meja tersedia</div>
        <div class="fs-3 fw-bold">{{ $meja->where('status_tampilan', 'tersedia')->count() }}</div>
        <div class="small text-muted">Tetap konfirmasi ketersediaan meja kepada kasir sebelum duduk.</div>
    </div></div></div>
    <div class="col-md-6"><div class="card h-100"><div class="card-body">
        <div class="text-muted small">Meja sedang terisi</div>
        <div class="fs-3 fw-bold">{{ $meja->where('status_tampilan', 'terisi')->count() }}</div>
        <div class="small text-muted">Meja ditandai tersedia kembali oleh kasir setelah pelanggan meninggalkan tempat.</div>
    </div></div></div>
</div>
<h5 class="mb-3">Status meja outlet</h5>
<div class="row g-2 mb-4">
    @foreach($meja as $m)
        <div class="col-6 col-md-3">
            <div class="card h-100 text-center">
                <div class="card-body p-3">
                    <div class="fw-bold">Meja {{ $m->nomor_meja }}</div>
                    <div class="small text-muted">{{ $m->area }} · {{ $m->kapasitas }} kursi</div>
                    <span class="badge mt-2 {{ $m->status_tampilan === 'terisi' ? 'bg-warning text-dark' : ($m->status_tampilan === 'dipesan' ? 'bg-danger' : 'bg-success') }}">
                        {{ $m->status_tampilan === 'terisi' ? 'Sedang terisi' : ($m->status_tampilan === 'dipesan' ? 'Dibooking' : 'Tersedia') }}
                    </span>
                    @if($m->dapat_pesan_meja)
                        <a href="{{ route('pelanggan.form', $m->nomor_meja) }}" class="btn btn-sm btn-outline-dark w-100 mt-2">
                            {{ $m->status_tampilan === 'terisi' || $m->status_tampilan === 'dipesan' ? 'Tambah pesanan' : 'Pesan di meja ini' }}
                        </a>
                    @else
                        <span class="d-block small text-danger mt-2">
                            {{ $m->booking_pemilik ? 'Selesaikan pesanan booking sebelum tambah pesanan' : ($m->status_tampilan === 'terisi' ? 'Meja digunakan member lain' : 'Meja dibooking member lain') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>

<section class="mb-5">
    <div class="page-heading">
        <div><span class="eyebrow">Jadwal kunjungan</span><h2>Booking meja Anda</h2><p>Meja tetap berstatus dibooking selama pesanan belum dibayar atau belum diterima. Setelah pesanan diterima, kasir mengosongkan meja saat pelanggan pergi.</p></div>
    </div>
    <div class="d-grid gap-3">
        @forelse($reservasi as $booking)
            @php($p = $booking->pesanan)
            <article class="card"><div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                    <div>
                        <h3 class="h5 mb-1">Meja {{ $booking->meja->nomor_meja }} · {{ $p->nomor_pesanan }}</h3>
                        <div class="small">{{ $booking->waktu_mulai->format('d/m/Y H:i') }}–{{ $booking->waktu_selesai->format('H:i') }} · {{ $booking->durasi_jam }} jam</div>
                        <div class="small text-muted">Pre-order Rp {{ number_format($p->total_harga, 0, ',', '.') }} · {{ $p->pembayaran?->status_bayar === 'berhasil' ? 'Lunas' : 'Menunggu pembayaran' }}</div>
                        <div class="small mt-1" data-booking-timer
                            data-start="{{ $booking->waktu_mulai->toIso8601String() }}"
                            data-end="{{ $booking->waktu_selesai->toIso8601String() }}"
                            data-checked-in="{{ $booking->waktu_diterima ? 'true' : 'false' }}"
                            aria-live="polite"></div>
                    </div>
                    <span class="badge {{ $booking->waktu_diterima ? 'bg-success' : ($booking->waktu_mulai->isFuture() ? 'bg-info text-dark' : 'bg-danger') }}">
                        {{ $booking->waktu_diterima ? 'Pesanan diterima · meja terisi' : ($booking->waktu_mulai->isFuture() ? 'Akan datang' : ($booking->waktu_checkin ? 'Check-in · menunggu pesanan' : 'Booking · menunggu pesanan')) }}
                    </span>
                </div>
                <div class="small mt-2">
                    @foreach($p->detail as $detail)
                        {{ $detail->jumlah }}× {{ $detail->menu->nama_menu }}@if(!$loop->last), @endif
                    @endforeach
                </div>
                @if($p->pembayaran?->status_bayar === 'menunggu')
                    <a class="btn btn-sm btn-outline-success mt-3" href="{{ route('pelanggan.pembayaran', $p->nomor_pesanan) }}">Lanjutkan pembayaran</a>
                @endif
                @if($p->status_pesanan === 'siap' && $p->pembayaran?->status_bayar === 'berhasil' && !$booking->waktu_diterima && $booking->waktu_mulai->lte(now()) && $booking->waktu_selesai->gt(now()))
                    <form method="POST" action="{{ route('member.pesanan.diterima', $p->id_pesanan) }}" class="mt-3">
                        @csrf
                        <button class="btn btn-success btn-sm">Pesanan sudah diambil / diantarkan</button>
                    </form>
                @elseif($booking->waktu_diterima)
                    <span class="badge bg-success mt-3">Dikonfirmasi diterima · {{ $booking->waktu_diterima->format('H:i') }}</span>
                @else
                    <div class="small text-muted mt-3">Status pesanan: {{ [
                        'menunggu_bayar' => 'menunggu pembayaran',
                        'antre' => 'menunggu antrean dapur',
                        'diproses' => 'sedang disiapkan',
                        'siap' => 'siap disajikan',
                        'disajikan' => 'sudah disajikan',
                    ][$p->status_pesanan] ?? $p->status_pesanan }}</div>
                @endif
            </div></article>
        @empty
            <div class="card"><div class="card-body text-muted">Belum ada booking aktif. Pilih meja dan waktu kunjungan melalui denah.</div></div>
        @endforelse
    </div>
</section>

<section class="mb-5">
    <div class="page-heading">
        <div><span class="eyebrow">Pemesanan Anda</span><h2>Pesanan yang sedang berjalan</h2><p>Pembaruan status pesanan yang terhubung dengan akun member ini.</p></div>
    </div>
    <div class="d-grid gap-3">
        @forelse($aktif as $p)
            <article class="card"><div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                    <div>
                        <h3 class="h5 mb-1">{{ $p->nomor_pesanan }} · Meja {{ $p->meja->nomor_meja }}</h3>
                        <div class="small">Pemesan: {{ $p->nama_pemesan_tampilan ?: 'Tidak dicantumkan' }}</div>
                        <div class="small text-muted">{{ $p->waktu_pesan->format('d/m/Y H:i') }} · Rp {{ number_format($p->total_harga, 0, ',', '.') }}</div>
                    </div>
                    <span class="badge {{ $p->status_pesanan === 'menunggu_bayar' ? 'bg-warning text-dark' : 'bg-info text-dark' }}">
                        {{ ['menunggu_bayar' => 'Menunggu pembayaran', 'antre' => 'Sedang disiapkan', 'diproses' => 'Sedang disiapkan', 'siap' => 'Siap disajikan'][$p->status_pesanan] }}
                    </span>
                </div>
                <div class="small mt-2">
                    @foreach($p->detail as $detail)
                        {{ $detail->jumlah }}× {{ $detail->menu->nama_menu }}@if(!$loop->last), @endif
                    @endforeach
                </div>
                @if($p->status_pesanan === 'siap' && $p->pembayaran?->status_bayar === 'berhasil')
                    <form method="POST" action="{{ route('member.pesanan.diterima', $p->id_pesanan) }}" class="mt-3">
                        @csrf
                        <button class="btn btn-success btn-sm">Pesanan sudah diambil / diantarkan</button>
                    </form>
                @endif
                <a class="btn btn-sm btn-outline-success mt-3" href="{{ route('pelanggan.status', $p->nomor_pesanan) }}">Lihat status pesanan</a>
            </div></article>
        @empty
            <div class="card"><div class="card-body text-muted">Tidak ada pesanan yang sedang berjalan.</div></div>
        @endforelse
    </div>
</section>

<section class="mb-5">
    <div class="page-heading">
        <div><span class="eyebrow">Riwayat transaksi</span><h2>Pesanan selesai dan riwayat sesi meja</h2><p>Pesanan selesai, dibatalkan, atau berasal dari sesi meja yang sudah ditutup dipindahkan ke sini.</p></div>
    </div>
    <div class="d-grid gap-3">
        @forelse($riwayat as $p)
            <article class="card"><div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                    <div>
                        <h3 class="h5 mb-1">{{ $p->nomor_pesanan }} · Meja {{ $p->meja->nomor_meja }}</h3>
                        <div class="small">Pemesan: {{ $p->nama_pemesan_tampilan ?: 'Tidak dicantumkan' }}</div>
                        <div class="small text-muted">{{ $p->waktu_pesan->format('d/m/Y H:i') }} · Rp {{ number_format($p->total_harga, 0, ',', '.') }}</div>
                    </div>
                    <span class="badge {{ $p->status_pesanan === 'dibatalkan' ? 'bg-secondary' : 'bg-success' }}">
                        {{ $p->status_pesanan === 'dibatalkan' ? 'Dibatalkan' : ($p->status_pesanan === 'disajikan' ? 'Sudah diterima' : 'Selesai') }}
                    </span>
                </div>
                <div class="small mt-2">
                    @foreach($p->detail as $detail)
                        {{ $detail->jumlah }}× {{ $detail->menu->nama_menu }}@if(!$loop->last), @endif
                    @endforeach
                </div>
                @if($p->alasan_batal)<div class="small text-muted mt-2">Alasan pembatalan: {{ $p->alasan_batal }}</div>@endif
                @if($p->sesi_meja_selesai && $p->status_pesanan !== 'dibatalkan')
                    <div class="small text-muted mt-2">Sesi meja ditutup oleh kasir. Status terakhir pesanan: {{ [
                        'menunggu_bayar' => 'menunggu pembayaran',
                        'antre' => 'sedang diproses',
                        'diproses' => 'sedang diproses',
                        'siap' => 'siap disajikan',
                        'disajikan' => 'sudah disajikan',
                        'selesai' => 'selesai',
                    ][$p->status_pesanan] ?? $p->status_pesanan }}.</div>
                @endif
                <a class="btn btn-sm btn-outline-secondary mt-3" href="{{ route('pelanggan.status', $p->nomor_pesanan) }}">Lihat rincian</a>
            </div></article>
        @empty
            <div class="card"><div class="card-body text-muted">Riwayat pesanan selesai atau dibatalkan akan tampil di sini.</div></div>
        @endforelse
    </div>
    <div class="mt-3">{{ $riwayat->links('pagination::bootstrap-5') }}</div>
</section>

<h5 class="mb-3">Jelajahi menu</h5>
@foreach($kategori as $k)
    @if($k->menu->count())
        <h6 class="mt-3 text-uppercase text-muted">{{ $k->nama_kategori }}</h6>
        <ul class="list-group">
            @foreach($k->menu as $m)
                <li class="list-group-item d-flex justify-content-between">
                    <span>{{ $m->nama_menu }}</span><span>Rp {{ number_format($m->harga, 0, ',', '.') }}</span>
                </li>
            @endforeach
        </ul>
    @endif
@endforeach
@endsection
