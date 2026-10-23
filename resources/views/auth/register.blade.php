@extends('layouts.app')
@section('judul', 'Daftar Member')
@section('isi')
<div class="auth-shell">
    <section class="auth-intro">
        <span class="auth-kicker">MEMBER BENTO KOPI</span>
        <h1>Nikmati lebih<br>banyak cerita.</h1>
        <p>Buat akun member untuk memulai pengalaman Anda bersama Bento Kopi.</p>
        <div class="auth-decoration" aria-hidden="true"><span></span><span></span><span></span></div>
    </section>
    <section class="auth-panel">
        <div class="auth-heading">
            <span class="auth-icon" aria-hidden="true">BK</span>
            <div>
                <h2>Buat akun member</h2>
                <p>Isi data berikut untuk mendaftar.</p>
            </div>
        </div>
        <form method="POST" action="{{ route('register') }}" class="auth-form">@csrf
            <div class="mb-3">
                <label for="nama_lengkap" class="form-label">Nama lengkap</label>
                <input id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" class="form-control" autocomplete="name" maxlength="100" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input id="username" name="username" value="{{ old('username') }}" class="form-control" autocomplete="username" maxlength="50" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Kata sandi</label>
                <input id="password" type="password" name="password" class="form-control" autocomplete="new-password" minlength="8" required>
                <div class="form-text">Minimal 8 karakter.</div>
            </div>
            <div class="mb-4">
                <label for="password_confirmation" class="form-label">Ulangi kata sandi</label>
                <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" autocomplete="new-password" minlength="8" required>
            </div>
            <button class="btn btn-dark w-100 auth-submit">Daftar sebagai member</button>
        </form>
        <p class="auth-switch">Sudah memiliki akun? <a href="{{ route('login') }}">Masuk</a></p>
    </section>
</div>
@endsection
