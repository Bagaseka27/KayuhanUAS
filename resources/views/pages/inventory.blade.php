@extends('layouts.app')

@section('title', 'Stok & Inventory - Kayuhan')

@section('content')
    <h3 class="fw-bold text-primary-custom mb-4">Kontrol Stok & Logistik</h3>

    <!-- Navigasi Tab -->
    <ul class="nav nav-tabs mb-4" id="inventoryTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-gudang">
                <i class="fas fa-warehouse me-2"></i>Gudang Pusat (Master)
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-rombong">
                <i class="fas fa-truck-loading me-2"></i>Stok Rombong
            </button>
        </li>
    </ul>

    <div class="tab-content">
        
        <!-- TAB 1: GUDANG PUSAT -->
        <div class="tab-pane fade show active" id="tab-gudang">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold m-0">Stok Master</h5>
                <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#modalRestock">
                    <i class="fas fa-plus me-2"></i> Barang Masuk (Restock)
                </button>
            </div>
            
            <div class="stat-card p-0 overflow-hidden">
                <div class="table-responsive">
                    <table class="table custom-table mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th class="text-center">Stok Saat Ini</th>
                                <th>Satuan</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>BRG-001</td>
                                <td class="fw-bold">Biji Kopi Arabica</td>
                                <td>Bahan Baku</td>
                                <td class="text-center fs-5 fw-bold">50</td>
                                <td>Kg</td>
                                <td class="text-center"><span class="badge bg-success">Aman</span></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-light text-primary"><i class="fas fa-edit"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>BRG-002</td>
                                <td class="fw-bold">Susu UHT Full Cream</td>
                                <td>Bahan Baku</td>
                                <td class="text-center fs-5 fw-bold text-danger">10</td>
                                <td>Liter</td>
                                <td class="text-center"><span class="badge bg-danger">Menipis</span></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-light text-primary"><i class="fas fa-edit"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>BRG-003</td>
                                <td class="fw-bold">Cup Paper 12oz</td>
                                <td>Packaging</td>
                                <td class="text-center fs-5 fw-bold">500</td>
                                <td>Pcs</td>
                                <td class="text-center"><span class="badge bg-success">Aman</span></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-light text-primary"><i class="fas fa-edit"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 2: STOK ROMBONG -->
        <div class="tab-pane fade" id="tab-rombong">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-2">
                    <label class="fw-bold text-muted">Pilih Rombong:</label>
                    <select class="form-select form-select-sm w-auto">
                        <option>RMB-01 (Taman Bungkul)</option>
                        <option>RMB-02 (Kampus B)</option>
                    </select>
                </div>
                <button class="btn btn-accent btn-sm text-white">
                    <i class="fas fa-box-open me-2"></i> Catat Pemakaian (Opname)
                </button>
            </div>

            <div class="stat-card p-0 overflow-hidden">
                <div class="table-responsive">
                    <table class="table custom-table mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Barang</th>
                                <th class="text-center">Stok Awal Shift</th>
                                <th class="text-center">Terjual (System)</th>
                                <th class="text-center">Sisa Fisik</th>
                                <th class="text-center">Selisih</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Cup Paper 12oz</td>
                                <td class="text-center">100</td>
                                <td class="text-center text-primary-custom fw-bold">45</td>
                                <td class="text-center">55</td>
                                <td class="text-center text-success"><i class="fas fa-check"></i> 0</td>
                            </tr>
                            <tr>
                                <td>Susu UHT (Liter)</td>
                                <td class="text-center">10</td>
                                <td class="text-center text-primary-custom fw-bold">4</td>
                                <td class="text-center">5</td>
                                <td class="text-center text-danger fw-bold">-1 (Hilang)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Restock -->
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