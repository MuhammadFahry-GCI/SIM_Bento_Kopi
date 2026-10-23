@extends('layouts.app')
@section('judul', 'Masuk')
@section('isi')
<div class="auth-shell">
    <section class="auth-intro">
        <span class="auth-kicker">BENTO KOPI PEKANBARU</span>
        <h1>Selamat datang<br>kembali.</h1>
        <p>Masuk untuk melanjutkan pengalaman dan aktivitas Anda di Bento Kopi.</p>
        <div class="auth-decoration" aria-hidden="true"><span></span><span></span><span></span></div>
    </section>
    <section class="auth-panel">
        <div class="auth-heading">
            <span class="auth-icon" aria-hidden="true">BK</span>
            <div>
                <h2>Masuk ke akun</h2>
                <p>Gunakan username dan kata sandi Anda.</p>
            </div>
        </div>
        <form method="POST" action="{{ route('login') }}" class="auth-form">@csrf
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input id="username" name="username" value="{{ old('username') }}" class="form-control" autocomplete="username" autofocus required>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">Kata sandi</label>
                <input id="password" type="password" name="password" class="form-control" autocomplete="current-password" required>
            </div>
            <button class="btn btn-dark w-100 auth-submit">Masuk</button>
        </form>
        <p class="auth-switch">Belum memiliki akun? <a href="{{ route('register') }}">Daftar sebagai member</a></p>
    </section>
</div>
@endsection
