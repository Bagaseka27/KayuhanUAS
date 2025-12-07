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
    <button class="mobile-toggle-btn" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>

    <div class="sidebar">
        <div class="profile-section" data-bs-toggle="modal" data-bs-target="#modalProfil" title="Klik untuk Edit Profil">
            <div class="bg-white rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; overflow: hidden;">
                @if(Auth::user()->foto)
                    <img src="{{ asset('storage/' . Auth::user()->foto) }}" style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    <i class="fas fa-user text-dark"></i>
                @endif
            </div>
            <div class="text-white profile-text">
                <h6 class="m-0 fw-bold">{{ Auth::user()->name ?? 'Barista' }}</h6>
                <small style="font-size: 0.7rem; opacity: 0.7;">Role: Barista</small>
            </div>
        </div>

        <div class="sidebar-menu">
            <a href="{{ route('barista.dashboard') }}" class="menu-item {{ request()->routeIs('barista.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="{{ route('barista.pos') }}" class="menu-item {{ request()->routeIs('barista.pos') ? 'active' : '' }}">
                <i class="fas fa-cash-register"></i> Kasir (POS)
            </a>
            <a href="{{ route('barista.menu') }}" class="menu-item {{ request()->routeIs('barista.menu') ? 'active' : '' }}">
                <i class="fas fa-coffee"></i> Manajemen Menu
            </a>
            <a href="{{ route('barista.absensi.index') }}" class="menu-item {{ request()->routeIs('barista.absensi.*') ? 'active' : '' }}">
                <i class="fas fa-clipboard-check"></i> Presensi
            </a>
            <a href="{{ route('barista.riwayat') }}" class="menu-item {{ request()->routeIs('barista.riwayat') ? 'active' : '' }}">
                <i class="fas fa-history"></i> Riwayat Transaksi
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
        <div class="container-fluid">
            <div class="white-content-wrapper"> 
            
                @yield('content') 
            
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalProfil" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Edit Profil</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <div style="width: 100px; height: 100px; background: #eee; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; border: 3px solid var(--accent); overflow: hidden;">
                                <img id="avatarPreview" 
                                    src="{{ Auth::user()->foto ? asset('storage/' . Auth::user()->foto) : '' }}" 
                                    class="{{ Auth::user()->foto ? '' : 'd-none' }}" 
                                    style="width: 100%; height: 100%; object-fit: cover;">
                                
                                <i id="avatarIcon" class="fas fa-user fa-3x text-muted {{ Auth::user()->foto ? 'd-none' : '' }}"></i>
                            </div>
                            
                            <div class="mt-2">
                                <label for="fotoInput" class="btn btn-sm btn-outline-secondary" style="cursor: pointer;">
                                    <i class="fas fa-camera me-1"></i> Ganti Foto
                                </label>
                                <input type="file" id="fotoInput" name="foto" class="d-none" accept="image/*" onchange="previewImage(event)">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Email (Login)</label>
                            <input type="email" class="form-control bg-light" value="{{ Auth::user()->email }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" value="{{ Auth::user()->name }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">No HP</label>
                            <input type="text" name="no_hp" class="form-control" value="{{ Auth::user()->no_hp }}">
                        </div>


                        <div class="mb-3">
                            <label class="form-label small fw-bold">Jabatan / Role</label>
                            <input type="text" class="form-control bg-light" value="Barista" readonly>
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
    
    <script>
        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById('avatarPreview');
            const icon = document.getElementById('avatarIcon');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                    icon.classList.add('d-none');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    
    @stack('scripts')
    <script>
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