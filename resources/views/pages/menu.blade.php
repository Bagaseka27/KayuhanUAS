@extends(Auth::user()->role == 'admin' ? 'layouts.app' : 'layouts.app_barista')

@section('title', 'Manajemen Menu - Kayuhan')

@section('content')
    {{-- DATA DUMMY (Biar tabel langsung ada isinya) --}}
    @php
        $menus = [
            (object)['id' => 'M01', 'name' => 'Kopi Susu Aren', 'base_price' => 12000, 'sell_price' => 18000, 'category' => 'Coffee'],
            (object)['id' => 'M02', 'name' => 'Americano', 'base_price' => 8000, 'sell_price' => 15000, 'category' => 'Coffee'],
            (object)['id' => 'M03', 'name' => 'Latte Ice', 'base_price' => 14000, 'sell_price' => 22000, 'category' => 'Coffee'],
            (object)['id' => 'M05', 'name' => 'Matcha Latte', 'base_price' => 15000, 'sell_price' => 24000, 'category' => 'Non-Coffee'],
        ];
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary-custom mb-0">Manajemen Menu</h3>
        
        {{-- Tombol Tambah Menu (Hanya muncul jika Admin) --}}
        @if(Auth::user()->role == 'admin')
        <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalAddMenu" onclick="resetMenuModal('#modalAddMenu')">
            <i class="fas fa-plus me-2"></i> Tambah Menu Baru
        </button>
        @endif
    </div>

    <div class="stat-card mb-4 py-3">
        <div class="row g-3">
            <div class="col-md-4">
                <input type="text" class="form-control form-control-sm" placeholder="Cari nama menu...">
            </div>
            <div class="col-md-3">
                <select class="form-select form-select-sm">
                    <option value="">Semua Kategori</option>
                    <option value="Coffee">Coffee</option>
                    <option value="Non-Coffee">Non-Coffee</option>
                </select>
            </div>
        </div>
    </div>

    <div class="stat-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table custom-table mb-0 table-hover">
                <thead class="bg-light">
                    <tr>
                        <th>Kode ID</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Harga Modal</th>
                        <th>Harga Jual</th>
                        <th>Margin</th>
                        @if(Auth::user()->role == 'admin')
                        <th class="text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($menus as $menu)
                    <tr>
                        <td class="fw-bold">{{ $menu->id }}</td>
                        <td>{{ $menu->name }}</td>
                        <td><span class="badge bg-secondary">{{ $menu->category }}</span></td>
                        <td>Rp {{ number_format($menu->base_price, 0, ',', '.') }}</td>
                        <td class="text-primary-custom fw-bold">Rp {{ number_format($menu->sell_price, 0, ',', '.') }}</td>
                        <td class="text-success small">
                            + Rp {{ number_format($menu->sell_price - $menu->base_price, 0, ',', '.') }}
                        </td>
                        
                        {{-- Tombol Aksi (Hanya muncul jika Admin) --}}
                        @if(Auth::user()->role == 'admin')
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary me-1"
                                onclick="fillMenuModal('{{ $menu->id }}', '{{ $menu->name }}', '{{ $menu->category }}', {{ $menu->base_price }}, {{ $menu->sell_price }})"
                                data-bs-toggle="modal" data-bs-target="#modalEditMenu">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="confirmDelete('{{ $menu->id }}', 'menu')">
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

    <div class="modal fade" id="modalAddMenu" tabindex="-1">
        <div class="modal-dialog">
            <form action="/menu/store" method="POST" class="modal-content" id="formAddMenu">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Menu Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kode Menu (ID)</label>
                        <input type="text" name="id" class="form-control" placeholder="Contoh: M06" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Menu</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select name="category" class="form-select">
                            <option value="Coffee">Coffee</option>
                            <option value="Non-Coffee">Non-Coffee</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Harga Modal (HPP)</label>
                            <input type="number" name="base_price" class="form-control" placeholder="0" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Harga Jual</label>
                            <input type="number" name="sell_price" class="form-control" placeholder="0" required>
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
    
    <div class="modal fade" id="modalEditMenu" tabindex="-1">
        <div class="modal-dialog">
            <form action="#" method="POST" class="modal-content" id="formEditMenu">
                @csrf
                @method('PUT') <div class="modal-header bg-dark text-white" style="background-color: var(--primary);">
                    <h5 class="modal-title">Edit Menu</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">ID Produk (Wajib)</label>
                        <input type="text" name="id" id="edit_menu_id" class="form-control bg-light" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Produk</label>
                        <input type="text" name="name" id="edit_menu_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select name="category" id="edit_menu_category" class="form-select">
                            <option value="Coffee">Coffee</option>
                            <option value="Non-Coffee">Non-Coffee</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Harga Modal (HPP)</label>
                            <input type="number" name="base_price" id="edit_menu_base_price" class="form-control" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Harga Jual</label>
                            <input type="number" name="sell_price" id="edit_menu_sell_price" class="form-control" required>
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

@endsection

@push('scripts')
<script>
    // Fungsi untuk mengisi Modal Edit saat tombol Edit diklik
    window.fillMenuModal = function(id, name, category, base_price, sell_price) {
        const form = document.getElementById('formEditMenu');
        
        // 1. Atur Form Action ke route update (Contoh: /menu/update/M01)
        form.action = `/menu/update/${id}`;
        
        // 2. Isi Fields
        document.getElementById('edit_menu_id').value = id;
        document.getElementById('edit_menu_name').value = name;
        document.getElementById('edit_menu_category').value = category;
        document.getElementById('edit_menu_base_price').value = base_price;
        document.getElementById('edit_menu_sell_price').value = sell_price;
    };
    
    // Fungsi untuk mereset modal Tambah saat dibuka
    window.resetMenuModal = function(modalId) {
        if (modalId === '#modalAddMenu') {
            document.getElementById('formAddMenu').reset();
            document.getElementById('formAddMenu').action = '/menu/store'; // Route Tambah
        }
    };
    
    // Fungsi dummy untuk tombol Hapus
    window.confirmDelete = function(id, tipe) {
        if (confirm(`Yakin ingin menghapus ${tipe} dengan ID ${id}?`)) {
            // Di sini Anda akan menambahkan logika fetch/AJAX ke route DELETE
            console.log(`Menghapus ${tipe} ID: ${id}`);
        }
    };
</script>
@endpush