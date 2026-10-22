@extends('layouts.app')
@section('judul', 'Masuk')
@section('isi')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm"><div class="card-body">
            <h5 class="card-title mb-3">Masuk Staf</h5>
            <form method="POST" action="{{ route('login') }}">@csrf
                <div class="mb-3"><label class="form-label">Username</label>
                    <input name="username" value="{{ old('username') }}" class="form-control" autofocus required></div>
                <div class="mb-3"><label class="form-label">Kata sandi</label>
                    <input type="password" name="password" class="form-control" required></div>
                <button class="btn btn-dark w-100">Masuk</button>
            </form>
            <p class="small text-muted mt-3 mb-0">Akun contoh: kasir1, dapur1, manajer1, member1 (kata sandi: password123)</p>
        </div></div>
    </div>
</div>
@endsection
