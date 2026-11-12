<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#111613">
    <title>@yield('judul', 'SIM-Bento') | Bento Kopi Pekanbaru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            color-scheme: dark;
            --bento-bg: #111613;
            --bento-surface: #1a211d;
            --bento-raised: #222b25;
            --bento-border: #303b33;
            --bento-ink: #f3f5ef;
            --bento-muted: #a4b0a7;
            --bento-green: #17865c;
            --bento-green-bright: #43c58b;
            --bento-coffee: #a9784b;
        }
        body { min-height: 100vh; color: var(--bento-ink); background: radial-gradient(ellipse at 85% 0, rgba(23,134,92,.13), transparent 34rem), var(--bento-bg); }
        a { color: var(--bento-green-bright); }
        .app-navbar { position: sticky; top: 0; z-index: 20; border-bottom: 1px solid var(--bento-border); background: rgba(17,22,19,.94); backdrop-filter: blur(18px); }
        .app-navbar-inner { display: flex; min-height: 76px; flex-wrap: wrap; align-items: center; gap: 1rem; padding-top: .6rem; padding-bottom: .6rem; }
        .brand-lockup { display: inline-flex; flex: 0 0 auto; align-items: center; gap: .75rem; color: var(--bento-ink); text-decoration: none; }
        .brand-lockup img { width: 52px; height: 52px; border-radius: 14px; object-fit: contain; background: #fff; }
        .brand-copy { display: grid; line-height: 1.15; }
        .brand-copy strong { font-size: .95rem; letter-spacing: .03em; }
        .brand-copy span { margin-top: .3rem; color: var(--bento-muted); font-size: .7rem; }
        .app-nav { display: flex; flex: 1 1 400px; flex-wrap: wrap; align-items: center; gap: .35rem; }
        .app-nav a { padding: .55rem .72rem; border: 1px solid transparent; border-radius: 10px; color: var(--bento-muted); font-size: .84rem; text-decoration: none; transition: .18s ease; }
        .app-nav a:hover, .app-nav a.active { border-color: var(--bento-border); color: #fff; background: var(--bento-raised); }
        .app-user { display: flex; align-items: center; gap: .7rem; margin-left: auto; }
        .user-chip { display: flex; align-items: center; gap: .55rem; color: var(--bento-ink); font-size: .83rem; }
        .user-dot { display: grid; width: 34px; height: 34px; place-items: center; border: 1px solid rgba(67,197,139,.3); border-radius: 50%; color: var(--bento-green-bright); background: rgba(23,134,92,.14); font-weight: 700; }
        .app-main { max-width: 1440px; min-height: calc(100vh - 76px); padding-top: 2rem; padding-bottom: 4rem; }
        .page-heading { display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: .75rem; margin-bottom: 1.5rem; }
        .page-heading h1, .page-heading h2 { margin: 0 0 .35rem; font-size: clamp(1.45rem, 3vw, 2rem); font-weight: 750; }
        .page-heading p { margin: 0; color: var(--bento-muted); }
        .eyebrow { display: inline-block; margin-bottom: .4rem; color: var(--bento-green-bright); font-size: .7rem; font-weight: 800; letter-spacing: .13em; text-transform: uppercase; }
        .card, .list-group-item, .dropdown-menu { border-color: var(--bento-border) !important; color: var(--bento-ink); background: var(--bento-surface); }
        .card { border-radius: 16px; box-shadow: 0 12px 30px rgba(0,0,0,.12); }
        .card-body { padding: 1.35rem; }
        .bg-white, .table.bg-white { color: var(--bento-ink) !important; background: var(--bento-surface) !important; }
        .border, .border-bottom, .border-top { border-color: var(--bento-border) !important; }
        .table { --bs-table-color: var(--bento-ink); --bs-table-bg: transparent; --bs-table-border-color: var(--bento-border); --bs-table-striped-color: var(--bento-ink); --bs-table-striped-bg: rgba(255,255,255,.025); --bs-table-hover-color: var(--bento-ink); --bs-table-hover-bg: rgba(67,197,139,.06); margin-bottom: 0; }
        .table > :not(caption) > * > * { padding: .8rem .75rem; }
        .table thead th { color: var(--bento-muted); font-size: .73rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; }
        .table-responsive { overflow: auto; border: 1px solid var(--bento-border); border-radius: 14px; background: var(--bento-surface); }
        .manager-list { overflow: hidden; border: 1px solid var(--bento-border); border-radius: 14px; background: var(--bento-surface); }
        .manager-list-row { display: flex; flex-wrap: wrap; align-items: center; gap: .65rem; padding: .8rem; border-bottom: 1px solid var(--bento-border); }
        .manager-inline-form { display: flex; min-width: 0; flex: 1 1 480px; flex-wrap: wrap; align-items: center; gap: .6rem; }
        .manager-inline-form .form-control, .manager-inline-form .form-select { flex: 1 1 110px; width: auto !important; }
        .form-control, .form-select { min-height: 42px; border-color: var(--bento-border); border-radius: 10px; color: var(--bento-ink); background-color: #141a16; }
        .form-control:focus, .form-select:focus { border-color: var(--bento-green-bright); color: #fff; background-color: #141a16; box-shadow: 0 0 0 .2rem rgba(67,197,139,.13); }
        .form-control::placeholder { color: #829087; }
        .form-control:disabled, .form-control[readonly] { color: var(--bento-muted); background-color: #1c241f; }
        .form-select option { color: var(--bento-ink); background: var(--bento-surface); }
        .form-check-input { border-color: #627067; background-color: #141a16; }
        .form-check-input:checked { border-color: var(--bento-green); background-color: var(--bento-green); }
        .payment-methods { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 250px), 1fr)); gap: .75rem; }
        .payment-option { display: flex; align-items: flex-start; gap: .75rem; min-height: 92px; padding: 1rem; border: 1px solid var(--bento-border); border-radius: 12px; background: #141a16; cursor: pointer; transition: border-color .15s ease, background-color .15s ease; }
        .payment-option:has(.form-check-input:checked) { border-color: var(--bento-green-bright); background: rgba(23,134,92,.12); }
        .payment-option .form-check-input { flex: 0 0 auto; margin: .2rem 0 0; }
        .payment-option-copy { display: grid; gap: .35rem; }
        .payment-option-copy strong { color: var(--bento-ink); font-size: .92rem; }
        .payment-option-copy small { color: var(--bento-muted); line-height: 1.45; }
        .payment-qr { width: min(100%, 340px); max-height: 62vh; object-fit: contain; background: #fff; }
        .payment-order-number { margin: 1.25rem 0; padding: 1rem; border: 1px dashed var(--bento-green-bright); border-radius: 12px; color: var(--bento-green-bright); font-size: 1.35rem; font-weight: 800; letter-spacing: .08em; }
        .print-only { display: none; }
        .form-label { color: #dce4dd; font-size: .87rem; font-weight: 600; }
        .form-text, .text-muted, .small.text-muted { color: var(--bento-muted) !important; }
        .btn { border-radius: 10px; font-weight: 600; transition: transform .15s ease, filter .15s ease, background-color .15s ease; }
        .btn:hover { transform: translateY(-1px); }
        .btn-dark, .btn-primary { border-color: var(--bento-green); color: #fff; background: var(--bento-green); }
        .btn-dark:hover, .btn-primary:hover { border-color: #20a672; color: #fff; background: #20a672; }
        .btn-success { border-color: #16865c; background: #16865c; }
        .btn-outline-dark { border-color: #58675d; color: var(--bento-ink); }
        .btn-outline-dark:hover { border-color: var(--bento-green); color: #fff; background: var(--bento-green); }
        .btn-outline-secondary { border-color: #455148; color: #d5ded7; }
        .btn-outline-secondary:hover { color: #fff; background: var(--bento-raised); }
        .btn-outline-success { border-color: #258d64; color: #6de0a9; }
        .btn-outline-danger { border-color: #a64e4e; color: #ff9a9a; }
        .badge { padding: .45em .7em; border-radius: 999px; font-weight: 600; }
        .text-bg-light { color: var(--bento-ink) !important; background: var(--bento-raised) !important; }
        .text-bg-secondary { background: #3b4740 !important; }
        .alert { border-radius: 12px; }
        .alert-success { border-color: rgba(67,197,139,.3); color: #a8f0ca; background: rgba(23,134,92,.18); }
        .alert-danger { border-color: rgba(255,120,120,.28); color: #ffc1c1; background: rgba(154,49,49,.16); }
        h1, h2, h3, h4, h5, h6 { color: #f2f5ef; letter-spacing: -.025em; }
        h1.h3, h5 { font-weight: 700; }
        code { color: #86e3b2; }
        .auth-shell { display: grid; grid-template-columns: 1fr 1fr; max-width: 1000px; min-height: 570px; margin: 3vh auto; overflow: hidden; border: 1px solid var(--bento-border); border-radius: 24px; background: var(--bento-surface); box-shadow: 0 24px 70px rgba(0,0,0,.3); }
        .auth-intro { position: relative; display: flex; flex-direction: column; justify-content: center; overflow: hidden; padding: 56px; color: #fff; background: linear-gradient(145deg, #123f2e, #14744e 68%, #173e2e); }
        .auth-kicker { margin-bottom: 22px; color: #a5f0c9; font-size: .75rem; font-weight: 700; letter-spacing: .18em; }
        .auth-intro h1 { position: relative; z-index: 1; margin-bottom: 18px; font-size: clamp(2.3rem, 5vw, 3.5rem); font-weight: 750; letter-spacing: -.05em; line-height: 1.05; }
        .auth-intro p { position: relative; z-index: 1; max-width: 330px; margin: 0; color: rgba(255,255,255,.78); line-height: 1.7; }
        .auth-decoration { position: absolute; right: -95px; bottom: -130px; width: 350px; height: 350px; border: 1px solid rgba(255,255,255,.16); border-radius: 50%; }
        .auth-decoration span { position: absolute; inset: 28px; border: 1px solid rgba(255,255,255,.14); border-radius: 50%; }
        .auth-decoration span:nth-child(2) { inset: 58px; }
        .auth-decoration span:nth-child(3) { inset: 88px; }
        .auth-panel { align-self: center; padding: 54px; }
        .auth-heading { display: flex; align-items: center; gap: 14px; margin-bottom: 34px; }
        .auth-icon { display: grid; width: 48px; height: 48px; flex: 0 0 48px; place-items: center; border-radius: 15px; color: #fff; background: var(--bento-green); font-size: .88rem; font-weight: 800; }
        .auth-heading h2 { margin: 0 0 5px; font-size: 1.35rem; font-weight: 700; }
        .auth-heading p { margin: 0; color: var(--bento-muted); font-size: .9rem; }
        .auth-form .form-label { font-size: .9rem; font-weight: 600; }
        .auth-form .form-control { min-height: 47px; }
        .password-toggle { display: inline-grid; min-width: 48px; place-items: center; }
        .password-toggle svg { width: 18px; height: 18px; }
        .auth-submit { min-height: 48px; border: 0; font-weight: 600; }
        .auth-switch { margin: 24px 0 0; color: var(--bento-muted); font-size: .9rem; text-align: center; }
        .auth-switch a { color: var(--bento-green-bright); font-weight: 700; text-decoration: none; }
        .auth-switch a:hover { text-decoration: underline; }
        @media (max-width: 767.98px) {
            .app-navbar-inner { gap: .6rem; }
            .app-nav { order: 3; flex-basis: 100%; flex-wrap: nowrap; overflow-x: auto; padding-bottom: .2rem; }
            .app-nav a { flex: 0 0 auto; }
            .app-user { margin-left: auto; }
            .user-chip span:last-child { display: none; }
            .app-main { padding-top: 1.25rem; }
            .auth-shell { grid-template-columns: 1fr; margin: 1rem auto; }
            .auth-intro { min-height: 220px; padding: 32px; }
            .auth-kicker { margin-bottom: 14px; }
            .auth-intro h1 { font-size: 2.3rem; }
            .auth-intro p { max-width: 400px; }
            .auth-panel { padding: 32px; }
            .table { min-width: 560px; }
            .manager-inline-form { flex-basis: 100%; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; }
        }
        @page { size: A4 landscape; margin: 12mm; }
        @media print {
            :root { color-scheme: light; }
            body { min-height: 0; color: #172019 !important; background: #fff !important; font-size: 10pt; }
            .app-navbar, .no-print { display: none !important; }
            .app-main { width: 100% !important; max-width: none !important; min-height: 0 !important; margin: 0 !important; padding: 0 !important; }
            #report-content, #report-content * { color: #172019 !important; }
            #report-content .print-only { display: block !important; }
            #report-content .eyebrow { color: #17613f !important; }
            #report-content .card, #report-content .table-responsive { border: 1px solid #cbd2cc !important; border-radius: 4px !important; background: #fff !important; box-shadow: none !important; }
            #report-content .card-body { padding: .8rem !important; }
            #report-content .table { --bs-table-color: #172019; --bs-table-bg: #fff; --bs-table-border-color: #d7ddd8; --bs-table-striped-color: #172019; --bs-table-striped-bg: #f3f6f3; font-size: 9pt; }
            #report-content .table > :not(caption) > * > * { padding: .4rem .5rem; }
            #report-content .text-muted { color: #525c55 !important; }
            #report-content .row { break-inside: avoid; }
            #report-content .card, #report-content tr { break-inside: avoid; }
            #report-content .table-responsive { overflow: visible !important; }
        }
    </style>
</head>
<body>
<header class="app-navbar">
    <div class="container app-navbar-inner">
        <a class="brand-lockup" href="{{ route('reservasi.index') }}" aria-label="Bento Kopi - beranda">
            <img src="{{ asset('images.jpg') }}" alt="">
            <span class="brand-copy"><strong>BENTO KOPI</strong><span>SIM • PEKANBARU</span></span>
        </a>
        @auth
            @php $peran = auth()->user()->peran; @endphp
            <nav class="app-nav" aria-label="Navigasi utama">
                <a class="{{ request()->routeIs('pengetahuan.index') ? 'active' : '' }}" href="{{ route('pengetahuan.index') }}">Panduan</a>
                @if($peran === 'kasir')
                    <a class="{{ request()->routeIs('kasir.index') ? 'active' : '' }}" href="{{ route('kasir.index') }}">Ringkasan kasir</a>
                    <a class="{{ request()->routeIs('kasir.baru') ? 'active' : '' }}" href="{{ route('kasir.baru') }}">Pesanan tunai</a>
                @elseif($peran === 'dapur')
                    <a class="{{ request()->routeIs('dapur.index') ? 'active' : '' }}" href="{{ route('dapur.index') }}">Antrean dapur</a>
                @elseif($peran === 'member')
                    <a class="{{ request()->routeIs('member.index') ? 'active' : '' }}" href="{{ route('member.index') }}">Beranda member</a>
                @elseif($peran === 'pelayan')
                    <a class="{{ request()->routeIs('pelayan.index') ? 'active' : '' }}" href="{{ route('pelayan.index') }}">Penyajian</a>
                @elseif(in_array($peran, ['manajer', 'group']))
                    <a class="{{ request()->routeIs('laporan') ? 'active' : '' }}" href="{{ route('laporan') }}">Laporan</a>
                    @if($peran === 'manajer')
                        <a class="{{ request()->routeIs('akun.*') ? 'active' : '' }}" href="{{ route('akun.index') }}">Akun</a>
                        <a class="{{ request()->routeIs('menu.*') ? 'active' : '' }}" href="{{ route('menu.index') }}">Menu</a>
                        <a class="{{ request()->routeIs('meja.*') ? 'active' : '' }}" href="{{ route('meja.index') }}">Meja</a>
                        <a class="{{ request()->routeIs('keluhan.manage', 'keluhan.update') ? 'active' : '' }}" href="{{ route('keluhan.manage') }}">Keluhan</a>
                        <a class="{{ request()->routeIs('pengetahuan.manage', 'pengetahuan.edit') ? 'active' : '' }}" href="{{ route('pengetahuan.manage') }}">Basis pengetahuan</a>
                        <a class="{{ request()->routeIs('log') ? 'active' : '' }}" href="{{ route('log') }}">Aktivitas</a>
                    @endif
                @endif
            </nav>
            <div class="app-user">
                <div class="user-chip">
                    <span class="user-dot" aria-hidden="true">{{ mb_strtoupper(mb_substr(auth()->user()->nama_lengkap, 0, 1)) }}</span>
                    <span>{{ auth()->user()->nama_lengkap }} <span class="text-muted">· {{ ucfirst($peran) }}</span></span>
                </div>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button class="btn btn-sm btn-outline-danger">Keluar</button>
                </form>
            </div>
        @else
            @if(request()->routeIs('reservasi.index'))
                <nav class="app-nav" aria-label="Navigasi utama">
                    <a href="#tentang">Tentang</a>
                    <a href="#pengalaman">Pengalaman</a>
                    <a href="#booking">Reservasi</a>
                </nav>
            @endif
            <div class="app-user">
                <a class="btn btn-dark px-4" href="{{ route('login') }}">Login</a>
            </div>
        @endauth
    </div>
</header>
<main class="container app-main">
    @yield('isi')
</main>
@stack('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const appAlerts = {
        success: @json(session('ok')),
        error: @json(session('error')),
        validationErrors: @json($errors->all()),
    };

    if (appAlerts.validationErrors.length > 0) {
        Swal.fire({
            icon: 'error',
            title: 'Periksa kembali data',
            html: '<ul class="text-start mb-0"></ul>',
            background: '#1a211d',
            color: '#f3f5ef',
            confirmButtonColor: '#17865c',
            didOpen: (popup) => {
                const list = popup.querySelector('ul');
                appAlerts.validationErrors.forEach((message) => {
                    const item = document.createElement('li');
                    item.textContent = message;
                    list.append(item);
                });
            },
        });
    } else if (appAlerts.success || appAlerts.error) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: appAlerts.success ? 'success' : 'error',
            titleText: appAlerts.success || appAlerts.error,
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
            background: '#1a211d',
            color: '#f3f5ef',
        });
    }

    document.addEventListener('submit', async (event) => {
        const form = event.target.closest('form[data-confirm]');
        if (!form || form.dataset.confirmed === 'true') return;

        event.preventDefault();
        const result = await Swal.fire({
            icon: 'warning',
            title: form.dataset.confirmTitle || 'Konfirmasi tindakan',
            text: form.dataset.confirm,
            showCancelButton: true,
            confirmButtonText: form.dataset.confirmButton || 'Ya, lanjutkan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#17865c',
            cancelButtonColor: '#58675d',
            background: '#1a211d',
            color: '#f3f5ef',
        });

        if (result.isConfirmed) {
            form.dataset.confirmed = 'true';
            form.requestSubmit();
        }
    });

    document.addEventListener('click', (event) => {
        const toggle = event.target.closest('[data-password-toggle]');
        if (!toggle) return;

        const input = document.getElementById(toggle.getAttribute('aria-controls'));
        if (!input) return;
        const visible = input.type === 'password';
        input.type = visible ? 'text' : 'password';
        toggle.querySelector('[data-eye-icon]').hidden = visible;
        toggle.querySelector('[data-eye-slash-icon]').hidden = !visible;
        toggle.setAttribute('aria-label', visible ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
        toggle.title = visible ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi';
        toggle.setAttribute('aria-pressed', String(visible));
    });

    const formatBookingTime = (seconds) => {
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        return hours > 0 ? `${hours}j ${minutes}m` : `${minutes}m`;
    };
    const updateBookingTimers = () => {
        const now = Date.now();
        document.querySelectorAll('[data-booking-timer]').forEach((timer) => {
            const start = new Date(timer.dataset.start).getTime();
            const end = new Date(timer.dataset.end).getTime();
            if (now < start) {
                timer.textContent = `Mulai dalam ${formatBookingTime(Math.ceil((start - now) / 1000))}`;
            } else if (now < end) {
                timer.textContent = `Sisa waktu booking: ${formatBookingTime(Math.ceil((end - now) / 1000))}`;
            } else {
                timer.textContent = timer.dataset.checkedIn === 'true'
                    ? 'Waktu booking habis · minta kasir mengosongkan meja'
                    : 'Waktu booking berakhir';
            }
        });
    };
    updateBookingTimers();
    setInterval(updateBookingTimers, 1000);
</script>
</body>
</html>
