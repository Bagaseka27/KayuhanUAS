@extends('layouts.app')

@section('title', 'Riwayat Transaksi - Kayuhan')

@section('content')
    {{-- DATA DUMMY RINGKASAN PENDAPATAN (Seharusnya dari Controller) --}}
    @php
        $total_pendapatan = 1350000;
        $pendapatan_tunai = 520000;
        $pendapatan_qris = 830000;
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary-custom mb-0">Riwayat Transaksi</h2>
        <div class="d-flex gap-2">
            <!-- Tombol Export (UI Only) -->
            <button class="btn btn-outline-success btn-sm">
                <i class="fas fa-file-excel me-1"></i> Export Excel
            </button>
            <button class="btn btn-primary-custom btn-sm">
                <i class="fas fa-print me-1"></i> Cetak Laporan
            </button>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="stat-card mb-4 py-3">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="small text-muted fw-bold">Dari Tanggal</label>
                <input type="date" class="form-control form-control-sm" value="{{ date('Y-m-01') }}">
            </div>
            <div class="col-md-3">
                <label class="small text-muted fw-bold">Sampai Tanggal</label>
                <input type="date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-3">
                <label class="small text-muted fw-bold">Cabang</label>
                <select class="form-select form-select-sm">
                    <option value="">Semua Cabang</option>
                    <option value="SBY01">Taman Bungkul</option>
                    <option value="SBY02">Kampus B</option>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-sm w-100 fw-bold text-white" style="background-color: var(--primary);">Tampilkan Data</button>
            </div>
        </div>
    </div>

    <!-- PENGELOMPOKAN PENDAPATAN (Sesuai Permintaan) -->
    <div class="row mb-4">
        {{-- Card 1: TOTAL PENDAPATAN --}}
        <div class="col-md-4">
            <div class="stat-card bg-primary-custom text-white shadow-sm" style="background-color: var(--primary);">
                <div class="text-xs fw-bold text-uppercase mb-1">TOTAL PENDAPATAN (Periode Filter)</div>
                <div class="h4 mb-0 fw-bold">Rp {{ number_format($total_pendapatan, 0, ',', '.') }}</div> 
                <small>Total pendapatan dari semua metode</small>
            </div>
        </div>
        
        {{-- Card 2: PENDAPATAN TUNAI --}}
        <div class="col-md-4">
            <div class="stat-card shadow-sm">
                <div class="text-xs fw-bold text-primary-custom text-uppercase mb-1">PENDAPATAN TUNAI (Cash)</div>
                <div class="h4 mb-0 fw-bold text-dark">Rp {{ number_format($pendapatan_tunai, 0, ',', '.') }}</div>
            </div>
        </div>
        
        {{-- Card 3: PENDAPATAN NON-TUNAI --}}
        <div class="col-md-4">
            <div class="stat-card shadow-sm">
                <div class="text-xs fw-bold text-primary-custom text-uppercase mb-1">PENDAPATAN NON-TUNAI (QRIS)</div>
                <div class="h4 mb-0 fw-bold text-dark">Rp {{ number_format($pendapatan_qris, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <!-- Tabel Transaksi -->
    <div class="stat-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table custom-table mb-0 table-hover">
                <thead class="bg-light">
                    <tr>
                        <th>ID Trx</th>
                        <th>Waktu</th>
                        <th>Kasir</th>
                        <th>Item</th>
                        <th>Metode</th>
                        <th>Total</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Dummy Data -->
                    <tr>
                        <td class="fw-bold">#TRX-901</td>
                        <td>24 Nov, 10:30</td>
                        <td>Siti Aminah</td>
                        <td>2x Kopi Susu Aren, 1x Croissant</td>
                        <td><span class="badge bg-info text-dark">QRIS</span></td>
                        <td class="fw-bold text-primary-custom">Rp 54.000</td>
                        <td class="text-center d-flex justify-content-center gap-1">
                            <!-- Edit -->
                            <button class="btn btn-sm btn-warning text-white" title="Edit Transaksi">
                                <i class="fas fa-edit"></i>
                            </button>
                            <!-- Hapus -->
                            <button class="btn btn-sm btn-danger text-white" title="Hapus Transaksi">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td class="fw-bold">#TRX-902</td>
                        <td>24 Nov, 10:45</td>
                        <td>Budi Santoso</td>
                        <td>1x Americano</td>
                        <td><span class="badge bg-secondary">Tunai</span></td>
                        <td class="fw-bold text-primary-custom">Rp 15.000</td>
                        <td class="text-center d-flex justify-content-center gap-1">
                            <!-- Edit -->
                            <button class="btn btn-sm btn-warning text-white" title="Edit Transaksi">
                                <i class="fas fa-edit"></i>
                            </button>
                            <!-- Hapus -->
                            <button class="btn btn-sm btn-danger text-white" title="Hapus Transaksi">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td class="fw-bold">#TRX-903</td>
                        <td>24 Nov, 11:00</td>
                        <td>Siti Aminah</td>
                        <td>3x Latte Ice</td>
                        <td><span class="badge bg-info text-dark">QRIS</span></td>
                        <td class="fw-bold text-primary-custom">Rp 66.000</td>
                        <td class="text-center d-flex justify-content-center gap-1">
                            <!-- Edit -->
                            <button class="btn btn-sm btn-warning text-white" title="Edit Transaksi">
                                <i class="fas fa-edit"></i>
                            </button>
                            <!-- Hapus -->
                            <button class="btn btn-sm btn-danger text-white" title="Hapus Transaksi">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination Dummy -->
    <div class="d-flex justify-content-end mt-3">
        <nav>
            <ul class="pagination pagination-sm">
                <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                <li class="page-item active"><a class="page-link bg-primary border-primary" href="#">1</a></li>
                <li class="page-item"><a class="page-link text-primary-custom" href="#">2</a></li>
                <li class="page-item"><a class="page-link text-primary-custom" href="#">3</a></li>
                <li class="page-item"><a class="page-link text-primary-custom" href="#">Next</a></li>
            </ul>
        </nav>
    </div>
@endsection