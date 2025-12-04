@extends('layouts.app_barista') 

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-primary-custom">Riwayat Transaksi</h1>
        
        <button class="btn btn-success" id="export-excel">
            <i class="fas fa-file-excel"></i> Export Excel
        </button>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body py-3">
            <form action="{{ route('barista.riwayat') }}" method="GET" class="row align-items-center">
                <div class="col-md-4">
                    <label for="tanggal_filter" class="form-label fw-bold">Pilih Tanggal Riwayat</label>
                    <input type="date" class="form-control" id="tanggal_filter" name="tanggal" value="{{ request('tanggal', date('Y-m-d')) }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary-custom mt-md-4 w-100">Tampilkan Riwayat</button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stat-card bg-gradient-primary">
                <div class="text-xs fw-bold text-white text-uppercase mb-1">TOTAL PENDAPATAN (Hari Ini)</div>
                <div class="h5 mb-0 fw-bold text-white">Rp 67.000</div> 
                <small class="text-white-50">Dari QRIS & Tunai</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="text-xs fw-bold text-primary-custom text-uppercase mb-1">PENDAPATAN TUNAI</div>
                <div class="h5 mb-0 fw-bold text-dark">Rp 22.000</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="text-xs fw-bold text-primary-custom text-uppercase mb-1">PENDAPATAN NON-TUNAI (QRIS)</div>
                <div class="h5 mb-0 fw-bold text-dark">Rp 45.000</div>
            </div>
        </div>
    </div>


    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-custom table-hover" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID TRANSAKSI</th>
                            <th>EMAIL</th>
                            <th>DETAIL PRODUK (ID & NAMA)</th> 
                            <th>JML ITEM</th>
                            <th>TOTAL BAYAR</th>
                            <th>DATETIME</th>
                            <th>METODE</th>
                            <th>AKSI</th> 
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>TRX-901</td>
                            <td>siti@kayuhan.com</td>
                            <td>
                                <small>
                                    <span class="text-primary-custom fw-bold">M01</span>: Kopi Susu Aren (1x)<br>
                                    <span class="text-primary-custom fw-bold">M03</span>: Latte Ice (1x)
                                </small>
                            </td>
                            <td>2</td>
                            <td class="text-accent-custom fw-bold">Rp 45,000</td>
                            <td>2024-11-24 10:00</td>
                            <td>
                                <span class="badge badge-payment text-dark" style="color: #000000 !important; background-color: #ffffff !important;">QRIS</span>
                            </td>
                            <td>
                                <a href="#" class="btn btn-sm btn-info-custom">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>TRX-902</td>
                            <td>siti@kayuhan.com</td>
                            <td>
                                <small>
                                    <span class="text-primary-custom fw-bold">M02</span>: Americano (1x)
                                </small>
                            </td>
                            <td>1</td>
                            <td class="text-accent-custom fw-bold">Rp 22,000</td>
                            <td>2024-11-24 10:15</td>
                            <td>
                                <span class="badge badge-payment text-dark" style="color: #000000 !important; background-color: #ffffff !important;">Tunai</span>
                            </td>
                            <td>
                                <a href="#" class="btn btn-sm btn-info-custom">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Menggunakan variabel CSS yang sudah didefinisikan */
    .text-primary-custom { color: var(--primary) !important; }
    .text-accent-custom { color: var(--accent) !important; } /* Emas */
    
    /* Badge Payment (QRIS & Tunai): Putih dengan teks Hitam */
    table tbody td span.badge.badge-payment.text-dark,
    span.badge.badge-payment.text-dark,
    .badge.badge-payment { 
        background-color: #ffffff !important; 
        color: #000000 !important; 
        border: 1px solid #dee2e6 !important; 
        font-weight: 500 !important;
    }
    
    .btn-info-custom { 
        background-color: #0d6efd !important; 
        color: white !important; 
    }
    
    .table-custom thead th {
        color: var(--text-dark) !important;
    }
</style>
@endpush

@endsection