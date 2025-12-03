@extends('layouts.app_barista') 

@section('content')
<div class="container-fluid">
    @if (session('success'))
        <div class="alert alert-success mt-3">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger mt-3">{{ session('error') }}</div>
    @endif
    
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Presensi Karyawan</h1>
    </div>

    <div class="card shadow mb-4 border-left-info">
        <div class="card-body">
            <h6 class="font-weight-bold text-info">Jadwal Shift Anda Hari Ini:</h6>
            <p class="mb-0">
                Jam Kerja: 
                <strong>{{ $jadwal->jam_masuk ?? '-' }}</strong> sampai 
                <strong>{{ $jadwal->jam_pulang ?? '-' }}</strong>
            </p>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Status Absensi</h6>
        </div>
        <div class="card-body">
            <div class="alert alert-warning text-center">
                Perhatian: Pastikan Anda memilih tombol yang benar. Absensi hanya tercatat sekali per kategori.
            </div>

            <div class="row justify-content-center mt-3">
                
                <div class="col-md-4 text-center mb-3">
                    <button type="button" class="btn btn-success btn-lg w-100" data-bs-toggle="modal" data-bs-target="#absenDatangModal">
                        <i class="fas fa-sign-in-alt"></i> Absen Masuk
                    </button>
                </div>

                <div class="col-md-4 text-center mb-3">
                    <button type="button" class="btn btn-warning btn-lg w-100" data-bs-toggle="modal" data-bs-target="#absenPulangModal">
                        <i class="fas fa-sign-out-alt"></i> Absen Pulang
                    </button>
                </div>
            </div>
            
            <hr>
            
            <p>Status Absensi Terakhir:</p>
            <ul>
                <li>Absen Datang: <strong>{{ $absen_datang->DATETIME_DATANG ?? 'Belum ada data' }}</strong></li>
                <li>Absen Pulang: <strong>{{ $absen_pulang->DATETIME_PULANG ?? 'Belum ada data' }}</strong></li>
            </ul>

        </div>
    </div>
    
    </div>

@include('pages.absensi_modal', ['type' => 'Datang']) 
@include('pages.absensi_modal', ['type' => 'Pulang'])

@endsection