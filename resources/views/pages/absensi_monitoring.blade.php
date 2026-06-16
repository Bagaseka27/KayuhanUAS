@extends('layouts.app') 

@section('content')
<div class="container-fluid">
    <h2 class="fw-bold text-primary-custom mb-4">Monitoring Absensi Karyawan</h2>

    {{-- Filter Data --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-success">Filter Data Absensi</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.absensi.monitoring') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="lokasi_filter" class="form-label">Cabang</label>
                        <select class="form-select" id="lokasi_filter" name="lokasi">
                            <option value="">-- Semua Cabang --</option>
                            @foreach ($lokasiFilter as $l)
                                <option value="{{ $l['id'] }}" {{ request('lokasi') == $l['id'] ? 'selected' : '' }}>
                                    {{ $l['nama'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="tanggal_filter" class="form-label">Tanggal</label>
                        {{-- Memastikan value input date terikat dengan variable $tanggalFilter --}}
                        <input type="date" class="form-control" id="tanggal_filter" name="tanggal" value="{{ $tanggalFilter }}">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary-custom btn-sm w-100">Tampilkan Data</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Data Absensi --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-success">
                Data Absensi Karyawan (Tanggal: {{ \Carbon\Carbon::parse($tanggalFilter)->translatedFormat('d F Y') }})
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="width: 12%;">Nama Karyawan</th>
                            <th style="width: 12%;">Email</th>
                            <th style="width: 10%;">Lokasi</th>
                            <th style="width: 10%;">Status</th>
                            <th style="width: 11%;">Waktu Datang</th>
                            <th style="width: 10%;">Foto Datang</th>
                            <th style="width: 11%;">Waktu Pulang</th>
                            <th style="width: 10%;">Foto Pulang</th>
                            <th style="width: 14%;">Keterangan</th> {{-- Tambah Kolom Keterangan --}}
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($karyawanList as $karyawan)
                            @php
                                $absensi = $karyawan->absensi ?? null;
                            @endphp
                            <tr>
                                <td>{{ $karyawan->NAMA }}</td>
                                <td>{{ $karyawan->EMAIL }}</td>
                                <td>
                                    {{ $karyawan->cabang->NAMA_LOKASI ?? $karyawan->rombong->NAMA_LOKASI ?? 'Tidak Ada' }}
                                </td>

                                {{-- Status --}}
                                <td>
                                    @if($absensi?->isTidakHadir())
                                        <span class="badge bg-warning text-dark">
                                            Tidak Hadir ({{ $absensi->getAlasanLabel() }})
                                        </span>
                                    @elseif($absensi?->STATUS === 'TERLAMBAT')
                                        <span class="badge bg-danger">Terlambat</span>
                                        @if($absensi->KOMPENSASI < 0)
                                            <br><small class="text-danger">-Rp {{ number_format(abs($absensi->KOMPENSASI)) }}</small>
                                        @endif
                                    @elseif($absensi?->STATUS === 'HADIR')
                                        <span class="badge bg-success">Hadir</span>
                                    @else
                                        <span class="badge bg-secondary">Belum Absen</span>
                                    @endif
                                </td>

                                {{-- Waktu Datang --}}
                                <td>
                                    @if($absensi?->isSudahAbsenDatang())
                                        {{ $absensi->DATETIME_DATANG->format('H:i:s') }}
                                        @if($absensi->LOKASI_DATANG)
                                            <br><small class="text-muted">{{ $absensi->LOKASI_DATANG }}</small>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">Belum Absen</span>
                                    @endif
                                </td>

                                {{-- Foto Datang --}}
                                <td>
                                    @if($absensi?->FOTO_DATANG)
                                        <button class="btn btn-primary-custom btn-sm w-100"
                                                onclick="lihatFoto('{{ addslashes($absensi->FOTO_DATANG) }}', '{{ $karyawan->NAMA }}', 'Datang')">
                                            <i class="fas fa-image"></i> Lihat Foto
                                        </button>
                                    @else
                                        <span class="badge bg-warning text-dark">Tidak Ada Foto</span>
                                    @endif
                                </td>

                                {{-- Waktu Pulang --}}
                                <td>
                                    @if($absensi?->isSudahAbsenPulang())
                                        {{ $absensi->DATETIME_PULANG->format('H:i:s') }}
                                        @if($absensi->LOKASI_PULANG)
                                            <br><small class="text-muted">{{ $absensi->LOKASI_PULANG }}</small>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">Belum Absen</span>
                                    @endif
                                </td>

                                {{-- Foto Pulang --}}
                                <td>
                                    @if($absensi?->FOTO_PULANG)
                                        <button class="btn btn-info btn-sm w-100"
                                                onclick="lihatFoto('{{ addslashes($absensi->FOTO_PULANG) }}', '{{ $karyawan->NAMA }}', 'Pulang')">
                                            <i class="fas fa-image"></i> Lihat Foto
                                        </button>
                                    @else
                                        <span class="badge bg-warning text-dark">Tidak Ada Foto</span>
                                    @endif
                                </td>

                                {{-- Kolom Keterangan (Surat Izin) --}}
                                <td>
                                    @if($absensi?->isTidakHadir() && $absensi->SURAT_IZIN)
                                        <button class="btn btn-secondary btn-sm w-100"
                                                onclick="lihatFoto('{{ addslashes($absensi->SURAT_IZIN) }}', '{{ $karyawan->NAMA }}', 'Surat Izin / Sakit')">
                                            <i class="fas fa-file-medical"></i> Lihat Surat
                                        </button>
                                    @elseif($absensi?->isTidakHadir())
                                        <span class="badge bg-light text-dark">Tanpa Surat</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center">Tidak ada data absensi yang ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal Pop-up Foto --}}
    <div class="modal fade" id="fotoModal" tabindex="-1" aria-labelledby="fotoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fotoModalLabel">
                        Dokumen <span id="modal-waktu"></span> - <span id="modal-nama-karyawan"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="" id="modal-foto-absen" class="img-fluid rounded shadow" alt="Foto Absensi Karyawan" style="max-height: 500px;">
                    <div id="foto-info" class="mt-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .table td {
        vertical-align: middle;
    }
    #modal-foto-absen {
        border: 2px solid #dee2e6;
    }
</style>
@endpush

@push('scripts')
<script>
    function lihatFoto(base64, nama, jenisKeterangan) {
        document.getElementById('modal-nama-karyawan').textContent = nama;
        document.getElementById('modal-waktu').textContent = jenisKeterangan;
        
        // Pengecekan jika data string base64 belum memiliki prefix header src img
        if (base64.startsWith('data:image')) {
            document.getElementById('modal-foto-absen').src = base64;
        } else {
            document.getElementById('modal-foto-absen').src = 'data:image/png;base64,' + base64;
        }
        
        document.getElementById('foto-info').innerHTML = '';

        var modal = new bootstrap.Modal(document.getElementById('fotoModal'));
        modal.show();
    }
</script>
@endpush

@endsection