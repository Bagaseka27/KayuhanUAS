<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayuhan Coffee - Barista</title>
    
    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

    <div class="sidebar">
        <!-- 1. BAGIAN PROFIL (KLIK UNTUK EDIT) -->
        <!-- Atribut data-bs-toggle="modal" ini yang bikin popup muncul -->
        <div class="profile-section" data-bs-toggle="modal" data-bs-target="#modalProfil" title="Klik untuk Edit Profil">
            <div class="bg-white rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; overflow: hidden;">
                <!-- Menampilkan Foto Profil Asli jika ada -->
                @if(Auth::user()->photo)
                    <img src="{{ asset('storage/' . Auth::user()->photo) }}" style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    <i class="fas fa-user text-dark"></i>
                @endif
            </div>
            <div class="text-white profile-text">
                <h6 class="m-0 fw-bold">{{ Auth::user()->name ?? 'Barista' }}</h6>
                <small style="font-size: 0.7rem; opacity: 0.7;">Role: Barista</small>
            </div>
        </div>

        <!-- 2. MENU NAVIGASI -->
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

        <!-- 3. TOMBOL LOGOUT -->
        <div class="bottom-menu">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn w-100 text-start border-0 bg-transparent">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="white-content-wrapper"> 
            
                @yield('content') 
            
            </div>
        </div>
    </div>

    <!-- 4. MODAL EDIT PROFIL (POPUP) -->
    <div class="modal fade" id="modalProfil" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Edit Profil</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <!-- Form Update Profil -->
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="modal-body">
                        <!-- Preview Foto (Lingkaran) -->
                        <div class="text-center mb-3">
                            <div style="width: 100px; height: 100px; background: #eee; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; border: 3px solid var(--accent); overflow: hidden;">
                                <!-- Image Preview Tag -->
                                <img id="avatarPreview" 
                                     src="{{ Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : '' }}" 
                                     class="{{ Auth::user()->photo ? '' : 'd-none' }}" 
                                     style="width: 100%; height: 100%; object-fit: cover;">
                                
                                <!-- Icon Default -->
                                <i id="avatarIcon" class="fas fa-user fa-3x text-muted {{ Auth::user()->photo ? 'd-none' : '' }}"></i>
                            </div>
                            
                            <!-- Tombol Pilih File -->
                            <div class="mt-2">
                                <label for="photoInput" class="btn btn-sm btn-outline-secondary" style="cursor: pointer;">
                                    <i class="fas fa-camera me-1"></i> Ganti Foto
                                </label>
                                <!-- Input File Asli (Sembunyi) -->
                                <input type="file" id="photoInput" name="photo" class="d-none" accept="image/*" onchange="previewImage(event)">
                            </div>
                        </div>
                        
                        <!-- Input Data Diri -->
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
                            <input type="text" name="phone" class="form-control" value="{{ Auth::user()->phone }}">
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

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script Preview Gambar -->
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
</body>
</html>