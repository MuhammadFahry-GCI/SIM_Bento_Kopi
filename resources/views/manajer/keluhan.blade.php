@extends('layouts.app')
@section('judul', 'Keluhan Pelanggan')
@section('isi')
    <div class="page-heading">
        <div><span class="eyebrow">Tindak lanjut layanan digital</span><h1>Keluhan pelanggan</h1>
            <p>Tinjau permintaan pelanggan, berikan tindak lanjut, dan ubah status penyelesaian.</p></div>
    </div>
    <div class="d-grid gap-3">
        @forelse($keluhan as $item)
            <article class="card"><div class="card-body">
                <div class="d-flex flex-wrap justify-content-between gap-2">
                    <div>
                        <span class="badge text-bg-success">{{ $kategori[$item->kategori] ?? $item->kategori }}</span>
                        <span class="small text-muted ms-2">{{ $item->waktu_lapor->format('d/m/Y H:i') }}</span>
                        <h2 class="h5 mt-2 mb-1">{{ $item->nama_pelapor ?: 'Pelanggan' }} · Pesanan {{ $item->pesanan?->nomor_pesanan ?? '—' }}</h2>
                        <div class="small text-muted">Kontak: {{ $item->kontak_pelapor ?: 'Tidak dicantumkan' }} · Tiket:
                            @if($item->kode_tiket)<a href="{{ route('keluhan.track', $item->kode_tiket) }}">{{ $item->kode_tiket }}</a>@else belum tersedia untuk keluhan lama @endif
                        </div>
                    </div>
                    <span class="badge {{ $item->status_keluhan === 'selesai' ? 'bg-success' : ($item->status_keluhan === 'diproses' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                        {{ ucfirst($item->status_keluhan) }}
                    </span>
                </div>
                <p class="my-3" style="white-space: pre-line">{{ $item->deskripsi }}</p>
                <form method="POST" action="{{ route('keluhan.update', $item->id_keluhan) }}" class="row g-2 align-items-end">
                    @csrf @method('PUT')
                    <div class="col-md-3">
                        <label class="form-label" for="status-{{ $item->id_keluhan }}">Status</label>
                        <select id="status-{{ $item->id_keluhan }}" name="status_keluhan" class="form-select" required>
                            @foreach(['baru' => 'Baru', 'diproses' => 'Diproses', 'selesai' => 'Selesai'] as $value => $label)
                                <option value="{{ $value }}" @selected($item->status_keluhan === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-7">
                        <label class="form-label" for="tindak-lanjut-{{ $item->id_keluhan }}">Tindak lanjut (wajib saat selesai)</label>
                        <textarea id="tindak-lanjut-{{ $item->id_keluhan }}" name="tindak_lanjut" class="form-control" rows="2" maxlength="2000">{{ $item->tindak_lanjut }}</textarea>
                    </div>
                    <div class="col-md-2"><button class="btn btn-primary w-100">Simpan</button></div>
                </form>
                @if($item->penangan)<p class="small text-muted mt-2 mb-0">Ditangani oleh {{ $item->penangan->nama_lengkap }}</p>@endif
            </div></article>
        @empty
            <div class="card"><div class="card-body text-muted">Belum ada keluhan pelanggan.</div></div>
        @endforelse
    </div>
    <div class="mt-3">{{ $keluhan->links('pagination::bootstrap-5') }}</div>
@endsection
