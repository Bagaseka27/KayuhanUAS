@extends('layouts.app')

@section('title', 'Lokasi & Cabang - Kayuhan')

@section('content')
   
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
                            <td>{{ $cab->ID_CABANG }}</td>
                            <td>{{ $cab->NAMA_LOKASI }}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-light text-primary edit-cabang-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditLokasi"
                                    data-type="cabang"
                                    data-id="{{ $cab->ID_CABANG }}"
                                    data-namalokasi="{{ $cab->NAMA_LOKASI }}">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <form action="/cabang/{{ $cab->ID_CABANG }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-light text-danger" onclick="return confirm('Hapus?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>

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
                        @foreach($rombongs as $rom)
                        <tr>
                            <td>{{ $rom->ID_ROMBONG }}</td>
                            <td>{{ $rom->cabang->NAMA_LOKASI ?? '-' }}</td>
                            <td class="text-center">

                                <button class="btn btn-sm btn-light text-primary edit-rombong-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditLokasi"
                                    data-type="rombong"
                                    data-id="{{ $rom->ID_ROMBONG }}"
                                    data-cabangid="{{ $rom->ID_CABANG }}"
                                    data-lokasi="{{ $rom->cabang->NAMA_LOKASI ?? '-' }}">
                                    <i class="fas fa-edit"></i>
                                </button>


                                <form action="/rombong/{{ $rom->ID_ROMBONG }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-light text-danger" onclick="return confirm('Hapus?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>

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
            <form action="/cabang" method="POST" class="modal-content border-0 shadow">
                @csrf
                <div class="modal-header text-white" style="background-color: var(--primary);">
                    <h5 class="modal-title">Kelola Cabang</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-secondary">ID Cabang</label>
                        <input type="text" name="ID_CABANG" class="form-control" placeholder="Contoh: CBG-SBY01">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-secondary">Nama Lokasi</label>
                        <input type="text" name="NAMA_LOKASI" class="form-control">
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
            <form action="/rombong" method="POST" class="modal-content border-0 shadow">
                @csrf
                <div class="modal-header text-white" style="background-color: var(--primary);">
                    <h5 class="modal-title">Tambah Rombong</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-secondary">ID Rombong</label>
                        <!-- Bisa diisi manual, atau kamu bisa buat generator JS jika mau auto -->
                        <input type="text" name="ID_ROMBONG" class="form-control" placeholder="Contoh: RM-001" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary">Pilih Cabang (Lokasi)</label>
                        <select name="ID_CABANG" class="form-select" required>
                            <option value="">-- Pilih Cabang --</option>
                            @foreach($cabangs as $cab)
                                <option value="{{ $cab->ID_CABANG }}">{{ $cab->NAMA_LOKASI }} ({{ $cab->ID_CABANG }})</option>
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
        const cabangData = @json($cabangs);
        const cabangListHtml = cabangData.map(cab => 
            `<option value="${cab.ID_CABANG}">${cab.NAMA_LOKASI} (${cab.ID_CABANG})</option>`
        ).join('');

        const modalEditLokasi = document.getElementById('modalEditLokasi');
        const form = document.getElementById('edit-lokasi-form');
        const dynamicFields = document.getElementById('dynamic-fields-container');
        const modalTitle = document.getElementById('modalEditLokasiLabel');
        const inputId = document.getElementById('edit_lokasi_id');

        modalEditLokasi.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const type = button.getAttribute('data-type');
            const id = button.getAttribute('data-id');

            inputId.value = id;
            form.action = `/${type}/${id}`; // e.g. /rombong/RM-001

            dynamicFields.innerHTML = '';

            if (type === 'cabang') {
                const namaLokasi = button.getAttribute('data-namalokasi') || '';
                modalTitle.textContent = 'Edit Cabang';

                dynamicFields.innerHTML = `
                    <div class="mb-3">
                        <label class="form-label text-secondary">Nama Lokasi</label>
                        <input type="text" name="NAMA_LOKASI" class="form-control" value="${namaLokasi}" required>
                    </div>
                `;
            } else if (type === 'rombong') {
                const currentLoc = button.getAttribute('data-lokasi') || '-';
                const currentCabangId = button.getAttribute('data-cabangid') || '';

                modalTitle.textContent = 'Edit Rombong';

                dynamicFields.innerHTML = `
                    <div class="mb-3">
                        <label class="form-label text-secondary">Pilih Lokasi Cabang Baru</label>
                        <select name="ID_CABANG" class="form-select" required>
                            <option value="">-- Pilih Cabang --</option>
                            ${cabangListHtml}
                        </select>
                    </div>
                    <p class="text-muted small">Lokasi saat ini: ${currentLoc}</p>
                `;

                const sel = modalEditLokasi.querySelector('select[name="ID_CABANG"]');
                if (sel) sel.value = currentCabangId;
            }
        });
    });
</script>
@endpush