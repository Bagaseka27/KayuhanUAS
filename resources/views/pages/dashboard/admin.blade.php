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
            <div class="text-end">
                <span class="fw-bold">{{ now()->translatedFormat('l, d M Y') }}</span><br>
                <span>Waktu Server: <span id="current-time">--:--:--</span></span>
            </div>
        </div>
    </div>

    <!-- 2. Statistik Cards (Lengkap) -->
    <div class="row g-4">
        <div class="col-md-4">
            <div class="stat-card bg-gradient-primary text-white">
                <h6 class="text-white-50">Total Omset Bulan Ini</h6>
                {{-- PERBAIKAN: Menambahkan guard ?? 0 --}}
                <h3 class="fw-bold">Rp {{ number_format($totalOmset ?? 0, 0, ',', '.') }}</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <h6 class="text-muted">Profit Bersih</h6>
                {{-- PERBAIKAN: Menambahkan guard ?? 0 --}}
                <h3 class="fw-bold text-success">Rp {{ number_format($profitBersih ?? 0, 0, ',', '.') }}</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <h6 class="text-muted">Karyawan Aktif</h6>
                <h3 class="fw-bold text-primary-custom">{{ $activeStaff ?? 0 }} Staff</h3>
                {{-- PERBAIKAN: Menghitung Barista dengan guard --}}
                <small class="text-muted">
                    {{ $adminCount }} Admin, {{ $baristaCount }} Barista
                </small>
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
                
                <div class="chart-container" style="position: relative; height: 350px;">
                    <canvas id="salesTrendChart"></canvas>
                </div>

            </div>
        </div>

        <!-- 4. Aksi Cepat Admin -->
        <div class="col-12 mt-4">
            <h5 class="fw-bold text-primary-custom mb-3">Aksi Cepat</h5>
            <div class="row g-3">
                <div class="col-md-3 col-6">
                    <a href="{{ url('/employee') }}" class="quick-action-btn text-decoration-none">
                        <i class="fas fa-user-plus"></i> Kelola Karyawan
                    </a>
                </div>
                <div class="col-md-3 col-6">
                    <a href="{{ url('/menu') }}" class="quick-action-btn text-decoration-none">
                        <i class="fas fa-coffee"></i> Update Menu
                    </a>
                </div>
                <div class="col-md-3 col-6">
                    <a href="{{ url('/history') }}" class="quick-action-btn text-decoration-none">
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

    const salesData = {
        labels: @json($salesTrendData['labels']),
        data: @json($salesTrendData['data'])
    };

    const ctx = document.getElementById('salesTrendChart').getContext('2d');

    // 👉 Tambahkan di sini
    console.log("CTX:", ctx);
    console.log("Sales Data:", salesData);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: salesData.labels,
            datasets: [{
                label: "Total Penjualan (Rp)",
                data: salesData.data,
                backgroundColor: 'rgba(0, 61, 46, 0.8)',
                borderColor: 'rgba(0, 61, 46, 1)',
                borderWidth: 1,
                borderRadius: 5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const timeElement = document.getElementById('current-time');

    function updateTime() {
        const now = new Date();

        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');

        timeElement.textContent = `${hours}:${minutes}:${seconds}`;
    }

    // Update pertama kali
    updateTime();

    // Update setiap 1 detik
    setInterval(updateTime, 1000);
});
</script>


@endpush