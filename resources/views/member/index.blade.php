@extends('layouts.app')
@section('judul', 'Beranda Member')
@section('isi')
<div class="page-heading"><div><span class="eyebrow">Bento Kopi member</span><h1>Halo, {{ auth()->user()->nama_lengkap }}</h1><p>Pilih waktu kunjungan dan meja untuk membuat booking seperti di landing page.</p></div></div>

<section class="mb-5">
    <div class="page-heading">
        <div><span class="eyebrow">Rencanakan kunjunganmu</span><h2>Pilih meja, nikmati waktumu.</h2><p>Denah dan proses booking di beranda ini menggunakan jadwal serta status meja yang sama dengan landing page.</p></div>
        <span class="badge text-bg-light fs-6">Durasi booking: 2 jam</span>
    </div>
    <form method="GET" action="{{ route('member.index') }}" class="card mb-4">
        <div class="card-body d-flex flex-wrap align-items-end gap-3">
            <div>
                <label for="waktu_mulai" class="form-label">Waktu mulai booking</label>
                <input id="waktu_mulai" type="datetime-local" name="waktu_mulai" value="{{ $waktuMulai->format('Y-m-d\TH:i') }}" min="{{ now()->addMinute()->format('Y-m-d\TH:i') }}" class="form-control" required>
            </div>
            <div>
                <label class="form-label">Durasi</label>
                <input type="hidden" name="durasi_jam" value="2">
                <div class="form-control bg-light text-dark">2 jam per sesi</div>
            </div>
            <div class="pb-2 small text-muted">Sampai <span id="booking-end-preview">{{ $waktuSelesai->format('d/m/Y H:i') }}</span></div>
            <button class="btn btn-outline-success">Lihat ketersediaan</button>
        </div>
    </form>
    @include('partials.denah_meja')
</section>

<section class="mb-5">
    <div class="page-heading">
        <div><span class="eyebrow">Jadwal kunjungan</span><h2>Booking meja Anda</h2><p>Slot ditahan saat pembayaran menunggu. Setelah lunas booking terkonfirmasi, dan meja baru terisi ketika member menerima pesanan. Kasir mengosongkan meja saat pelanggan pergi.</p></div>
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
                    <span class="badge {{ $booking->status_reservasi === 'menunggu_pembayaran' ? 'bg-warning text-dark' : ($booking->waktu_diterima ? 'bg-success' : ($booking->waktu_mulai->isFuture() ? 'bg-info text-dark' : 'bg-danger')) }}">
                        {{ $booking->status_reservasi === 'menunggu_pembayaran' ? 'Menunggu pembayaran · slot ditahan' : ($booking->waktu_diterima ? 'Pesanan diterima · meja terisi' : ($booking->waktu_mulai->isFuture() ? 'Booking terkonfirmasi · akan datang' : ($booking->waktu_checkin ? 'Check-in · menunggu pesanan' : 'Booking terkonfirmasi · menunggu pesanan')) ) }}
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
                @if($booking->status_reservasi === 'menunggu_pembayaran')
                    <form method="POST" action="{{ route('member.reservasi.batal', $booking->id_reservasi) }}" class="mt-2"
                        data-confirm="Slot meja akan dilepas dan pesanan pre-order dibatalkan."
                        data-confirm-title="Batalkan booking?"
                        data-confirm-button="Ya, batalkan booking">
                        @csrf
                        <button class="btn btn-sm btn-outline-danger">Batalkan booking belum lunas</button>
                    </form>
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
@push('scripts')
<script>
    const bookingStartInput = document.getElementById('waktu_mulai');
    const bookingEndPreview = document.getElementById('booking-end-preview');
    const updateBookingEnd = () => {
        const start = new Date(bookingStartInput.value);
        if (Number.isNaN(start.getTime())) return;
        start.setHours(start.getHours() + 2);
        bookingEndPreview.textContent = new Intl.DateTimeFormat('id-ID', {
            dateStyle: 'medium',
            timeStyle: 'short',
        }).format(start);
    };
    bookingStartInput.addEventListener('input', updateBookingEnd);
</script>
@endpush
@endsection
