<div class="sidebar" id="sidebar">
    <!-- 1. BAGIAN PROFIL / BRAND (ATAS) -->
    <div class="sidebar-brand" title="Klik untuk Edit Profil">
        <div style="width:40px; height:40px; background:white; border-radius:50%; display:flex; align-items:center; justify-content:center; color:var(--primary); font-size:1.4rem; overflow:hidden">
            <!-- Ikon User -->
            <i class="fas fa-user"></i> 
        </div>
        <div class="d-flex flex-column" style="overflow:hidden;">
            <!-- Hardcode Nama  -->
            <span class="text-truncate fw-bold">{{ Auth::user()->name ?? 'Andi Wijaya' }}</span>
            <small style="font-size:0.7rem; opacity:0.8;">Role: Admin / Owner</small>
        </div>
    </div>
    
    <!-- 2. BAGIAN MENU NAVIGASI (TANPA BATASAN) -->
    <div class="sidebar-menu">

        <!-- 1. Dashboard -->
        <a href="{{ url('/dashboard') }}" class="menu-item {{ Request::is('dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i> Dashboard
        </a>
        
        <!-- 2. Karyawan & Gaji -->
        <a href="{{ url('/employees') }}" class="menu-item {{ Request::is('employees') ? 'active' : '' }}">
            <i class="fas fa-users"></i> Karyawan & Gaji
        </a>

        <!-- 3. Manajemen Menu -->
        <a href="{{ url('/menu') }}" class="menu-item {{ Request::is('menu') ? 'active' : '' }}">
            <i class="fas fa-coffee"></i> Manajemen Menu
        </a>

        <!-- 4. Stok -->
        <a href="{{ url('/inventory') }}" class="menu-item {{ Request::is('inventory') ? 'active' : '' }}">
            <i class="fas fa-boxes"></i> Stok (Gudang/Rombong)
        </a>

        <!-- 5. Lokasi -->
        <a href="{{ url('/locations') }}" class="menu-item {{ Request::is('locations') ? 'active' : '' }}">
            <i class="fas fa-store"></i> Lokasi (Cabang & Rombong)
        </a>

        <!-- 6. Manajemen Transaksi -->
        <a href="{{ url('/history') }}" class="menu-item {{ Request::is('history') ? 'active' : '' }}">
            <i class="fas fa-file-invoice-dollar"></i> Manajemen Transaksi
        </a>

        <a href="{{ url('/absensi-monitoring') }}" class="menu-item {{ Request::is('absensi-monitoring') ? 'active' : '' }}">
            <i class="fas fa-clipboard-check"></i> Monitoring Absensi
        </a>
        
    </div>

    <!-- 3. BAGIAN LOGOUT (BAWAH) -->
    <div class="bottom-menu px-3 w-100 position-absolute bottom-0 mb-4">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-light w-100 text-start border-0 py-2">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </button>
        </form>
    </div>
</div>