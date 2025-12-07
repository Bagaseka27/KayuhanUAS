@extends('layouts.app_barista') 

@section('title', 'Presensi Karyawan')

@section('content')
<div class="container-fluid">
    
    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Presensi Karyawan</h1>
    </div>

    {{-- Kartu Jadwal Shift --}}
    <div class="card shadow mb-4 border-left-info">
        <div class="card-body">
            <h6 class="font-weight-bold text-info">Jadwal Shift Anda Hari Ini:</h6>
            <p class="mb-0">
                Jam Kerja: 
                <strong>{{ $jadwal->jam_masuk ?? 'Tidak ada jadwal' }}</strong> 
                @if(isset($jadwal->jam_masuk)) sampai @endif 
                <strong>{{ $jadwal->jam_pulang ?? '' }}</strong>
            </p>
            @if(isset($jadwal->lokasi_nama))
            <p class="text-muted small mb-0">Lokasi: {{ $jadwal->lokasi_nama }}</p>
            @endif
        </div>
    </div>

    {{-- Kartu Status Absensi --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Status Absensi</h6>
        </div>
        <div class="card-body">
            <div class="alert alert-warning text-center">
                Perhatian: Pastikan Anda memilih tombol yang benar. Absensi hanya tercatat sekali per kategori.
            </div>

            <div class="row justify-content-center mt-3">
                
                {{-- Tombol Absen Masuk --}}
                <div class="col-md-4 text-center mb-3">
                    {{-- Tombol akan dinonaktifkan jika sudah Absen Datang --}}
                    <button type="button" class="btn btn-success btn-lg w-100" 
                            @if($absen_datang) disabled @endif 
                            data-bs-toggle="modal" data-bs-target="#absenDatangModal">
                        <i class="fas fa-sign-in-alt"></i> Absen Masuk
                    </button>
                </div>

                {{-- Tombol Absen Pulang --}}
                <div class="col-md-4 text-center mb-3">
                    {{-- Tombol akan dinonaktifkan jika belum Absen Datang atau sudah Absen Pulang --}}
                    <button type="button" class="btn btn-warning btn-lg w-100" 
                            @if(!$absen_datang || $absen_pulang) disabled @endif 
                            data-bs-toggle="modal" data-bs-target="#absenPulangModal">
                        <i class="fas fa-sign-out-alt"></i> Absen Pulang
                    </button>
                </div>
            </div>
            
            <hr>
            
            <p>Status Absensi Terakhir Hari Ini:</p>
            <ul>
                {{-- PERBAIKAN: Menggunakan Carbon untuk menampilkan waktu dengan format yang benar --}}
                <li>
                    Absen Datang: 
                    <strong class="{{ $absen_datang ? 'text-success' : 'text-danger' }}">
                        {{ $absen_datang ? \Carbon\Carbon::parse($absen_datang->DATETIME_DATANG)->format('H:i:s') : 'Belum ada data' }}
                    </strong>
                </li>
                <li>
                    Absen Pulang: 
                    <strong class="{{ $absen_pulang ? 'text-success' : 'text-danger' }}">
                        {{ $absen_pulang ? \Carbon\Carbon::parse($absen_pulang->DATETIME_PULANG)->format('H:i:s') : 'Belum ada data' }}
                    </strong>
                </li>
            </ul>

        </div>
    </div>
    
</div>

{{-- Memanggil Modal --}}
@include('pages.absensi_modal', ['type' => 'Datang']) 
@include('pages.absensi_modal', ['type' => 'Pulang'])

@endsection

@push('scripts')
<script>
    function updateDateTimeInput() {
        const now = new Date();
        const formattedDateTime = now.getFullYear() + '-' + 
            String(now.getMonth() + 1).padStart(2, '0') + '-' + 
            String(now.getDate()).padStart(2, '0') + ' ' + 
            String(now.getHours()).padStart(2, '0') + ':' + 
            String(now.getMinutes()).padStart(2, '0') + ':' + 
            String(now.getSeconds()).padStart(2, '0');

        // input DATETIME_DATANG di modal absen datang dan pulang
        document.getElementById('DATETIME_DATANG').value = formattedDateTime;
        document.getElementById('DATETIME_PULANG').value = formattedDateTime;
    }

    document.addEventListener('DOMContentLoaded', updateDateTimeInput);
    setInterval(updateDateTimeInput, 1000);
</script>
@endpush