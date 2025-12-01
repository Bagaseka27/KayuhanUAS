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
                <h6 class="text-white-50">Total Omset</h6>
                <h3 class="fw-bold">Rp 45.2 Jt</h3>
                <small class="text-white-50"><i class="fas fa-arrow-up"></i> +12% bulan ini</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <h6 class="text-muted">Profit Bersih</h6>
                <h3 class="fw-bold text-success">Rp 18.5 Jt</h3>
                <small class="text-muted">Target: Rp 20 Jt</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <h6 class="text-muted">Karyawan Aktif</h6>
                <h3 class="fw-bold text-primary-custom">3 Staff</h3>
                <small class="text-muted">1 Admin, 2 Barista</small>
            </div>
        </div>

        <!-- 3. Grafik Penjualan -->
        <div class="col-12 mt-4">
            <div class="stat-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-primary-custom m-0">Tren Penjualan</h5>
                    <select class="form-select form-select-sm w-auto">
                        <option>Tahun 2024</option>
                    </select>
                </div>
                <div style="height: 300px;">
                    <canvas id="salesChart"></canvas>
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
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('salesChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                    datasets: [{
                        label: 'Omset (Juta Rp)',
                        data: [12, 19, 30, 25, 45, 50],
                        backgroundColor: '#003D2E',
                        borderRadius: 5,
                        barThickness: 40
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { borderDash: [5, 5] } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }
    });
</script>
@endpush