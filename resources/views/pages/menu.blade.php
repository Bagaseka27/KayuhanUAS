@extends(Auth::user()->role == 'admin' ? 'layouts.app' : 'layouts.app_barista')

@section('title', 'Manajemen Menu - Kayuhan')

@section('content')
    {{-- HAPUS DATA DUMMY (kecuali jika testing), DATA DIAMBIL DARI CONTROLLER MELALUI VARIABEL $menuItems --}}
    @php
        // Menggunakan variabel $menuItems yang dikirim Controller
        if (!isset($menuItems)) { $menuItems = collect($menus ?? []); } 
        if (!isset($categories)) { $categories = ['Coffee', 'Non-Coffee']; } 
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary-custom mb-0">Manajemen Menu</h3>
        
        {{-- Tombol Tambah Menu (HANYA UNTUK ADMIN) --}}
        @if(Auth::user()->role == 'admin')
        <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalAddMenu" onclick="resetMenuModal()">
            <i class="fas fa-plus me-2"></i> Tambah Menu Baru
        </button>
        @endif
    </div>

    <!-- Filter & Search Section (Akses penuh untuk Barista) -->
    <div class="stat-card mb-4 py-3">
        <div class="row g-3">
            <div class="col-md-4">
                <input type="text" class="form-control form-control-sm" placeholder="Cari nama menu...">
            </div>
            <div class="col-md-3">
                <select class="form-select form-select-sm">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Tabel Menu -->
    <div class="stat-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table custom-table mb-0 table-hover">
                <thead class="bg-light">
                    <tr>
                        <th>Kode ID</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        {{-- HARGA MODAL DAN MARGIN HANYA UNTUK ADMIN --}}
                        @if(Auth::user()->role == 'admin')
                        <th>Harga Modal</th>
                        <th>Margin</th>
                        @endif
                        <th>Harga Jual</th>
                        {{-- KOLOM AKSI HANYA UNTUK ADMIN --}}
                        @if(Auth::user()->role == 'admin')
                        <th class="text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    {{-- LOOP DATA DARI DATABASE --}}
                    @foreach($menuItems as $menu)
                    <tr>
                        {{-- Menggunakan nama kolom DB: ID_PRODUK --}}
                        <td class="fw-bold">{{ $menu->ID_PRODUK ?? $menu->id }}</td>
                        {{-- Menggunakan nama kolom DB: NAMA_PRODUK --}}
                        <td>{{ $menu->NAMA_PRODUK ?? $menu->name }}</td>
                        {{-- Menggunakan nama kolom DB: CATEGORY --}}
                        <td><span class="badge bg-secondary">{{ $menu->CATEGORY ?? $menu->category ?? 'Uncategorized' }}</span></td>
                        
                        {{-- HARGA MODAL (HANYA UNTUK ADMIN) --}}
                        @if(Auth::user()->role == 'admin')
                        {{-- Menggunakan nama kolom DB: HARGA_DASAR --}}
                        <td>Rp {{ number_format($menu->HARGA_DASAR ?? $menu->base_price, 0, ',', '.') }}</td>
                        {{-- MARGIN (HANYA UNTUK ADMIN) --}}
                        <td class="text-success small">
                            @php
                                $jual = $menu->HARGA_JUAL ?? $menu->sell_price;
                                $dasar = $menu->HARGA_DASAR ?? $menu->base_price;
                            @endphp
                            + Rp {{ number_format($jual - $dasar, 0, ',', '.') }}
                        </td>
                        @endif

                        {{-- Harga Jual (Dilihat oleh semua peran) --}}
                        <td class="text-primary-custom fw-bold">Rp {{ number_format($menu->HARGA_JUAL ?? $menu->sell_price, 0, ',', '.') }}</td>
                        
                        {{-- Tombol Aksi (HANYA UNTUK ADMIN) --}}
                        @if(Auth::user()->role == 'admin')
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary me-1"
                                onclick="fillMenuModal(
                                    '{{ $menu->ID_PRODUK ?? $menu->id }}', 
                                    '{{ $menu->NAMA_PRODUK ?? $menu->name }}', 
                                    '{{ $menu->CATEGORY ?? $menu->category ?? 'Coffee' }}', 
                                    {{ $menu->HARGA_DASAR ?? $menu->base_price ?? 0 }}, 
                                    {{ $menu->HARGA_JUAL ?? $menu->sell_price ?? 0 }}
                                )"
                                data-bs-toggle="modal" data-bs-target="#modalEditMenu">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="confirmDelete('{{ $menu->ID_PRODUK ?? $menu->id }}', 'menu')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL HANYA DIBUAT JIKA ADMIN (agar tidak ada error JavaScript di Barista) --}}
    @if(Auth::user()->role == 'admin')
        <!-- MODAL TAMBAH MENU -->
        <div class="modal fade" id="modalAddMenu" tabindex="-1">
            <div class="modal-dialog">
                <form action="{{ url('menu/store') }}" method="POST" class="modal-content" id="formAddMenu">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Menu Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Kode Menu (ID)</label>
                            <input type="text" name="ID_PRODUK" class="form-control" placeholder="Contoh: M06" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Menu</label>
                            <input type="text" name="NAMA_PRODUK" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <select name="CATEGORY" class="form-select">
                                <option value="Coffee">Coffee</option>
                                <option value="Non-Coffee">Non-Coffee</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">Harga Modal (HPP)</label>
                                <input type="number" name="HARGA_DASAR" class="form-control" placeholder="0" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Harga Jual</label>
                                <input type="number" name="HARGA_JUAL" class="form-control" placeholder="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary-custom">Simpan Menu</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- MODAL EDIT MENU -->
        <div class="modal fade" id="modalEditMenu" tabindex="-1">
            <div class="modal-dialog">
                <form action="#" method="POST" class="modal-content" id="formEditMenu">
                    @csrf
                    @method('PUT') 
                    <div class="modal-header bg-dark text-white" style="background-color: var(--primary);">
                        <h5 class="modal-title">Edit Menu</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">ID Produk (Wajib)</label>
                            <input type="text" name="ID_PRODUK" id="edit_ID_PRODUK" class="form-control bg-light" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Produk</label>
                            <input type="text" name="NAMA_PRODUK" id="edit_NAMA_PRODUK" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <select name="CATEGORY" id="edit_CATEGORY" class="form-select">
                                <option value="Coffee">Coffee</option>
                                <option value="Non-Coffee">Non-Coffee</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">Harga Modal (HPP)</label>
                                <input type="number" name="HARGA_DASAR" id="edit_HARGA_DASAR" class="form-control" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Harga Jual</label>
                                <input type="number" name="HARGA_JUAL" id="edit_HARGA_JUAL" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary-custom">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

