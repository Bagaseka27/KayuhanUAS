@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    
    <!-- Navigation Tabs / Title -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h2 class="fw-bold text-primary-custom mb-1" style="font-family: 'Outfit', sans-serif;">Persetujuan Gaji Barista</h2>
            <p class="text-muted mb-0">Kelola dan setujui pengajuan pencairan/penyimpanan gaji barista.</p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('gaji.daftarPengambilan') }}" class="btn btn-outline-success px-4 py-2 me-2 fw-bold rounded-3">
                <i class="fas fa-money-bill-wave me-2"></i> Pengambilan Gaji
            </a>
            <a href="{{ route('gaji.daftarPenyimpanan') }}" class="btn btn-success px-4 py-2 fw-bold rounded-3 shadow-sm">
                <i class="fas fa-piggy-bank me-2"></i> Penyimpanan Gaji
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-left: 5px solid #28a745; background-color: #fff;">
        <div class="d-flex align-items-center">
            <i class="fas fa-check-circle text-success me-3" style="font-size: 1.5rem;"></i>
            <div>
                <strong class="text-dark">Berhasil!</strong>
                <div class="text-muted small">{{ session('success') }}</div>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-left: 5px solid #dc3545; background-color: #fff;">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-circle text-danger me-3" style="font-size: 1.5rem;"></i>
            <div>
                <strong class="text-dark">Gagal!</strong>
                <div class="text-muted small">{{ session('error') }}</div>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Filter Status -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('gaji.daftarPenyimpanan') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="status" class="form-label fw-bold text-secondary small">Filter Status</label>
                    <select name="status" class="form-select border-0 bg-light rounded-3" id="status" onchange="this.form.submit()">
                        <option value="menunggu" @if($status === 'menunggu') selected @endif>⏳ Menunggu Diproses</option>
                        <option value="disetujui" @if($status === 'disetujui') selected @endif>✅ Disetujui</option>
                        <option value="ditolak" @if($status === 'ditolak') selected @endif>❌ Ditolak</option>
                        <option value="" @if($status === '') selected @endif>📂 Semua Status</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary text-uppercase small fw-bold">
                        <tr>
                            <th class="py-3 ps-4">Tanggal Penyimpanan</th>
                            <th>Nama Karyawan</th>
                            <th>Email</th>
                            <th>Nominal Disimpan</th>
                            <th>Status</th>
                            <th class="text-center pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse($disimpan as $d)
                        <tr>
                            <td class="py-3 ps-4 text-muted">{{ \Carbon\Carbon::parse($d->TANGGAL_PENYIMPANAN)->format('d/m/Y') }}</td>
                            <td class="fw-bold text-dark">{{ $d->karyawan?->NAMA ?? 'N/A' }}</td>
                            <td class="text-muted">{{ $d->EMAIL }}</td>
                            <td>
                                <span class="badge bg-success-light text-success fw-bold px-3 py-2" style="font-size: 0.9rem; background-color: #e8f5e9;">
                                    Rp {{ number_format($d->NOMINAL, 0, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                @if($d->STATUS === 'menunggu')
                                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Menunggu</span>
                                @elseif($d->STATUS === 'disetujui')
                                    <span class="badge bg-success text-white px-3 py-2 rounded-pill">Disetujui</span>
                                @else
                                    <span class="badge bg-danger text-white px-3 py-2 rounded-pill">Ditolak</span>
                                @endif
                            </td>
                            <td class="text-center pe-4">
                                @if($d->STATUS === 'menunggu')
                                    <button type="button" class="btn btn-primary btn-sm px-3 rounded-pill fw-bold py-1.5 shadow-sm" data-bs-toggle="modal" data-bs-target="#terimaModal{{ $d->id }}">
                                        <i class="fas fa-check-circle me-1"></i> Proses Gaji
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm px-3 rounded-pill fw-bold py-1.5 ms-1" data-bs-toggle="modal" data-bs-target="#tolakModal{{ $d->id }}">
                                        <i class="fas fa-times-circle me-1"></i> Tolak
                                    </button>
                                @else
                                    <button type="button" class="btn btn-outline-info btn-sm px-3 rounded-pill fw-bold py-1.5" data-bs-toggle="modal" data-bs-target="#detailModal{{ $d->id }}">
                                        <i class="fas fa-eye me-1"></i> Detail
                                    </button>
                                @endif
                            </td>
                        </tr>

                        <!-- Modal Terima -->
                        <div class="modal fade" id="terimaModal{{ $d->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 rounded-4 shadow">
                                    <div class="modal-header text-white py-3" style="background-color: #003d2e;">
                                        <h5 class="modal-title fw-bold">Setujui Penyimpanan Gaji</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('gaji.terimaPenyimpanan', $d->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-body p-4">
                                            <p class="mb-2"><strong>Karyawan:</strong> {{ $d->karyawan?->NAMA }}</p>
                                            <p class="mb-2"><strong>Email:</strong> {{ $d->EMAIL }}</p>
                                            <p class="mb-3"><strong>Nominal Simpan:</strong> Rp {{ number_format($d->NOMINAL, 0, ',', '.') }}</p>
                                            
                                            <div class="alert alert-info border-0 rounded-3 small mb-3">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Menyetujui pengajuan ini akan mentransfer seluruh sisa gaji barista sebesar Rp {{ number_format($d->NOMINAL, 0, ',', '.') }} ke tabungan tersimpan mereka.
                                            </div>

                                            <div class="mb-3">
                                                <label for="catatan_terima" class="form-label fw-bold small text-secondary">Catatan Admin (Opsional)</label>
                                                <textarea name="CATATAN_ADMIN" class="form-control bg-light border-0 rounded-3" rows="3" placeholder="Tambahkan catatan..."></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 bg-light p-3 rounded-bottom-4">
                                            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-success px-4 fw-bold">Tandai Sudah Diproses</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Tolak -->
                        <div class="modal fade" id="tolakModal{{ $d->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 rounded-4 shadow">
                                    <div class="modal-header text-white py-3 bg-danger">
                                        <h5 class="modal-title fw-bold">Tolak Penyimpanan Gaji</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('gaji.tolakPenyimpanan', $d->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-body p-4">
                                            <p class="mb-2"><strong>Karyawan:</strong> {{ $d->karyawan?->NAMA }}</p>
                                            <p class="mb-3"><strong>Nominal Simpan:</strong> Rp {{ number_format($d->NOMINAL, 0, ',', '.') }}</p>
                                            <div class="mb-3">
                                                <label for="catatan_tolak" class="form-label fw-bold small text-secondary">Alasan Penolakan</label>
                                                <textarea name="CATATAN_ADMIN" class="form-control bg-light border-0 rounded-3" rows="3" placeholder="Berikan alasan penolakan..." required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 bg-light p-3 rounded-bottom-4">
                                            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-danger px-4 fw-bold">Tolak Pengajuan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Detail -->
                        <div class="modal fade" id="detailModal{{ $d->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 rounded-4 shadow">
                                    <div class="modal-header text-white py-3 bg-info">
                                        <h5 class="modal-title fw-bold">Detail Penyimpanan Gaji</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="d-flex justify-content-between border-bottom py-2">
                                            <strong>Karyawan:</strong> <span>{{ $d->karyawan?->NAMA }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between border-bottom py-2">
                                            <strong>Email:</strong> <span>{{ $d->EMAIL }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between border-bottom py-2">
                                            <strong>Nominal Disimpan:</strong> <span>Rp {{ number_format($d->NOMINAL, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between border-bottom py-2">
                                            <strong>Status:</strong> 
                                            <span>
                                                @if($d->STATUS === 'disetujui')
                                                    <span class="badge bg-success">Disetujui</span>
                                                @else
                                                    <span class="badge bg-danger">Ditolak</span>
                                                @endif
                                            </span>
                                        </div>
                                        <div class="d-flex justify-content-between border-bottom py-2">
                                            <strong>Catatan Admin:</strong> <span>{{ $d->CATATAN_ADMIN ?? '-' }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between border-bottom py-2">
                                            <strong>Diproses Oleh:</strong> <span>{{ $d->adminProses?->NAMA ?? $d->DIPROSES_OLEH ?? '-' }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between py-2">
                                            <strong>Waktu Proses:</strong> <span>{{ $d->TANGGAL_DIPROSES ? \Carbon\Carbon::parse($d->TANGGAL_DIPROSES)->format('d/m/Y H:i') : '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 bg-light p-3 rounded-bottom-4">
                                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-inbox me-2"></i> Tidak ada data pengajuan penyimpanan gaji.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($disimpan->hasPages())
            <div class="p-3 bg-light border-top">
                {{ $disimpan->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
