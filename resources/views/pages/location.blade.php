@extends('layouts.app')

@section('title', 'Lokasi & Cabang - Kayuhan')

@section('content')
    {{-- DATA DUMMY (Agar Modal Edit Bisa Muncul Per Item) --}}
    @php
        $cabangs = [
            (object)['id' => 'SBY01', 'name' => 'Taman Bungkul'],
            (object)['id' => 'SBY02', 'name' => 'Kampus Unair B'],
            (object)['id' => 'SBY03', 'name' => 'Merr Surabaya'],
        ];

        $rombongs = [
            (object)['id' => 'RMB-01', 'loc' => 'Taman Bungkul'],
            (object)['id' => 'RMB-02', 'loc' => 'Kampus Unair B'],
            (object)['id' => 'RMB-03', 'loc' => '-'],
        ];
    @endphp

    <h3 class="fw-bold text-primary-custom mb-4">Manajemen Lokasi</h3>

    <div class="row">
        <!-- 1. TABEL CABANG (KIRI) -->
        <div class="col-md-7 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold m-0"><i class="fas fa-map-marker-alt me-2 text-accent"></i>Daftar Cabang</h5>
                <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#modalAddCabang">
                    <i class="fas fa-plus"></i> Tambah Cabang
                </button>
            </div>
            <div class="stat-card p-0 overflow-hidden shadow-sm border-0 rounded-4">
                <table class="table custom-table mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>ID</th>
                            <th>Nama Lokasi</th>

                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cabangs as $cab)
                        <tr>
                            <td>{{ $cab->id }}</td>
                            <td class="fw-bold">{{ $cab->name }}</td>
                            <td class="text-center">
                                <!-- Tombol Edit -->
                                <button class="btn btn-sm btn-light text-primary me-1" data-bs-toggle="modal" data-bs-target="#modalEditCabang{{ $cab->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <!-- Tombol Hapus -->
                                <button class="btn btn-sm btn-light text-danger" onclick="confirm('Hapus cabang {{ $cab->name }}?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- MODAL EDIT CABANG (Per Item) -->
                        <div class="modal fade" id="modalEditCabang{{ $cab->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <form action="#" method="POST" class="modal-content border-0 shadow">
                                    @csrf @method('PUT')
                                    <div class="modal-header text-white" style="background-color: var(--primary);">
                                        <h5 class="modal-title">Edit Cabang</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label text-secondary">ID Cabang</label>
                                            <input type="text" class="form-control bg-light" value="{{ $cab->id }}" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-secondary">Nama Lokasi</label>
                                            <input type="text" name="name" class="form-control" value="{{ $cab->name }}">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary px-4">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 2. TABEL ROMBONG (KANAN) -->
        <div class="col-md-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold m-0"><i class="fas fa-bicycle me-2 text-accent"></i>Unit Rombong</h5>
                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAddRombong">
                    <i class="fas fa-plus"></i> Tambah Unit
                </button>
            </div>
            <div class="stat-card p-0 overflow-hidden shadow-sm border-0 rounded-4">
                <table class="table custom-table mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>ID Unit</th>
                            <th>Lokasi</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rombongs as $romb)
                        <tr>
                            <td class="fw-bold">{{ $romb->id }}</td>
                            <td>{{ $romb->loc }}</td>
                            <td class="text-center">
                                <!-- Tombol Edit -->
                                <button class="btn btn-sm btn-light text-primary me-1" data-bs-toggle="modal" data-bs-target="#modalEditRombong{{ $romb->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <!-- Tombol Hapus -->
                                <button class="btn btn-sm btn-light text-danger" onclick="confirm('Hapus unit {{ $romb->id }}?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- MODAL EDIT ROMBONG (Per Item) -->
                        <div class="modal fade" id="modalEditRombong{{ $romb->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <form action="#" method="POST" class="modal-content border-0 shadow">
                                    @csrf @method('PUT')
                                    <div class="modal-header text-white" style="background-color: var(--primary);">
                                        <h5 class="modal-title">Kelola Rombong</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label text-secondary">ID Unit</label>
                                            <input type="text" class="form-control bg-light" value="{{ $romb->id }}" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-secondary">Lokasi Saat Ini</label>
                                            <select name="location" class="form-select">
                                                <option selected>{{ $romb->loc }}</option>
                                                <option>Taman Bungkul</option>
                                                <option>Kampus Unair B</option>
                                                <option>-</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary px-4">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ================= MODAL TAMBAH (GLOBAL) ================= -->

    <!-- 1. MODAL TAMBAH CABANG -->
    <div class="modal fade" id="modalAddCabang" tabindex="-1">
        <div class="modal-dialog">
            <form action="#" method="POST" class="modal-content border-0 shadow">
                @csrf
                <div class="modal-header text-white" style="background-color: var(--primary);">
                    <h5 class="modal-title">Kelola Cabang</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-secondary">ID Cabang</label>
                        <input type="text" name="id" class="form-control" placeholder="Contoh: CBG-SBY01">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-secondary">Nama Lokasi</label>
                        <input type="text" name="name" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 2. MODAL TAMBAH ROMBONG -->
    <div class="modal fade" id="modalAddRombong" tabindex="-1">
        <div class="modal-dialog">
            <form action="#" method="POST" class="modal-content border-0 shadow">
                @csrf
                <div class="modal-header text-white" style="background-color: var(--primary);">
                    <h5 class="modal-title">Kelola Rombong</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-secondary">ID Rombong (Auto)</label>
                        <input type="text" class="form-control bg-light" value="Auto Generated" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-secondary">Pilih Cabang (Lokasi)</label>
                        <select name="cabang_id" class="form-select">
                            <option>Taman Bungkul (CBG-SBY01)</option>
                            <option>Kampus Unair B (CBG-SBY02)</option>
                            <option>Merr Surabaya (CBG-SBY03)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection