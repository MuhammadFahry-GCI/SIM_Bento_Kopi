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
                <div class="input-group">
                    <input id="password" type="password" name="password" class="form-control" autocomplete="current-password" required>
                    <button class="btn btn-outline-secondary password-toggle" type="button" data-password-toggle aria-label="Tampilkan kata sandi" title="Tampilkan kata sandi" aria-controls="password" aria-pressed="false">
                        <svg data-eye-icon aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg data-eye-slash-icon aria-hidden="true" hidden viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8"/><path d="M9.9 5.2A10.7 10.7 0 0 1 12 5c6.4 0 10 7 10 7a15.7 15.7 0 0 1-3.1 3.9M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7a10.7 10.7 0 0 0 4.1-.8"/></svg>
                    </button>
                </div>
            </div>
            <button class="btn btn-dark w-100 auth-submit">Masuk</button>
        </form>
        <p class="auth-switch">Belum memiliki akun? <a href="{{ route('register') }}">Daftar sebagai member</a></p>
    </section>
</div>
@endsection
