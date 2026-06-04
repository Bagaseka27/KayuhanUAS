@extends('layouts.app')

@section('title', 'Stok & Inventory - Kayuhan')

@section('content')
{{-- Alert Notification untuk feedback CRUD Gudang --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<h3 class="fw-bold text-primary-custom mb-4">Kontrol Stok (Admin)</h3>

<ul class="nav nav-tabs mb-4" id="inventoryTabs" role="tablist">
    <li class="nav-item">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-gudang">
            <i class="fas fa-warehouse me-2"></i>Stok Gudang
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-rombong">
            <i class="fas fa-truck-loading me-2"></i>Stok Rombong
        </button>
    </li>
</ul>

<div class="tab-content">

    {{-- ======================== TAB GUDANG (FULL CRUD ADMIN) ============================= --}}
    <div class="tab-pane fade show active" id="tab-gudang">
        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#modalStokGudang" data-mode="create">
                <i class="fas fa-plus me-2"></i> Tambah Barang
            </button>
        </div>

        <div class="card p-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table custom-table mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>ID BARANG</th>
                            <th>NAMA BARANG</th>
                            <th class="text-center">MASUK</th>
                            <th class="text-center">KELUAR</th>
                            <th class="text-center">TOTAL (JUMLAH)</th>
                            <th class="text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($master as $m)
                        <tr 
                            data-id="{{ $m->ID_BARANG }}"
                            data-nama="{{ $m->NAMA_BARANG }}"
                            data-masuk="{{ $m->MASUK }}"
                            data-keluar="{{ $m->KELUAR }}"
                        >
                            <td class="fw-bold">{{ $m->ID_BARANG }}</td>
                            <td>{{ $m->NAMA_BARANG }}</td>
                            <td class="text-center">{{ $m->MASUK }}</td>
                            <td class="text-center">{{ $m->KELUAR }}</td>
                            <td class="text-center fw-bold">{{ $m->JUMLAH }}</td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-sm text-info edit-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalStokGudang"
                                        data-mode="edit"
                                        data-id="{{ $m->ID_BARANG }}">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <form action="{{ route('gudang.delete', $m->ID_BARANG) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus barang ini dari gudang?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm text-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    {{-- ======================== TAB ROMBONG (ONLY VIEW FOR ADMIN) ============================= --}}
    <div class="tab-pane fade" id="tab-rombong">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-2">
                <label class="fw-bold text-muted">Pilih Rombong:</label>
                <select class="form-select form-select-sm w-auto" id="select-rombong-filter">
                    <option value="">Semua Rombong</option>
                    @foreach ($rombongList as $rb)
                        <option value="{{ $rb->ID_ROMBONG }}">{{ $rb->ID_ROMBONG }}</option>
                    @endforeach
                </select>
            </div>
            {{-- Tombol tambah stok rombong dicopot karena diatur oleh barista --}}
        </div>

        <div class="card p-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table custom-table mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>ID BARANG</th>
                            <th>NAMA BARANG</th>
                            <th>ID ROMBONG</th>
                            <th class="text-center">STOK AWAL</th>
                            <th class="text-center">STOK AKHIR</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rombong as $r)
                        <tr 
                            data-id="{{ $r->id }}"
                            data-rombong="{{ $r->rombong_id }}"
                        >
                            <td class="fw-bold">{{ $r->barang_id }}</td>
                            <td>{{ $r->barang->NAMA_BARANG ?? 'Barang Tidak Ditemukan' }}</td>
                            <td>{{ $r->rombong_id }}</td>
                            <td class="text-center">{{ $r->stok_awal ?? '-' }}</td>
                            <td class="text-center fw-bold 
                                {{ $r->stok_akhir !== null && $r->stok_awal !== null && $r->stok_akhir < $r->stok_awal 
                                    ? 'text-danger' 
                                    : 'text-success' 
                                }}">
                                {{ $r->stok_akhir ?? '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>


{{-- ======================= MODAL CRUD GUDANG (ADMIN ONLY) ======================= --}}
<div class="modal fade" id="modalStokGudang" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="#" class="modal-content" id="formStokGudang">
            @csrf
            <div id="method-field"></div>

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Kelola Stok Gudang</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">ID Barang</label>
                    <input type="text" name="ID_BARANG" id="ID_BARANG" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama Barang</label>
                    <input type="text" name="NAMA_BARANG" id="NAMA_BARANG" class="form-control" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Barang Masuk</label>
                        <input type="number" name="MASUK" id="MASUK" class="form-control" min="0" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Barang Keluar</label>
                        <input type="number" name="KELUAR" id="KELUAR" class="form-control" min="0" required>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ==================== LOGIK MODAL GUDANG ====================
    var modalStokGudang = document.getElementById('modalStokGudang');

    modalStokGudang.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var mode = button.dataset.mode;           
        var form = document.getElementById('formStokGudang');
        var methodField = document.getElementById('method-field');

        form.reset();
        methodField.innerHTML = "";    

        if (mode === "create") {
            form.action = "{{ route('gudang.store') }}";
            document.getElementById('ID_BARANG').readOnly = false;
        } else if (mode === "edit") {
            var id = button.dataset.id;
            var row = document.querySelector(`tr[data-id="${id}"]`);

            form.action = "/inventory/gudang/update/" + id;
            methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';

            document.getElementById('ID_BARANG').value = id;
            document.getElementById('ID_BARANG').readOnly = true;

            document.getElementById('NAMA_BARANG').value = row.dataset.nama;
            document.getElementById('MASUK').value = row.dataset.masuk;
            document.getElementById('KELUAR').value = row.dataset.keluar;
        }
    });

    // ==================== FILTER DATA ROMBONG ====================
    document.getElementById('select-rombong-filter').addEventListener('change', function () {
        const selected = this.value.trim();
        const rows = document.querySelectorAll('#tab-rombong tbody tr');

        rows.forEach(row => {
            const rombongId = row.dataset.rombong;

            if (selected === "" || selected === rombongId) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    });

});
</script>
@endpush