@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="mb-3">Data Gaji Karyawan</h2>
        </div>
    </div>

    <!-- Filter Periode -->
    <div class="card mb-3">
        <div class="card-body">
            <form action="{{ route('gaji.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="periode" class="form-label">Periode</label>
                    <input type="month" name="periode" class="form-control" value="{{ $periode }}" required>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Data Gaji Per Karyawan -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Nama Karyawan</th>
                            <th>Email</th>
                            <th>Total Jam Kerja</th>
                            <th>Total Gaji Akhir</th>
                            <th>Tabungan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($gaji as $g)
                        <tr>
                            <td>{{ $g->karyawan?->NAMA }}</td>
                            <td>{{ $g->EMAIL }}</td>
                            <td>{{ number_format($g->TOTAL_JAM_KERJA, 2) }} jam</td>
                            <td class="fw-bold text-success">Rp {{ number_format($g->TOTAL_GAJI_AKHIR, 0, ',', '.') }}</td>
                            <td class="fw-bold text-info">Rp {{ number_format($g->TABUNGAN, 0, ',', '.') }}</td>
                            <td>
                                <a href="{{ route('gaji.detail', $g->EMAIL) }}?periode={{ $periode }}" class="btn btn-info btn-sm" title="Lihat Detail Harian">
                                    <i class="fas fa-list"></i> Detail
                                </a>
                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editGaji{{ $g->ID_GAJI }}" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('gaji.destroy', $g->ID_GAJI) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Modal Edit -->
                        <div class="modal fade" id="editGaji{{ $g->ID_GAJI }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Gaji - {{ $g->karyawan?->NAMA }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('gaji.update', $g->ID_GAJI) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Gaji Pokok</label>
                                                <p class="form-control-plaintext">Rp {{ number_format($g->TOTAL_GAJI_POKOK, 0, ',', '.') }}</p>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Bonus Penjualan</label>
                                                <p class="form-control-plaintext">Rp {{ number_format($g->BONUS_PENJUALAN, 0, ',', '.') }}</p>
                                            </div>
                                            <div class="mb-3">
                                                <label for="potongan" class="form-label">Potongan (Adjustment)</label>
                                                <input type="number" id="potongan" name="TOTAL_POTONGAN" class="form-control" value="{{ $g->TOTAL_POTONGAN }}" step="0.01">
                                                <small class="text-muted">
                                                    @if($g->MENIT_TERLAMBAT > 15)
                                                        <br>⚠️ Keterlambatan > 15 menit. Otomatis dipotong 50% dari total gaji pokok + bonus
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                        <div class="modal-footer d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="badge bg-info p-2" style="font-size: 0.9rem;">
                                                    <i class="fas fa-wallet me-1"></i> Tabungan: Rp {{ number_format($g->TABUNGAN, 0, ',', '.') }}
                                                </span>
                                            </div>
                                            <div>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Simpan</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-inbox"></i> Belum ada data gaji untuk periode {{ $periode }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.table-hover tbody tr:hover {
    background-color: #f5f5f5;
}
</style>
@endsection
