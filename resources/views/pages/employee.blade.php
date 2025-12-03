employee.blade.php

@extends('layouts.app')

@section('title', 'Manajemen SDM - Kayuhan')

@section('content')
    {{-- CSS KHUSUS UNTUK HALAMAN INI (Tab Effect) --}}
    <style>
        /* Hilangkan border default bootstrap */
        .nav-tabs {
            border-bottom: 2px solid #e9ecef;
        }
        
        .nav-tabs .nav-link {
            border: none;
            color: #6c757d; /* Warna abu-abu saat tidak aktif */
            font-weight: 600;
            padding: 10px 20px;
            position: relative;
            transition: all 0.3s ease;
            background: transparent;
        }

        /* Efek Hover (Saat kursor diarahkan) */
        .nav-tabs .nav-link:hover {
            color: var(--primary); /* Berubah jadi Hijau */
            border: none;
        }

        /* Garis Bawah Hijau yang Bergerak */
        .nav-tabs .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 3px;
            bottom: -2px; /* Posisi tepat di garis bawah */
            left: 0;
            background-color: var(--primary); /* Warna Hijau Kayuhan */
            transition: width 0.3s ease; /* Animasi gerak */
        }

        /* Saat Hover atau Aktif, garis memanjang 100% */
        .nav-tabs .nav-link:hover::after,
        .nav-tabs .nav-link.active::after {
            width: 100%;
        }

        /* State Aktif */
        .nav-tabs .nav-link.active {
            color: var(--primary);
            background: transparent;
        }
    </style>

    {{-- DATA DUMMY --}}
    @php
        $employees = [
            (object)['email' => 'budi@kayuhan.com', 'id_jabatan' => '4', 'id_rombong' => 'RMB-01', 'id_cabang' => 'CBG-SBY01', 'name' => 'Budi Santoso', 'phone' => '08123456789', 'role' => 'Barista'],
            (object)['email' => 'siti@kayuhan.com', 'id_jabatan' => '3', 'id_rombong' => 'RMB-01', 'id_cabang' => 'CBG-SBY01', 'name' => 'Siti Aminah', 'phone' => '08198765432', 'role' => 'Barista'],
            (object)['email' => 'admin@kayuhan.com', 'id_jabatan' => '1', 'id_rombong' => '-', 'id_cabang' => '-', 'name' => 'Andi Wijaya', 'phone' => '08111122233', 'role' => 'Admin']
        ];
        $payrolls = [
            (object)['id' => 'GJ-241101', 'name' => 'Budi Santoso', 'periode' => '2024-11', 'basic' => 1250000, 'bonus' => 500000, 'total' => 1750000]
        ];
    @endphp

    <!-- JUDUL HALAMAN -->
    <h2 class="fw-bold text-primary-custom mb-4">Data Karyawan, Gaji & Jadwal</h2>

    <!-- NAVIGASI TAB DENGAN EFEK ANIMASI -->
    <ul class="nav nav-tabs mb-4" id="employeeTabs" role="tablist">
        <li class="nav-item me-2">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-karyawan">
                Data Karyawan
            </button>
        </li>
        <li class="nav-item me-2">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-gaji">
                Data Gaji
            </button>
        </li>
        <li class="nav-item me-2">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-jadwal">
                Jadwal Shift
            </button>
        </li>
    </ul>

    <div class="tab-content">
        
        <!-- === TAB 1: DATA KARYAWAN === -->
        <div class="tab-pane fade show active" id="tab-karyawan">
            <!-- Tombol Tambah (HIJAU) -->
            <div class="d-flex justify-content-end mb-3">
                <button class="btn text-white fw-bold py-2 px-3 rounded-3" style="background-color: var(--primary);" data-bs-toggle="modal" data-bs-target="#modalKaryawan">
                    <i class="fas fa-user-plus me-2"></i> Tambah Karyawan
                </button>
            </div>

            <!-- Tabel Card Putih -->
            <div class="stat-card p-0 overflow-hidden shadow-sm border-0 rounded-4">
                <div class="table-responsive">
                    <table class="table custom-table mb-0 align-middle">
                        <thead class="bg-light text-secondary text-uppercase small fw-bold">
                            <tr>
                                <th class="py-3 ps-4">EMAIL (PK)</th>
                                <th>ID JABATAN</th>
                                <th>ID ROMBONG</th>
                                <th>ID CABANG</th>
                                <th>NAMA</th>
                                <th>NO HP</th>
                                <th>POSISI</th>
                                <th class="text-center pe-4">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @foreach($employees as $emp)
                            <tr>
                                <td class="ps-4">{{ $emp->email }}</td>
                                <td>{{ $emp->id_jabatan }}</td>
                                <td>{{ $emp->id_rombong }}</td>
                                <td>{{ $emp->id_cabang }}</td>
                                <td class="fw-bold text-dark">{{ $emp->name }}</td>
                                <td>{{ $emp->phone }}</td>
                                <td>{{ $emp->role }}</td>
                                <td class="text-center pe-4">
                                    <button class="btn btn-sm btn-light text-primary me-1 rounded-2"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-light text-danger rounded-2"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- === TAB 2: DATA GAJI === -->
        <div class="tab-pane fade" id="tab-gaji">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center bg-white px-3 py-2 rounded-3 shadow-sm">
                    <label class="fw-bold me-2 text-primary-custom">Periode:</label>
                    <input type="month" class="form-control border-0 bg-transparent fw-bold" value="2024-11">
                </div>
                <!-- Tombol Tambah (HIJAU) -->
                    <button class="btn text-white fw-bold py-2 px-3 rounded-3" style="background-color: var(--primary);" data-bs-toggle="modal" data-bs-target="#modalGaji">
                        <i class="fas fa-plus me-2"></i> Tambah Data Gaji
                    </button>
            </div>

            <div class="stat-card p-0 overflow-hidden shadow-sm border-0 rounded-4">
                <div class="table-responsive">
                    <table class="table custom-table mb-0 align-middle">
                        <thead class="bg-light text-secondary text-uppercase small fw-bold">
                            <tr>
                                <th class="py-3 ps-4">ID GAJI</th>
                                <th>KARYAWAN</th>
                                <th>PERIODE</th>
                                <th>GAJI POKOK</th>
                                <th>BONUS</th>
                                <th>TOTAL</th>
                                <th class="text-center pe-4">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @foreach($payrolls as $pay)
                            <tr>
                                <td class="ps-4 fw-bold">{{ $pay->id }}</td>
                                <td>{{ $pay->name }}</td>
                                <td>{{ $pay->periode }}</td>
                                <td>Rp {{ number_format($pay->basic) }}</td>
                                <td class="text-success fw-bold">+ Rp {{ number_format($pay->bonus) }}</td>
                                <td class="fw-bold text-primary-custom fs-6">Rp {{ number_format($pay->total) }}</td>
                                <td class="text-center pe-4">
                                    <button class="btn btn-sm btn-light text-primary me-1 rounded-2"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-light text-danger rounded-2"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- === TAB 3: JADWAL SHIFT === -->
        <div class="tab-pane fade" id="tab-jadwal">
            <div class="d-flex justify-content-between mb-3">
                <div class="alert alert-light border shadow-sm py-2 px-3 mb-0 d-flex align-items-center text-primary-custom">
                    <i class="fas fa-info-circle me-2"></i> Jadwal diatur per minggu.
                </div>
                <!-- Tombol Tambah (HIJAU) -->
                    <button class="btn text-white fw-bold py-2 px-3 rounded-3" style="background-color: var(--primary);" data-bs-toggle="modal" data-bs-target="#modalJadwal">
                        <i class="fas fa-calendar-plus me-2"></i> Buat Jadwal
                    </button>
            </div>
            
            <div class="stat-card p-5 text-center shadow-sm border-0 rounded-4">
                <i class="fas fa-calendar-alt fa-3x text-muted mb-3 opacity-25"></i>
                <p class="text-muted fw-bold">Belum ada jadwal shift yang dibuat.</p>
            </div>
        </div>
    </div>

    <!-- ================= MODAL POP-UP ================= -->

    <!-- 1. MODAL KARYAWAN -->
    <div class="modal fade" id="modalKaryawan" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form action="#" method="POST" class="modal-content border-0 shadow">
                @csrf
                <div class="modal-header bg-primary-custom text-white" style="background-color: var(--primary);">
                    <h5 class="modal-title fw-bold">Kelola Data Karyawan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Email (PK)</label>
                        <input type="email" name="email" class="form-control" placeholder="user@kayuhan.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">No HP</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-secondary">Posisi (Role)</label>
                            <select name="role" class="form-select">
                                <option value="Barista">Barista</option>
                                <option value="Admin">Admin</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-secondary">ID Jabatan (FK1)</label>
                            <select name="id_jabatan" class="form-select">
                                <option value="1">1 - Admin</option>
                                <option value="2">2 - Training</option>
                                <option value="3">3 - Junior</option>
                                <option value="4">4 - Senior</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-secondary">ID Cabang (FK2)</label>
                            <select name="id_cabang" class="form-select">
                                <option value="-">-</option>
                                <option value="CBG-SBY01">CBG-SBY01</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-secondary">ID Rombong (FK3)</label>
                            <select name="id_rombong" class="form-select">
                                <option value="-">-</option>
                                <option value="RMB-01">RMB-01</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary fw-bold px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary text-white fw-bold px-4 bg-primary border-0" style="background-color: var(--primary);">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 2. MODAL GAJI -->
    <div class="modal fade" id="modalGaji" tabindex="-1">
        <div class="modal-dialog">
            <form action="#" method="POST" class="modal-content border-0 shadow">
                @csrf
                <div class="modal-header bg-primary-custom text-white" style="background-color: var(--primary);">
                    <h5 class="modal-title fw-bold">Hitung Gaji (Payroll)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Pilih Karyawan</label>
                        <select name="employee_id" class="form-select">
                            <option>Budi Santoso (Senior)</option>
                            <option>Siti Aminah (Junior)</option>
                        </select>
                    </div>
                    
                    <div class="alert alert-info border-0 py-2 small" style="background-color: #e7f1ff; color: #004085;">
                        <i class="fas fa-info-circle me-1"></i> <strong>Gaji Pokok: Rp 50.000/hari</strong><br>
                        <span class="ms-3">Bonus Harian: <strong>Rp 1.000/cup</strong> (jika > 50 cup)</span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Periode</label>
                        <input type="month" name="period" class="form-control" value="2024-11">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Jumlah Hari Masuk (Hari)</label>
                        <input type="number" name="days" class="form-control" placeholder="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Total Gaji Pokok (Otomatis)</label>
                        <input type="text" class="form-control bg-light" value="0" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Total Bonus (Manual)</label>
                        <input type="number" name="bonus" class="form-control" placeholder="0">
                        <small class="text-muted">*Hitung manual berdasarkan cup > 50/hari</small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary fw-bold px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary text-white fw-bold px-4 bg-primary border-0" style="background-color: var(--primary);">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 3. MODAL JADWAL -->
    <div class="modal fade" id="modalJadwal" tabindex="-1">
        <div class="modal-dialog">
            <form action="#" method="POST" class="modal-content border-0 shadow">
                @csrf
                <div class="modal-header bg-primary-custom text-white" style="background-color: var(--primary);">
                    <h5 class="modal-title fw-bold">Buat Jadwal Shift</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Karyawan</label>
                        <select name="employee_id" class="form-select">
                            <option>Budi Santoso (Barista)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Lokasi Cabang</label>
                        <select name="location" class="form-select">
                            <option>Taman Bungkul</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Tanggal</label>
                        <input type="date" name="date" class="form-control">
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold text-secondary">Jam Mulai</label>
                            <input type="time" name="start_time" class="form-control">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold text-secondary">Jam Selesai</label>
                            <input type="time" name="end_time" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary fw-bold px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary text-white fw-bold px-4 bg-primary border-0" style="background-color: var(--primary);">Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>
@endsection