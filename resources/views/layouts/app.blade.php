<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('judul', 'SIM-Bento') | Bento Kopi Pekanbaru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
