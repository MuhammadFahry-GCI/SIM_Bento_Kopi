@extends('layouts.app')
@section('judul', 'Dashboard Manajemen')
@section('isi')
    <div id="report-content">
        <div class="page-heading">
            <div>
                <span class="eyebrow">Informasi manajemen Bento Kopi</span>
                <h1>Dashboard manajemen</h1>
                <p>Indikator operasional dan penjualan yang dihitung dari transaksi tersimpan.</p>
            </div>
            <div class="d-flex flex-column align-items-md-end gap-2 no-print">
                <button type="button" class="btn btn-success" onclick="window.print()">Cetak / Simpan PDF</button>
            </div>
        </div>
        <p class="small text-muted print-only">Dicetak pada {{ now()->format('d/m/Y H:i') }} WIB</p>

        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl">
                <div class="card h-100"><div class="card-body">
                    <div class="text-muted small">Omzet hari ini</div>
                    <div class="fs-4 fw-bold">Rp {{ number_format($ringkasanHariIni->total, 0, ',', '.') }}</div>
                    <div class="small text-muted mt-2">Membantu memantau pencapaian penjualan harian.</div>
                </div></div>
            </div>
            <div class="col-sm-6 col-xl">
                <div class="card h-100"><div class="card-body">
                    <div class="text-muted small">Transaksi dibayar hari ini</div>
                    <div class="fs-4 fw-bold">{{ number_format($ringkasanHariIni->jumlah, 0, ',', '.') }}</div>
                    <div class="small text-muted mt-2">Mendukung penjadwalan staf dan persiapan operasional.</div>
                </div></div>
            </div>
            <div class="col-sm-6 col-xl">
                <div class="card h-100"><div class="card-body">
                    <div class="text-muted small">Rata-rata nilai transaksi</div>
                    <div class="fs-4 fw-bold">{{ $rataPesanan !== null ? 'Rp ' . number_format($rataPesanan, 0, ',', '.') : 'Belum ada data' }}</div>
                    <div class="small text-muted mt-2">Dari {{ number_format($jumlahPesananDibayar, 0, ',', '.') }} transaksi lunas; membantu mengevaluasi paket dan promosi.</div>
                </div></div>
            </div>
            <div class="col-sm-6 col-xl">
                <div class="card h-100"><div class="card-body">
                    <div class="text-muted small">Waktu penyajian rata-rata</div>
                    <div class="fs-4 fw-bold">{{ $rataDetik !== null ? number_format($rataDetik / 60, 1, ',', '.') . ' menit' : 'Belum ada data' }}</div>
                    <div class="small text-muted mt-2">Mendukung evaluasi kapasitas dan proses dapur.</div>
                </div></div>
            </div>
            <div class="col-sm-6 col-xl">
                <div class="card h-100"><div class="card-body">
                    <div class="text-muted small">Keluhan perlu ditangani</div>
                    <div class="fs-4 fw-bold">{{ number_format($jumlahKeluhanTerbuka, 0, ',', '.') }}</div>
                    <div class="small text-muted mt-2">Membantu manajer memprioritaskan layanan pelanggan.</div>
                </div></div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            @foreach($perMetode as $r)
                <div class="col-md-4">
                    <div class="card h-100"><div class="card-body">
                        <div class="text-muted small">Penjualan {{ strtoupper($r->metode) }} · {{ $r->jumlah }} transaksi</div>
                        <div class="fs-5 fw-bold">Rp {{ number_format($r->total, 0, ',', '.') }}</div>
                        <div class="small text-muted mt-2">Membantu mengevaluasi penggunaan kanal pembayaran.</div>
                    </div></div>
                </div>
            @endforeach
        </div>

        <div class="row g-3 mb-4">
            <div class="col-lg-7">
                <section class="card h-100">
                    <div class="card-body">
                        <h2 class="h5">Omzet harian · 7 hari terakhir</h2>
                        <p class="small text-muted">Gunakan pola naik-turun untuk merencanakan stok dan kebutuhan staf per hari.</p>
                        <div class="d-flex align-items-end justify-content-around gap-2 border-bottom pb-2" role="img" aria-label="Grafik batang omzet harian 7 hari terakhir">
                            @foreach($perHari as $hari)
                                @php $tinggi = $maksOmzet > 0 ? max(4, ($hari->total / $maksOmzet) * 100) : 4; @endphp
                                <div class="d-flex flex-column align-items-center gap-2" style="height: 190px; flex: 1 1 0">
                                    <span class="small text-muted text-nowrap">{{ $hari->total > 0 ? number_format($hari->total / 1000, 0, ',', '.') . ' rb' : '—' }}</span>
                                    <div class="d-flex align-items-end w-100" style="flex: 1">
                                        <div class="rounded-top w-100" style="height: {{ $tinggi }}%; min-height: 4px; background: linear-gradient(180deg, #43c58b, #17865c)" title="{{ $hari->tanggal }}: Rp {{ number_format($hari->total, 0, ',', '.') }}"></div>
                                    </div>
                                    <span class="small text-muted">{{ $hari->label }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>
            </div>
            <div class="col-lg-5">
                <section class="card h-100">
                    <div class="card-body">
                        <h2 class="h5">Sebaran status pesanan</h2>
                        <p class="small text-muted">Pantau antrean untuk mengatur kapasitas kasir, dapur, dan pelayanan.</p>
                        <div class="d-grid gap-3" role="img" aria-label="Grafik batang horizontal jumlah pesanan berdasarkan status">
                            @foreach($perStatus as $status)
                                @php $lebar = $maksStatus > 0 ? ($status->jumlah / $maksStatus) * 100 : 0; @endphp
                                <div>
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span>{{ $status->label }}</span><strong>{{ $status->jumlah }}</strong>
                                    </div>
                                    <div class="progress" role="presentation" style="height: 9px; background: #303b33">
                                        <div class="progress-bar" style="width: {{ $lebar }}%; background: #43c58b"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-lg-8">
                <section class="card h-100">
                    <div class="card-body">
                        <h2 class="h5 mb-1">Analisis menu terlaris</h2>
                        <p class="small text-muted mb-3">Data penjualan lunas membantu menentukan prioritas stok, promosi, dan evaluasi menu.</p>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle">
                                <thead><tr><th>Menu</th><th>Terjual</th><th>Omzet</th></tr></thead>
                                <tbody>
                                    @forelse($perMenu as $r)
                                        <tr>
                                            <td>{{ $r->nama_menu }}</td>
                                            <td>{{ number_format($r->qty, 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($r->omzet, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-muted">Belum ada data penjualan lunas.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
            <div class="col-lg-4">
                <section class="card h-100">
                    <div class="card-body">
                        <h2 class="h5">Skenario keputusan</h2>
                        @if($perMenu->isNotEmpty())
                            <p>Jika tren saat ini berlanjut, <strong>{{ $perMenu->first()->nama_menu }}</strong> adalah menu dengan penjualan tertinggi ({{ number_format($perMenu->first()->qty, 0, ',', '.') }} terjual). Manajer dapat memastikan stok bahan dan kesiapan produksi menu tersebut.</p>
                        @else
                            <p>Belum ada transaksi lunas untuk dibandingkan. Manajer dapat menunggu data penjualan sebelum mengubah stok atau promosi menu.</p>
                        @endif
                        @if($jumlahKeluhanTerbuka > 0)
                            <p class="mb-0">Ada <strong>{{ $jumlahKeluhanTerbuka }} keluhan</strong> yang belum selesai. Prioritaskan peninjauan dan dokumentasikan tindak lanjut pada halaman Keluhan.</p>
                        @else
                            <p class="mb-0">Tidak ada keluhan terbuka. Pertahankan pemantauan layanan melalui tiket pelanggan.</p>
                        @endif
                    </div>
                </section>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-7">
                <section class="card h-100">
                    <div class="card-body">
                        <h2 class="h5 mb-1">Rincian penjualan · 7 hari terakhir</h2>
                        <p class="small text-muted mb-3">Mendukung perbandingan kinerja harian dan perencanaan jadwal operasional.</p>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead><tr><th>Tanggal</th><th>Transaksi</th><th>Total</th></tr></thead>
                                <tbody>
                                    @foreach($perHari as $r)
                                        <tr><td>{{ $r->tanggal }}</td><td>{{ $r->jumlah }}</td><td>Rp {{ number_format($r->total, 0, ',', '.') }}</td></tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
            <div class="col-lg-5">
                <section class="card h-100">
                    <div class="card-body">
                        <h2 class="h5 mb-1">Metode pembayaran</h2>
                        <p class="small text-muted mb-3">Komposisi transaksi lunas mendukung evaluasi kanal pembayaran.</p>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead><tr><th>Metode</th><th>Transaksi</th><th>Omzet</th></tr></thead>
                                <tbody>
                                    @forelse($perMetode as $r)
                                        <tr><td>{{ strtoupper($r->metode) }}</td><td>{{ $r->jumlah }}</td><td>Rp {{ number_format($r->total, 0, ',', '.') }}</td></tr>
                                    @empty
                                        <tr><td colspan="3" class="text-muted">Belum ada pembayaran berhasil.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <h2 class="h5 mt-4 mb-3">Pesanan dibatalkan dan alasannya</h2>
        <p class="small text-muted">Alasan pembatalan dapat membantu menemukan kendala pemesanan atau pembayaran yang perlu diperbaiki.</p>
        <div class="table-responsive">
            <table class="table table-sm">
                <thead><tr><th>Nomor</th><th>Waktu</th><th>Alasan</th></tr></thead>
                <tbody>
                    @forelse($dibatalkan as $p)
                        <tr><td>{{ $p->nomor_pesanan }}</td><td>{{ $p->waktu_pesan->format('d/m H:i') }}</td><td>{{ $p->alasan_batal }}</td></tr>
                    @empty
                        <tr><td colspan="3" class="text-muted">Tidak ada pembatalan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
