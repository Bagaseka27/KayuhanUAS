@extends('layouts.app')

@section('title', 'Stok & Inventory - Kayuhan')

@section('content')
<h3 class="fw-bold text-primary-custom mb-4">Kontrol Stok & Logistik</h3>

<ul class="nav nav-tabs mb-4" id="inventoryTabs" role="tablist">
    <li class="nav-item">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-gudang">
            <i class="fas fa-warehouse me-2"></i>Stok Gudang (Master)
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-rombong">
            <i class="fas fa-truck-loading me-2"></i>Stok Rombong (Detail)
        </button>
    </li>
</ul>

<div class="tab-content">

    {{-- ======================== TAB GUDANG ============================= --}}
    <div class="tab-pane fade show active" id="tab-gudang">
        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#modalStokGudang" data-mode="create">
                <i class="fas fa-plus me-2"></i> Tambah Barang Master
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

                                <button class="btn btn-sm text-info edit-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalStokGudang"
                                    data-mode="edit"
                                    data-id="{{ $m->ID_BARANG }}">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <form action="{{ route('gudang.delete', $m->ID_BARANG) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm text-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>

                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>



    {{-- ======================== TAB ROMBONG ============================= --}}
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

            <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahStokRombong">
                <i class="fas fa-plus me-2"></i> Tambah Stok Rombong
            </button>
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
                            <th class="text-center">AKSI</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($rombong as $r)
                        <tr 
                            data-id="{{ $r->id }}"
                            data-nama="{{ $r->barang->NAMA_BARANG }}"
                            data-rombong="{{ $r->rombong_id }}"
                            data-awal="{{ $r->stok_awal }}"
                            data-akhir="{{ $r->stok_akhir }}"
                        >

                            <td class="fw-bold">{{ $r->barang_id }}</td>
                            <td>{{ $r->barang->NAMA_BARANG }}</td>
                            <td>{{ $r->rombong_id }}</td>

                            <td class="text-center">{{ $r->stok_awal ?? '-' }}</td>

                            <td class="text-center fw-bold 
                                {{ $r->stok_akhir !== null && $r->stok_awal !== null && $r->stok_akhir < $r->stok_awal 
                                    ? 'text-danger' 
                                    : 'text-success' 
                                }}">
                                {{ $r->stok_akhir ?? '-' }}
                            </td>

                            <td class="text-center">

                                <button class="btn btn-sm text-info edit-rombong-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditStokRombong"
                                    data-id="{{ $r->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <form action="{{ route('rombong.delete', $r->id) }}" method="POST" class="d-inline">
                                    @csrf 
                                    @method('DELETE')
                                    <button class="btn btn-sm text-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>

                            </td>

                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>

    </div>

</div>




{{-- ======================= MODAL CRUD GUDANG ======================= --}}
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


{{-- ======================= MODAL EDIT STOK ROMBONG ======================= --}}
<div class="modal fade" id="modalEditStokRombong" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content" id="formStokRombong">
            @csrf
            <div id="method-rom"></div>

            <input type="hidden" name="id" id="stok_rombong_id">
            <input type="hidden" name="barang_id_submit" id="barang_id_submit">
            <input type="hidden" name="rombong_id_submit" id="rombong_id_submit">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Edit Stok Rombong</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label">Barang</label>
                    <select class="form-select" id="rombong_barang" disabled>
                        @foreach ($master as $m)
                        <option value="{{ $m->ID_BARANG }}">{{ $m->NAMA_BARANG }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Rombong</label>
                    <select class="form-select" id="rombong_lokasi" disabled>
                        @foreach ($rombongList as $rb)
                        <option value="{{ $rb->ID_ROMBONG }}">{{ $rb->ID_ROMBONG }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Stok Awal</label>
                        <input type="number" name="stok_awal" id="stok_awal" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Stok Akhir</label>
                        <input type="number" name="stok_akhir" id="stok_akhir" class="form-control" required>
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



{{-- ======================= MODAL BATCH STOK ROMBONG ======================= --}}
<div class="modal fade" id="modalTambahStokRombong" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('rombong.batchStore') }}" method="POST" class="modal-content" id="formBatchStokRombong">
            @csrf

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Tambah Stok Rombong (Batch)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label fw-bold">Rombong Tujuan</label>
                    <select name="rombong_tujuan" id="rombong_tujuan" class="form-select" required>
                        <option value="">-- Pilih Rombong --</option>
                        @foreach ($rombongList as $rb)
                            <option value="{{ $rb->ID_ROMBONG }}">{{ $rb->ID_ROMBONG }}</option>
                        @endforeach
                    </select>
                </div>

                <h6 class="mt-4 mb-3 fw-bold">Daftar Barang:</h6>

                <div id="item-container"></div>

                <button type="button" class="btn btn-sm btn-outline-primary mt-3" id="add-item-row">
                    <i class="fas fa-plus"></i> Tambah Baris
                </button>

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

