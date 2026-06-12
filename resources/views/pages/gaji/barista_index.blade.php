@extends('layouts.app_barista')

@section('content')
<div class="container-fluid py-4" style="background-color: #f4f7f6; min-height: 90vh;">
    
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1" style="font-family: 'Outfit', sans-serif;">Gaji Saya</h2>
            <p class="text-muted mb-0">Kelola pendapatan harian, simpanan, dan penarikan gaji Anda.</p>
        </div>
        <div class="badge px-3 py-2 text-white" style="background-color: #003d2e; font-size: 0.9rem; border-radius: 20px;">
            <i class="fas fa-calendar-alt me-2"></i> Hari ini: {{ now()->translatedFormat('l, d F Y') }}
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

    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <!-- Total Gaji Bulan Ini -->
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #005f45 0%, #003d2e 100%);">
                <div class="card-body p-4 position-relative z-index-2">
                    <h6 class="text-white-50 text-uppercase fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">Total Gaji Bulan Ini</h6>
                    <h2 class="fw-bold mb-3" style="font-size: 1.5rem;">Rp {{ number_format($gajiBulanIni?->TOTAL_GAJI_AKHIR ?? 0, 0, ',', '.') }}</h2>
                    <div class="d-flex align-items-center small text-white-50">
                        <i class="fas fa-history me-2"></i> Periode: {{ $periode }}
                    </div>
                </div>
                <!-- Background Icon -->
                <i class="fas fa-wallet position-absolute opacity-10" style="font-size: 6rem; right: -10px; bottom: -10px; color: #fff;"></i>
            </div>
        </div>

        <!-- Total Saldo Tersedia -->
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #2a9d8f 0%, #1a635a 100%);">
                <div class="card-body p-4 position-relative z-index-2">
                    <h6 class="text-white-50 text-uppercase fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">Total Saldo Tersedia</h6>
                    <h2 class="fw-bold mb-3" style="font-size: 1.5rem;">Rp {{ number_format($sisaGaji, 0, ',', '.') }}</h2>
                    <div class="d-flex align-items-center small text-white-50">
                        <i class="fas fa-money-bill-wave me-2"></i> Gaji Pokok + Tabungan
                    </div>
                </div>
                <!-- Background Icon -->
                <i class="fas fa-coins position-absolute opacity-10" style="font-size: 6rem; right: -10px; bottom: -10px; color: #fff;"></i>
            </div>
        </div>

        <!-- Tabungan Disimpan -->
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #bfa15f 0%, #8c733f 100%);">
                <div class="card-body p-4 position-relative z-index-2">
                    <h6 class="text-white-50 text-uppercase fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">Tabungan Disimpan</h6>
                    <h2 class="fw-bold mb-3" style="font-size: 1.5rem;">Rp {{ number_format($totalTabungan, 0, ',', '.') }}</h2>
                    <div class="d-flex align-items-center small text-white-50">
                        <i class="fas fa-piggy-bank me-2"></i> Akumulasi Simpanan
                    </div>
                </div>
                <!-- Background Icon -->
                <i class="fas fa-piggy-bank position-absolute opacity-10" style="font-size: 6rem; right: -10px; bottom: -10px; color: #fff;"></i>
            </div>
        </div>

        <!-- Total Jam Kerja -->
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #f4a261 0%, #d87d31 100%);">
                <div class="card-body p-4 position-relative z-index-2">
                    <h6 class="text-white-50 text-uppercase fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">Total Jam Kerja</h6>
                    <h2 class="fw-bold mb-3" style="font-size: 1.5rem;">{{ number_format($gajiBulanIni?->TOTAL_JAM_KERJA ?? 0, 2) }} Jam</h2>
                    <div class="d-flex align-items-center small text-white-50">
                        <i class="fas fa-clock me-2"></i> Akumulasi Bulan Ini
                    </div>
                </div>
                <!-- Background Icon -->
                <i class="fas fa-hourglass-half position-absolute opacity-10" style="font-size: 6rem; right: -10px; bottom: -10px; color: #fff;"></i>
            </div>
        </div>
    </div>

    <!-- Action Section: Ambil & Simpan Gaji -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold text-dark mb-3" style="font-family: 'Outfit', sans-serif;">
                <i class="fas fa-cogs text-primary-custom me-2"></i> Menu Aksi Gaji
            </h5>

            <!-- Status Alert Lock Pengambilan -->
            @if(!$isFriday)
            <div class="alert alert-warning border-0 rounded-3 d-flex align-items-center mb-4" style="background-color: #fff8e1; border-left: 4px solid #ffb300;">
                <i class="fas fa-lock text-warning me-3" style="font-size: 1.2rem;"></i>
                <div class="text-dark small">
                    <strong>Fitur Terkunci:</strong> Saat ini adalah hari <strong>{{ now()->translatedFormat('l') }}</strong>. Pengambilan dan penyimpanan gaji hanya dapat diakses pada hari <strong>Jumat</strong>.
                </div>
            </div>
            @elseif($hasRequestedThisWeek)
            <div class="alert alert-info border-0 rounded-3 d-flex align-items-center mb-4" style="background-color: #e3f2fd; border-left: 4px solid #1e88e5;">
                <i class="fas fa-info-circle text-info me-3" style="font-size: 1.2rem;"></i>
                <div class="text-dark small">
                    <strong>Pengajuan Terkunci:</strong> Anda sudah mengajukan pengambilan atau penyimpanan gaji untuk minggu ini. Sesuai aturan, klaim hanya dapat dilakukan 1 kali dalam seminggu.
                </div>
            </div>
            @else
            <div class="alert alert-success border-0 rounded-3 d-flex align-items-center mb-4" style="background-color: #e8f5e9; border-left: 4px solid #43a047;">
                <i class="fas fa-lock-open text-success me-3" style="font-size: 1.2rem;"></i>
                <div class="text-dark small">
                    <strong>Fitur Terbuka:</strong> Hari ini adalah hari Jumat! Silakan ajukan penarikan nominal atau simpan sisa gaji Anda ke tabungan.
                </div>
            </div>
            @endif

            <!-- Buttons Grid -->
            <div class="row g-3">
                <div class="col-md-6">
                    <button type="button" 
                            class="btn btn-lg w-100 py-3 rounded-3 fw-bold text-white d-flex align-items-center justify-content-center shadow-sm" 
                            style="background-color: #003d2e; border: none; transition: all 0.3s;"
                            @if(!$isFriday || $hasRequestedThisWeek || $sisaGaji <= 0) disabled @endif
                            data-bs-toggle="modal" 
                            data-bs-target="#modalAmbilGaji">
                        <i class="fas fa-hand-holding-usd me-3" style="font-size: 1.3rem;"></i>
                        Ambil Sebagian Gaji
                    </button>
                </div>
                <div class="col-md-6">
                    <button type="button" 
                            class="btn btn-lg w-100 py-3 rounded-3 fw-bold text-white d-flex align-items-center justify-content-center shadow-sm" 
                            style="background-color: #2a9d8f; border: none; transition: all 0.3s;"
                            @if(!$isFriday || $hasRequestedThisWeek || $sisaGajiHarian <= 0) disabled @endif
                            data-bs-toggle="modal" 
                            data-bs-target="#modalSimpanGaji">
                        <i class="fas fa-piggy-bank me-3" style="font-size: 1.3rem;"></i>
                        Simpan Seluruh Gaji
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- History Tabs -->
    <div class="row g-4">
        <!-- Riwayat Pengambilan -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0" style="font-family: 'Outfit', sans-serif;">Riwayat Pengambilan Gaji</h5>
                    <i class="fas fa-history text-muted"></i>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="table-responsive mt-3">
                        <table class="table table-hover align-middle custom-table-barista">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-3">Tanggal</th>
                                    <th>Nominal</th>
                                    <th>Status</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($riwayatPengambilan as $rp)
                                <tr>
                                    <td class="py-3 text-muted" style="font-size: 0.85rem;">{{ \Carbon\Carbon::parse($rp->TANGGAL_PENGAMBILAN)->format('d/m/Y') }}</td>
                                    <td class="fw-bold text-dark">Rp {{ number_format($rp->NOMINAL, 0, ',', '.') }}</td>
                                    <td>
                                        @if($rp->STATUS === 'menunggu')
                                            <span class="badge bg-warning text-dark px-2.5 py-1.5 rounded-pill" style="font-size: 0.75rem;">Menunggu</span>
                                        @elseif($rp->STATUS === 'disetujui')
                                            <span class="badge bg-success text-white px-2.5 py-1.5 rounded-pill" style="font-size: 0.75rem;">Disetujui</span>
                                        @else
                                            <span class="badge bg-danger text-white px-2.5 py-1.5 rounded-pill" style="font-size: 0.75rem;">Ditolak</span>
                                        @endif
                                    </td>
                                    <td class="text-muted small">{{ $rp->CATATAN_ADMIN ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="fas fa-info-circle me-2"></i> Belum ada riwayat pengambilan
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Riwayat Penyimpanan -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0" style="font-family: 'Outfit', sans-serif;">Riwayat Penyimpanan Gaji</h5>
                    <i class="fas fa-piggy-bank text-muted"></i>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="table-responsive mt-3">
                        <table class="table table-hover align-middle custom-table-barista">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-3">Tanggal</th>
                                    <th>Nominal</th>
                                    <th>Status</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($riwayatPenyimpanan as $rp)
                                <tr>
                                    <td class="py-3 text-muted" style="font-size: 0.85rem;">{{ \Carbon\Carbon::parse($rp->TANGGAL_PENYIMPANAN)->format('d/m/Y') }}</td>
                                    <td class="fw-bold text-dark">Rp {{ number_format($rp->NOMINAL, 0, ',', '.') }}</td>
                                    <td>
                                        @if($rp->STATUS === 'menunggu')
                                            <span class="badge bg-warning text-dark px-2.5 py-1.5 rounded-pill" style="font-size: 0.75rem;">Menunggu</span>
                                        @elseif($rp->STATUS === 'disetujui')
                                            <span class="badge bg-success text-white px-2.5 py-1.5 rounded-pill" style="font-size: 0.75rem;">Disetujui</span>
                                        @else
                                            <span class="badge bg-danger text-white px-2.5 py-1.5 rounded-pill" style="font-size: 0.75rem;">Ditolak</span>
                                        @endif
                                    </td>
                                    <td class="text-muted small">{{ $rp->CATATAN_ADMIN ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="fas fa-info-circle me-2"></i> Belum ada riwayat penyimpanan
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL AMBIL GAJI -->
<div class="modal fade" id="modalAmbilGaji" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('barista.gaji.storePengambilan') }}" method="POST" class="modal-content border-0 rounded-4 shadow">
            @csrf
            <div class="modal-header text-white border-0 py-3" style="background-color: #003d2e;">
                <h5 class="modal-title fw-bold" id="modalAmbilGajiLabel">
                    <i class="fas fa-hand-holding-usd me-2"></i> Formulir Ambil Gaji
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3 p-3 bg-light rounded-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted small">Maksimum Dapat Diambil:</span>
                        <strong class="text-success">Rp {{ number_format($sisaGaji, 0, ',', '.') }}</strong>
                    </div>
                    <small class="text-muted d-block" style="font-size: 0.75rem; line-height: 1.3;">
                        *Jika nominal yang ditarik kurang dari total gaji, sisanya otomatis dimasukkan ke tabungan Anda setelah dikonfirmasi oleh Admin.
                    </small>
                </div>

                <div class="mb-3">
                    <label for="nominal" class="form-label fw-bold text-dark">Nominal Pengambilan (Rp)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 fw-bold">Rp</span>
                        <input type="number" 
                               id="nominal" 
                               name="NOMINAL" 
                               class="form-control border-start-0" 
                               placeholder="Masukkan nominal (Kelipatan 1000)" 
                               min="1000" 
                               max="{{ $sisaGaji }}" 
                               step="1000" 
                               required>
                    </div>
                    <div class="form-text text-muted small mt-2">
                        Minimum penarikan: Rp 1.000
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light p-3 rounded-bottom-4">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn text-white px-4 fw-bold" style="background-color: #003d2e;">Kirim Pengajuan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL SIMPAN GAJI -->
<div class="modal fade" id="modalSimpanGaji" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('barista.gaji.storePenyimpanan') }}" method="POST" class="modal-content border-0 rounded-4 shadow">
            @csrf
            <div class="modal-header text-white border-0 py-3" style="background-color: #2a9d8f;">
                <h5 class="modal-title fw-bold" id="modalSimpanGajiLabel">
                    <i class="fas fa-piggy-bank me-2"></i> Konfirmasi Simpan Gaji
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3 text-success">
                    <i class="fas fa-piggy-bank fa-3x"></i>
                </div>
                <h5 class="fw-bold mb-2">Simpan Seluruh Sisa Gaji?</h5>
                <p class="text-muted small px-3">
                    Anda akan mengajukan penyimpanan seluruh sisa gaji Anda sebesar:
                </p>
                <div class="badge bg-success-light text-success fw-bold py-2.5 px-4 mb-3" style="font-size: 1.3rem; border-radius: 10px; background-color: #e8f5e9;">
                    Rp {{ number_format($sisaGajiHarian, 0, ',', '.') }}
                </div>
                <p class="text-muted small px-3">
                    Seluruh nominal di atas akan langsung ditransfer ke tabungan tersimpan Anda setelah disetujui oleh admin.
                </p>
            </div>
            <div class="modal-footer border-0 bg-light p-3 rounded-bottom-4 justify-content-center">
                <button type="button" class="btn btn-secondary px-4 mx-2" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn text-white px-4 fw-bold mx-2" style="background-color: #2a9d8f;">Ya, Simpan Semua</button>
            </div>
        </form>
    </div>
</div>

<style>
    .custom-table-barista th {
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .custom-table-barista td {
        font-size: 0.9rem;
    }
    .btn-lg:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.15);
    }
</style>
@endsection
