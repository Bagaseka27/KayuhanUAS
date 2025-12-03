<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayuhan Coffee - Barista</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

    <div class="sidebar">
        <div class="profile-section">
            <div class="bg-white rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                <i class="fas fa-user text-dark"></i>
            </div>
            <div class="text-white">
                <h6 class="m-0 fw-bold">{{ Auth::user()->name ?? 'Barista' }}</h6>
                <small style="font-size: 0.7rem; opacity: 0.7;">Role: Barista</small>
            </div>
        </div>

        <a href="{{ route('barista.dashboard') }}" class="menu-item {{ request()->routeIs('barista.dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i> Dashboard
        </a>
        
        <a href="{{ route('barista.pos') }}" class="menu-item {{ request()->routeIs('barista.pos') ? 'active' : '' }}">
            <i class="fas fa-cash-register"></i> Kasir (POS)
        </a>
        
        <a href="{{ route('barista.menu') }}" class="menu-item {{ request()->routeIs('barista.menu') ? 'active' : '' }}">
            <i class="fas fa-coffee"></i> Manajemen Menu
        </a>
        
        <a href="{{ route('barista.riwayat') }}" class="menu-item {{ request()->routeIs('barista.riwayat') ? 'active' : '' }}">
            <i class="fas fa-history"></i> Riwayat Transaksi
        </a>

        <form action="{{ route('logout') }}" method="POST" class="mt-auto">
            @csrf
            <button type="submit" class="logout-btn w-100 text-start border-0 bg-transparent">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>

    <div class="main-content">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>