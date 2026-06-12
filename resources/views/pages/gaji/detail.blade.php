@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-3">Detail Gaji Harian</h2>
            <h5 class="text-muted">{{ $karyawan->NAMA }} ({{ $karyawan->EMAIL }})</h5>
            <p class="text-muted">Periode: {{ $periode }}</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ url('/employee#tab-gaji') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Summary Card -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Jam Kerja</h6>
                    <h3>{{ number_format($gajiHarian->sum('JAM_KERJA_TERJADWAL'), 2) }} jam</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Gaji Pokok</h6>
                    <h3>Rp {{ number_format($gajiHarian->sum('GAJI_POKOK_HARIAN'), 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Bonus</h6>
                    <h3>Rp {{ number_format($gajiHarian->sum('BONUS_HARIAN'), 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h6 class="card-title">Total Gaji Akhir</h6>
                    <h3>Rp {{ number_format($gajiHarian->sum('TOTAL_GAJI_HARIAN'), 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Detail Harian -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-3">Breakdown Gaji Per Hari</h5>
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead class="table-dark">
                        <tr>
                            <th>Tanggal</th>
                            <th>Jadwal</th>
                            <th>Jam Kerja</th>
                            <th>Gaji Pokok</th>
                            <th>Penjualan (Cup)</th>
                            <th>Bonus</th>
                            <th>Terlambat</th>
                            <th>Potongan</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($gajiHarian as $gh)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($gh->TANGGAL)->format('d/m/Y') }}</td>
                            <td>
                                <small>{{ $gh->JAM_MULAI_JADwal }} - {{ $gh->JAM_SELESAI_JADWAL }}</small>
                            </td>
                            <td>{{ number_format($gh->JAM_KERJA_TERJADWAL, 2) }} jam</td>
                            <td>Rp {{ number_format($gh->GAJI_POKOK_HARIAN, 0, ',', '.') }}</td>
                            <td>{{ $gh->PENJUALAN_CUP }} cup</td>
                            <td>
                                @if($gh->CUP_BONUS > 0)
                                    <span class="text-success">+{{ $gh->CUP_BONUS }} cup</span>
                                    <br><small>Rp {{ number_format($gh->BONUS_HARIAN, 0, ',', '.') }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($gh->MENIT_TERLAMBAT > 0)
                                    <span class="text-danger">{{ $gh->MENIT_TERLAMBAT }} menit</span>
                                    @if($gh->POTONGAN_50_PCT)
                                        <br><small class="badge bg-danger">50%</small>
                                    @endif
                                @else
                                    <span class="text-success">Tepat Waktu</span>
                                @endif
                            </td>
                            <td class="text-danger">Rp {{ number_format($gh->POTONGAN_TERLAMBAT, 0, ',', '.') }}</td>
                            <td class="fw-bold text-success">Rp {{ number_format($gh->TOTAL_GAJI_HARIAN, 0, ',', '.') }}</td>
                            <td>
                                @if($gh->STATUS_ABSENSI === 'HADIR')
                                    <span class="badge bg-success">Hadir</span>
                                @elseif($gh->STATUS_ABSENSI === 'TERLAMBAT')
                                    <span class="badge bg-warning">Terlambat</span>
                                @elseif($gh->STATUS_ABSENSI === 'TIDAK_HADIR')
                                    <span class="badge bg-danger">Tidak Hadir</span>
                                @elseif($gh->STATUS_ABSENSI === 'SAKIT')
                                    <span class="badge bg-info">Sakit</span>
                                @elseif($gh->STATUS_ABSENSI === 'IZIN')
                                    <span class="badge bg-secondary">Izin</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <i class="fas fa-inbox"></i> Belum ada data gaji harian untuk periode ini
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