@endsection

{{-- JAVASCRIPT HANYA DIPERLUKAN JIKA ADMIN (karena Barista Read-Only) --}}
@if(Auth::user()->role == 'admin')
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Definisikan route URL sebagai variabel JavaScript lokal di dalam DOMContentLoaded.
        const URL_MENU_STORE = '{{ url('menu/store') }}';
        const URL_MENU_UPDATE = '{{ url('menu/update') }}';
        const URL_MENU_DELETE = '{{ url('menu/delete') }}';

        // Fungsi untuk mengisi Modal Edit saat tombol Edit diklik
        window.fillMenuModal = function(id_produk, nama_produk, category, harga_dasar, harga_jual) {
            const form = document.getElementById('formEditMenu');
            
            // 1. Atur Form Action ke route update (Menggunakan variabel lokal)
            form.action = URL_MENU_UPDATE + '/' + id_produk;
            
            // 2. Isi Fields dengan NAMA ID yang sudah dikoreksi
            document.getElementById('edit_ID_PRODUK').value = id_produk;
            document.getElementById('edit_NAMA_PRODUK').value = nama_produk;
            document.getElementById('edit_CATEGORY').value = category; 
            document.getElementById('edit_HARGA_DASAR').value = harga_dasar;
            document.getElementById('edit_HARGA_JUAL').value = harga_jual;
        };
        
        // Fungsi untuk mereset modal Tambah saat dibuka
        window.resetMenuModal = function() {
            document.getElementById('formAddMenu').reset();
            document.getElementById('formAddMenu').action = URL_MENU_STORE; 
        };
        
        // Fungsi Hapus yang akan submit form DELETE
        window.confirmDelete = function(id, tipe) {
            if (confirm(`Yakin ingin menghapus ${tipe} dengan ID ${id}?`)) {
                // Membuat form DELETE secara dinamis
                const deleteForm = document.createElement('form');
                deleteForm.action = URL_MENU_DELETE + '/' + id; 
                deleteForm.method = 'POST';
                deleteForm.insertAdjacentHTML('beforeend', '@csrf');
                deleteForm.insertAdjacentHTML('beforeend', '<input type="hidden" name="_method" value="DELETE">');
                
                // Submit form
                document.body.appendChild(deleteForm);
                deleteForm.submit();
            }
        };
    });
</script>
@endpush
@endif