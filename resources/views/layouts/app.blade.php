<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('judul', 'SIM-Bento') | Bento Kopi Pekanbaru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --bento-ink: #211d19; --bento-coffee: #79583d; --bento-cream: #f5f0e8; }
        body { color: var(--bento-ink); }
        .navbar { background: var(--bento-ink) !important; }
        .auth-shell { display: grid; grid-template-columns: 1fr 1fr; max-width: 960px; min-height: 560px; margin: 4vh auto; overflow: hidden; border-radius: 24px; background: #fff; box-shadow: 0 20px 60px rgba(43, 33, 24, .12); }
        .auth-intro { position: relative; display: flex; flex-direction: column; justify-content: center; overflow: hidden; padding: 56px; color: #fff; background: linear-gradient(145deg, #34271f 5%, #79583d 100%); }
        .auth-kicker { margin-bottom: 22px; color: #e7c9a5; font-size: .75rem; font-weight: 700; letter-spacing: .18em; }
        .auth-intro h1 { position: relative; z-index: 1; margin-bottom: 18px; font-size: clamp(2.3rem, 5vw, 3.5rem); font-weight: 700; letter-spacing: -.05em; line-height: 1.05; }
        .auth-intro p { position: relative; z-index: 1; max-width: 330px; margin: 0; color: rgba(255,255,255,.76); line-height: 1.7; }
        .auth-decoration { position: absolute; right: -95px; bottom: -130px; width: 350px; height: 350px; border: 1px solid rgba(255,255,255,.16); border-radius: 50%; }
        .auth-decoration span { position: absolute; inset: 28px; border: 1px solid rgba(255,255,255,.14); border-radius: 50%; }
        .auth-decoration span:nth-child(2) { inset: 58px; }
        .auth-decoration span:nth-child(3) { inset: 88px; }
        .auth-panel { align-self: center; padding: 54px; }
        .auth-heading { display: flex; align-items: center; gap: 14px; margin-bottom: 34px; }
        .auth-icon { display: grid; width: 48px; height: 48px; flex: 0 0 48px; place-items: center; border-radius: 15px; color: #fff; background: var(--bento-coffee); font-size: .88rem; font-weight: 800; }
        .auth-heading h2 { margin: 0 0 5px; font-size: 1.35rem; font-weight: 700; }
        .auth-heading p { margin: 0; color: #77716b; font-size: .9rem; }
        .auth-form .form-label { font-size: .9rem; font-weight: 600; }
        .auth-form .form-control { min-height: 47px; border-color: #e6e0d9; border-radius: 10px; }
        .auth-form .form-control:focus { border-color: #a98564; box-shadow: 0 0 0 .2rem rgba(121,88,61,.12); }
        .auth-submit { min-height: 48px; border: 0; border-radius: 10px; background: var(--bento-ink); font-weight: 600; }
        .auth-submit:hover { background: var(--bento-coffee); }
        .auth-switch { margin: 24px 0 0; color: #77716b; font-size: .9rem; text-align: center; }
        .auth-switch a { color: var(--bento-coffee); font-weight: 700; text-decoration: none; }
        .auth-switch a:hover { text-decoration: underline; }
        @media (max-width: 767.98px) {
            .auth-shell { grid-template-columns: 1fr; margin: 1rem auto; }
            .auth-intro { min-height: 230px; padding: 32px; }
            .auth-kicker { margin-bottom: 14px; }
            .auth-intro h1 { font-size: 2.3rem; }
            .auth-intro p { max-width: 400px; }
            .auth-panel { padding: 32px; }
        }
    </style>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <span class="navbar-brand">SIM-Bento</span>
        @auth
            @php $peran = auth()->user()->peran; @endphp
            <div class="d-flex flex-wrap gap-2 align-items-center">
                @if($peran === 'kasir')
                    <a class="btn btn-sm btn-outline-light" href="{{ route('kasir.index') }}">Kasir</a>
                    <a class="btn btn-sm btn-outline-light" href="{{ route('kasir.baru') }}">Pesanan Tunai</a>
                @elseif($peran === 'dapur')
                    <a class="btn btn-sm btn-outline-light" href="{{ route('dapur.index') }}">Antrean Dapur</a>
                @elseif($peran === 'member')
                    <a class="btn btn-sm btn-outline-light" href="{{ route('member.index') }}">Beranda Member</a>
                @elseif($peran === 'pelayan')
                    <a class="btn btn-sm btn-outline-light" href="{{ route('pelayan.index') }}">Penyajian</a>
                @elseif(in_array($peran, ['manajer', 'group']))
                    <a class="btn btn-sm btn-outline-light" href="{{ route('laporan') }}">Laporan</a>
                    <a class="btn btn-sm btn-outline-light" href="{{ route('log') }}">Log Aktivitas</a>
                    @if($peran === 'manajer')
                        <a class="btn btn-sm btn-outline-light" href="{{ route('akun.index') }}">Akun</a>
                        <a class="btn btn-sm btn-outline-light" href="{{ route('menu.index') }}">Menu</a>
                        <a class="btn btn-sm btn-outline-light" href="{{ route('meja.index') }}">Meja</a>
                    @endif
                @endif
                <span class="text-light small">{{ auth()->user()->nama_lengkap }} ({{ $peran }})</span>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button class="btn btn-sm btn-danger">Keluar</button>
                </form>
            </div>
        @endauth
    </div>
</nav>
<div class="container py-3">
    @if(session('ok'))<div class="alert alert-success">{{ session('ok') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif
    @yield('isi')
</div>
@stack('scripts')
</body>
</html>
