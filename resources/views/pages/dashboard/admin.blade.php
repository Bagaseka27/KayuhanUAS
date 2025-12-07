@extends('layouts.app')

@section('title', 'Dashboard Admin - Kayuhan')

@section('content')

    <!-- 1. Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-primary-custom mb-0">Dashboard Overview</h3>
            <p class="text-muted">Selamat datang, <span class="fw-bold">{{ Auth::user()->name }}</span> (Owner)!</p>
        </div>
        <div class="d-none d-md-block text-end">
            <p class="mb-0 fw-bold text-primary-custom">{{ date('l, d M Y') }}</p>
            <small class="text-muted">System Status: Online</small>
        </div>
    </div>

    <!-- 2. Statistik Cards (Lengkap) -->
    <div class="row g-4">
        <div class="col-md-4">
            <div class="stat-card bg-gradient-primary text-white">
                <h6 class="text-white-50">Total Omset Bulan Ini</h6>
                {{-- PERBAIKAN: Menambahkan guard ?? 0 --}}
                <h3 class="fw-bold">Rp {{ number_format($totalOmset ?? 0, 0, ',', '.') }}</h3>
                <small class="text-white-50"><i class="fas fa-arrow-up"></i> +12% bulan ini</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <h6 class="text-muted">Profit Bersih</h6>
                {{-- PERBAIKAN: Menambahkan guard ?? 0 --}}
                <h3 class="fw-bold text-success">Rp {{ number_format($profitBersih ?? 0, 0, ',', '.') }}</h3>
                <small class="text-muted">Target: Rp {{ number_format($targetProfit ?? 0, 0, ',', '.') }}</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <h6 class="text-muted">Karyawan Aktif</h6>
                {{-- PERBAIKAN: Menambahkan guard ?? 0 --}}
                <h3 class="fw-bold text-primary-custom">{{ $activeStaff ?? 0 }} Staff</h3>
                {{-- PERBAIKAN: Menghitung Barista dengan guard --}}
                <small class="text-muted">1 Admin, {{ ($activeStaff ?? 1) - 1 }} Barista</small>
            </div>
        </div>

        <!-- 3. Grafik Penjualan (Memanfaatkan Chart.js) -->
        <div class="col-12 mt-4">
            <div class="stat-card p-4">
                
                {{-- KOTAK CHART: Chart area yang baru --}}
                <div class="card-header py-3 d-flex justify-content-between align-items-center mb-3 p-0 border-0">
                    {{-- PERBAIKAN: Menambahkan guard ?? Carbon\Carbon::now()->year --}}
                    <h5 class="fw-bold text-primary-custom m-0">Tren Penjualan (Tahun {{ $selectedYear ?? Carbon\Carbon::now()->year }})</h5>
                    {{-- Form Filter Tahun --}}
                    {{-- PERBAIKAN KRITIS: Mengganti admin.dashboard dengan dashboard --}}
                    <form action="{{ route('dashboard') }}" method="GET" class="d-flex">
                        <select name="tahun" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                            @for ($y = Carbon\Carbon::now()->year; $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ ($selectedYear ?? Carbon\Carbon::now()->year) == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                            @endfor
                        </select>
                    </form>
                </div>
                
                <div style="height: 350px;">
                    {{-- ID CANVAS UNTUK JAVASCRIPT --}}
                    <canvas id="salesTrendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- 4. Aksi Cepat Admin -->
        <div class="col-12 mt-4">
            <h5 class="fw-bold text-primary-custom mb-3">Aksi Cepat</h5>
            <div class="row g-3">
                <div class="col-md-3 col-6">
                    <a href="{{ url('/employees') }}" class="quick-action-btn text-decoration-none">
                        <i class="fas fa-user-plus"></i> Kelola Karyawan
                    </a>
                </div>
                <div class="col-md-3 col-6">
                    <a href="{{ url('/menu') }}" class="quick-action-btn text-decoration-none">
                        <i class="fas fa-coffee"></i> Update Menu
                    </a>
                </div>
                <div class="col-md-3 col-6">
                    <a href="#" class="quick-action-btn text-decoration-none">
                        <i class="fas fa-file-invoice"></i> Laporan Keuangan
                    </a>
                </div>
                <div class="col-md-3 col-6">
                    <a href="{{ url('/inventory') }}" class="quick-action-btn text-decoration-none">
                        <i class="fas fa-boxes"></i> Cek Stok Gudang
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
{{-- Memastikan Chart.js dimuat --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script> 

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Ambil data dari Controller (menambahkan guard kosong [] untuk JS)
    const salesData = @json($salesTrendData ?? ['labels' => [], 'data' => []]);
    
    // 2. Konfigurasi Chart.js
    const ctx = document.getElementById('salesTrendChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'bar', // Anda bisa ganti ke 'line'
        data: {
            labels: salesData.labels, // Nama bulan: Jan, Feb, Mar, dst.
            datasets: [{
                label: "Total Penjualan (Rp)",
                backgroundColor: 'rgba(0, 61, 46, 0.8)', // Warna Hijau Gelap
                borderColor: 'rgba(0, 61, 46, 1)',
                data: salesData.data, // Data penjualan bulanan
                borderWidth: 1,
                borderRadius: 5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Penjualan (Rp)',
                    },
                    // Format label Y-axis (misalnya 500000 menjadi Rp 500rb)
                    ticks: {
                        callback: function(value, index, ticks) {
                            if (value >= 1000000) {
                                return 'Rp ' + (value / 1000000).toFixed(1) + ' Jt';
                            } else if (value >= 1000) {
                                return 'Rp ' + (value / 1000) + ' Rb';
                            }
                            return 'Rp ' + value.toLocaleString('id-ID'); // Format Rupiah
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += 'Rp ' + context.parsed.y.toLocaleString('id-ID'); // Format Rupiah
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush