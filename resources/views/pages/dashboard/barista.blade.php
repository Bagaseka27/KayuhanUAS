@extends('layouts.app_barista')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-bold text-primary-custom">Dashboard Overview</h2>
            <p class="text-muted m-0">Selamat datang kembali, Barista Staff!</p>
        </div>
        <div class="text-end">
            <h6 class="fw-bold text-primary-custom mb-0">{{ date('l, d M Y') }}</h6>
            <small class="text-muted">System Status: Online</small>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="card-custom">
                <p class="text-muted mb-2">Penjualan Shift Ini</p>
                <h1 class="fw-bold text-primary-custom">Rp 1.250.000</h1>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card-custom">
                <p class="text-muted mb-2">Transaksi Berhasil</p>
                <h1 class="fw-bold text-dark">45 Cup</h1>
            </div>
        </div>
    </div>

    <div class="card-custom p-0 overflow-hidden">
        <div class="p-4 pb-2">
            <h5 class="fw-bold text-primary-custom">Jadwal Shift Karyawan</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-custom mb-0">
                <thead>
                    <tr>
                        <th>TANGGAL</th>
                        <th>NAMA KARYAWAN</th>
                        <th>LOKASI</th>
                        <th>JAM SHIFT</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="fw-bold">2024-11-25</td>
                        <td>Budi Santoso</td>
                        <td><span class="badge bg-light text-dark border">Taman Bungkul</span></td>
                        <td>08:00 - 16:00</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">2024-11-25</td>
                        <td>Siti Aminah</td>
                        <td><span class="badge bg-light text-dark border">Taman Bungkul</span></td>
                        <td>10:00 - 18:00</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection