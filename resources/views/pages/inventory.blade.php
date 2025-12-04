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
                                <th class="text-center">JUMLAH (GUDANG PUSAT)</th>
                                <th class="text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr data-id="BRG-001" data-nama="Biji Kopi Arabica (Kg)" data-stok="50">
                                <td class="fw-bold">BRG-001</td>
                                <td>Biji Kopi Arabica (Kg)</td>
                                <td class="text-center fs-6 fw-bold">50</td>
                                <td class="text-center">
                                    <button class="btn btn-sm text-info edit-btn" data-bs-toggle="modal" data-bs-target="#modalStokGudang" data-mode="edit" data-id="BRG-001">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm text-danger delete-btn" onclick="confirmDelete('BRG-001', 'master')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr data-id="BRG-002" data-nama="Susu UHT Full Cream (L)" data-stok="100">
                                <td class="fw-bold">BRG-002</td>
                                <td>Susu UHT Full Cream (L)</td>
                                <td class="text-center fs-6 fw-bold">100</td>
                                <td class="text-center">
                                    <button class="btn btn-sm text-info edit-btn" data-bs-toggle="modal" data-bs-target="#modalStokGudang" data-mode="edit" data-id="BRG-002">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm text-danger delete-btn" onclick="confirmDelete('BRG-002', 'master')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                    </table>
                </div>
            </div>
            
            </div>

        <div class="tab-pane fade" id="tab-rombong">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-2">
                    <label class="fw-bold text-muted">Pilih Rombong:</label>
                    <select class="form-select form-select-sm w-auto" id="select-rombong-filter">
                        <option value="">Semua Rombong</option>
                        <option value="RMB-01">RMB-01</option>
                        <option value="RMB-02">RMB-02</option>
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
                            <tr data-id="RMB-BRG-001" data-nama="Biji Kopi Arabica (Kg)" data-rombong="RMB-01" data-awal="5" data-akhir="4">
                                <td class="fw-bold">BRG-001</td>
                                <td>Biji Kopi Arabica (Kg)</td>
                                <td>RMB-01</td>
                                <td class="text-center">5</td>
                                <td class="text-center fw-bold text-danger">4</td> 
                                <td class="text-center">
                                    <button class="btn btn-sm text-info edit-rombong-btn" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEditStokRombong" 
                                            data-mode="edit" 
                                            data-id="RMB-BRG-001">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm text-danger delete-btn" onclick="confirmDelete('RMB-BRG-001', 'rombong')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalStokGudang" tabindex="-1" aria-labelledby="modalStokGudangLabel">
        <div class="modal-dialog">
            <form action="#" method="POST" class="modal-content" id="formStokGudang">
                @csrf
                <input type="hidden" name="id" id="stok_gudang_id">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalStokGudangLabel">Kelola Stok Gudang</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama_barang" class="form-label">Nama Barang</label>
                        <input type="text" name="nama_barang" id="nama_barang" class="form-control" placeholder="Biji Kopi Arabica (Kg)" required>
                    </div>
                    <div class="mb-3">
                        <label for="jumlah_stok" class="form-label">Jumlah Stok</label>
                        <input type="number" name="jumlah_stok" id="jumlah_stok" class="form-control" placeholder="50" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="modal fade" id="modalEditStokRombong" tabindex="-1" aria-labelledby="modalEditStokRombongLabel">
        <div class="modal-dialog">
            <form action="#" method="POST" class="modal-content" id="formStokRombong">
                @csrf
                <input type="hidden" name="id" id="stok_rombong_id">
                <input type="hidden" name="barang_id_submit" id="barang_id_submit">
                <input type="hidden" name="rombong_id_submit" id="rombong_id_submit">
                
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalEditStokRombongLabel">Edit Stok Rombong</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rombong_barang" class="form-label">Barang</label>
                        <select name="barang_id" id="rombong_barang" class="form-select" disabled> 
                            <option value="BRG-001">Biji Kopi Arabica (Kg)</option>
                            <option value="BRG-002">Susu UHT Full Cream (L)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="rombong_lokasi" class="form-label">Rombong</label>
                        <select name="rombong_id" id="rombong_lokasi" class="form-select" disabled> 
                            <option value="RMB-01">RMB-01</option>
                            <option value="RMB-02">RMB-02</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="stok_awal" class="form-label">Stok Awal</label>
                            <input type="number" name="stok_awal" id="stok_awal" class="form-control" placeholder="5" required> 
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="stok_akhir" class="form-label">Stok Akhir</label>
                            <input type="number" name="stok_akhir" id="stok_akhir" class="form-control" placeholder="4" required>
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
    
    <div class="modal fade" id="modalTambahStokRombong" tabindex="-1" aria-labelledby="modalTambahStokRombongLabel">
        <div class="modal-dialog modal-lg">
            <form action="/inventory/rombong/batch-store" method="POST" class="modal-content" id="formBatchStokRombong">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTambahStokRombongLabel">Tambah Stok Rombong (Batch)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rombong_tujuan" class="form-label fw-bold">Rombong Tujuan</label>
                        <select name="rombong_tujuan" id="rombong_tujuan" class="form-select" required>
                            <option value="">-- Pilih Rombong --</option>
                            <option value="RMB-01">RMB-01</option>
                            <option value="RMB-02">RMB-02</option>
                        </select>
                    </div>

                    <h6 class="mt-4 mb-3 fw-bold">Daftar Barang yang Ditambahkan:</h6>
                    
                    <div id="item-container">
                        </div>

                    <button type="button" class="btn btn-sm btn-outline-primary mt-3" id="add-item-row">
                        <i class="fas fa-plus"></i> Tambah Baris Barang
                    </button>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Stok Batch</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modalRestock" tabindex="-1">
         <div class="modal-dialog">
            <form action="#" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Form Barang Masuk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Pilih Barang</label>
                        <select name="barang_id" class="form-select">
                            <option value="1">Biji Kopi Arabica</option>
                            <option value="2">Susu UHT</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah Masuk</label>
                        <input type="number" name="qty" class="form-control" placeholder="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan / Supplier</label>
                        <textarea name="keterangan" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom">Simpan Stok</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    // Data dummy barang master untuk dropdown di modal batch
    const masterItems = [
        { id: 'BRG-001', name: 'Biji Kopi Arabica (Kg)' },
        { id: 'BRG-002', name: 'Susu UHT Full Cream (L)' },
        { id: 'BRG-003', name: 'Cup Paper 12oz (Pcs)' },
    ];

    let itemCounter = 0; // Counter untuk nama input array yang unik

    // Fungsi untuk membuat satu baris input barang dan jumlah (untuk modal batch)
    function createItemRow(initialValue = {id: '', qty: ''}) {
        const index = itemCounter++;
        const itemRow = document.createElement('div');
        itemRow.classList.add('row', 'g-2', 'mb-3', 'item-row-dynamic');
        itemRow.innerHTML = `
            <div class="col-6">
                <select name="items[${index}][id]" class="form-select form-select-sm" required>
                    <option value="">-- Pilih Barang --</option>
                    ${masterItems.map(item => 
                        `<option value="${item.id}" ${initialValue.id === item.id ? 'selected' : ''}>${item.name}</option>`
                    ).join('')}
                </select>
            </div>
            <div class="col-4">
                <input type="number" name="items[${index}][qty]" class="form-control form-control-sm" placeholder="Jumlah Tambah" value="${initialValue.qty}" required min="1">
            </div>
            <div class="col-2 d-flex align-items-center">
                <button type="button" class="btn btn-sm btn-danger remove-item-row">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        return itemRow;
    }

    // Fungsi umum untuk konfirmasi hapus (DIGUNAKAN KARENA TOMBOL HAPUS ADA LAGI)
    function confirmDelete(id, tipe) {
        if (confirm(`Hapus data ${tipe} dengan ID ${id} ini?`)) {
            // Lakukan AJAX DELETE atau form submit ke route yang sesuai
            console.log(`Menghapus item ${tipe} dengan ID: ${id}`);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        // --- 1. SETUP MODAL BATCH INPUT ROMBONG ---
        const itemContainer = document.getElementById('item-container');
        const addButton = document.getElementById('add-item-row');
        const modalTambah = document.getElementById('modalTambahStokRombong');
        
        if (addButton) {
            addButton.addEventListener('click', () => {
                itemContainer.appendChild(createItemRow());
            });
        }
        
        itemContainer.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-item-row') || e.target.closest('.remove-item-row')) {
                e.target.closest('.item-row-dynamic').remove();
            }
        });

        if (modalTambah) {
            modalTambah.addEventListener('show.bs.modal', function () {
                itemContainer.innerHTML = '';
                itemCounter = 0;
                itemContainer.appendChild(createItemRow());
            });
        }
        
        // --- 2. LISTENER MODAL GUDANG PUSAT (Untuk Edit) ---
        var modalStokGudang = document.getElementById('modalStokGudang');
        if (modalStokGudang) {
            modalStokGudang.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var mode = button.getAttribute('data-mode');
                var modalTitle = modalStokGudang.querySelector('.modal-title');
                var form = modalStokGudang.querySelector('#formStokGudang');
                form.reset();

                if (mode === 'create') {
                    modalTitle.textContent = 'Tambah Stok Gudang';
                    form.action = '/inventory/master/store'; 
                } else if (mode === 'edit') {
                    var itemId = button.getAttribute('data-id');
                    var row = document.querySelector(`tr[data-id="${itemId}"]`);
                    var namaBarang = row.querySelector('td:nth-child(2)').textContent;
                    var stok = row.querySelector('td:nth-child(3)').textContent;
                    
                    modalTitle.textContent = 'Edit Stok Gudang: ' + itemId;
                    
                    modalStokGudang.querySelector('#stok_gudang_id').value = itemId;
                    modalStokGudang.querySelector('#nama_barang').value = namaBarang.trim();
                    modalStokGudang.querySelector('#jumlah_stok').value = stok.trim();

                    form.action = '/inventory/master/update/' + itemId; 
                }
            });
        }

        // --- 3. LISTENER MODAL STOK ROMBONG (Untuk Edit/Opname) ---
        var modalRombong = document.getElementById('modalEditStokRombong');
        
        if (modalRombong) {
            modalRombong.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var mode = button.getAttribute('data-mode');
                var modalTitle = modalRombong.querySelector('.modal-title');
                var form = modalRombong.querySelector('#formStokRombong');
                form.reset();
                
                if (mode === 'edit') {
                    var itemId = button.getAttribute('data-id');
                    var row = document.querySelector(`tr[data-id="${itemId}"]`);
                    
                    var barangId = row.querySelector('td:nth-child(1)').textContent;
                    var rombongId = row.getAttribute('data-rombong');
                    var stokAwal = row.getAttribute('data-awal');
                    var stokAkhir = row.getAttribute('data-akhir'); 

                    modalTitle.textContent = 'Edit Stok Rombong: ' + rombongId;
                    
                    // 1. Isi field display (select disabled)
                    modalRombong.querySelector('#rombong_barang').value = barangId.trim(); 
                    modalRombong.querySelector('#rombong_lokasi').value = rombongId;
                    
                    // 2. Isi field EDITABLE
                    modalRombong.querySelector('#stok_awal').value = stokAwal;
                    modalRombong.querySelector('#stok_akhir').value = stokAkhir || stokAwal;
                    
                    // 3. ISI HIDDEN INPUTS (Untuk submission)
                    modalRombong.querySelector('#stok_rombong_id').value = itemId;
                    modalRombong.querySelector('#barang_id_submit').value = barangId.trim();
                    modalRombong.querySelector('#rombong_id_submit').value = rombongId;
                    
                    form.action = '/inventory/rombong/update/' + itemId; 
                }
            });
        }
    });
</script>
@endpush