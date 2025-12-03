@extends('layouts.app') 

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Monitoring Absensi Karyawan</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Data Absensi</h6>
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
                        <button type="submit" class="btn btn-primary w-100">Tampilkan Data</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Absensi Karyawan (Tanggal: {{ \Carbon\Carbon::parse($tanggalFilter)->translatedFormat('d F Y') }})</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nama Karyawan</th>
                            <th>Email</th>
                            <th>Lokasi (ID)</th> <th>Waktu Datang</th>
                            <th>Foto Datang</th>
                            <th>Waktu Pulang</th>
                            <th>Foto Pulang</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($karyawanList as $karyawan)
                            <tr>
                                <td>{{ $karyawan->nama }}</td>
                                <td>{{ $karyawan->email }}</td>
                                <td>{{ $karyawan->id_cabang ?? $karyawan->id_rombong ?? '-' }}</td> 
                                
                                <td>{{ $karyawan->absenDatang->DATETIME_DATANG ?? '-' }}</td>
                                
                                <td>
                                    @if($karyawan->absenDatang)
                                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#fotoModal" data-foto="{{ asset('storage/absensi/datang/' . $karyawan->absenDatang->FOTO) }}">Lihat Foto</button>
                                    @else
                                        -
                                    @endif
                                </td>
                                
                                <td>{{ $karyawan->absenPulang->DATETIME_PULANG ?? '-' }}</td>
                                
                                <td>
                                    @if($karyawan->absenPulang)
                                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#fotoModal" data-foto="{{ asset('storage/absensi/pulang/' . $karyawan->absenPulang->FOTO) }}">Lihat Foto</button>
                                    @else
                                        -
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

    <div class="modal fade" id="fotoModal" tabindex="-1" aria-labelledby="fotoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fotoModalLabel">Foto Absensi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="" id="modal-foto-absen" class="img-fluid rounded" alt="Foto Absensi Karyawan">
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var fotoModal = document.getElementById('fotoModal');
            fotoModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var fotoUrl = button.getAttribute('data-foto');
                var modalImage = fotoModal.querySelector('#modal-foto-absen');
                modalImage.src = fotoUrl;
            });
        });
    </script>
@endsection