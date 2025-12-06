@extends(Auth::user()->role == 'Admin' ? 'layouts.app' : 'layouts.app_barista')

@section('title', 'Manajemen Menu - Kayuhan')

@section('content')
    @php
        if (!isset($menuItems)) { $menuItems = collect([]); } 
        if (!isset($categories)) { $categories = ['Coffee', 'Non-Coffee']; } 
    @endphp

    <style>
        .modal-header-kayuhan { background-color: #003d2e; color: white; }
        .modal-header-kayuhan .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }
        .btn-simpan { background-color: #0d6efd; color: white; }
        .btn-batal { background-color: #6c757d; color: white; }
        
        .img-preview-container {
            width: 150px;
            height: 150px;
            border: 2px dashed #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-radius: 8px;
            margin-bottom: 10px;
            background: #f8f9fa;
        }
        .img-preview-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark mb-0">Manajemen Menu</h3>
        
        @if(Auth::user()->role == 'Admin')
        <button class="btn" style="background-color: #003d2e; color: white;" 
                data-bs-toggle="modal" data-bs-target="#modalMenu" onclick="openCreateModal()">
            <i class="fas fa-plus me-2"></i> Tambah Menu
        </button>
        @endif
    </div>


    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Foto</th> 
                            <th>ID Produk</th>
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
                            <td class="ps-4">
                                @if($menu->FOTO)
                                    <img src="{{ asset('storage/' . $menu->FOTO) }}" alt="Foto" 
                                        style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                @else
                                    <span class="text-muted small">No Img</span>
                                @endif
                            </td>
                            <td class="fw-bold">{{ $menu->ID_PRODUK }}</td>
                            <td>{{ $menu->NAMA_PRODUK }}</td>
                            <td><span class="badge bg-secondary">{{ $menu->KATEGORI ?? 'Umum' }}</span></td>
                            <td>Rp {{ number_format($menu->HARGA_DASAR, 0, ',', '.') }}</td>
                            <td class="fw-bold text-success">Rp {{ number_format($menu->HARGA_JUAL, 0, ',', '.') }}</td>
                            
                            @if(Auth::user()->role == 'Admin')
                            <td class="text-end pe-4">
                                <button class="btn btn-sm btn-warning text-white me-1"
                                    onclick="openEditModal(
                                        '{{ $menu->ID_PRODUK }}', 
                                        '{{ $menu->NAMA_PRODUK }}', 
                                        '{{ $menu->KATEGORI ?? 'Coffee' }}', 
                                        '{{ $menu->HARGA_DASAR }}', 
                                        '{{ $menu->HARGA_JUAL }}',
                                        '{{ $menu->FOTO ? asset('storage/' . $menu->FOTO) : '' }}' 
                                    )"> 
                                    Edit
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="confirmDelete('{{ $menu->ID_PRODUK }}')">
                                    Hapus
                                </button>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">Belum ada data menu.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if(Auth::user()->role == 'Admin')
    <div class="modal fade" id="modalMenu" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('menu.store') }}" method="POST" id="formMenu" class="modal-content" enctype="multipart/form-data">
                @csrf
                <div id="method-spoofing"></div> 

                <div class="modal-header modal-header-kayuhan">
                    <h5 class="modal-title fw-bold" id="modalTitle">Kelola Data Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-8">
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
                                    <label class="form-label fw-bold small text-muted">HARGA DASAR</label>
                                    <input type="number" name="HARGA_DASAR" id="HARGA_DASAR" class="form-control" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted">HARGA JUAL</label>
                                <input type="number" name="HARGA_JUAL" id="HARGA_JUAL" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-md-4 d-flex flex-column align-items-center">
                            <label class="form-label fw-bold small text-muted align-self-start">FOTO MENU</label>
                            
                            <div class="img-preview-container">
                                <img id="img-preview" src="" class="d-none" alt="Preview">
                                <span id="text-preview" class="text-muted small text-center">Pilih foto<br>untuk preview</span>
                            </div>

                            <input type="file" name="FOTO" id="FOTO" class="form-control form-control-sm" accept="image/*" onchange="previewImage(event)">
                            <small class="text-muted mt-2" style="font-size: 0.7rem;">Format: JPG, PNG. Max: 2MB</small>
                        </div>
                    </div>
                </div>

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
    const ROUTE_STORE = "{{ route('menu.store') }}";
    const ROUTE_UPDATE_TEMPLATE = "{{ route('menu.update', '0') }}"; 
    const ROUTE_DELETE_TEMPLATE = "{{ route('menu.destroy', '0') }}";

    function previewImage(event) {
        const input = event.target;
        const imgPreview = document.getElementById('img-preview');
        const textPreview = document.getElementById('text-preview');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imgPreview.src = e.target.result;
                imgPreview.classList.remove('d-none');
                textPreview.classList.add('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function openCreateModal() {
        const form = document.getElementById('formMenu');
        form.reset();
        form.action = ROUTE_STORE;
        
        document.getElementById('modalTitle').innerText = "Tambah Menu Baru";
        document.getElementById('method-spoofing').innerHTML = ''; 
        document.getElementById('ID_PRODUK').readOnly = false; 

        document.getElementById('img-preview').src = "";
        document.getElementById('img-preview').classList.add('d-none');
        document.getElementById('text-preview').classList.remove('d-none');
    }

    function openEditModal(id, nama, kategori, h_dasar, h_jual, fotoUrl) {
        const form = document.getElementById('formMenu');
        var myModal = new bootstrap.Modal(document.getElementById('modalMenu'));
        myModal.show();

        document.getElementById('modalTitle').innerText = "Edit Menu: " + nama;
        document.getElementById('ID_PRODUK').value = id;
        document.getElementById('NAMA_PRODUK').value = nama;
        document.getElementById('KATEGORI').value = kategori;
        document.getElementById('HARGA_DASAR').value = h_dasar;
        document.getElementById('HARGA_JUAL').value = h_jual;
        document.getElementById('ID_PRODUK').readOnly = true; 

        const imgPreview = document.getElementById('img-preview');
        const textPreview = document.getElementById('text-preview');
        
        document.getElementById('FOTO').value = ""; 

        if (fotoUrl) {
            imgPreview.src = fotoUrl;
            imgPreview.classList.remove('d-none');
            textPreview.classList.add('d-none');
        } else {
            imgPreview.classList.add('d-none');
            textPreview.classList.remove('d-none');
        }

        let updateUrl = ROUTE_UPDATE_TEMPLATE.replace('/0', '/' + id);
        form.action = updateUrl;
        document.getElementById('method-spoofing').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    }

    function confirmDelete(id) {
        if (confirm('Apakah Anda yakin ingin menghapus menu ID: ' + id + '?')) {
            let form = document.createElement('form');
            form.method = 'POST';
            let deleteUrl = ROUTE_DELETE_TEMPLATE.replace('/0', '/' + id);
            form.action = deleteUrl;
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