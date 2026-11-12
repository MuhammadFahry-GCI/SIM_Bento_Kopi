@extends('layouts.app')
@section('judul', 'Bento Kopi')
@section('isi')
<style>
    .landing-page { --landing-cream: #f3eadc; --landing-gold: #d7a975; max-width: 1240px; margin: 0 auto; padding: 1.5rem 1.25rem 5rem; }
    .landing-hero { position: relative; display: grid; min-height: 510px; grid-template-columns: 1.02fr .98fr; align-items: center; gap: clamp(2rem, 5vw, 4.5rem); overflow: hidden; padding: clamp(1.5rem, 5vw, 4.5rem); border: 1px solid rgba(215,169,117,.2); border-radius: 30px; background: radial-gradient(ellipse at 9% 0, rgba(23,134,92,.22), transparent 34rem), linear-gradient(135deg, #1d2821, #131915 72%); }
    .landing-copy { position: relative; z-index: 1; }
    .landing-kicker { display: inline-flex; align-items: center; gap: .55rem; margin-bottom: 1.15rem; color: #b8d7c3; font-size: .73rem; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; }
    .landing-kicker:before { width: 24px; height: 1px; background: var(--landing-gold); content: ""; }
    .landing-title { max-width: 610px; margin: 0; color: var(--landing-cream); font-family: Georgia, "Times New Roman", serif; font-size: clamp(2.7rem, 5.4vw, 5rem); font-weight: 500; letter-spacing: -.055em; line-height: 1.02; }
    .landing-title em { color: #83cba4; font-weight: 500; }
    .landing-description { max-width: 490px; margin: 1.35rem 0 1.7rem; color: #b5c0b8; font-size: 1.02rem; line-height: 1.8; }
    .landing-actions { display: flex; flex-wrap: wrap; align-items: center; gap: .8rem; }
    .landing-primary { padding: .8rem 1.2rem; border: 1px solid #43b981; border-radius: 11px; color: #fff; background: #17865c; font-weight: 700; text-decoration: none; transition: .18s ease; }
    .landing-primary:hover { transform: translateY(-2px); border-color: #58d399; color: #fff; background: #1b996a; }
    .landing-secondary { padding: .8rem 1.2rem; border: 1px solid rgba(243,234,220,.25); border-radius: 11px; color: var(--landing-cream); font-weight: 650; text-decoration: none; transition: .18s ease; }
    .landing-secondary:hover { border-color: rgba(243,234,220,.6); color: #fff; background: rgba(255,255,255,.05); }
    .landing-note { display: flex; align-items: center; gap: .6rem; margin-top: 1.35rem; color: #9faa9f; font-size: .82rem; }
    .landing-note svg { flex: 0 0 auto; color: var(--landing-gold); }
    .landing-visual { position: relative; min-height: 390px; }
    .landing-photo { position: absolute; inset: 0 0 0 1.5rem; overflow: hidden; border: 1px solid rgba(243,234,220,.18); border-radius: 22px; background: #303832; box-shadow: 0 28px 65px rgba(0,0,0,.32); }
    .landing-photo:after { position: absolute; inset: 35% 0 0; background: linear-gradient(transparent, rgba(12,17,14,.68)); content: ""; }
    .landing-photo img { width: 100%; height: 100%; object-fit: cover; object-position: center; }
    .landing-photo-caption { position: absolute; z-index: 1; right: 1.2rem; bottom: 1.1rem; left: 1.2rem; color: #fff; }
    .landing-photo-caption strong { display: block; font-family: Georgia, "Times New Roman", serif; font-size: 1.35rem; font-weight: 500; }
    .landing-photo-caption span { color: rgba(255,255,255,.78); font-size: .82rem; }
    .landing-stamp { position: absolute; z-index: 2; top: 1.3rem; left: -.2rem; display: grid; width: 90px; height: 90px; place-content: center; border: 1px solid rgba(255,255,255,.2); border-radius: 50%; color: #fff; background: #17865c; box-shadow: 0 12px 30px rgba(0,0,0,.24); text-align: center; transform: rotate(-9deg); }
    .landing-stamp strong { font-family: Georgia, "Times New Roman", serif; font-size: 1.45rem; line-height: 1; }
    .landing-stamp span { margin-top: .25rem; font-size: .58rem; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; }
    .landing-section { padding-top: 5.2rem; scroll-margin-top: 100px; }
    .landing-section-heading { max-width: 650px; margin-bottom: 1.75rem; }
    .landing-section-heading h2 { margin: .25rem 0 .6rem; color: var(--landing-cream); font-family: Georgia, "Times New Roman", serif; font-size: clamp(2rem, 4vw, 3.2rem); font-weight: 500; letter-spacing: -.035em; }
    .landing-section-heading p { max-width: 560px; margin: 0; color: var(--bento-muted); line-height: 1.75; }
    .landing-highlights { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
    .landing-highlight { min-height: 205px; padding: 1.5rem; border: 1px solid var(--bento-border); border-radius: 18px; background: linear-gradient(145deg, rgba(255,255,255,.035), rgba(255,255,255,.012)); }
    .landing-highlight-icon { display: grid; width: 42px; height: 42px; margin-bottom: 1.1rem; place-items: center; border: 1px solid rgba(67,197,139,.25); border-radius: 13px; color: #8be0b3; background: rgba(23,134,92,.14); }
    .landing-highlight-icon svg { width: 21px; height: 21px; }
    .landing-highlight h3 { margin: 0 0 .55rem; color: var(--landing-cream); font-size: 1.03rem; font-weight: 700; }
    .landing-highlight p { margin: 0; color: var(--bento-muted); font-size: .9rem; line-height: 1.65; }
    .landing-booking-heading { display: flex; flex-wrap: wrap; align-items: end; justify-content: space-between; gap: 1rem; margin-bottom: 1.5rem; }
    .landing-booking-heading .landing-section-heading { margin: 0; }
    .booking-details { display: flex; flex-wrap: wrap; gap: .65rem; }
    .booking-detail { display: inline-flex; align-items: center; gap: .5rem; padding: .65rem .8rem; border: 1px solid var(--bento-border); border-radius: 999px; color: #d7dfd9; background: rgba(255,255,255,.025); font-size: .8rem; }
    .booking-detail strong { color: #8be0b3; }
    .booking-hero { position: relative; overflow: hidden; padding: clamp(1.25rem, 4vw, 2rem); border: 1px solid rgba(67,197,139,.2); border-radius: 20px; background: linear-gradient(120deg, rgba(23,134,92,.13), rgba(169,120,75,.08)), #171e19; }
    .booking-hero > * { position: relative; z-index: 1; }
    .landing-footer { display: flex; flex-wrap: wrap; justify-content: space-between; gap: .75rem; margin-top: 4.5rem; padding-top: 1.25rem; border-top: 1px solid var(--bento-border); color: var(--bento-muted); font-size: .8rem; }
    @media (max-width: 800px) {
        .landing-page { padding: .75rem .85rem 3.5rem; }
        .landing-hero { min-height: auto; grid-template-columns: 1fr; gap: 2rem; padding: clamp(1.5rem, 6vw, 2.5rem); }
        .landing-visual { min-height: 340px; }
        .landing-photo { inset: 0 0 0 1rem; }
        .landing-stamp { left: -.1rem; }
        .landing-highlights { grid-template-columns: 1fr; }
        .landing-highlight { min-height: auto; }
        .landing-section { padding-top: 3.5rem; }
    }
    @media (max-width: 480px) {
        .landing-title { font-size: clamp(2.5rem, 13vw, 3.5rem); }
        .landing-visual { min-height: 290px; }
        .landing-actions > a { flex: 1 1 100%; text-align: center; }
        .booking-details { gap: .45rem; }
        .booking-detail { font-size: .74rem; }
    }
</style>

<div class="landing-page">
    <section class="landing-hero" aria-labelledby="landing-title">
        <div class="landing-copy">
            <span class="landing-kicker">Bento Kopi · Pekanbaru</span>
            <h1 class="landing-title" id="landing-title">Tempat singgah,<br><em>cerita bertumbuh.</em></h1>
            <p class="landing-description">Ada waktu untuk kopi, obrolan, dan momen sederhana. Temukan suasana Bento Kopi dan rencanakan kunjunganmu dengan mudah.</p>
            <div class="landing-actions">
                <a class="landing-primary" href="#booking">Lihat meja &amp; reservasi <span aria-hidden="true">→</span></a>
                <a class="landing-secondary" href="#tentang">Kenali Bento Kopi</a>
            </div>
            <div class="landing-note">
                <svg aria-hidden="true" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                <span>Temukan suasana Bento Kopi di Pekanbaru</span>
            </div>
        </div>
        <div class="landing-visual">
            <div class="landing-stamp" aria-label="Bento Kopi"><strong>BK</strong><span>Ngopi dulu</span></div>
            <div class="landing-photo">
                <img src="{{ asset('foto bento kopi.jpg') }}" alt="Tampak depan kedai Bento Kopi" fetchpriority="high">
                <div class="landing-photo-caption"><strong>Selamat datang di Bento Kopi</strong><span>Teman untuk setiap jeda dan cerita.</span></div>
            </div>
        </div>
    </section>

    <section class="landing-section" id="tentang" aria-labelledby="about-title">
        <div class="landing-section-heading">
            <span class="eyebrow">Ruang untuk menikmati waktu</span>
            <h2 id="about-title">Kopi terasa lebih nikmat saat dinikmati bersama.</h2>
            <p>Bento Kopi hadir untuk menemani harimu—mulai dari mencari teman ngobrol, menikmati menu favorit, hingga merencanakan waktu berkumpul.</p>
        </div>
        <div class="landing-highlights" id="pengalaman">
            <article class="landing-highlight">
                <span class="landing-highlight-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 8h13v7a5 5 0 0 1-5 5H9a5 5 0 0 1-5-5V8Z"/><path d="M17 10h2a2 2 0 1 1 0 4h-2M7 4c0 1 1 1 1 2M12 3c0 1 1 1 1 2"/></svg></span>
                <h3>Teman setiap jeda</h3>
                <p>Luangkan waktu sejenak, nikmati kopi, dan isi ulang energi sebelum melanjutkan aktivitas.</p>
            </article>
            <article class="landing-highlight">
                <span class="landing-highlight-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11h18M5 11l1 9h12l1-9M8 11V7a4 4 0 0 1 8 0v4M9 15h.01M15 15h.01"/></svg></span>
                <h3>Pilihan menu untuk dinikmati</h3>
                <p>Temukan pilihan kopi dan hidangan yang pas untuk menemani obrolan atau waktu santaimu.</p>
            </article>
            <article class="landing-highlight">
                <span class="landing-highlight-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM20 8v6M23 11h-6"/></svg></span>
                <h3>Berkumpul tanpa terburu-buru</h3>
                <p>Ajak teman dan keluarga, lalu pilih meja yang tersedia agar kunjungan terasa lebih terencana.</p>
            </article>
        </div>
    </section>

    <section class="landing-section" id="booking" aria-labelledby="booking-title">
        <div class="landing-booking-heading">
            <div class="landing-section-heading">
                <span class="eyebrow">Rencanakan kunjunganmu</span>
                <h2 id="booking-title">Pilih meja, nikmati waktumu.</h2>
                <p>Pilih jadwal dan lihat ketersediaan meja sebelum datang.</p>
            </div>
            <div class="booking-details" aria-label="Informasi reservasi">
                <span class="booking-detail"><strong>2 jam</strong> durasi per sesi</span>
                <span class="booking-detail"><strong>Rp50.000</strong> minimum pre-order</span>
            </div>
        </div>

        <form method="GET" action="{{ route('reservasi.index') }}" class="card mb-4">
            <div class="card-body d-flex flex-wrap align-items-end gap-3">
                <div>
                    <label for="waktu_mulai" class="form-label">Waktu mulai booking</label>
                    <input id="waktu_mulai" type="datetime-local" name="waktu_mulai" value="{{ $waktuMulai->format('Y-m-d\TH:i') }}" min="{{ now()->addMinute()->format('Y-m-d\TH:i') }}" class="form-control" required>
                </div>
                <div>
                    <label class="form-label">Durasi</label>
                    <input type="hidden" name="durasi_jam" value="2">
                    <div class="form-control bg-light text-dark">2 jam per sesi</div>
                </div>
                <div class="pb-2 small text-muted">Sampai <span id="booking-end-preview">{{ $waktuSelesai->format('d/m/Y H:i') }}</span></div>
                <button class="btn btn-outline-success">Lihat ketersediaan</button>
            </div>
        </form>

        @include('partials.denah_meja')
        <footer class="landing-footer">
            <span>Bento Kopi Pekanbaru · Tempat cerita dimulai.</span>
            <a href="{{ route('pengetahuan.index') }}">Lihat panduan pemesanan</a>
        </footer>
    </section>
</div>
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
</script>
@endpush
@endsection
