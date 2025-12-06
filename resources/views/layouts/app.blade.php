<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayuhan Coffee - Admin</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
</head>
<body>

    <div class="sidebar">
        <!-- Profile Section (Bisa Diklik) -->
        <div class="profile-section" data-bs-toggle="modal" data-bs-target="#modalProfil" title="Klik untuk Edit Profil">
            <div class="bg-white rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                <i class="fas fa-user text-dark"></i>
            </div>
            <div class="text-white profile-text">
                <h6 class="m-0 fw-bold">{{ Auth::user()->name ?? 'Admin' }}</h6>
                <small style="font-size: 0.7rem; opacity: 0.7;">Role: Admin / Owner</small>
            </div>
        </div>

        <div class="sidebar-menu">
            <a href="{{ route('dashboard') }}" class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="{{ route('employee') }}" class="menu-item {{ request()->routeIs('employee') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Karyawan & Gaji
            </a>
            <a href="{{ route('menu.index') }}" class="menu-item {{ request()->routeIs('menu') ? 'active' : '' }}">
                <i class="fas fa-coffee"></i> Manajemen Menu
            </a>
            <a href="{{ route('inventory') }}" class="menu-item {{ request()->routeIs('inventory') ? 'active' : '' }}">
                <i class="fas fa-boxes"></i> Stok (Gudang/Rombong)
            </a>
            <a href="{{ route('locations.index') }}" class="menu-item {{ request()->routeIs('locations.index') ? 'active' : '' }}">
                <i class="fas fa-map-marker-alt"></i> Lokasi (Cabang & Rombong)
            </a>
            <a href="{{ route('history') }}" class="menu-item {{ request()->routeIs('history') ? 'active' : '' }}">
                <i class="fas fa-history"></i> Manajemen Transaksi
            </a>
            <a href="{{ route('admin.absensi.monitoring') }}" class="menu-item {{ request()->routeIs('absensi-monitoring') ? 'active' : '' }}">
                <i class="fas fa-history"></i> Monitoring Absensi
            </a>
        </div>

        <div class="bottom-menu">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn w-100 text-start border-0 bg-transparent">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </div>

    <div class="main-content">
        @yield('content')
    </div>

    <!-- MODAL EDIT PROFIL ADMIN -->
    <div class="modal fade" id="modalProfil" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Edit Profil</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <div style="width: 80px; height: 80px; background: #eee; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; border: 2px solid var(--accent);">
                            <i class="fas fa-user fa-3x text-muted"></i>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary mt-2">Ganti Foto</button>
                    </div>
                    
                    <form action="#" method="POST">
                        @csrf
                        <!-- 1. Email (Readonly) -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Email (Login)</label>
                            <input type="email" class="form-control bg-light" value="{{ Auth::user()->email ?? '' }}" readonly>
                        </div>

                        <!-- 2. Nama Lengkap -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Lengkap</label>
                            <input type="text" class="form-control" value="{{ Auth::user()->name ?? '' }}">
                        </div>

                        <!-- 3. No HP (BARU DITAMBAHKAN) -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold">No HP</label>
                            <input type="text" class="form-control" value="08123456789">
                        </div>

                        <!-- 4. Jabatan / Role -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Role</label>
                            <input type="text" class="form-control bg-light" value="Admin / Owner" readonly>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary-custom">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>