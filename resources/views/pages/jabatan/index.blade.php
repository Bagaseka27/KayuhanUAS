@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>Data Jabatan</h2>
        </div>
        <div class="col-md-4 text-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahJabatanModal">
                <i class="fas fa-plus"></i> Tambah Jabatan
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Nama Jabatan</th>
                            <th>Upah per Jam</th>
                            <th>Bonus per Cup</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jabatan as $j)
                        <tr>
                            <td>{{ $j->NAMA_JABATAN }}</td>
                            <td>Rp {{ number_format($j->UPAH_PER_JAM, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($j->BONUS_PENJUALAN_PER_CUP, 0, ',', '.') }}</td>
                            <td>
                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editJabatanModal{{ $j->ID_JABATAN }}">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <form action="{{ route('jabatan.delete', $j->ID_JABATAN) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Modal Edit -->
                        <div class="modal fade" id="editJabatanModal{{ $j->ID_JABATAN }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Jabatan</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('jabatan.update', $j->ID_JABATAN) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Nama Jabatan</label>
                                                <input type="text" name="NAMA_JABATAN" class="form-control" value="{{ $j->NAMA_JABATAN }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Upah per Jam (Rp)</label>
                                                <input type="number" name="UPAH_PER_JAM" class="form-control" value="{{ $j->UPAH_PER_JAM }}" min="0" step="0.01" required>
                                                <small class="text-muted">Default: 5000</small>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Bonus per Cup Penjualan (Rp)</label>
                                                <input type="number" name="BONUS_PENJUALAN_PER_CUP" class="form-control" value="{{ $j->BONUS_PENJUALAN_PER_CUP }}" min="0" step="0.01" required>
                                                <small class="text-muted">Bonus diberikan jika penjualan > 50 cup</small>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                Tidak ada data jabatan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Jabatan -->
<div class="modal fade" id="tambahJabatanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Jabatan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('jabatan.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Jabatan</label>
                        <select name="NAMA_JABATAN" class="form-select" required>
                            <option value="">-- Pilih Jabatan --</option>
                            <option value="Senior">Senior</option>
                            <option value="Junior">Junior</option>
                            <option value="Training">Training</option>
                            <option value="Lainnya">Lainnya (Input Manual)</option>
                        </select>
                        <input type="text" name="NAMA_JABATAN_MANUAL" class="form-control mt-2 d-none" placeholder="Masukkan nama jabatan" id="namaJabatanManual">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Upah per Jam (Rp)</label>
                        <input type="number" name="UPAH_PER_JAM" class="form-control" value="5000" min="0" step="0.01" required>
                        <small class="text-muted">Default: 5000</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bonus per Cup Penjualan (Rp)</label>
                        <input type="number" name="BONUS_PENJUALAN_PER_CUP" class="form-control" value="0" min="0" step="0.01" required>
                        <small class="text-muted">Bonus diberikan jika penjualan > 50 cup</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.querySelector('select[name="NAMA_JABATAN"]').addEventListener('change', function() {
    const manual = document.getElementById('namaJabatanManual');
    if (this.value === 'Lainnya') {
        manual.classList.remove('d-none');
    } else {
        manual.classList.add('d-none');
    }
});
</script>
@endsection
