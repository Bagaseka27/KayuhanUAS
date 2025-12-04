@extends('layouts.app')

@section('title', 'Manajemen SDM - Kayuhan')

@section('content')
    {{-- CSS KHUSUS UNTUK HALAMAN INI (Tab Effect) --}}
    <style>
        /* ... (CSS Anda) ... */
        .nav-tabs { border-bottom: 2px solid #e9ecef; }
        .nav-tabs .nav-link { border: none; color: #6c757d; font-weight: 600; padding: 10px 20px; position: relative; transition: all 0.3s ease; background: transparent; }
        .nav-tabs .nav-link:hover { color: var(--primary); border: none; }
        .nav-tabs .nav-link::after { content: ''; position: absolute; width: 0; height: 3px; bottom: -2px; left: 0; background-color: var(--primary); transition: width 0.3s ease; }
        .nav-tabs .nav-link:hover::after,
        .nav-tabs .nav-link.active::after { width: 100%; }
        .nav-tabs .nav-link.active { color: var(--primary); background: transparent; }
    </style>

    {{-- DATA DUMMY --}}
    @php
        $employees = [
            (object)['email' => 'budi@kayuhan.com', 'id_jabatan' => '4', 'id_rombong' => 'RMB-01', 'id_cabang' => 'CBG-SBY01', 'name' => 'Budi Santoso', 'phone' => '08123456789', 'role' => 'Barista', 'jabatan_name' => 'Senior'],
            (object)['email' => 'siti@kayuhan.com', 'id_jabatan' => '3', 'id_rombong' => 'RMB-01', 'id_cabang' => 'CBG-SBY01', 'name' => 'Siti Aminah', 'phone' => '08198765432', 'role' => 'Barista', 'jabatan_name' => 'Junior'],
            (object)['email' => 'admin@kayuhan.com', 'id_jabatan' => '1', 'id_rombong' => '-', 'id_cabang' => '-', 'name' => 'Andi Wijaya', 'phone' => '08111122233', 'role' => 'Admin', 'jabatan_name' => 'Admin']
        ];
        $payrolls = [
            (object)['id' => 'GJ-241101', 'email' => 'budi@kayuhan.com', 'name' => 'Budi Santoso', 'jabatan_name' => 'Senior', 'periode' => '2024-11', 'basic' => 1250000, 'bonus' => 500000, 'total' => 1750000, 'days' => 25, 'cup_bonus' => 500000]
        ];
        // Tambahan data dummy untuk select
        $jabatanList = ['1' => 'Admin', '2' => 'Training', '3' => 'Junior', '4' => 'Senior'];
        $cabangList = ['CBG-SBY01' => 'Taman Bungkul'];
        $rombongList = ['RMB-01' => 'Rombong 01'];
    @endphp

    <h2 class="fw-bold text-primary-custom mb-4">Data Karyawan, Gaji & Jadwal</h2>

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
            <div class="d-flex justify-content-end mb-3">
                <button class="btn text-white fw-bold py-2 px-3 rounded-3" style="background-color: var(--primary);" 
                    data-bs-toggle="modal" data-bs-target="#modalKaryawan" onclick="resetKaryawanModal()">
                    <i class="fas fa-user-plus me-2"></i> Tambah Karyawan
                </button>
            </div>

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
                                    <button class="btn btn-sm btn-light text-primary me-1 rounded-2"
                                        onclick="fillKaryawanModal('{{ $emp->email }}', '{{ $emp->name }}', '{{ $emp->phone }}', '{{ $emp->role }}', '{{ $emp->id_jabatan }}', '{{ $emp->id_cabang }}', '{{ $emp->id_rombong }}')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light text-danger rounded-2" onclick="confirmDelete('{{ $emp->email }}', 'karyawan')"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

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
                                    <button class="btn btn-sm btn-light text-primary me-1 rounded-2"
                                        onclick="fillGajiModal('{{ $pay->id }}', '{{ $pay->email }}', '{{ $pay->name }} ({{ $pay->jabatan_name }})', '{{ $pay->periode }}', {{ $pay->basic }}, {{ $pay->cup_bonus }}, {{ $pay->days }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light text-danger rounded-2" onclick="confirmDelete('{{ $pay->id }}', 'gaji')"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="tab-jadwal">
            <div class="d-flex justify-content-between mb-3">
                <div class="alert alert-light border shadow-sm py-2 px-3 mb-0 d-flex align-items-center text-primary-custom">
                    <i class="fas fa-info-circle me-2"></i> Jadwal diatur per minggu.
                </div>
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
                            <label class="form-label fw-bold text-secondary">Posisi (Role)</label>
                            <select name="role" class="form-select">
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

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-secondary">ID Cabang (FK2)</label>
                            <select name="id_cabang" class="form-select">
                                <option value="-">-</option>
                                @foreach($cabangList as $id => $name)
                                    <option value="{{ $id }}">{{ $name }} ({{ $id }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-secondary">ID Rombong (FK3)</label>
                            <select name="id_rombong" class="form-select">
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
                            @foreach($employees as $emp)
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

    <div class="modal fade" id="modalJadwal" tabindex="-1">
        </div>
@endsection

@push('scripts')
<script>
    // Fungsi untuk mereset modal kembali ke mode 'Tambah' saat ditutup/dibuka dari tombol 'Tambah'
    // ==============================================================================================

    document.addEventListener('DOMContentLoaded', function() {
        // --- KARYAWAN MODAL RESET/FILL ---
        const formKaryawan = document.getElementById('formKaryawan');
        const modalKaryawan = document.getElementById('modalKaryawan');

        const resetKaryawanModal = () => {
            document.querySelector('#modalKaryawan .modal-title').textContent = 'Kelola Data Karyawan'; 
            formKaryawan.action = '#'; // Route tambah
            formKaryawan.reset();
            formKaryawan.querySelector('input[name="email"]').removeAttribute('readonly');
            
            const methodInput = formKaryawan.querySelector('input[name="_method"]');
            if (methodInput) { methodInput.remove(); }
        };

        window.fillKaryawanModal = function(email, name, phone, role, id_jabatan, id_cabang, id_rombong) {
            resetKaryawanModal(); // Selalu reset sebelum mengisi
            
            document.querySelector('#modalKaryawan .modal-title').textContent = 'Edit Data Karyawan';
            formKaryawan.action = `/employee/update/${email}`; 
            
            // Masukkan method spoofing PUT
            formKaryawan.insertAdjacentHTML('beforeend', '<input type="hidden" name="_method" value="PUT">');

            // Isi Fields
            formKaryawan.querySelector('input[name="email"]').value = email;
            formKaryawan.querySelector('input[name="email"]').setAttribute('readonly', true);
            formKaryawan.querySelector('input[name="name"]').value = name;
            formKaryawan.querySelector('input[name="phone"]').value = phone;

            formKaryawan.querySelector('select[name="role"]').value = role;
            formKaryawan.querySelector('select[name="id_jabatan"]').value = id_jabatan;
            formKaryawan.querySelector('select[name="id_cabang"]').value = id_cabang;
            formKaryawan.querySelector('select[name="id_rombong"]').value = id_rombong;

            new bootstrap.Modal(modalKaryawan).show();
        };

        // Tambahkan listener untuk mereset saat modal ditutup
        modalKaryawan.addEventListener('hidden.bs.modal', resetKaryawanModal);


        // --- GAJI MODAL RESET/FILL ---
        const formGaji = document.getElementById('formGaji');
        const modalGaji = document.getElementById('modalGaji');
        const gajiEmployeeSelect = document.getElementById('gaji_employee_select');
        const originalEmployeeOptions = gajiEmployeeSelect.innerHTML; // Simpan opsi awal

        const resetGajiModal = () => {
             document.querySelector('#modalGaji .modal-title').textContent = 'Hitung Gaji (Payroll)';
             formGaji.action = '#'; // Route tambah
             formGaji.reset();
             
             // Kembalikan opsi karyawan asli dan hapus disabled
             gajiEmployeeSelect.innerHTML = originalEmployeeOptions;
             gajiEmployeeSelect.removeAttribute('disabled');
             
             const methodInput = formGaji.querySelector('input[name="_method"]');
             if (methodInput) { methodInput.remove(); }
        };

        window.fillGajiModal = function(id, email, name_jabatan, periode, basic, bonus, days) {
            resetGajiModal(); // Selalu reset sebelum mengisi
            
            document.querySelector('#modalGaji .modal-title').textContent = `Edit Gaji ${name_jabatan}`;
            formGaji.action = `/payroll/update/${id}`;
            
            // Masukkan method spoofing PUT
            formGaji.insertAdjacentHTML('beforeend', '<input type="hidden" name="_method" value="PUT">');
            
            // Isi Field Karyawan (READONLY di mode edit)
            gajiEmployeeSelect.innerHTML = `<option value="${email}">${name_jabatan}</option>`;
            gajiEmployeeSelect.value = email;
            gajiEmployeeSelect.setAttribute('disabled', true);
            
            // Isi Fields
            document.getElementById('gaji_period_input').value = periode;
            document.getElementById('gaji_days_input').value = days; // Jumlah Hari Masuk
            document.getElementById('gaji_basic_auto').value = basic; // Gaji Pokok
            document.getElementById('gaji_bonus_input').value = bonus; // Bonus
            
            new bootstrap.Modal(modalGaji).show();
        };
        
        // Tambahkan listener untuk mereset saat modal ditutup
        modalGaji.addEventListener('hidden.bs.modal', resetGajiModal);


        // --- FUNGSI DUMMY DELETE ---
        window.confirmDelete = function(id, tipe) {
             if (confirm(`Yakin ingin menghapus data ${tipe} dengan ID ${id} ini?`)) {
                 // Di sini Anda akan menambahkan logika fetch/AJAX ke route DELETE
                 console.log(`Menghapus ${tipe} ID: ${id}`);
             }
        };

    });
</script>
@endpush