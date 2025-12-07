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
            color: #6c757d;
            font-weight: 600;
            padding: 10px 20px;
            position: relative;
            transition: all 0.3s ease;
            background: transparent;
        }

        .nav-tabs .nav-link:hover {
            color: var(--primary);
            border: none;
        }

        .nav-tabs .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 3px;
            bottom: -2px;
            left: 0;
            background-color: var(--primary);
            transition: width 0.3s ease;
        }

        .nav-tabs .nav-link:hover::after,
        .nav-tabs .nav-link.active::after {
            width: 100%;
        }

        .nav-tabs .nav-link.active {
            color: var(--primary);
            background: transparent;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #38704D;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 10px;
            margin-bottom: 15px;
            margin-top: 30px;
        }
    </style>

    <h2 class="fw-bold text-primary-custom mb-4">Data Karyawan, Gaji & Jadwal</h2>

    {{-- Tabs --}}
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
        <li class="nav-item me-2">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-jabatan">
                Jabatan
            </button>
        </li>
    </ul>

    <div class="tab-content">
        {{-- Karyawan Tab --}}
        <div class="tab-pane fade show active" id="tab-karyawan">
            <div class="d-flex justify-content-end mb-3">
                <button class="btn text-white fw-bold py-2 px-3 rounded-3" style="background-color: var(--primary);" data-bs-toggle="modal" data-bs-target="#modalKaryawan" onclick="resetKaryawanModal()">
                    <i class="fas fa-user-plus me-2"></i> Tambah Karyawan
                </button>
            </div>

            <h3 class="section-title">Data Karyawan Admin</h3>
            <div class="stat-card p-0 overflow-hidden shadow-sm border-0 rounded-4">
                <div class="table-responsive">
                    <table class="table custom-table mb-0 align-middle">
                        <thead class="bg-light text-secondary text-uppercase small fw-bold">
                            <tr><th class="py-3 ps-4">EMAIL (PK)</th><th>NAMA</th><th>NO HP</th><th class="ps-4 text-start" style="width: 120px;">AKSI</th></tr>
                        </thead>
                        <tbody class="bg-white">
                            @php
                                $adminData = $karyawanData->filter(fn($data) => ($data->role ?? '') == 'Admin' || ($data->jabatan_name ?? '') == 'Admin');
                            @endphp
                            @forelse ($adminData as $data)
                            <tr>
                                <td>{{ $data->email }}</td>
                                <td>{{ $data->name }}</td>
                                <td>{{ $data->no_telp }}</td>
                                <td>
                                    <button onclick="fillKaryawanModal('{{ $data->email }}','{{ $data->name }}','{{ $data->no_telp }}','{{ $data->role ?? 'Admin' }}','{{ $data->ID_JABATAN ?? '' }}','{{ $data->ID_CABANG ?? '' }}','{{ $data->ID_ROMBONG ?? '' }}')" class="btn btn-sm btn-warning">Edit</button>
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

            <h3 class="section-title mt-5">Data Karyawan Barista</h3>
            <div class="stat-card p-0 overflow-hidden shadow-sm border-0 rounded-4">
                <div class="table-responsive">
                    <table class="table custom-table mb-0 align-middle">
                        <thead class="bg-light text-secondary text-uppercase small fw-bold">
                            <tr><th class="py-3 ps-4">EMAIL (PK)</th><th>ID JABATAN</th><th>ID ROMBONG</th><th>ID CABANG</th><th>NAMA</th><th>NO HP</th><th>POSISI</th><th class="ps-4 text-start" style="width: 120px;">AKSI</th></tr>
                        </thead>
                        <tbody class="bg-white">
                            @php $baristaData = $karyawanData->filter(fn($data) => ($data->role ?? '') == 'Barista' || ($data->jabatan_name ?? '') == 'Barista'); @endphp
                            @forelse ($baristaData as $data)
                            <tr>
                                <td>{{ $data->email }}</td>
                                <td>{{ $data->ID_JABATAN ?? '-' }}</td>
                                <td>{{ $data->ID_ROMBONG ?? '-' }}</td>
                                <td>{{ $data->ID_CABANG ?? '-' }}</td>
                                <td>{{ $data->name }}</td>
                                <td>{{ $data->no_telp }}</td>
                                <td>{{ $data->jabatan_name ?? $data->role }}</td>
                                <td>
                                    <button onclick="fillKaryawanModal('{{ $data->email }}','{{ $data->name }}','{{ $data->no_telp }}','{{ $data->role ?? 'Barista' }}','{{ $data->ID_JABATAN ?? '' }}','{{ $data->ID_CABANG ?? '' }}','{{ $data->ID_ROMBONG ?? '' }}')" class="btn btn-sm btn-warning">Edit</button>
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

        {{-- Gaji Tab --}}
        <div class="tab-pane fade" id="tab-gaji">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center bg-white px-3 py-2 rounded-3 shadow-sm">
                    <label class="fw-bold me-2 text-primary-custom">Periode:</label>
                    <input type="month" class="form-control border-0 bg-transparent fw-bold" value="{{ now()->format('Y-m') }}">
                </div>
                <button class="btn text-white fw-bold py-2 px-3 rounded-3" style="background-color: var(--primary);" data-bs-toggle="modal" data-bs-target="#modalGaji" onclick="resetGajiModal()">
                    <i class="fas fa-plus me-2"></i> Tambah Data Gaji
                </button>
            </div>

            <div class="stat-card p-0 overflow-hidden shadow-sm border-0 rounded-4">
                <div class="table-responsive">
                    <table class="table custom-table mb-0 align-middle">
                        <thead class="bg-light text-secondary text-uppercase small fw-bold">
                            <tr><th>No</th><th>Nama Karyawan</th><th>Periode</th><th>Gaji Pokok</th><th>Bonus</th><th>Kompensasi</th><th>Total Gaji Akhir</th><th>Aksi</th></tr>
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
                                <td>Rp {{ number_format($gaji->TOTAL_GAJI_AKHIR, 0, ',', '.') }}</td>
                                <td>
                                    <button onclick="fillGajiModal('{{ $gaji->ID_GAJI }}','{{ $gaji->EMAIL }}','{{ $gaji->karyawan->NAMA ?? 'N/A' }} ({{ $gaji->karyawan->jabatan_name ?? 'N/A' }})','{{ $gaji->PERIODE }}','{{ $gaji->TOTAL_GAJI_POKOK }}','{{ $gaji->JUMLAH_HARI_MASUK }}','{{ $gaji->TOTAL_BONUS }}','{{ $gaji->TOTAL_KOMPENSASI }}')" class="btn btn-sm btn-warning">Edit</button>
                                    <button onclick="confirmDelete('{{ $gaji->ID_GAJI }}', 'Gaji')" class="btn btn-sm btn-danger">Hapus</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Jadwal Tab --}}
        <div class="tab-pane fade" id="tab-jadwal">
            <div class="d-flex justify-content-between mb-3">
                <div class="alert alert-light border shadow-sm py-2 px-3 mb-0 d-flex align-items-center text-primary-custom">
                    <i class="fas fa-info-circle me-2"></i> Jadwal diatur per hari.
                </div>
                <button class="btn text-white fw-bold py-2 px-3 rounded-3" style="background-color: var(--primary);" data-bs-toggle="modal" data-bs-target="#modalJadwal" onclick="resetJadwalModal()">
                    <i class="fas fa-calendar-plus me-2"></i> Buat Jadwal
                </button>
            </div>

            <div class="stat-card p-0 overflow-hidden shadow-sm border-0 rounded-4">
                <div class="table-responsive">
                    <table class="table custom-table mb-0 align-middle">
                        <thead class="bg-light text-secondary text-uppercase small fw-bold">
                            <tr><th class="py-3 ps-4">ID JADWAL</th><th>KARYAWAN</th><th>LOKASI (CABANG)</th><th>TANGGAL</th><th>JAM</th><th class="text-center pe-4">AKSI</th></tr>
                        </thead>
                        <tbody class="bg-white">
                            @if(count($jadwals) > 0)
                                @foreach ($jadwals as $jadwal)
                                <tr>
                                    <td>{{ $jadwal->ID_JADWAL }}</td>
                                    <td>{{ $jadwal->karyawan->NAMA ?? 'N/A' }}</td>
                                    <td>{{ $jadwal->cabang->NAMA_LOKASI ?? 'N/A' }}</td>
                                    <td>{{ $jadwal->TANGGAL }}</td>
                                    <td>{{ \Carbon\Carbon::parse($jadwal->JAM_MULAI)->format('H:i') . ' - ' . \Carbon\Carbon::parse($jadwal->JAM_SELESAI)->format('H:i') }}</td>
                                    <td>
                                        <button onclick="fillJadwalModal('{{ $jadwal->ID_JADWAL }}','{{ $jadwal->EMAIL }}','{{ $jadwal->ID_CABANG }}','{{ $jadwal->TANGGAL }}','{{ $jadwal->JAM_MULAI }}','{{ $jadwal->JAM_SELESAI }}')" class="btn btn-sm btn-warning">Edit</button>
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

        {{-- Jabatan Tab --}}
        <div class="tab-pane fade" id="tab-jabatan">
            <div class="d-flex justify-content-end mb-3">
                <button class="btn text-white fw-bold py-2 px-3 rounded-3" style="background-color: var(--primary);" data-bs-toggle="modal" data-bs-target="#modalJabatan" onclick="resetJabatanModal()">
                    <i class="fas fa-plus me-2"></i> Tambah Jabatan
                </button>
            </div>

            <div class="stat-card p-0 overflow-hidden shadow-sm border-0 rounded-4">
                <div class="table-responsive">
                    <table class="table custom-table mb-0 align-middle">
                        <thead class="bg-light text-secondary text-uppercase small fw-bold">
                            <tr><th>ID</th><th>Nama Jabatan</th><th>Gaji / Hari</th><th>Bonus / Cup</th><th class="text-center">Aksi</th></tr>
                        </thead>
                        <tbody class="bg-white">
                            @forelse($jabatanListFull as $j)
                            <tr>
                                <td>{{ $j->ID_JABATAN }}</td>
                                <td>{{ $j->NAMA_JABATAN }}</td>
                                <td>Rp {{ number_format($j->GAJI_POKOK_PER_HARI, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($j->BONUS_PER_CUP, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <button class="btn btn-warning btn-sm" onclick="fillJabatanModal('{{ $j->ID_JABATAN }}','{{ $j->NAMA_JABATAN }}','{{ $j->GAJI_POKOK_PER_HARI }}','{{ $j->BONUS_PER_CUP }}')">Edit</button>
                                    <button class="btn btn-danger btn-sm" onclick="confirmDeleteJabatan('{{ $j->ID_JABATAN }}')">Hapus</button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-3">Belum ada data jabatan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL KARYAWAN --}}
    <div class="modal fade" id="modalKaryawan" tabindex="-1"><div class="modal-dialog modal-lg">
    <form id="formKaryawan" action="{{ route('employee.store') }}" method="POST" class="modal-content border-0 shadow">@csrf
        <input type="hidden" name="ID_CABANG" id="hiddenCabang">
        <input type="hidden" name="ID_ROMBONG" id="hiddenRombong">

        <div class="modal-header text-white" style="background-color: var(--primary);">
            <h5 class="modal-title fw-bold">Kelola Data Karyawan</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body p-4">
            <div class="mb-3"><label class="form-label fw-bold text-secondary">Email (PK)</label><input type="email" name="EMAIL" class="form-control" required></div>
            <div class="mb-3"><label class="form-label fw-bold text-secondary">Nama Lengkap</label><input type="text" name="NAMA" class="form-control" required></div>
            <div class="mb-3"><label class="form-label fw-bold text-secondary">No HP</label><input type="text" name="NO_HP" class="form-control" required></div>
            <div class="mb-3"><label class="form-label fw-bold text-secondary">Password</label><input type="password" name="PASSWORD" class="form-control" required></div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-secondary">Role</label>
                    <select name="ROLE" id="role_select" class="form-select" required><option value="Barista">Barista</option><option value="Admin">Admin</option></select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-secondary">ID Jabatan</label>
                    <select name="ID_JABATAN" class="form-select" required>
                        @foreach($jabatanList as $id => $name)
                            <option value="{{ $id }}">{{ $id }} - {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div id="location_fields_container" class="row" style="display:none;">
                <div class="col-md-6 mb-3"><label class="form-label fw-bold text-secondary">ID Cabang</label>
                    <select name="ID_CABANG" id="id_cabang_select" class="form-select"><option value="">-</option>@foreach($cabangList as $id => $name)<option value="{{ $id }}">{{ $name }}</option>@endforeach</select>
                </div>
                <div class="col-md-6 mb-3"><label class="form-label fw-bold text-secondary">ID Rombong</label>
                    <select name="ID_ROMBONG" id="id_rombong_select" class="form-select"><option value="">-</option>@foreach($rombongList as $id => $name)<option value="{{ $id }}">{{ $id }}</option>@endforeach</select>
                </div>
            </div>
        </div>

        <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary text-white fw-bold">Simpan</button>
        </div>
    </form></div></div>

    {{-- MODAL GAJI --}}
    <div class="modal fade" id="modalGaji" tabindex="-1"><div class="modal-dialog">
    <form action="{{ route('gaji.store') }}" method="POST" id="formGaji" class="modal-content border-0 shadow">@csrf
        <div class="modal-header text-white" style="background-color: var(--primary);">
            <h5 class="modal-title fw-bold">Hitung Gaji</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body p-4">
            <div class="mb-3">
                <label class="fw-bold text-secondary">Pilih Karyawan</label>
                <select name="EMAIL" id="gaji_employee_select" class="form-select" required>
                    @foreach($karyawanData as $emp)
                        <option value="{{ $emp->email }}">{{ $emp->name }} ({{ $emp->jabatan_name ?? '' }})</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3"><label class="fw-bold text-secondary">Periode</label><input type="month" name="PERIODE" id="gaji_period_input" class="form-control" required></div>

            <div class="mb-3"><label class="fw-bold text-secondary">Jumlah Hari Masuk</label><input type="number" name="JUMLAH_HARI_MASUK" id="gaji_days_input" class="form-control" required min="0"></div>

            <div class="mb-3"><label class="fw-bold text-secondary">Total Gaji Pokok (Auto)</label><input type="number" name="TOTAL_GAJI_POKOK" id="gaji_basic_auto" class="form-control bg-light" readonly></div>

            <div class="mb-3"><label class="fw-bold text-secondary">Bonus (jumlah cup)</label><input type="number" name="INPUT_BONUS" id="gaji_bonus_input" class="form-control" min="0"></div>

            <div class="mb-3"><label class="fw-bold text-secondary">Total Bonus (Auto)</label><input type="number" name="TOTAL_BONUS" id="gaji_bonus_total" class="form-control bg-light" readonly></div>

            <div class="mb-3"><label class="fw-bold text-secondary">Kompensasi</label><input type="number" name="TOTAL_KOMPENSASI" id="gaji_kompensasi_input" class="form-control" value="0" min="0"></div>
        </div>

        <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary text-white fw-bold">Simpan</button>
        </div>
    </form></div></div>

    {{-- MODAL JADWAL --}}
    <div class="modal fade" id="modalJadwal" tabindex="-1"><div class="modal-dialog">
    <form action="{{ route('jadwal.store') }}" method="POST" id="formJadwal" class="modal-content border-0 shadow">@csrf
        <div class="modal-header text-white" style="background-color: var(--primary);">
            <h5 class="modal-title fw-bold">Buat Jadwal Shift</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
            <input type="hidden" name="ID_JADWAL" id="jadwal_id_input">
            <div class="mb-3"><label class="fw-bold text-secondary">Karyawan</label><select name="EMAIL" class="form-select" id="jadwal_employee_select" required>@foreach ($employeeDropdown as $email => $nama)<option value="{{ $email }}">{{ $nama }}</option>@endforeach</select></div>
            <div class="mb-3"><label class="fw-bold text-secondary">Lokasi Cabang</label><select name="ID_CABANG" class="form-select" id="jadwal_cabang_select" required>@foreach($cabangList as $id => $name)<option value="{{ $id }}">{{ $name }}</option>@endforeach</select></div>
            <div class="mb-3"><label class="fw-bold text-secondary">Tanggal</label><input type="date" name="TANGGAL" id="jadwal_tanggal_input" class="form-control" required></div>
            <div class="row"><div class="col-6 mb-3"><label class="fw-bold text-secondary">Jam Mulai</label><input type="time" name="JAM_MULAI" id="jadwal_jam_mulai_input" class="form-control" required></div><div class="col-6 mb-3"><label class="fw-bold text-secondary">Jam Selesai</label><input type="time" name="JAM_SELESAI" id="jadwal_jam_selesai_input" class="form-control" required></div></div>
        </div>
        <div class="modal-footer bg-light"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary text-white fw-bold">Simpan Jadwal</button></div>
    </form></div></div>

    {{-- MODAL JABATAN --}}
    <div class="modal fade" id="modalJabatan" tabindex="-1"><div class="modal-dialog">
    <form id="formJabatan" action="{{ route('jabatan.store') }}" method="POST" class="modal-content border-0 shadow">@csrf
        <div class="modal-header text-white" style="background-color: var(--primary);"><h5 class="modal-title fw-bold">Kelola Jabatan</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
        <div class="modal-body p-4">
            <div class="mb-3"><label class="fw-bold text-secondary">Nama Jabatan</label><input type="text" name="NAMA_JABATAN" id="jabatan_nama" class="form-control" required></div>
            <div class="mb-3"><label class="fw-bold text-secondary">Gaji Pokok / Hari</label><input type="number" name="GAJI_POKOK_PER_HARI" id="jabatan_gaji" class="form-control" required></div>
            <div class="mb-3"><label class="fw-bold text-secondary">Bonus / CUP</label><input type="number" name="BONUS_PER_CUP" id="jabatan_bonus" class="form-control" required></div>
        </div>
        <div class="modal-footer bg-light"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary text-white fw-bold">Simpan</button></div>
    </form></div></div>

@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const form = document.getElementById('formKaryawan');
        const roleSelect = document.getElementById('role_select');
        const cabangSelect = document.getElementById('id_cabang_select');
        const rombongSelect = document.getElementById('id_rombong_select');
        const container = document.getElementById('location_fields_container');
        const hiddenCabang = document.getElementById('hiddenCabang');
        const hiddenRombong = document.getElementById('hiddenRombong');

        function toggleFields() {
            const isBarista = roleSelect.value === "Barista";
            container.style.display = isBarista ? "flex" : "none";
            if (!isBarista) {
                hiddenCabang.value = "";
                hiddenRombong.value = "";
            }
        }
        roleSelect.addEventListener("change", toggleFields);

        window.resetKaryawanModal = () => {
            form.reset();
            form.action = "{{ route('employee.store') }}";
            const method = form.querySelector('input[name="_method"]');
            if (method) method.remove();
            form.querySelector('input[name="EMAIL"]').removeAttribute('readonly');
            roleSelect.value = "Barista";
            toggleFields();
        };

        window.fillKaryawanModal = (email, nama, hp, role, idJabatan, cabang, rombong) => {
            resetKaryawanModal();
            form.action = `/employee/${encodeURIComponent(email)}`;
            form.insertAdjacentHTML("beforeend", `<input type="hidden" name="_method" value="PUT">`);
            form.querySelector('input[name="EMAIL"]').value = email;
            form.querySelector('input[name="EMAIL"]').setAttribute('readonly', true);
            form.querySelector('input[name="NAMA"]').value = nama;
            form.querySelector('input[name="NO_HP"]').value = hp;
            form.querySelector('input[name="PASSWORD"]').value = "";
            form.querySelector('select[name="ID_JABATAN"]').value = idJabatan;
            roleSelect.value = role;
            cabangSelect.value = cabang ?? "";
            rombongSelect.value = rombong ?? "";
            toggleFields();
            new bootstrap.Modal(document.getElementById('modalKaryawan')).show();
        };

        // GAJI LOGIC
        const formGaji = document.getElementById("formGaji");
        const gajiEmployeeSelect = document.getElementById("gaji_employee_select");
        const gajiDaysInput = document.getElementById("gaji_days_input");
        const gajiBasicAuto = document.getElementById("gaji_basic_auto");
        const gajiBonusInput = document.getElementById("gaji_bonus_input");
        const gajiBonusTotal = document.getElementById("gaji_bonus_total");
        const gajiKompensasi = document.getElementById("gaji_kompensasi_input");

        // store selected jabatan rates globally for modal session
        let currentGajiPerHari = 0;
        let currentBonusPerCup = 0;

        function resetGajiModal() {
            formGaji.reset();
            formGaji.action = "{{ route('gaji.store') }}";
            const m = formGaji.querySelector('input[name="_method"]'); if (m) m.remove();
            gajiEmployeeSelect.removeAttribute("disabled");
            currentGajiPerHari = 0; currentBonusPerCup = 0;
            gajiBasicAuto.value = ""; gajiBonusTotal.value = "";
        }

        window.resetGajiModal = resetGajiModal;

        // when employee selected, fetch jabatan rates
        async function fetchJabatanRates(email) {
            if (!email) return;
            try {
                const res = await fetch(`/api/jabatan-karyawan/${encodeURIComponent(email)}`);
                if (!res.ok) throw new Error('Gagal ambil data jabatan');
                const data = await res.json();
                currentGajiPerHari = parseFloat(data.gaji_per_hari) || 0;
                currentBonusPerCup = parseFloat(data.bonus_per_cup) || 0;
                recalcGaji();
            } catch (err) {
                console.error(err);
            }
        }

        function recalcGaji() {
            const hari = parseInt(gajiDaysInput.value) || 0;
            gajiBasicAuto.value = Math.round(hari * currentGajiPerHari);
            const bonusCups = parseInt(gajiBonusInput.value) || 0;
            gajiBonusTotal.value = Math.round(bonusCups * currentBonusPerCup);
        }

        gajiEmployeeSelect.addEventListener('change', (e) => fetchJabatanRates(e.target.value));
        gajiDaysInput.addEventListener('input', recalcGaji);
        gajiBonusInput.addEventListener('input', recalcGaji);

        window.fillGajiModal = function(id, email, namaJabatan, periode, basic, days, totalBonus, kompensasi) {
            resetGajiModal();
            formGaji.action = `/gaji/update/${id}`;
            formGaji.insertAdjacentHTML("beforeend", `<input type="hidden" name="_method" value="PUT">`);
            document.getElementById("gaji_employee_select").innerHTML = `<option value="${email}">${namaJabatan}</option>`;
            document.getElementById("gaji_employee_select").setAttribute("disabled", true);
            document.getElementById("gaji_period_input").value = periode;
            document.getElementById("gaji_days_input").value = days ?? 0;
            document.getElementById("gaji_basic_auto").value = basic ?? 0;
            // set input bonus value as number of cups if possible (we don't have stored cups count, so leave INPUT_BONUS blank)
            document.getElementById("gaji_bonus_input").value = "";
            document.getElementById("gaji_bonus_total").value = totalBonus ?? 0;
            document.getElementById("gaji_kompensasi_input").value = kompensasi ?? 0;
            // fetch current rates for this employee
            fetchJabatanRates(email);
            new bootstrap.Modal(document.getElementById('modalGaji')).show();
        };

        // JADWAL
        const formJadwal = document.getElementById("formJadwal");
        window.resetJadwalModal = function() {
            formJadwal.reset();
            formJadwal.action = "{{ route('jadwal.store') }}";
            const m = formJadwal.querySelector('input[name="_method"]'); if (m) m.remove();
        };
        function toInputTimeFormat(timeString) { if (!timeString) return ""; return timeString.substring(0,5); }
        window.fillJadwalModal = function(id_jadwal, email, id_cabang, tanggal, jam_mulai, jam_selesai) {
            resetJadwalModal();
            formJadwal.action = '/jadwal/update/' + encodeURIComponent(id_jadwal);
            formJadwal.insertAdjacentHTML("beforeend", `<input type="hidden" name="_method" value="PUT">`);
            document.getElementById("jadwal_id_input").value = id_jadwal;
            document.getElementById("jadwal_employee_select").value = email;
            document.getElementById("jadwal_cabang_select").value = id_cabang;
            document.getElementById("jadwal_tanggal_input").value = tanggal;
            document.getElementById("jadwal_jam_mulai_input").value = toInputTimeFormat(jam_mulai);
            document.getElementById("jadwal_jam_selesai_input").value = toInputTimeFormat(jam_selesai);
            new bootstrap.Modal(document.getElementById('modalJadwal')).show();
        };

        // sync hidden fields for cabang / rombong
        document.getElementById('id_cabang_select')?.addEventListener("change", (e) => { hiddenCabang.value = e.target.value; });
        document.getElementById('id_rombong_select')?.addEventListener("change", (e) => { hiddenRombong.value = e.target.value; });

        // DELETE helper
        window.confirmDelete = function(id, tipe) {
            if (!confirm(`Yakin ingin menghapus data ${tipe} dengan ID ${id} ini?`)) return;
            const formDel = document.createElement('form');
            formDel.method = 'POST';
            formDel.innerHTML = `@csrf`;
            if (tipe === 'Karyawan') {
                formDel.action = `/employee/${encodeURIComponent(id)}`;
                formDel.insertAdjacentHTML('beforeend', `<input type="hidden" name="_method" value="DELETE">`);
            } else if (tipe === 'Gaji') {
                formDel.action = `/gaji/${encodeURIComponent(id)}`;
                formDel.insertAdjacentHTML('beforeend', `<input type="hidden" name="_method" value="DELETE">`);
            } else if (tipe === 'Jadwal') {
                formDel.action = `/jadwal/delete/${encodeURIComponent(id)}`;
                formDel.insertAdjacentHTML('beforeend', `<input type="hidden" name="_method" value="DELETE">`);
            } else {
                // fallback: support jabatan delete via confirmDeleteJabatan
                console.log('Tipe hapus tidak dikenali:', tipe);
                return;
            }
            document.body.appendChild(formDel);
            formDel.submit();
        };

        // JABATAN CRUD helpers
        window.resetJabatanModal = () => {
            const formJ = document.getElementById('formJabatan');
            formJ.reset();
            formJ.action = "{{ route('jabatan.store') }}";
            const method = formJ.querySelector('input[name=\"_method\"]');
            if (method) method.remove();
        };

        window.fillJabatanModal = (id, nama, gaji, bonus) => {
            resetJabatanModal();
            const formJ = document.getElementById('formJabatan');
            formJ.action = `/jabatan/update/${encodeURIComponent(id)}`;
            formJ.insertAdjacentHTML("beforeend", `<input type="hidden" name="_method" value="PUT">`);
            document.getElementById('jabatan_nama').value = nama;
            document.getElementById('jabatan_gaji').value = gaji;
            document.getElementById('jabatan_bonus').value = bonus;
            new bootstrap.Modal(document.getElementById('modalJabatan')).show();
        };

        window.confirmDeleteJabatan = (id) => {
            if (!confirm("Yakin ingin menghapus jabatan ini?")) return;
            const form = document.createElement("form");
            form.method = "POST";
            form.action = `/jabatan/delete/${encodeURIComponent(id)}`;
            form.innerHTML = `@csrf<input type="hidden" name="_method" value="DELETE">`;
            document.body.appendChild(form);
            form.submit();
        };
});
</script>
@endpush
