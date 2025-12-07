@extends(Auth::user()->role == 'Admin' ? 'layouts.app' : 'layouts.app_barista')

@section('title', 'Manajemen Menu - Kayuhan')

@section('content')
    @php
        if (!isset($menuItems)) { $menuItems = collect([]); } 
        if (!isset($categories)) { $categories = ['Coffee', 'Non-Coffee']; } 
    @endphp

    <style>
        .modal-header-kayuhan {
            background-color: #003d2e;
            color: white;
        }
        .modal-header-kayuhan .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }
        .btn-simpan { background-color: #0d6efd; color: white; }
        .btn-batal { background-color: #6c757d; color: white; }
    </style>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary-custom mb-4">Manajemen Menu</h2>
        
        {{-- Tombol Tambah Menu --}}
        @if(Auth::user()->role == 'Admin')
        <button class="btn" style="background-color: #003d2e; color: white;" 
                data-bs-toggle="modal" data-bs-target="#modalMenu" onclick="openCreateModal()">
            <i class="fas fa-plus me-2"></i> Tambah Menu
        </button>
        @endif
    </div>

    {{-- TABEL DATA --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">ID Produk</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Harga Dasar</th>
                            <th>Harga Jual</th>
                            @if(Auth::user()->role == 'Admin')
                            <th class="text-end pe-4">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($menuItems as $menu)
                        <tr>
                            <td class="ps-4 fw-bold">{{ $menu->ID_PRODUK }}</td>
                            <td>{{ $menu->NAMA_PRODUK }}</td>
                            <td><span class="badge bg-secondary">{{ $menu->KATEGORI ?? 'Umum' }}</span></td>
                            <td>Rp {{ number_format($menu->HARGA_DASAR, 0, ',', '.') }}</td>
                            <td class="fw-bold text-success">Rp {{ number_format($menu->HARGA_JUAL, 0, ',', '.') }}</td>
                            
                            @if(Auth::user()->role == 'Admin')
                            <td class="text-end pe-4">
                                {{-- Tombol Edit --}}
                                <button class="btn btn-sm btn-warning text-white me-1"
                                    onclick="openEditModal(
                                        '{{ $menu->ID_PRODUK }}', 
                                        '{{ $menu->NAMA_PRODUK }}', 
                                        '{{ $menu->KATEGORI ?? 'Coffee' }}', 
                                        '{{ $menu->HARGA_DASAR }}', 
                                        '{{ $menu->HARGA_JUAL }}'
                                    )">
                                    Edit
                                </button>
                                {{-- Tombol Hapus --}}
                                <button class="btn btn-sm btn-danger" onclick="confirmDelete('{{ $menu->ID_PRODUK }}')">
                                    Hapus
                                </button>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">Belum ada data menu.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL UNIFIED (Satu Modal untuk Create & Edit) --}}
    @if(Auth::user()->role == 'Admin')
    <div class="modal fade" id="modalMenu" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            {{-- Form Action Awal Menggunakan Route Store --}}
            <form action="{{ route('menu.store') }}" method="POST" id="formMenu" class="modal-content">
                @csrf
                <div id="method-spoofing"></div> 

                {{-- HEADER HIJAU --}}
                <div class="modal-header modal-header-kayuhan">
                    <h5 class="modal-title fw-bold" id="modalTitle">Kelola Data Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- BODY --}}
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">ID PRODUK (PK)</label>
                        <input type="text" name="ID_PRODUK" id="ID_PRODUK" class="form-control" placeholder="Contoh: M001" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">NAMA PRODUK</label>
                        <input type="text" name="NAMA_PRODUK" id="NAMA_PRODUK" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-muted">KATEGORI</label>
                            <select name="KATEGORI" id="KATEGORI" class="form-select">
                                @foreach($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-muted">HARGA DASAR (HPP)</label>
                            <input type="number" name="HARGA_DASAR" id="HARGA_DASAR" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">HARGA JUAL</label>
                        <input type="number" name="HARGA_JUAL" id="HARGA_JUAL" class="form-control" required>
                    </div>
                </div>

                {{-- FOOTER --}}
                <div class="modal-footer border-0 pt-0 pb-4 pe-4">
                    <button type="button" class="btn btn-batal px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-simpan px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    @endif
@endsection

@push('scripts')
<script>
    // === SETUP ROUTE DARI LARAVEL KE JS ===
    // Kita gunakan ID '0' sebagai placeholder sementara
    const ROUTE_STORE = "{{ route('menu.store') }}";
    const ROUTE_UPDATE_TEMPLATE = "{{ route('menu.update', '0') }}"; 
    const ROUTE_DELETE_TEMPLATE = "{{ route('menu.destroy', '0') }}";

    // 1. Fungsi CREATE
    function openCreateModal() {
        const form = document.getElementById('formMenu');
        
        // Reset Form
        form.reset();
        
        // Set Action ke route Store
        form.action = ROUTE_STORE;
        
        // UI Updates
        document.getElementById('modalTitle').innerText = "Tambah Menu Baru";
        document.getElementById('method-spoofing').innerHTML = ''; // Hapus method PUT
        document.getElementById('ID_PRODUK').readOnly = false; // ID boleh diisi manual
    }

    // 2. Fungsi EDIT
    function openEditModal(id, nama, kategori, h_dasar, h_jual) {
        const form = document.getElementById('formMenu');
        var myModal = new bootstrap.Modal(document.getElementById('modalMenu'));
        myModal.show();

        // UI Updates
        document.getElementById('modalTitle').innerText = "Edit Menu: " + nama;
        document.getElementById('ID_PRODUK').value = id;
        document.getElementById('NAMA_PRODUK').value = nama;
        document.getElementById('KATEGORI').value = kategori;
        document.getElementById('HARGA_DASAR').value = h_dasar;
        document.getElementById('HARGA_JUAL').value = h_jual;
        document.getElementById('ID_PRODUK').readOnly = true; // ID tidak boleh diedit

        // == LOGIKA PENTING: REPLACE ID ==
        // Mengganti '/0' (placeholder) dengan ID produk asli (misal 'M001')
        // Contoh: .../menu/update/0  menjadi  .../menu/update/M001
        let updateUrl = ROUTE_UPDATE_TEMPLATE.replace('/0', '/' + id);
        
        form.action = updateUrl;

        // Tambahkan Method PUT karena HTML Form cuma support GET/POST
        document.getElementById('method-spoofing').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    }

    // 3. Fungsi DELETE
    function confirmDelete(id) {
        if (confirm('Apakah Anda yakin ingin menghapus menu ID: ' + id + '?')) {
            let form = document.createElement('form');
            form.method = 'POST';
            
            // == LOGIKA PENTING: REPLACE ID ==
            let deleteUrl = ROUTE_DELETE_TEMPLATE.replace('/0', '/' + id);
            form.action = deleteUrl;

            // CSRF & Method DELETE
            let csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);

            let method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';
            form.appendChild(method);

            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endpush