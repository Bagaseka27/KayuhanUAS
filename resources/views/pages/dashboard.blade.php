@extends('layouts.app')

@section('title', 'Dashboard - Kayuhan')

@section('content')
    <!-- 1. Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-primary-custom mb-0">Dashboard Overview</h3>
            <!-- Mengambil nama user yang sedang login -->
            <p class="text-muted">Selamat datang, <span class="fw-bold">{{ Auth::user()->name ?? 'User' }}</span>!</p>
        </div>
        <div class="d-none d-md-block text-end">
            <!-- Menampilkan Tanggal Hari Ini (PHP) -->
            <p class="mb-0 fw-bold text-primary-custom">{{ date('l, d M Y') }}</p>
            <small class="text-muted">System Status: Online</small>
        </div>
    </div>

    <!-- 2. Statistik Cards -->
    <div class="row g-4">
        <!-- Card 1: Total Omset -->
        <div class="col-md-4">
            <div class="stat-card bg-gradient-primary text-white">
                <h6 class="text-white-50">Total Omset</h6>
                <h3 class="fw-bold">Rp 45.2 Jt</h3>
                <small class="text-white-50"><i class="fas fa-arrow-up"></i> +12% dari bulan lalu</small>
            </div>
        </div>
        
        <!-- Card 2: Profit Bersih -->
        <div class="col-md-4">
            <div class="stat-card">
                <h6 class="text-muted">Profit Bersih</h6>
                <h3 class="fw-bold text-success">Rp 18.5 Jt</h3>
                <small class="text-muted">Target: Rp 20 Jt</small>
            </div>
        </div>

        <!-- Card 3: Karyawan (Agar Layout Seimbang 3 Kolom) -->
        <div class="col-md-4">
            <div class="stat-card">
                <h6 class="text-muted">Karyawan Aktif</h6>
                <h3 class="fw-bold text-primary-custom">3 Staff</h3>
                <small class="text-muted">1 Admin, 2 Barista</small>
            </div>
        </div>

        <!-- 3. Chart Section -->
        <div class="col-12 mt-4">
            <div class="stat-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-primary-custom m-0">Tren Penjualan Bulanan</h5>
                    <select class="form-select form-select-sm w-auto">
                        <option>Tahun 2024</option>
                        <option>Tahun 2023</option>
                    </select>
                </div>
                <!-- Canvas untuk Chart.js -->
                <div style="height: 300px;">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>

        <!-- 4. Quick Actions (Menu Cepat) -->
        <div class="col-12 mt-4">
            <h5 class="fw-bold text-primary-custom mb-3">Aksi Cepat</h5>
            <div class="row g-3">
                <div class="col-md-3 col-6">
                    <a href="{{ url('/menu') }}" class="quick-action-btn text-decoration-none">
                        <i class="fas fa-coffee"></i> Tambah Menu
                    </a>
                </div>
                <div class="col-md-3 col-6">
                    <a href="{{ url('/employees') }}" class="quick-action-btn text-decoration-none">
                        <i class="fas fa-user-plus"></i> Data Karyawan
                    </a>
                </div>
                <div class="col-md-3 col-6">
                    <!-- Contoh trigger modal transaksi manual -->
                    <button class="quick-action-btn w-100" onclick="alert('Fitur ini ada di halaman Transaksi')">
                        <i class="fas fa-file-invoice-dollar"></i> Input Transaksi
                    </button>
                </div>
                <div class="col-md-3 col-6">
                    <a href="#" class="quick-action-btn text-decoration-none">
                        <i class="fas fa-calculator"></i> Laporan Keuangan
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

<!-- Script Khusus Halaman Ini -->
@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Cek apakah elemen chart ada
        const ctx = document.getElementById('salesChart');
        
        if (ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                    datasets: [{
                        label: 'Omset (Juta Rp)',
                        data: [12, 19, 30, 25, 45, 50], // Nanti data ini diganti variabel PHP dari Controller
                        backgroundColor: '#003D2E', // Warna Hijau Kayuhan
                        borderRadius: 5,
                        barThickness: 30
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false // Sembunyikan legenda default
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                borderDash: [2, 2] // Garis putus-putus biar estetik
                            }
                        },
                        x: {
                            grid: {
                                display: false // Hilangkan garis vertikal
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush