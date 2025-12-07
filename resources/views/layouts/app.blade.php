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
    <button class="mobile-toggle-btn" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>

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
            <a href="{{ route('employee.index') }}" class="menu-item {{ request()->routeIs('employee.index') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Manajemen Karyawan
            </a>
            <a href="{{ route('menu.index') }}" class="menu-item {{ request()->routeIs('menu') ? 'active' : '' }}">
                <i class="fas fa-coffee"></i> Manajemen Menu
            </a>

            <a href="{{ route('inventory.index') }}" class="menu-item {{ request()->routeIs('inventory.index') ? 'active' : '' }}">
                <i class="fas fa-boxes"></i> Stok (Gudang/Rombong)
            </a>
            <a href="{{ route('locations.index') }}" class="menu-item {{ request()->routeIs('locations.index') ? 'active' : '' }}">
                <i class="fas fa-map-marker-alt"></i> Manajemen Lokasi
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
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Edit Profil</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        
                        <!-- FOTO PROFIL -->
                        <div class="text-center mb-3">
                            <div style="width: 90px; height: 90px; overflow: hidden; border-radius: 50%; margin: auto; border: 2px solid var(--accent); background: #eee;">
                                <img id="previewFoto" 
                                    src="{{ Auth::user()->foto ? asset('storage/'.Auth::user()->foto) : 'https://via.placeholder.com/90' }}" 
                                    class="w-100 h-100" 
                                    style="object-fit: cover;">
                            </div>

                            <label class="btn btn-outline-secondary btn-sm mt-2">
                                Ganti Foto
                                <input type="file" name="foto" class="d-none" accept="image/*" onchange="previewImage(event)">
                            </label>
                        </div>

                        <!-- EMAIL -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Email (Login)</label>
                            <input type="email" class="form-control bg-light" value="{{ Auth::user()->email }}" readonly>
                        </div>

                        <!-- NAMA -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Lengkap</label>
                            <input type="text" class="form-control" name="name" value="{{ Auth::user()->name }}" required>
                        </div>

                        <!-- NO HP -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold">No HP</label>
                            <input type="text" class="form-control" name="no_hp" 
                                value="{{ Auth::user()->no_hp ?? '' }}" required>
                        </div>

                        <!-- ROLE -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Role</label>
                            <input type="text" class="form-control bg-light" value="{{ Auth::user()->role }}" readonly>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary-custom">Simpan Perubahan</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
   @stack('scripts')
    <script>

        function previewImage(event) {
            const output = document.getElementById('previewFoto');
            output.src = URL.createObjectURL(event.target.files[0]);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');
            let hideTimer;
            let isSidebarOpen = false;

            // Fungsi untuk menyembunyikan tombol dengan class 'd-none'
            function hideButton() {
                // Sembunyikan hanya jika sidebar TIDAK terbuka
                if (toggleBtn && !isSidebarOpen) {
                    toggleBtn.classList.add('d-none');
                }
            }

            // Fungsi untuk menampilkan tombol dan mengatur timer
            function showButton() {
                if (toggleBtn) {
                    toggleBtn.classList.remove('d-none');
                    
                    // Reset timer setiap kali tombol ditampilkan
                    clearTimeout(hideTimer);
                    hideTimer = setTimeout(hideButton, 3000); // Tombol menghilang setelah 3 detik (3000 ms)
                }
            }

            // 1. Event listener untuk toggle sidebar (yang sudah ada)
            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener('click', function() {
                    // Perbarui status sidebar
                    isSidebarOpen = !sidebar.classList.contains('show');
                    sidebar.classList.toggle('show');
                    
                    if (isSidebarOpen) {
                        // Jika sidebar terbuka, jangan sembunyikan tombol
                        clearTimeout(hideTimer);
                    } else {
                        // Jika sidebar ditutup, sembunyikan tombol setelah 3 detik
                        showButton(); 
                    }
                });
            }
            
            // 2. Event listener untuk mendeteksi scroll/sentuhan pada dokumen (mobile behavior)
            // Menggunakan document.documentElement untuk menangkap scroll global
            document.addEventListener('scroll', showButton);
            document.addEventListener('touchstart', showButton);
            document.addEventListener('mousemove', showButton); // Tambahkan mousemove untuk desktop debugging

            // 3. Panggil sekali saat dimuat untuk memulai timer
            showButton(); 
        });
    </script>
</body>
</html>