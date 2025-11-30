@extends('layouts.app')

@section('title', 'Manajemen Karyawan - Kayuhan')

@section('content')
    <h3 class="fw-bold text-primary-custom mb-4">Data Karyawan, Gaji & Jadwal</h3>

    <!-- Navigasi Tab -->
    <ul class="nav nav-tabs mb-4" id="employeeTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-karyawan">
                <i class="fas fa-users me-2"></i>Data Karyawan
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-gaji">
                <i class="fas fa-money-bill-wave me-2"></i>Data Gaji
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-jadwal">
                <i class="fas fa-calendar-alt me-2"></i>Jadwal Shift
            </button>
        </li>
    </ul>

    <div class="tab-content">
        
        <!-- TAB 1: DATA KARYAWAN -->
        <div class="tab-pane fade show active" id="tab-karyawan">
            <div class="d-flex justify-content-end mb-3">
                <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#modalKaryawan">
                    <i class="fas fa-user-plus me-2"></i> Tambah Karyawan
                </button>
            </div>
            <div class="stat-card p-0 overflow-hidden">
                <div class="table-responsive">
                    <table class="table custom-table mb-0 table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th>Email</th>
                                <th>Nama Lengkap</th>
                                <th>Posisi</th>
                                <th>Cabang</th>
                                <th>No HP</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Contoh Data Dummy (Nanti diganti @foreach) -->
                            <tr>
                                <td>budi@kayuhan.com</td>
                                <td class="fw-bold">Budi Santoso</td>
                                <td><span class="badge bg-success">Barista</span></td>
                                <td>Taman Bungkul</td>
                                <td>08123456789</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary me-1"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>siti@kayuhan.com</td>
                                <td class="fw-bold">Siti Aminah</td>
                                <td><span class="badge bg-success">Barista</span></td>
                                <td>Kampus B</td>
                                <td>08198765432</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary me-1"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 2: DATA GAJI -->
        <div class="tab-pane fade" id="tab-gaji">
            <div class="d-flex justify-content-between mb-3">
                <div class="d-flex align-items-center">
                    <label class="me-2 fw-bold text-muted">Periode:</label>
                    <input type="month" class="form-control form-control-sm w-auto" value="{{ date('Y-m') }}">
                </div>
                <button class="btn btn-primary-custom btn-sm">
                    <i class="fas fa-calculator me-2"></i> Hitung Gaji Baru
                </button>
            </div>
            <div class="stat-card p-0 overflow-hidden">
                <div class="table-responsive">
                    <table class="table custom-table mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>ID Slip</th>
                                <th>Karyawan</th>
                                <th>Gaji Pokok</th>
                                <th>Bonus</th>
                                <th>Total Terima</th>
                                <th class="text-center">Cetak</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#SLIP-001</td>
                                <td>Budi Santoso</td>
                                <td>Rp 1.500.000</td>
                                <td class="text-success">+ Rp 200.000</td>
                                <td class="fw-bold text-primary-custom">Rp 1.700.000</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-light text-secondary"><i class="fas fa-print"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 3: JADWAL SHIFT -->
        <div class="tab-pane fade" id="tab-jadwal">
            <div class="alert alert-info border-0 d-flex align-items-center" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                <div>Jadwal shift diatur mingguan. Pastikan tidak ada jadwal bentrok.</div>
            </div>
            <div class="stat-card p-0 overflow-hidden">
                <div class="table-responsive">
                    <table class="table custom-table mb-0">
                        <thead>
                            <tr>
                                <th>Hari/Tanggal</th>
                                <th>Shift Pagi (08:00 - 16:00)</th>
                                <th>Shift Sore (16:00 - 23:00)</th>
                                <th>Lokasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold">Senin, 25 Nov</td>
                                <td>Budi Santoso</td>
                                <td>Siti Aminah</td>
                                <td>Taman Bungkul</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- MODAL TAMBAH KARYAWAN -->
    <div class="modal fade" id="modalKaryawan" tabindex="-1">
        <div class="modal-dialog">
            <form action="#" method="POST" class="modal-content">
                @csrf <!-- Token Keamanan Laravel -->
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Karyawan Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email (untuk Login)</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Posisi</label>
                            <select name="role" class="form-select">
                                <option value="Barista">Barista</option>
                                <option value="Admin">Admin</option>
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">No HP</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
@endsection