const masterItems = @json($master);
let itemCounter = 0;

function createItemRow() {
    const index = itemCounter++;

    const row = document.createElement('div');
    row.classList.add('row', 'g-2', 'mb-3', 'item-row-dynamic');

    row.innerHTML = `
        <div class="col-6">
            <select name="items[${index}][id]" class="form-select form-select-sm" required>
                <option value="">-- Pilih Barang --</option>
                ${masterItems.map(item => `
                    <option value="${item.ID_BARANG}">${item.NAMA_BARANG}</option>
                `)}
            </select>
        </div>

        <div class="col-4">
            <input type="number" name="items[${index}][qty]" class="form-control form-control-sm" placeholder="Jumlah" min="1" required>
        </div>

        <div class="col-2 d-flex align-items-center">
            <button type="button" class="btn btn-sm btn-danger remove-item-row">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;

    return row;
}


document.addEventListener('DOMContentLoaded', function () {

    // ================= ADD/REMOVE BARIS ==================
    const itemContainer = document.getElementById('item-container');
    const addButton = document.getElementById('add-item-row');
    const modalTambah = document.getElementById('modalTambahStokRombong');

    addButton.addEventListener('click', () => itemContainer.appendChild(createItemRow()));

    itemContainer.addEventListener('click', e => {
        if (e.target.closest('.remove-item-row')) {
            e.target.closest('.item-row-dynamic').remove();
        }
    });

    modalTambah.addEventListener('show.bs.modal', () => {
        itemContainer.innerHTML = '';
        itemCounter = 0;
        itemContainer.appendChild(createItemRow());
    });



    // ==================== MODAL GUDANG ====================
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




    // ==================== MODAL ROMBONG ====================
    var modalRombong = document.getElementById('modalEditStokRombong');

    modalRombong.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var id = button.dataset.id;
        var row = document.querySelector(`tr[data-id="${id}"]`);

        document.getElementById('stok_rombong_id').value = id;

        var barangId = row.querySelector('td:nth-child(1)').innerText.trim();
        var rombongId = row.dataset.rombong;

        document.getElementById('barang_id_submit').value = barangId;
        document.getElementById('rombong_id_submit').value = rombongId;

        document.getElementById('rombong_barang').value = barangId;
        document.getElementById('rombong_lokasi').value = rombongId;

        document.getElementById('stok_awal').value = row.dataset.awal;
        document.getElementById('stok_akhir').value = row.dataset.akhir ?? row.dataset.awal;

        document.getElementById('formStokRombong').action = "/inventory/rombong/update/" + id;

        // WAJIB untuk PUT
        document.getElementById('method-rom').innerHTML = 
            '<input type="hidden" name="_method" value="PUT">';
    });

});
document.getElementById('select-rombong-filter').addEventListener('change', function () {

    const selected = this.value.trim();
    const rows = document.querySelectorAll('#tab-rombong tbody tr');

    rows.forEach(row => {
        const rombongId = row.dataset.rombong;

        // Jika filter kosong → tampilkan semua
        if (selected === "" || selected === rombongId) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
});
</script>
@endpush
