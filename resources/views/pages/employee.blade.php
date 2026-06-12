@extends('layouts.app')

@section('title', 'Manajemen SDM - Kayuhan')

@section('content')
    {{-- CSS KHUSUS UNTUK HALAMAN INI (Tab Effect) --}}
    <style>
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
                            <tr><th class="py-3 ps-4">EMAIL (PK)</th><th>NAMA</th><th>NO HP</th><th class="text-center pe-4">AKSI</th></tr>
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
                            <tr><th class="py-3 ps-4">EMAIL (PK)</th><th>ID JABATAN</th><th>ID ROMBONG</th><th>ID CABANG</th><th>NAMA</th><th>NO HP</th><th>POSISI</th><th class="text-center pe-4">AKSI</th></tr>
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
            <div class="d-flex gap-3 mb-4">
                <a href="{{ route('gaji.daftarPengambilan') }}" class="btn text-white fw-bold py-2.5 px-4 rounded-3 shadow-sm" style="background-color: var(--primary);">
                    <i class="fas fa-hand-holding-usd me-2"></i> Pengambilan Gaji
                </a>
                <a href="{{ route('gaji.daftarPenyimpanan') }}" class="btn text-white fw-bold py-2.5 px-4 rounded-3 shadow-sm" style="background-color: #d4a373;">
                    <i class="fas fa-piggy-bank me-2"></i> Penyimpanan Gaji
                </a>
                <button type="button" class="btn text-white fw-bold py-2.5 px-4 rounded-3 shadow-sm" style="background-color: #2a9d8f; border: none;" data-bs-toggle="modal" data-bs-target="#modalTabungan">
                    <i class="fas fa-wallet me-2"></i> Tabungan
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
                                    <a href="{{ route('gaji.show', $gaji->ID_GAJI) }}" class="btn btn-sm btn-info" title="Lihat Detail">
                                        <i class="fas fa-eye"></i> Lihat
                                    </a>
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
                    <i class="fas fa-info-circle me-2"></i> Jadwal diatur per minggu.
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
                            <tr><th>ID</th><th>Nama Jabatan</th><th>Gaji / Jam</th><th>Bonus / Cup</th><th class="text-center">Aksi</th></tr>
                        </thead>
                        <tbody class="bg-white">
                            @forelse($jabatanListFull as $j)
                            <tr>
                                <td>{{ $j->ID_JABATAN }}</td>
                                <td>{{ $j->NAMA_JABATAN }}</td>
                                <td>Rp {{ number_format($j->UPAH_PER_JAM, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($j->BONUS_PER_CUP, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <button class="btn btn-warning btn-sm" onclick="fillJabatanModal('{{ $j->ID_JABATAN }}','{{ $j->NAMA_JABATAN }}','{{ $j->UPAH_PER_JAM }}','{{ $j->BONUS_PER_CUP }}')">Edit</button>
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
            <div class="mb-3">
                <label class="fw-bold text-secondary">Upah / Jam (Rp)</label>
                <input type="number" name="UPAH_PER_JAM" id="jabatan_upah_jam" class="form-control" value="5000" min="0" step="0.01" required>
                <small class="text-muted">Default: 5000</small>
            </div>
            <div class="mb-3">
                <label class="fw-bold text-secondary">Bonus / Cup Penjualan (Rp)</label>
                <input type="number" name="BONUS_PENJUALAN_PER_CUP" id="jabatan_bonus_cup" class="form-control" min="0" step="0.01" required>
                <small class="text-muted">Bonus diberikan jika penjualan > 50 cup</small>
            </div>
        </div>
        <div class="modal-footer bg-light"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary text-white fw-bold">Simpan</button></div>
    </form></div></div>

    {{-- MODAL TABUNGAN BARISTA --}}
    <div class="modal fade" id="modalTabungan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header text-white border-0 py-3" style="background-color: #2a9d8f;">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-wallet me-2"></i> Akumulasi Tabungan Barista
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" style="background-color: #f4f7f6;">
                    <div class="row g-3">
                        @php
                            $baristasTabungan = \App\Models\Karyawan::with('tabungan')
                                ->where('ROLE', 'Barista')
                                ->get();
                        @endphp
                        @forelse($baristasTabungan as $bt)
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm rounded-3">
                                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                                    <div>
                                        <h5 class="fw-bold text-dark mb-1" style="font-family: 'Outfit', sans-serif; font-size: 1.1rem;">{{ $bt->NAMA }}</h5>
                                        <small class="text-muted">{{ $bt->EMAIL }}</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="text-muted small d-block mb-1">Total Tabungan</span>
                                        <h5 class="fw-bold text-success mb-0" style="font-size: 1.15rem;">Rp {{ number_format($bt->tabungan?->SALDO ?? 0, 0, ',', '.') }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12 text-center text-muted py-4">
                            <i class="fas fa-info-circle me-2"></i> Belum ada data barista.
                        </div>
                        @endforelse
                    </div>
                </div>
                <div class="modal-footer border-0 bg-white p-3 rounded-bottom-4">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", () => {
        // Active tab from URL hash
        const hash = window.location.hash;
        if (hash) {
            const tabEl = document.querySelector(`button[data-bs-target="${hash}"]`);
            if (tabEl) {
                const tab = new bootstrap.Tab(tabEl);
                tab.show();
            }
        }

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

        window.fillJabatanModal = (id, nama, upahJam, bonusCup) => {
            resetJabatanModal();
            const formJ = document.getElementById('formJabatan');
            formJ.action = `/jabatan/update/${encodeURIComponent(id)}`;
            formJ.insertAdjacentHTML("beforeend", `<input type="hidden" name="_method" value="PUT">`);
            document.getElementById('jabatan_nama').value = nama;
            document.getElementById('jabatan_upah_jam').value = upahJam || 5000;
            document.getElementById('jabatan_bonus_cup').value = bonusCup || 0;
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
