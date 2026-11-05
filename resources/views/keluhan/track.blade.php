@extends('layouts.app')
@section('judul', 'Lacak Keluhan')
@section('isi')
    <div class="page-heading">
        <div><span class="eyebrow">Layanan pelanggan digital</span><h1>Lacak keluhan</h1><p>Periksa status penanganan keluhan Anda.</p></div>
    </div>
    <div class="card"><div class="card-body">
        <div class="text-muted small">Kode tiket</div>
        <div class="payment-order-number">{{ $keluhan->kode_tiket }}</div>
        <p>Topik: <strong>{{ $kategori[$keluhan->kategori] ?? 'Lainnya' }}</strong></p>
        <p>Status:
            <span class="badge {{ $keluhan->status_keluhan === 'selesai' ? 'bg-success' : ($keluhan->status_keluhan === 'diproses' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                {{ ['baru' => 'Menunggu ditinjau', 'diproses' => 'Sedang ditindaklanjuti', 'selesai' => 'Selesai'][$keluhan->status_keluhan] }}
            </span>
        </p>
        @if($keluhan->tindak_lanjut)
            <div class="rounded border p-3 mt-3">
                <div class="fw-semibold">Tindak lanjut dari Bento Kopi</div>
                <p class="mb-0 mt-2" style="white-space: pre-line">{{ $keluhan->tindak_lanjut }}</p>
            </div>
        @else
            <p class="text-muted mb-0">Belum ada tindak lanjut. Silakan simpan kode tiket di atas untuk memeriksa pembaruan.</p>
        @endif
        <p class="small text-muted mt-3 mb-0">Pesanan {{ $keluhan->pesanan->nomor_pesanan }} · Dikirim {{ $keluhan->waktu_lapor->format('d/m/Y H:i') }}</p>
    </div></div>
@endsection
