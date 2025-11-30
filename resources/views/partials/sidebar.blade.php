<div class="sidebar" id="sidebar">
    <div class="sidebar-brand" title="Edit Profil">
        <div style="width:40px; height:40px; background:white; border-radius:50%; display:flex; align-items:center; justify-content:center; color:var(--primary); font-size:1.4rem; overflow:hidden">
            <i class="fas fa-user"></i> 
        </div>
        <div class="d-flex flex-column" style="overflow:hidden;">
            <span class="text-truncate">{{ Auth::user()->name ?? 'User' }}</span>
            <small style="font-size:0.7rem; opacity:0.8;">{{ Auth::user()->role ?? 'Guest' }}</small>
        </div>
    </div>
    
    <div class="sidebar-menu">
        <a href="{{ url('/dashboard') }}" class="menu-item {{ Request::is('dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i> Dashboard
        </a>
        
        @if(Auth::user()->role == 'Barista')
            <a href="{{ url('/pos') }}" class="menu-item {{ Request::is('pos') ? 'active' : '' }}">
                <i class="fas fa-cash-register"></i> Kasir (POS)
            </a>
        @endif

        @if(Auth::user()->role == 'Admin')
            <a href="{{ url('/employees') }}" class="menu-item {{ Request::is('employees') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Karyawan
            </a>
        @endif
        
        <a href="{{ url('/menu') }}" class="menu-item {{ Request::is('menu') ? 'active' : '' }}">
            <i class="fas fa-coffee"></i> Manajemen Menu
        </a>
    </div>

    <div class="px-3 w-100 position-absolute bottom-0 mb-4">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-light w-100 text-start border-0 py-2">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </button>
        </form>
    </div>
</div>