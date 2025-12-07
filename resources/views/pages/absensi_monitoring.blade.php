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
                            <th style="width: 15%;">Nama Karyawan</th>
                            <th style="width: 15%;">Email</th>
                            <th style="width: 10%;">Lokasi</th>
                            <th style="width: 15%;">Waktu Datang</th>
                            <th style="width: 10%;">Foto Datang</th>
                            <th style="width: 15%;">Waktu Pulang</th>
                            <th style="width: 10%;">Foto Pulang</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($karyawanList as $karyawan)
                            <tr>
                                <td>{{ $karyawan->NAMA }}</td>
                                <td>{{ $karyawan->EMAIL }}</td>
                                <td>
                                    {{ $karyawan->cabang->NAMA_LOKASI ?? $karyawan->rombong->NAMA_LOKASI ?? 'Tidak Ada' }}
                                </td>
                                <td>
                                    @if($karyawan->absenDatang)
                                        {{ \Carbon\Carbon::parse($karyawan->absenDatang->DATETIME_DATANG)->format('H:i:s') }}
                                    @else
                                        <span class="badge bg-secondary">Belum Absen</span>
                                    @endif
                                </td>

                                {{-- Tombol Foto Datang --}}
                                <td>
                                    @php
                                        $fotoDatang = $karyawan->absenDatang->FOTO ?? null;
                                        $fotoDatangUrl = 'https://via.placeholder.com/150?text=No+Photo';
                                        $fotoExists = false;
                                        $debugPath = '';
                                        
                                        if ($fotoDatang) {
                                            // Cek beberapa kemungkinan path
                                            $paths = [
                                                ['path' => storage_path('app/public/absensi/datang/' . $fotoDatang), 'url' => asset('storage/absensi/datang/' . $fotoDatang)],
                                                ['path' => public_path('storage/absensi/datang/' . $fotoDatang), 'url' => asset('storage/absensi/datang/' . $fotoDatang)],
                                                ['path' => public_path('absensi/datang/' . $fotoDatang), 'url' => asset('absensi/datang/' . $fotoDatang)],
                                            ];
                                            
                                            foreach ($paths as $pathData) {
                                                if (file_exists($pathData['path'])) {
                                                    $fotoExists = true;
                                                    $fotoDatangUrl = $pathData['url'];
                                                    $debugPath = $pathData['path'];
                                                    break;
                                                }
                                            }
                                            
                                            // Jika file tidak ditemukan, tetap coba tampilkan dengan path standar
                                            if (!$fotoExists) {
                                                $fotoDatangUrl = asset('storage/absensi/datang/' . $fotoDatang);
                                                $debugPath = 'File not found in any checked paths';
                                            }
                                        }
                                    @endphp
                                    
                                    @if($fotoDatang)
                                        <button class="btn btn-primary-custom btn-sm w-100"
                                                data-bs-toggle="modal"
                                                data-bs-target="#fotoModal"
                                                data-foto="{{ $fotoDatangUrl }}"
                                                data-nama="{{ $karyawan->NAMA }}"
                                                data-waktu="Datang"
                                                data-exists="{{ $fotoExists ? 'yes' : 'no' }}">
                                            <i class="fas fa-image"></i> Lihat Foto
                                        </button>
                                        @if(!$fotoExists)
                                            <small class="text-danger d-block mt-1">File tidak ditemukan</small>
                                            <small class="text-muted d-block" style="font-size: 10px;">{{ $debugPath }}</small>
                                        @endif
                                    @else
                                        <span class="badge bg-warning text-dark">Tidak Ada Foto</span>
                                    @endif
                                </td>

                                <td>
                                    @if($karyawan->absenPulang)
                                        {{ \Carbon\Carbon::parse($karyawan->absenPulang->DATETIME_PULANG)->format('H:i:s') }}
                                    @else
                                        <span class="badge bg-secondary">Belum Absen</span>
                                    @endif
                                </td>

                                {{-- Tombol Foto Pulang --}}
                                <td>
                                    @php
                                        $fotoPulang = $karyawan->absenPulang->FOTO ?? null;
                                        $fotoPulangUrl = 'https://via.placeholder.com/150?text=No+Photo';
                                        $fotoPulangExists = false;
                                        $debugPath = '';
                                        
                                        if ($fotoPulang) {
                                            // Cek beberapa kemungkinan path
                                            $paths = [
                                                ['path' => storage_path('app/public/absensi/pulang/' . $fotoPulang), 'url' => asset('storage/absensi/pulang/' . $fotoPulang)],
                                                ['path' => public_path('storage/absensi/pulang/' . $fotoPulang), 'url' => asset('storage/absensi/pulang/' . $fotoPulang)],
                                                ['path' => public_path('absensi/pulang/' . $fotoPulang), 'url' => asset('absensi/pulang/' . $fotoPulang)],
                                            ];
                                            
                                            foreach ($paths as $pathData) {
                                                if (file_exists($pathData['path'])) {
                                                    $fotoPulangExists = true;
                                                    $fotoPulangUrl = $pathData['url'];
                                                    $debugPath = $pathData['path'];
                                                    break;
                                                }
                                            }
                                            
                                            // Jika file tidak ditemukan, tetap coba tampilkan dengan path standar
                                            if (!$fotoPulangExists) {
                                                $fotoPulangUrl = asset('storage/absensi/pulang/' . $fotoPulang);
                                                $debugPath = 'File not found in any checked paths';
                                            }
                                        }
                                    @endphp
                                    
                                    @if($fotoPulang)
                                        <button class="btn btn-info btn-sm w-100"
                                                data-bs-toggle="modal"
                                                data-bs-target="#fotoModal"
                                                data-foto="{{ $fotoPulangUrl }}"
                                                data-nama="{{ $karyawan->NAMA }}"
                                                data-waktu="Pulang"
                                                data-exists="{{ $fotoPulangExists ? 'yes' : 'no' }}">
                                            <i class="fas fa-image"></i> Lihat Foto
                                        </button>
                                        @if(!$fotoPulangExists)
                                            <small class="text-danger d-block mt-1">File tidak ditemukan</small>
                                            <small class="text-muted d-block" style="font-size: 10px;">{{ $debugPath }}</small>
                                        @endif
                                    @else
                                        <span class="badge bg-warning text-dark">Tidak Ada Foto</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center">Tidak ada data absensi yang ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal Foto --}}
    <div class="modal fade" id="fotoModal" tabindex="-1" aria-labelledby="fotoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fotoModalLabel">
                        Foto Absensi <span id="modal-nama-karyawan"></span> - <span id="modal-waktu"></span>
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

    {{-- Script Modal --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var fotoModal = document.getElementById('fotoModal');
            
            fotoModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var fotoUrl = button.getAttribute('data-foto');
                var namaKaryawan = button.getAttribute('data-nama');
                var waktu = button.getAttribute('data-waktu');
                var exists = button.getAttribute('data-exists');
                
                var modalImage = fotoModal.querySelector('#modal-foto-absen');
                var modalNama = fotoModal.querySelector('#modal-nama-karyawan');
                var modalWaktu = fotoModal.querySelector('#modal-waktu');
                var fotoInfo = fotoModal.querySelector('#foto-info');
                
                modalImage.src = fotoUrl;
                modalNama.textContent = namaKaryawan;
                modalWaktu.textContent = waktu;
                
                // Tambahkan info debug
                if (exists === 'no') {
                    fotoInfo.innerHTML = '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> File foto tidak ditemukan di server. Menampilkan placeholder.</div>';
                } else {
                    fotoInfo.innerHTML = '';
                }
                
                // Handle error saat load gambar
                modalImage.onerror = function() {
                    this.src = 'https://via.placeholder.com/400x300?text=Foto+Tidak+Dapat+Dimuat';
                    fotoInfo.innerHTML = '<div class="alert alert-danger"><i class="fas fa-times-circle"></i> Gagal memuat foto dari URL: <br><small>' + fotoUrl + '</small></div>';
                };
            });
        });
    </script>
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
@endsection