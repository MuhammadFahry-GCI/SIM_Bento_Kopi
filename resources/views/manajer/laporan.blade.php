@extends('layouts.app')
@section('judul', 'Laporan')
@section('isi')
    <div id="report-content">
        <div class="page-heading">
            <div><span class="eyebrow">Ringkasan outlet</span>
                <h1>Laporan</h1>
                <p>Performa penjualan dan operasional Bento Kopi.</p>
            </div>
            <div class="d-flex flex-column align-items-md-end gap-2 no-print">
                <button type="button" class="btn btn-success" onclick="window.print()">Cetak / Simpan PDF</button>
            </div>
        </div>
        <p class="small text-muted print-only">Dicetak pada {{ now()->format('d/m/Y H:i') }} WIB</p>
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted small">Waktu penyajian rata-rata</div>
                        <div class="fs-3 fw-bold">
                            {{ $rataDetik ? number_format($rataDetik / 60, 1, ',', '.') . ' menit' : 'Belum ada data' }}
                        </div>
                    </div>
                </div>
            </div>
            @foreach ($perMetode as $r)
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-muted small">Penjualan {{ strtoupper($r->metode) }} ({{ $r->jumlah }}
                                transaksi)</div>
                            <div class="fs-3 fw-bold">Rp {{ number_format($r->total, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="mb-3">Penjualan 7 hari terakhir</h5>
                        <table class="table table-sm bg-white">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Transaksi</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($perHari as $r)
                                    <tr>
                                        <td>{{ $r->tanggal }}</td>
                                        <td>{{ $r->jumlah }}</td>
                                        <td>Rp {{ number_format($r->total, 0, ',', '.') }}</td>
                                    </tr>
                                @empty<tr>
                                        <td colspan="3" class="text-muted">Belum ada data.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="mb-3">Menu terlaris</h5>
                        <table class="table table-sm bg-white">
                            <thead>
                                <tr>
                                    <th>Menu</th>
                                    <th>Terjual</th>
                                    <th>Omzet</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($perMenu as $r)
                                    <tr>
                                        <td>{{ $r->nama_menu }}</td>
                                        <td>{{ $r->qty }}</td>
                                        <td>Rp {{ number_format($r->omzet, 0, ',', '.') }}</td>
                                    </tr>
                                @empty<tr>
                                        <td colspan="3" class="text-muted">Belum ada data.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <h5 class="mt-4 mb-3">Pesanan dibatalkan dan alasannya</h5>
        <table class="table table-sm bg-white">
            <thead>
                <tr>
                    <th>Nomor</th>
                    <th>Waktu</th>
                    <th>Alasan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dibatalkan as $p)
                    <tr>
                        <td>{{ $p->nomor_pesanan }}</td>
                        <td>{{ $p->waktu_pesan->format('d/m H:i') }}</td>
                        <td>{{ $p->alasan_batal }}</td>
                    </tr>
                @empty<tr>
                        <td colspan="3" class="text-muted">Tidak ada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
