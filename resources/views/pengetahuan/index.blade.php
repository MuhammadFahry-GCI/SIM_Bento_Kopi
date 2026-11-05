@extends('layouts.app')
@section('judul', $canManage ? 'Kelola Basis Pengetahuan' : 'Panduan Pelanggan')
@section('isi')
    <div class="page-heading">
        <div>
            <span class="eyebrow">Pusat pengetahuan Bento Kopi</span>
            <h1>{{ $canManage ? 'Basis pengetahuan' : 'Panduan dan FAQ' }}</h1>
            <p>Cari informasi layanan, produk, dan prosedur operasional dalam satu tempat.</p>
        </div>
        @if($canManage)
            <a href="{{ route('pengetahuan.index') }}" class="btn btn-outline-secondary">Lihat halaman publik</a>
        @endif
    </div>

    @if($canManage)
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="mb-3">Tambah pengetahuan</h5>
                <form method="POST" action="{{ route('pengetahuan.store') }}" class="row g-3">
                    @csrf
                    <div class="col-md-6">
                        <label class="form-label" for="judul">Judul</label>
                        <input id="judul" name="judul" value="{{ old('judul') }}" class="form-control" maxlength="150" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="kategori">Kelompok</label>
                        <select id="kategori" name="kategori" class="form-select" required>
                            @foreach($kategori as $pilihan)
                                <option value="{{ $pilihan }}" @selected(old('kategori') === $pilihan)>{{ $pilihan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="akses">Akses konten</label>
                        <select id="akses" name="akses" class="form-select" required>
                            <option value="publik" @selected(old('akses', 'staf') === 'publik')>Publik (pelanggan dan staf)</option>
                            <option value="staf" @selected(old('akses', 'staf') === 'staf')>Internal staf (tidak ditampilkan ke pelanggan)</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="isi">Isi</label>
                        <textarea id="isi" name="isi" class="form-control" rows="4" maxlength="20000" required>{{ old('isi') }}</textarea>
                    </div>
                    <div class="col-12"><button class="btn btn-primary">Simpan artikel</button></div>
                </form>
            </div>
        </div>
    @endif

    <form method="GET" action="{{ $canManage ? route('pengetahuan.manage') : route('pengetahuan.index') }}" class="card mb-4">
        <div class="card-body row g-2 align-items-end">
            <div class="col-md-7">
                <label for="q" class="form-label">Cari judul atau isi</label>
                <input id="q" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control" placeholder="Contoh: QRIS, Wi-Fi, pemesanan">
            </div>
            <div class="col-md-3">
                <label for="filter-kategori" class="form-label">Kelompok</label>
                <select id="filter-kategori" name="kategori" class="form-select">
                    <option value="">Semua kelompok</option>
                    @foreach($kategori as $pilihan)
                        <option value="{{ $pilihan }}" @selected(($filters['kategori'] ?? '') === $pilihan)>{{ $pilihan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-primary flex-grow-1">Cari</button>
                <a class="btn btn-outline-secondary" href="{{ $canManage ? route('pengetahuan.manage') : route('pengetahuan.index') }}">Reset</a>
            </div>
        </div>
    </form>

    <div class="row g-3">
        @forelse($artikel as $item)
            <div class="col-12">
                <article class="card">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between gap-2 mb-2">
                                <div class="d-flex gap-2">
                                    <span class="badge text-bg-success">{{ $item->kategori }}</span>
                                    @if($canManage)<span class="badge text-bg-secondary">{{ $item->akses === 'staf' ? 'Internal staf' : 'Publik' }}</span>@endif
                                </div>
                            @if($canManage)
                                <div class="d-flex gap-2">
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('pengetahuan.edit', $item->id_artikel) }}">Ubah</a>
                                    <form method="POST" action="{{ route('pengetahuan.destroy', $item->id_artikel) }}"
                                        data-confirm="Artikel ini akan dihapus dari basis pengetahuan."
                                        data-confirm-title="Hapus artikel?"
                                        data-confirm-button="Ya, hapus artikel">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                        <h2 class="h5">{{ $item->judul }}</h2>
                        <div class="text-break" style="white-space: pre-line">{{ $item->isi }}</div>
                        <p class="small text-muted mb-0 mt-3">
                            Diperbarui {{ $item->updated_at->format('d/m/Y H:i') }}
                            · {{ $item->editor?->nama_lengkap ?? $item->pembuat?->nama_lengkap ?? 'Tim Bento Kopi' }}
                        </p>
                    </div>
                </article>
            </div>
        @empty
            <div class="col-12">
                <div class="card"><div class="card-body text-muted">
                    {{ ($filters['q'] ?? null) || ($filters['kategori'] ?? null) ? 'Tidak ada artikel yang cocok dengan pencarian.' : 'Belum ada artikel pengetahuan.' }}
                </div></div>
            </div>
        @endforelse
    </div>
    <div class="mt-3">{{ $artikel->links('pagination::bootstrap-5') }}</div>
    <p class="small text-muted mt-4">Manajer outlet mengelola dan memperbarui konten. Pelanggan melihat artikel publik; staf yang masuk dapat melihat artikel publik dan internal.</p>
@endsection
