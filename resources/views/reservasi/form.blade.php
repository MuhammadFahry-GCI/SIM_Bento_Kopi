@extends('layouts.app')
@section('judul', 'Booking Meja ' . $meja->nomor_meja)
@section('isi')
<div class="page-heading">
    <div><span class="eyebrow">Booking meja {{ $meja->nomor_meja }}</span><h1>Atur kunjunganmu</h1><p>{{ ucfirst($meja->area) }} · {{ $meja->kapasitas }} kursi · Durasi tetap 2 jam.</p></div>
    <a class="btn btn-outline-secondary" href="{{ auth()->user()->peran === 'member' ? route('member.index', ['waktu_mulai' => old('waktu_mulai', $waktuMulai), 'durasi_jam' => $durasiJam]) : route('reservasi.index', ['waktu_mulai' => old('waktu_mulai', $waktuMulai), 'durasi_jam' => $durasiJam]) }}">Kembali ke denah</a>
</div>

<form method="POST" action="{{ route('reservasi.store', $meja->id_meja) }}">
    @csrf
    <div class="card mb-3"><div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label for="waktu_mulai" class="form-label">Waktu mulai</label>
                <input id="waktu_mulai" type="datetime-local" name="waktu_mulai" value="{{ old('waktu_mulai', $waktuMulai) }}" min="{{ now()->addMinute()->format('Y-m-d\TH:i') }}" class="form-control" required>
                <div class="form-text">Pilih jam kedatangan yang diinginkan.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Durasi booking</label>
                <input type="hidden" name="durasi_jam" value="2">
                <div class="form-control bg-light text-dark">2 jam per sesi</div>
                <div class="form-text">Sampai <span id="booking-end-preview">{{ \Illuminate\Support\Carbon::parse(old('waktu_mulai', $waktuMulai))->addHours(2)->format('d/m/Y H:i') }}</span>. Slot booking ditahan sampai lunas atau dibatalkan.</div>
            </div>
            <div class="col-md-6">
                <label for="nama_pemesan" class="form-label">Nama pemesan</label>
                <input id="nama_pemesan" name="nama_pemesan" value="{{ old('nama_pemesan', auth()->user()->peran === 'member' ? auth()->user()->nama_lengkap : '') }}" class="form-control" maxlength="100" required>
                @if(auth()->user()->peran === 'kasir')
                    <div class="form-text">Booking kasir untuk pelanggan tanpa akun member; kasir mencatat pesanan diterima saat pelanggan sudah menerimanya.</div>
                @endif
            </div>
            <div class="col-12">
                <label for="catatan" class="form-label">Catatan (opsional)</label>
                <textarea id="catatan" name="catatan" class="form-control" rows="2" maxlength="500">{{ old('catatan') }}</textarea>
            </div>
        </div>
    </div></div>

    <div class="page-heading mt-4">
        <div><span class="eyebrow">Pre-order</span><h2 class="h3">Pilih menu</h2><p>Jumlah pesanan harus mencapai minimal Rp50.000.</p></div>
        <span class="badge text-bg-light fs-6">Total pilihan: <span id="booking-total">Rp 0</span></span>
    </div>
    @include('partials.menu_inputs')

    <div class="card mt-3"><div class="card-body">
        @include('partials.payment_method', ['isKasir' => auth()->user()->peran === 'kasir'])
        @if(auth()->user()->peran === 'kasir')
            <fieldset class="mb-3">
                <legend class="form-label mb-2">Status pembayaran</legend>
                <div class="payment-methods">
                    <label class="payment-option" for="booking_status_menunggu">
                        <input class="form-check-input" type="radio" name="status_bayar" id="booking_status_menunggu" value="menunggu" @checked(old('status_bayar', 'menunggu') === 'menunggu') required>
                        <span class="payment-option-copy"><strong>Belum dibayar</strong><small>Pesanan menunggu konfirmasi pembayaran kasir.</small></span>
                    </label>
                    <label class="payment-option" for="booking_status_berhasil">
                        <input class="form-check-input" type="radio" name="status_bayar" id="booking_status_berhasil" value="berhasil" @checked(old('status_bayar') === 'berhasil') required>
                        <span class="payment-option-copy"><strong>Sudah lunas</strong><small>Pilih setelah pembayaran diterima atau diverifikasi.</small></span>
                    </label>
                </div>
            </fieldset>
        @endif
        <p class="small text-muted">Booking hanya berhasil jika total menu yang dipilih minimal Rp50.000 dan meja tersedia di waktu tersebut.</p>
        <button class="btn btn-dark w-100">Booking meja dan lanjutkan pembayaran</button>
    </div></div>
</form>
@push('scripts')
<script>
    const bookingStartInput = document.getElementById('waktu_mulai');
    const bookingEndPreview = document.getElementById('booking-end-preview');
    const updateBookingEnd = () => {
        const start = new Date(bookingStartInput.value);
        if (Number.isNaN(start.getTime())) return;
        start.setHours(start.getHours() + 2);
        bookingEndPreview.textContent = new Intl.DateTimeFormat('id-ID', {
            dateStyle: 'medium',
            timeStyle: 'short',
        }).format(start);
    };
    bookingStartInput.addEventListener('input', updateBookingEnd);

    const bookingQuantities = document.querySelectorAll('[data-menu-price]');
    const bookingTotal = document.getElementById('booking-total');
    const formatRupiah = (number) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(number);
    const updateBookingTotal = () => {
        const total = [...bookingQuantities].reduce((sum, input) => sum + (Number(input.value) || 0) * Number(input.dataset.menuPrice), 0);
        bookingTotal.textContent = formatRupiah(total);
    };
    bookingQuantities.forEach((input) => input.addEventListener('input', updateBookingTotal));
    updateBookingTotal();
</script>
@endpush
@endsection
