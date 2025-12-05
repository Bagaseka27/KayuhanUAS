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

        /* Efek Hover */
        .nav-tabs .nav-link:hover { 
            color: var(--primary); 
            border: none;
        }

        /* Garis Bawah Hijau yang Bergerak */
        .nav-tabs .nav-link::after { 
            content: '';
            position: absolute;
            width: 0;
            height: 3px;
            bottom: -2px; 
            left: 0;
            background-color: var(--primary); /* Warna Hijau Kayuhan */
            transition: width 0.3s ease;
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
        
        /* Gaya Tambahan untuk Judul Bagian */
        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #38704D; /* Warna hijau gelap yang senada */
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 10px;
            margin-bottom: 15px;
            margin-top: 30px;
        }
    </style>


    <h2 class="fw-bold text-primary-custom mb-4">Data Karyawan, Gaji & Jadwal</h2>

    {{-- Tabs (Data Karyawan, Data Gaji, Jadwal Shift) --}}
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
        
        <div class="tab-pane fade show active" id="tab-karyawan">
            
            {{-- Tombol Tambah Karyawan (Terapkan di sini) --}}
            <div class="d-flex justify-content-end mb-3">
                <button class="btn text-white fw-bold py-2 px-3 rounded-3" style="background-color: var(--primary);" 
                    data-bs-toggle="modal" data-bs-target="#modalKaryawan" onclick="resetKaryawanModal()">
                    <i class="fas fa-user-plus me-2"></i> Tambah Karyawan
                </button>
            </div>

            {{-- ========================================================================= --}}
            {{-- BAGIAN 1: TABEL DATA KARYAWAN ADMIN (Tampilan Sederhana) --}}
            {{-- ========================================================================= --}}
            <h3 class="section-title">Data Karyawan Admin</h3>
            <div class="stat-card p-0 overflow-hidden shadow-sm border-0 rounded-4">
                <div class="table-responsive">
                    <table class="table custom-table mb-0 align-middle">
                        <thead class="bg-light text-secondary text-uppercase small fw-bold">
                            <tr>
                                <th class="py-3 ps-4">EMAIL (PK)</th>
                                <th>NAMA</th>
                                <th>NO HP</th>
                                <th class="text-center pe-4">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @php
                                $adminData = $karyawanData->filter(fn($data) => $data->jabatan_name == 'Admin');
                            @endphp
                            @forelse ($adminData as $data)
                            <tr>
                                <td>{{ $data->email }}</td>
                                <td>{{ $data->name }}</td>
                                <td>{{ $data->no_telp }}</td>
                                <td>
                                    {{-- Tombol Aksi --}}
                                    <button 
                                        onclick="fillKaryawanModal(
                                            '{{ $data->email }}', 
                                            '{{ $data->name }}', 
                                            '{{ $data->no_telp }}', 
                                            '{{ $data->jabatan_name }}', 
                                            '{{ $data->ID_JABATAN ?? '' }}', {{-- Aman: Tambah operator ?? '' --}}
                                            '{{ $data->ID_CABANG ?? '-' }}', 
                                            '{{ $data->ID_ROMBONG ?? '-' }}'
                                        )" 
                                        class="btn btn-sm btn-warning">Edit</button>
                                    <button onclick="confirmDelete('{{ $data->email }}', 'Karyawan')" class="btn btn-sm btn-danger">Hapus</button>
                                </td>
                            </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-3">Belum ada data Karyawan dengan peran Admin.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            {{-- ========================================================================= --}}
            {{-- BAGIAN 2: TABEL DATA KARYAWAN BARISTA (Tampilan Lengkap) --}}
            {{-- ========================================================================= --}}
            <h3 class="section-title mt-5">Data Karyawan Barista</h3>
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
                            @php
                                $baristaData = $karyawanData->filter(fn($data) => $data->jabatan_name == 'Barista');
                            @endphp
                            @forelse ($baristaData as $data)
                            <tr>
                                <td>{{ $data->email }}</td>
                                <td>{{ $data->ID_JABATAN ?? '-' }}</td> {{-- FIX: Tambah operator ?? '-' --}}
                                <td>{{ $data->ID_ROMBONG ?? '-' }}</td>
                                <td>{{ $data->ID_CABANG ?? '-' }}</td>
                                <td>{{ $data->name }}</td>
                                <td>{{ $data->no_telp }}</td>
                                <td>{{ $data->jabatan_name }}</td>
                                <td>
                                    {{-- Tombol Aksi --}}
                                    <button 
                                        onclick="fillKaryawanModal(
                                            '{{ $data->email }}', 
                                            '{{ $data->name }}', 
                                            '{{ $data->no_telp }}', 
                                            '{{ $data->jabatan_name }}', 
                                            '{{ $data->ID_JABATAN ?? '' }}', {{-- Aman: Tambah operator ?? '' --}}
                                            '{{ $data->ID_CABANG ?? '-' }}', 
                                            '{{ $data->ID_ROMBONG ?? '-' }}'
                                        )" 
                                        class="btn btn-sm btn-warning">Edit</button>
                                    <button onclick="confirmDelete('{{ $data->email }}', 'Karyawan')" class="btn btn-sm btn-danger">Hapus</button>
                                </td>
                            </tr>
                            @empty
                                <tr><td colspan="8" class="text-center text-muted py-3">Belum ada data Karyawan dengan peran Barista.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Tab Data Gaji (Tidak Berubah) --}}
        <div class="tab-pane fade" id="tab-gaji">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center bg-white px-3 py-2 rounded-3 shadow-sm">
                    <label class="fw-bold me-2 text-primary-custom">Periode:</label>
                    <input type="month" class="form-control border-0 bg-transparent fw-bold" value="2024-11">
                </div>
                <button class="btn text-white fw-bold py-2 px-3 rounded-3" style="background-color: var(--primary);" 
                    data-bs-toggle="modal" data-bs-target="#modalGaji" onclick="resetGajiModal()">
                    <i class="fas fa-plus me-2"></i> Tambah Data Gaji
                </button>
            </div>

            <div class="stat-card p-0 overflow-hidden shadow-sm border-0 rounded-4">
                <div class="table-responsive">
                    <table class="table custom-table mb-0 align-middle">
                        <thead class="bg-light text-secondary text-uppercase small fw-bold">
                            <tr>
                                <th>No</th>
                                <th>Nama Karyawan</th>
                                <th>Periode</th>
                                <th>Gaji Pokok</th>
                                <th>Bonus</th>
                                <th>Kompensasi</th>
                                <th>Total Gaji Akhir</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @foreach ($payrollsData as $gaji)
                            <tr>
                                <td>{{ $loop->iteration }}</td> 
                                <td>{{ $gaji->karyawan->NAMA ?? 'Data Karyawan Tidak Ditemukan' }}</td>                                
                                <td>{{ $gaji->PERIODE }}</td>
                                <td>Rp {{ number_format($gaji->TOTAL_GAJI_POKOK, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($gaji->TOTAL_BONUS, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($gaji->TOTAL_KOMPENSASI, 0, ',', '.') }}</td>
                                <td>**Rp {{ number_format($gaji->TOTAL_GAJI_AKHIR, 0, ',', '.') }}**</td>
                                <td>
                                    <button 
                                        onclick="fillGajiModal(
                                            '{{ $gaji->ID_GAJI }}', 
                                            '{{ $gaji->EMAIL }}', 
                                            '{{ $gaji->karyawan->NAMA ?? 'N/A' }} ({{ $gaji->karyawan->jabatan_name ?? 'N/A' }})', 
                                            '{{ $gaji->PERIODE }}', 
                                            '{{ $gaji->TOTAL_GAJI_POKOK }}', 
                                            '{{ $gaji->TOTAL_BONUS }}', 
                                            '{{ $gaji->TOTAL_KOMPENSASI }}',
                                            '{{ $gaji->JUMLAH_HARI_MASUK ?? 0 }}'
                                        )" 
                                        class="btn btn-sm btn-warning">Edit</button>
                                    <button onclick="confirmDelete('{{ $gaji->ID_GAJI }}', 'Gaji')" class="btn btn-sm btn-danger">Hapus</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Tab Jadwal Shift (Tidak Berubah) --}}
        <div class="tab-pane fade" id="tab-jadwal">
            <div class="d-flex justify-content-between mb-3">
                <div class="alert alert-light border shadow-sm py-2 px-3 mb-0 d-flex align-items-center text-primary-custom">
                    <i class="fas fa-info-circle me-2"></i> Jadwal diatur per minggu.
                </div>
                <button class="btn text-white fw-bold py-2 px-3 rounded-3" style="background-color: var(--primary);" 
                    data-bs-toggle="modal" data-bs-target="#modalJadwal" onclick="resetJadwalModal()">
                    <i class="fas fa-calendar-plus me-2"></i> Buat Jadwal
                </button>
            </div>
            
            <div class="stat-card p-0 overflow-hidden shadow-sm border-0 rounded-4">
                <div class="table-responsive">
                    <table class="table custom-table mb-0 align-middle">
                        <thead class="bg-light text-secondary text-uppercase small fw-bold">
                            <tr>
                                <th class="py-3 ps-4">ID JADWAL</th>
                                <th>KARYAWAN</th>
                                <th>LOKASI (CABANG)</th>
                                <th>TANGGAL</th>
                                <th>JAM</th>
                                <th class="text-center pe-4">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @if(count($jadwals) > 0)
                                @foreach ($jadwals as $jadwal)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    
                                    {{-- Tampilkan data relasi karyawan dan cabang --}}
                                    <td>{{ $jadwal->karyawan->NAMA ?? 'N/A' }}</td>
                                    <td>{{ $jadwal->cabang->NAMA_LOKASI ?? 'N/A' }}</td>
                                    
                                    {{-- Tampilkan data jadwal --}}
                                    <td>{{ $jadwal->TANGGAL }}</td>
                                    <td>{{ $jadwal->JAM_MULAI . ' - ' . $jadwal->JAM_SELESAI }}</td>
                                    
                                    <td>
                                        {{-- Tombol Aksi (Pastikan ID yang dilempar adalah ID_JADWAL) --}}
                                        <button 
                                            onclick="fillJadwalModal('{{ $jadwal->ID_JADWAL }}', '{{ $jadwal->EMAIL }}', '{{ $jadwal->ID_CABANG }}', '{{ $jadwal->TANGGAL }}', '{{ $jadwal->JAM_MULAI }}', '{{ $jadwal->JAM_SELESAI }}')" 
                                            class="btn btn-sm btn-warning">Edit</button>
                                        <button onclick="confirmDelete('{{ $jadwal->ID_JADWAL }}', 'Jadwal')" class="btn btn-sm btn-danger">Hapus</button>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr><td colspan="6" class="text-center text-muted py-4">Belum ada jadwal shift yang dibuat.</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- MODAL TAMBAH/EDIT KARYAWAN (DILENGKAPI LOGIKA ROLE ADMIN) --}}
    {{-- ========================================================================= --}}
    <div class="modal fade" id="modalKaryawan" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form action="#" method="POST" class="modal-content border-0 shadow" id="formKaryawan">
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
                            {{-- Input untuk memilih Role (Admin/Barista) --}}
                            <label class="form-label fw-bold text-secondary">Posisi (Role)</label>
                            <select name="role" class="form-select" id="role_select">
                                <option value="Barista">Barista</option>
                                <option value="Admin">Admin</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-secondary">ID Jabatan (FK1)</label>
                            <select name="id_jabatan" class="form-select">
                                @foreach($jabatanList as $id => $name)
                                    <option value="{{ $id }}">{{ $id }} - {{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Kontainer untuk ID Cabang dan ID Rombong yang akan disembunyikan/dinonaktifkan untuk Admin --}}
                    <div class="row" id="location_fields_container">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-secondary">ID Cabang (FK2)</label>
                            <select name="id_cabang" class="form-select" id="id_cabang_select">
                                <option value="-">-</option>
                                @foreach($cabangList as $id => $name)
                                    <option value="{{ $id }}">{{ $name }} ({{ $id }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-secondary">ID Rombong (FK3)</label>
                            <select name="id_rombong" class="form-select" id="id_rombong_select">
                                <option value="-">-</option>
                                @foreach($rombongList as $id => $name)
                                    <option value="{{ $id }}">{{ $id }}</option>
                                @endforeach
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

    {{-- Modal Gaji (Tidak Berubah) --}}
    <div class="modal fade" id="modalGaji" tabindex="-1">
        <div class="modal-dialog">
            <form action="#" method="POST" class="modal-content border-0 shadow" id="formGaji">
                @csrf
                <div class="modal-header bg-primary-custom text-white" style="background-color: var(--primary);">
                    <h5 class="modal-title fw-bold">Hitung Gaji (Payroll)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Pilih Karyawan</label>
                        <select name="employee_id" class="form-select" id="gaji_employee_select">
                            @foreach($karyawanData as $emp)
                                <option value="{{ $emp->email }}">{{ $emp->name }} ({{ $emp->jabatan_name }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="alert alert-info border-0 py-2 small" style="background-color: #e7f1ff; color: #004085;">
                        <i class="fas fa-info-circle me-1"></i> <strong>Gaji Pokok: Rp 50.000/hari</strong><br>
                        <span class="ms-3">Bonus Harian: <strong>Rp 1.000/cup</strong> (jika > 50 cup)</span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Periode</label>
                        <input type="month" name="period" class="form-control" value="2024-11" id="gaji_period_input">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Jumlah Hari Masuk (Hari)</label>
                        <input type="number" name="days" class="form-control" placeholder="0" id="gaji_days_input">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Total Gaji Pokok (Otomatis)</label>
                        <input type="text" class="form-control bg-light" value="0" readonly id="gaji_basic_auto">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Total Bonus (Manual)</label>
                        <input type="number" name="bonus" class="form-control" placeholder="0" id="gaji_bonus_input">
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

    {{-- Modal Jadwal (Tidak Berubah) --}}
    <div class="modal fade" id="modalJadwal" tabindex="-1">
        <div class="modal-dialog">
            <form action="#" method="POST" class="modal-content border-0 shadow" id="formJadwal">
                @csrf
                <div class="modal-header bg-primary-custom text-white" style="background-color: var(--primary);">
                    <h5 class="modal-title fw-bold">Buat Jadwal Shift</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="id_jadwal" id="jadwal_id_input">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Karyawan</label>
                        <select name="employee_email" class="form-select" id="jadwal_employee_select">
                            @foreach ($employeeDropdown as $email => $nama)
                                <option value="{{ $email }}">{{ $nama }} ({{ $email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Lokasi Cabang</label>
                        <select name="id_cabang" class="form-select" id="jadwal_cabang_select">
                            @foreach($cabangList as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" id="jadwal_tanggal_input">
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold text-secondary">Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="form-control" id="jadwal_jam_mulai_input">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold text-secondary">Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="form-control" id="jadwal_jam_selesai_input">
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        const formKaryawan = document.getElementById('formKaryawan');
        const modalKaryawan = document.getElementById('modalKaryawan');
        const roleSelect = document.getElementById('role_select');
        const idCabangSelect = document.getElementById('id_cabang_select');
        const idRombongSelect = document.getElementById('id_rombong_select');
        const locationFieldsContainer = document.getElementById('location_fields_container');

        // Fungsi untuk mengontrol visibilitas dan status Cabang/Rombong berdasarkan Role
        const toggleLocationFields = () => {
            const isBarista = roleSelect.value === 'Barista';
            
            // Tampilkan/Sembunyikan kontainer field
            locationFieldsContainer.style.display = isBarista ? 'flex' : 'none';

            // Nonaktifkan/Aktifkan field (Penting saat Submit Form)
            idCabangSelect.disabled = !isBarista;
            idRombongSelect.disabled = !isBarista;
            
            // Opsional: set nilai default ke '-' jika non-Barista untuk menghindari error FK
            if (!isBarista) {
                idCabangSelect.value = '-';
                idRombongSelect.value = '-';
            }
        };

        // Panggil fungsi saat role berubah
        roleSelect.addEventListener('change', toggleLocationFields);

        // --- KARYAWAN MODAL RESET/FILL ---
        const resetKaryawanModal = () => {
            document.querySelector('#modalKaryawan .modal-title').textContent = 'Kelola Data Karyawan'; 
            formKaryawan.action = '/employee/store'; // Asumsikan route store
            formKaryawan.reset();
            formKaryawan.querySelector('input[name="email"]').removeAttribute('readonly');
            
            const methodInput = formKaryawan.querySelector('input[name="_method"]');
            if (methodInput) { methodInput.remove(); }
            
            // Pastikan field lokasi terlihat saat reset (default Barista)
            roleSelect.value = 'Barista';
            toggleLocationFields(); 
        };

        window.fillKaryawanModal = function(email, name, phone, role, id_jabatan, id_cabang, id_rombong) {
            resetKaryawanModal(); 
            
            document.querySelector('#modalKaryawan .modal-title').textContent = 'Edit Data Karyawan';
            formKaryawan.action = `/employee/${email}`; // Asumsikan route update
            
            formKaryawan.insertAdjacentHTML('beforeend', '<input type="hidden" name="_method" value="PUT">');

            formKaryawan.querySelector('input[name="email"]').value = email;
            formKaryawan.querySelector('input[name="email"]').setAttribute('readonly', true);
            formKaryawan.querySelector('input[name="name"]').value = name;
            formKaryawan.querySelector('input[name="phone"]').value = phone;

            formKaryawan.querySelector('select[name="role"]').value = role;
            formKaryawan.querySelector('select[name="id_jabatan"]').value = id_jabatan;
            
            // Set nilai Cabang dan Rombong
            idCabangSelect.value = id_cabang;
            idRombongSelect.value = id_rombong;

            // Panggil toggle untuk menyesuaikan tampilan Cabang/Rombong
            toggleLocationFields(); 

            new bootstrap.Modal(modalKaryawan).show();
        };
        modalKaryawan.addEventListener('hidden.bs.modal', resetKaryawanModal);
        
        // Panggil toggle saat DOM pertama kali dimuat
        toggleLocationFields(); 


        // --- GAJI MODAL RESET/FILL ---
        const formGaji = document.getElementById('formGaji');
        const modalGaji = document.getElementById('modalGaji');
        const gajiEmployeeSelect = document.getElementById('gaji_employee_select');
        const originalEmployeeOptions = gajiEmployeeSelect.innerHTML;

        const resetGajiModal = () => {
             document.querySelector('#modalGaji .modal-title').textContent = 'Hitung Gaji (Payroll)';
             formGaji.action = '/payroll/store'; // Asumsikan route store
             formGaji.reset();
             
             gajiEmployeeSelect.innerHTML = originalEmployeeOptions;
             gajiEmployeeSelect.removeAttribute('disabled');
             
             const methodInput = formGaji.querySelector('input[name="_method"]');
             if (methodInput) { methodInput.remove(); }
        };

        window.fillGajiModal = function(id, email, name_jabatan, periode, basic, bonus, kompensasi, days) {
            resetGajiModal();
            
            document.querySelector('#modalGaji .modal-title').textContent = `Edit Gaji ${name_jabatan}`;
            formGaji.action = `/payroll/update/${id}`; // Asumsikan route update
            
            formGaji.insertAdjacentHTML('beforeend', '<input type="hidden" name="_method" value="PUT">');
            
            // Isi Field Karyawan (READONLY di mode edit)
            gajiEmployeeSelect.innerHTML = `<option value="${email}">${name_jabatan}</option>`;
            gajiEmployeeSelect.value = email;
            gajiEmployeeSelect.setAttribute('disabled', true);
            
            // Isi Fields
            document.getElementById('gaji_period_input').value = periode;
            document.getElementById('gaji_days_input').value = days;
            document.getElementById('gaji_basic_auto').value = basic; 
            document.getElementById('gaji_bonus_input').value = bonus;
            // Note: Kompensasi tidak ada di modal gaji yang asli, saya tambahkan di sini jika Anda ingin menggunakannya
            // document.getElementById('gaji_kompensasi_input').value = kompensasi;
            
            new bootstrap.Modal(modalGaji).show();
        };
        modalGaji.addEventListener('hidden.bs.modal', resetGajiModal);


        // --- JADWAL MODAL RESET/FILL ---
        const formJadwal = document.getElementById('formJadwal');
        const modalJadwal = document.getElementById('modalJadwal');
        
        // Fungsi Reset (Tambah Jadwal)
        window.resetJadwalModal = function() {
            document.querySelector('#modalJadwal .modal-title').textContent = 'Buat Jadwal Shift'; 
            formJadwal.action = '/jadwal/store'; // Asumsikan Route Tambah
            formJadwal.reset();
            
            const methodInput = formJadwal.querySelector('input[name="_method"]');
            if (methodInput) { methodInput.remove(); }
        };

        // Fungsi Isi Data (Edit Jadwal)
        window.fillJadwalModal = function(id_jadwal, email, id_cabang, tanggal, jam_mulai, jam_selesai) {
            resetJadwalModal(); 
            
            document.querySelector('#modalJadwal .modal-title').textContent = `Edit Jadwal ${id_jadwal}`;
            formJadwal.action = `/jadwal/update/${id_jadwal}`; // Asumsikan Route Update
            
            formJadwal.insertAdjacentHTML('beforeend', '<input type="hidden" name="_method" value="PUT">');

            // Isi Fields
            document.getElementById('jadwal_id_input').value = id_jadwal;
            document.getElementById('jadwal_employee_select').value = email;
            document.getElementById('jadwal_cabang_select').value = id_cabang;
            document.getElementById('jadwal_tanggal_input').value = tanggal;
            document.getElementById('jadwal_jam_mulai_input').value = jam_mulai;
            document.getElementById('jadwal_jam_selesai_input').value = jam_selesai;

            new bootstrap.Modal(modalJadwal).show();
        };

        // Tambahkan listener untuk mereset saat modal ditutup
        modalJadwal.addEventListener('hidden.bs.modal', resetJadwalModal);


        // --- FUNGSI DUMMY DELETE ---
        // employee.blade.php (Di dalam tag <script>)

// ... (di bagian akhir script)

        // --- FUNGSI DELETE KARYAWAN (Menggunakan Form DELETE) ---
        window.confirmDelete = function(id, tipe) {
             if (confirm(`Yakin ingin menghapus data ${tipe} dengan ID ${id} ini?`)) {
                 if (tipe === 'Karyawan') {
                     // Buat form dinamis untuk submit DELETE
                     const form = document.createElement('form');
                     form.method = 'POST';
                     form.action = `/employee/${id}`; // Mengarah ke DELETE /employee/{email}

                     // Token CSRF
                     form.innerHTML += '@csrf';
                     
                     // Method Spoofing
                     form.innerHTML += '<input type="hidden" name="_method" value="DELETE">';
                     
                     document.body.appendChild(form);
                     form.submit();
                 } 
                 // Tambahkan logika untuk Gaji dan Jadwal jika ingin menghapus
                 // else if (tipe === 'Gaji') { ... }
                 // else if (tipe === 'Jadwal') { ... }
                 else {
                    console.log(`Menghapus ${tipe} ID: ${id} (Logika perlu ditambahkan)`);
                 }
             }
        };
    });
</script>
@endpush