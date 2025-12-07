@extends('layouts.app_barista') 

@section('title', 'Riwayat Transaksi Saya')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-primary-custom">Riwayat Transaksi</h1>
        
        <div class="d-flex gap-2 align-items-center">
            
            {{-- BAGIAN FUNGSI EKSPORT & CETAK (HANYA UNTUK ADMIN) --}}
            @if(Auth::check() && Auth::user()->role === 'Admin')
                
                {{-- Form Tersembunyi untuk Export Excel --}}
                <form id="export-excel-form" method="GET" action="{{ route('riwayat.export.excel') }}" style="display: none;">
                    <input type="hidden" name="from_date" value="{{ $fromDate ?? date('Y-m-d') }}">
                    <input type="hidden" name="to_date" value="{{ $toDate ?? date('Y-m-d') }}">
                </form>

                {{-- Form Tersembunyi untuk Cetak Laporan --}}
                <form id="cetak-laporan-form" method="GET" action="{{ route('riwayat.cetak.pdf') }}" style="display: none;">
                    <input type="hidden" name="from_date" value="{{ $fromDate ?? date('Y-m-d') }}">
                    <input type="hidden" name="to_date" value="{{ $toDate ?? date('Y-m-d') }}">
                </form>

                {{-- Tombol Export Excel --}}
                <button class="btn btn-outline-success btn-sm" onclick="document.getElementById('export-excel-form').submit()">
                    <i class="fas fa-file-excel me-1"></i> Export Excel
                </button>
                
                {{-- Tombol Cetak Laporan --}}
                <button class="btn btn-info-custom btn-sm" onclick="document.getElementById('cetak-laporan-form').submit()">
                    <i class="fas fa-print me-1"></i> Cetak Laporan
                </button>
            
            @endif
            {{-- AKHIR BAGIAN FUNGSI EKSPORT & CETAK --}}

            <div class="text-end ms-3">
                {{-- Bagian Waktu Dibuat Dinamis --}}
                <h6 class="fw-bold text-primary-custom mb-0" id="current-date">{{ date('l, d M Y') }}</h6>
                <small class="text-muted">Waktu Server: <span id="current-time-riwayat"></span></small>
            </div>
        </div>
    </div>

    {{-- FILTER SECTION --}}
    <div class="card shadow mb-4">
        <div class="card-body py-3">
            {{-- Menggunakan route barista.riwayat yang sudah dikonfigurasi --}}
            <form action="{{ route('barista.riwayat') }}" method="GET" class="row align-items-center">
                <div class="col-md-4">
                    <label for="from_date_filter" class="form-label fw-bold">Dari Tanggal</label>
                    {{-- Menggunakan fromDate yang dikirim dari Controller --}}
                    <input type="date" class="form-control" id="from_date_filter" name="from_date" value="{{ $fromDate ?? date('Y-m-d') }}">
                </div>
                <div class="col-md-4">
                    <label for="to_date_filter" class="form-label fw-bold">Sampai Tanggal</label>
                    {{-- Menggunakan toDate yang dikirim dari Controller --}}
                    <input type="date" class="form-control" id="to_date_filter" name="to_date" value="{{ $toDate ?? date('Y-m-d') }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary-custom mt-md-4 w-100">Tampilkan Riwayat</button>
                </div>
            </form>
        </div>
    </div>
    
    {{-- RINGKASAN PENDAPATAN (DYNAMIC) --}}
    <div class="row mb-4">
        {{-- Card 1: TOTAL PENDAPATAN --}}
        <div class="col-md-4">
            <div class="stat-card bg-gradient-primary">
                <div class="text-xs fw-bold text-white text-uppercase mb-1">TOTAL PENDAPATAN (Tersaring)</div>
                <div class="h5 mb-0 fw-bold text-white">Rp {{ number_format($total_pendapatan ?? 0, 0, ',', '.') }}</div> 
                <small class="text-white-50">Total transaksi yang Anda catat.</small>
            </div>
        </div>
        
        {{-- Card 2: PENDAPATAN TUNAI --}}
        <div class="col-md-4">
            <div class="stat-card">
                <div class="text-xs fw-bold text-primary-custom text-uppercase mb-1">PENDAPATAN TUNAI</div>
                <div class="h5 mb-0 fw-bold text-dark">Rp {{ number_format($pendapatan_tunai ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>
        
        {{-- Card 3: PENDAPATAN NON-TUNAI --}}
        <div class="col-md-4">
            <div class="stat-card">
                <div class="text-xs fw-bold text-primary-custom text-uppercase mb-1">PENDAPATAN NON-TUNAI (QRIS)</div>
                <div class="h5 mb-0 fw-bold text-dark">Rp {{ number_format($pendapatan_qris ?? 0, 0, ',', '.') }}</div>
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
                            <th>JML ITEM (Total)</th>
                            <th>TOTAL BAYAR</th>
                            <th>DATETIME</th>
                            <th>METODE</th>
                            {{-- AKSI DIHAPUS --}}
                        </tr>
                    </thead>
                    <tbody>
                        {{-- LOOP DATA DARI CONTROLLER --}}
                        @forelse($riwayats as $trx)
                        <tr>
                            <td class="fw-bold">{{ $trx->ID_TRANSAKSI }}</td>
                            <td>{{ $trx->EMAIL }}</td>
                            <td>
                                {{-- LOOP DETAIL PRODUK --}}
                                @php
                                    $totalItems = 0;
                                @endphp
                                @foreach($trx->detailtransaksi as $detail)
                                    <small class="d-block">
                                        <span class="text-primary-custom fw-bold">{{ $detail->ID_PRODUK }}</span>: 
                                        {{ $detail->menu->NAMA_PRODUK ?? 'Produk Dihapus' }} ({{ $detail->JML_ITEM }}x)
                                    </small>
                                    @php
                                        $totalItems += $detail->JML_ITEM;
                                    @endphp
                                @endforeach
                            </td>
                            {{-- Menampilkan total jumlah item dari semua detail --}}
                            <td class="fw-bold">{{ $totalItems }}</td>
                            <td class="text-accent-custom fw-bold">Rp {{ number_format($trx->TOTAL_BAYAR, 0, ',', '.') }}</td>
                            <td>{{ \Carbon\Carbon::parse($trx->DATETIME)->format('Y-m-d H:i') }}</td>
                            <td>
                                <span class="badge badge-payment text-dark badge-{{ $trx->METODE_PEMBAYARAN == 'Tunai' ? 'secondary' : 'info' }}">
                                    {{ $trx->METODE_PEMBAYARAN }}
                                </span>
                            </td>
                            {{-- KOLOM AKSI DIHAPUS --}}
                        </tr>
                        @empty
                        <tr>
                            {{-- COLSPAN disesuaikan --}}
                            <td colspan="7" class="text-center py-4 text-muted">Tidak ada transaksi yang ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    {{-- Pagination Dinamis --}}
    <div class="d-flex justify-content-end mt-3">
        {{ $riwayats->links() }}
    </div>
</div>

@endsection

@push('styles')
<style>
    .text-primary-custom { color: var(--primary) !important; }
    .text-accent-custom { color: var(--accent) !important; } /* Emas */
    .bg-gradient-primary {
        background-color: var(--primary) !important;
        background-image: linear-gradient(180deg, var(--primary) 10%, #1c527f 100%) !important;
    }
    
    /* Badge Payment */
    .badge-payment { 
        border: 1px solid #dee2e6 !important; 
        font-weight: 500 !important;
    }
    .badge-info { 
        background-color: #0dcaf0 !important;
        color: #000000 !important; 
    }
    .badge-secondary { 
        background-color: #6c757d !important;
        color: #ffffff !important; 
    }

    .btn-info-custom { 
        background-color: #0d6efd !important; 
        color: white !important; 
    }
    
    .table-custom thead th {
        color: var(--text-dark) !important;
    }
    .stat-card {
        padding: 1rem;
        border-radius: 0.35rem;
        background-color: #fff;
        border: 1px solid #e3e6f0;
    }
    .text-white-50 {
        color: rgba(255, 255, 255, 0.5) !important;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const timeElement = document.getElementById('current-time-riwayat');
        
        // Fungsi untuk mendapatkan waktu saat ini 
        function updateTimeRiwayat() {
            if (!timeElement) return; 
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                timeZone: 'Asia/Jakarta' 
            });
            
            timeElement.textContent = timeString;
        }

        updateTimeRiwayat();        
        setInterval(updateTimeRiwayat, 1000);
    });
</script>
@endpush