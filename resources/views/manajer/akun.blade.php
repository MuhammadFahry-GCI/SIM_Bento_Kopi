@extends('layouts.app')
@section('judul', 'Kelola Akun')
@section('isi')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
    <div>
        <span class="text-uppercase text-muted small fw-semibold">Manajemen pengguna</span>
        <h1 class="h3 mb-1">Kelola akun</h1>
        <p class="text-muted mb-0">Buat akun member dan staf kasir atau dapur.</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h2 class="h5 mb-1">Buat akun baru</h2>
                <p class="text-muted small mb-4">Akun yang dibuat akan langsung aktif.</p>
                <form method="POST" action="{{ route('akun.store') }}">@csrf
                    <div class="mb-3">
                        <label for="nama_lengkap" class="form-label">Nama lengkap</label>
                        <input id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" class="form-control" maxlength="100" required>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input id="username" name="username" value="{{ old('username') }}" class="form-control" maxlength="50" required>
                    </div>
                    <div class="mb-3">
                        <label for="peran" class="form-label">Peran akun</label>
                        <select id="peran" name="peran" class="form-select" required>
                            <option value="member" @selected(old('peran') === 'member')>Member</option>
                            <option value="kasir" @selected(old('peran') === 'kasir')>Kasir</option>
                            <option value="dapur" @selected(old('peran') === 'dapur')>Dapur</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Kata sandi awal</label>
                        <input id="password" type="password" name="password" class="form-control" autocomplete="new-password" minlength="8" required>
                        <div class="form-text">Minimal 8 karakter.</div>
                    </div>
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">Ulangi kata sandi</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" autocomplete="new-password" minlength="8" required>
                    </div>
                    <button class="btn btn-dark w-100">Buat akun</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h5 mb-0">Daftar akun</h2>
                    <span class="badge text-bg-light">{{ $pengguna->count() }} akun</span>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Nama</th><th>Username</th><th>Peran</th><th>Status</th></tr></thead>
                        <tbody>
                        @forelse($pengguna as $akun)
                            <tr>
                                <td>{{ $akun->nama_lengkap }}</td>
                                <td>{{ $akun->username }}</td>
                                <td><span class="badge text-bg-secondary">{{ ucfirst($akun->peran) }}</span></td>
                                <td><span class="badge {{ $akun->aktif ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $akun->aktif ? 'Aktif' : 'Nonaktif' }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Belum ada akun untuk ditampilkan.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
