<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') - Sistem K3 PT Pusri Palembang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="{{ asset('css/pusri.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark navbar-pusri mb-4">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
            <i class="bi bi-shield-check text-warning fs-4"></i>
            <div>
                <div>SI-K3 PUSRI</div>
                <small>PT PUPUK SRIWIDJAJA PALEMBANG</small>
            </div>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav me-auto ps-lg-3">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('incidents.index') ? 'active' : '' }}" href="{{ route('incidents.index') }}">
                        Daftar Laporan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-warning fw-semibold {{ request()->routeIs('incidents.create') ? 'active' : '' }}" href="{{ route('incidents.create') }}">
                        <i class="bi bi-camera me-1"></i> Buat Laporan
                    </a>
                </li>
                @if(auth()->user()->isAdmin())
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->is('admin*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown">
                            Admin
                        </a>
                        <ul class="dropdown-menu shadow-sm">
                            <li><a class="dropdown-item" href="{{ route('admin.users.index') }}">Kelola Akun</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.master.index') }}">Master Data</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.audit.index') }}">Audit Log</a></li>
                        </ul>
                    </li>
                @endif
                @if(auth()->user()->isAdmin() || auth()->user()->isK3())
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('incidents.export') ? 'active' : '' }}" href="{{ route('incidents.export') }}">
                            Ekspor CSV
                        </a>
                    </li>
                @endif
            </ul>
            <div class="d-flex align-items-center gap-3">
                <div class="text-white text-end lh-sm d-none d-md-block">
                    <div class="fw-semibold small">{{ auth()->user()->name }}</div>
                    <small class="badge bg-warning text-dark px-1.5 py-0.5" style="font-size: 0.65rem;">
                        {{ auth()->user()->roleLabel() }}
                    </small>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="m-0">@csrf
                    <button class="btn btn-sm btn-outline-light rounded px-2.5 py-1" title="Keluar">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<main class="container pb-5">
    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2 border-0 shadow-sm py-2">
            <i class="bi bi-check-circle-fill text-success"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center gap-2 border-0 shadow-sm py-2">
            <i class="bi bi-exclamation-triangle-fill text-danger"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm py-2">
            <div class="fw-semibold mb-1">Periksa kembali data formulir:</div>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>

<footer class="mt-auto py-3 bg-white border-top text-muted small">
    <div class="container d-flex flex-column flex-sm-row justify-content-between align-items-center gap-1">
        <div>
            PT Pupuk Sriwidjaja Palembang &copy; {{ date('Y') }} &bull; Sistem Keselamatan &amp; Kesehatan Kerja (K3)
        </div>
        <div class="text-muted">
            Departemen K3 &amp; Lingkungan Hidup
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
