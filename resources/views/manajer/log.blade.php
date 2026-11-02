@extends('layouts.app')
@section('judul', 'Log Aktivitas')
@section('isi')
<div class="page-heading"><div><span class="eyebrow">Audit sistem</span><h1>Log aktivitas</h1><p>Aktivitas penjualan dan akses akun ditampilkan terpisah.</p></div></div>

<section class="mb-5">
    <div class="page-heading"><div><span class="eyebrow">Transaksi</span><h2>Aktivitas penjualan</h2><p>Pemesanan, pembayaran, pembatalan, dan status penyajian.</p></div></div>
    @include('partials.activity_log_table', ['logs' => $aktivitasPenjualan])
</section>

<section class="mb-5">
    <div class="page-heading"><div><span class="eyebrow">Akses akun</span><h2>Aktivitas login</h2><p>Catatan masuk dan keluar akun staf.</p></div></div>
    @include('partials.activity_log_table', ['logs' => $aktivitasLogin])
</section>

<section>
    <div class="page-heading"><div><span class="eyebrow">Pengelolaan sistem</span><h2>Aktivitas lainnya</h2><p>Perubahan akun, menu, meja, dan pengaturan outlet.</p></div></div>
    @include('partials.activity_log_table', ['logs' => $aktivitasLain])
</section>
@endsection
