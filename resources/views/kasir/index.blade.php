@extends('layouts.app')
@section('judul', 'Kasir')
@section('isi')
<div class="page-heading">
    <div><span class="eyebrow">Operasional</span><h1>Kasir</h1><p>Pantau pembayaran masuk dan pesanan aktif.</p></div>
    <span class="badge text-bg-light">{{ $menunggu->count() }} menunggu pembayaran</span>
</div>
<h5 class="mb-3">Pesanan menunggu pembayaran</h5>
@forelse($menunggu as $p)
    <div class="card mb-2"><div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div><strong>{{ $p->nomor_pesanan }}</strong> - Meja {{ $p->meja->nomor_meja }}
            <div class="small">Pemesan: {{ $p->nama_pemesan_tampilan ?: 'Tidak dicantumkan' }}</div>
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
            <span class="badge bg-secondary">{{ $p->status_pesanan }}</span>
            @if($p->pembayaran)<span class="badge text-bg-light">{{ $p->pembayaran->metode === 'qris' ? 'QRIS · cashless' : 'Tunai' }} · {{ $p->pembayaran->status_bayar === 'berhasil' ? 'Lunas' : 'Belum lunas' }}</span>@endif
        </div>
        @include('kasir._batal', ['p' => $p])
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
        <div><span class="eyebrow">Kontrol meja</span><h2>Meja sedang terisi</h2>
            <p>Periksa pelanggan yang masih menggunakan meja. Setelah pelanggan meninggalkan tempat, tandai meja sebagai tersedia.</p>
        </div>
    </div>
    <div class="row g-2">
        @forelse($mejaTerisi as $m)
            <div class="col-sm-6 col-lg-4">
                <div class="card h-100"><div class="card-body">
                    <div class="d-flex justify-content-between gap-2">
                        <strong>Meja {{ $m->nomor_meja }}</strong>
                        <span class="badge {{ $m->jumlah_pesanan_aktif > 0 ? 'bg-warning text-dark' : 'bg-success' }}">
                            {{ $m->jumlah_pesanan_aktif > 0 ? $m->jumlah_pesanan_aktif . ' pesanan aktif' : 'Tidak ada pesanan aktif' }}
                        </span>
                    </div>
                    <div class="small text-muted mt-1">{{ ucfirst($m->area) }} · {{ $m->kapasitas }} kursi</div>
                    <form method="POST" action="{{ route('kasir.meja.kosongkan', $m->id_meja) }}" class="mt-3"
                        data-confirm="Pastikan pelanggan sudah meninggalkan meja ini sebelum status diubah."
                        data-confirm-title="Buka kembali meja?"
                        data-confirm-button="Ya, tandai tersedia">
                        @csrf
                        <button class="btn btn-sm btn-outline-success w-100">
                            Tandai meja tersedia
                        </button>
                    </form>
                    @if($m->jumlah_pesanan_aktif > 0)
                        <div class="small text-muted mt-2">Jika meja dibuka kembali, pesanan sesi ini masuk riwayat dan tidak dilanjutkan dari antrean. Klik hanya setelah pelanggan pergi.</div>
                    @else
                        <div class="small text-muted mt-2">Jika pelanggan sudah pergi, klik tombol untuk memperbarui ketersediaan meja.</div>
                    @endif
                </div></div>
            </div>
        @empty
            <div class="col-12"><div class="card"><div class="card-body text-muted">Semua meja berstatus tersedia.</div></div></div>
        @endforelse
    </div>
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
