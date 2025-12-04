@extends('layouts.app')

@section('title', 'Lokasi & Cabang - Kayuhan')

@section('content')
    {{-- DATA DUMMY (Tetap di sini) --}}
    @php
        $cabangs = [
            (object)['id' => 'SBY01', 'name' => 'Taman Bungkul'],
            (object)['id' => 'SBY02', 'name' => 'Kampus Unair B'],
            (object)['id' => 'SBY03', 'name' => 'Merr Surabaya'],
        ];

        $rombongs = [
            (object)['id' => 'RMB-01', 'loc' => 'Taman Bungkul', 'cabang_id' => 'SBY01'],
            (object)['id' => 'RMB-02', 'loc' => 'Kampus Unair B', 'cabang_id' => 'SBY02'],
            (object)['id' => 'RMB-03', 'loc' => 'Belum Ditentukan', 'cabang_id' => null],
        ];

        // Daftar cabang untuk dropdown (digunakan di Modal Add/Edit Rombong)
        $cabangList = collect($cabangs)->pluck('name', 'id')->all();
    @endphp

    <h3 class="fw-bold text-primary-custom mb-4">Manajemen Lokasi</h3>

    <div class="row">
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
                                <button class="btn btn-sm btn-light text-primary me-1 edit-cabang-btn"
                                    data-bs-toggle="modal" data-bs-target="#modalEditLokasi"
                                    data-type="cabang"
                                    data-id="{{ $cab->id }}"
                                    data-name="{{ $cab->name }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-light text-danger" onclick="confirm('Hapus cabang {{ $cab->name }}?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

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
                                <button class="btn btn-sm btn-light text-primary me-1 edit-rombong-btn"
                                    data-bs-toggle="modal" data-bs-target="#modalEditLokasi"
                                    data-type="rombong"
                                    data-id="{{ $romb->id }}"
                                    data-loc="{{ $romb->loc }}"
                                    data-cabangid="{{ $romb->cabang_id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-light text-danger" onclick="confirm('Hapus unit {{ $romb->id }}?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

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
                            @foreach($cabangs as $cab)
                                <option value="{{ $cab->id }}">{{ $cab->name }} ({{ $cab->id }})</option>
                            @endforeach
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

    <div class="modal fade" id="modalEditLokasi" tabindex="-1" aria-labelledby="modalEditLokasiLabel">
        <div class="modal-dialog">
            <form action="#" method="POST" class="modal-content border-0 shadow" id="edit-lokasi-form">
                @csrf
                @method('PUT')
                <div class="modal-header text-white" style="background-color: var(--primary);">
                    <h5 class="modal-title" id="modalEditLokasiLabel">Kelola Lokasi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-secondary">ID Lokasi</label>
                        <input type="text" id="edit_lokasi_id" class="form-control bg-light" readonly>
                    </div>
                    
                    <div id="dynamic-fields-container">
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Update</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data Cabang yang tersedia untuk dropdown Rombong
        const cabangData = @json($cabangs);
        const cabangListHtml = cabangData.map(cab => 
            `<option value="${cab.id}">${cab.name} (${cab.id})</option>`
        ).join('');

        const modalEditLokasi = document.getElementById('modalEditLokasi');
        const form = document.getElementById('edit-lokasi-form');
        const dynamicFields = document.getElementById('dynamic-fields-container');
        const modalTitle = document.getElementById('modalEditLokasiLabel');
        const inputId = document.getElementById('edit_lokasi_id');

        // Listener untuk modal edit generik
        modalEditLokasi.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const type = button.getAttribute('data-type');
            const id = button.getAttribute('data-id');

            // 1. Setup ID dan Action Form
            inputId.value = id;
            form.action = `/locations/${type}/${id}`; // Contoh: /locations/cabang/SBY01

            dynamicFields.innerHTML = ''; // Bersihkan field sebelumnya

            if (type === 'cabang') {
                const name = button.getAttribute('data-name');
                modalTitle.textContent = 'Edit Cabang';
                
                // Isi field untuk Edit Cabang (Nama Lokasi)
                dynamicFields.innerHTML = `
                    <div class="mb-3">
                        <label class="form-label text-secondary">Nama Lokasi</label>
                        <input type="text" name="name" class="form-control" value="${name}" required>
                    </div>
                `;

            } else if (type === 'rombong') {
                const currentLoc = button.getAttribute('data-loc');
                const currentCabangId = button.getAttribute('data-cabangid');
                modalTitle.textContent = 'Edit Rombong';
                
                // Isi field untuk Edit Rombong (Pilih Cabang/Lokasi)
                dynamicFields.innerHTML = `
                    <div class="mb-3">
                        <label class="form-label text-secondary">Pilih Lokasi Cabang Baru</label>
                        <select name="cabang_id" class="form-select" required>
                            <option value="">-- Pilih Cabang --</option>
                            ${cabangListHtml}
                        </select>
                    </div>
                    <p class="text-muted small">Lokasi saat ini: ${currentLoc}</p>
                `;
                
                // Set nilai terpilih di dropdown
                modalEditLokasi.querySelector('select[name="cabang_id"]').value = currentCabangId;
            }
        });
    });
</script>
@endpush