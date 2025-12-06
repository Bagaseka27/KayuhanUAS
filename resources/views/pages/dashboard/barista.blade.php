@extends('layouts.app_barista')

@section('title', 'Dashboard')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-bold text-primary-custom">Dashboard Overview</h2>
            <p class="text-muted m-0">Selamat datang kembali, {{ Auth::user()->name ?? 'Barista Staff' }}!</p>
        </div>
        <div class="text-end">
            {{-- Bagian Waktu Dibuat Dinamis --}}
            <h6 class="fw-bold text-primary-custom mb-0" id="current-date">{{ date('l, d M Y') }}</h6>
            <small class="text-muted">Waktu Server: <span id="current-time"></span></small>
        </div>
    </div>

    <div class="row g-4 mb-5">
        {{-- CARD 1: Penjualan Shift Ini (Dynamic) --}}
        <div class="col-md-6">
            <div class="card-custom">
                <p class="text-muted mb-2">Penjualan Shift Ini</p>
                <h1 class="fw-bold text-primary-custom">
                    Rp {{ number_format($penjualan_shift_ini ?? 0, 0, ',', '.') }}
                </h1>
            </div>
        </div>
        
        {{-- CARD 2: Transaksi Berhasil (Dynamic) --}}
        <div class="col-md-6">
            <div class="card-custom">
                <p class="text-muted mb-2">Item Terjual Hari Ini</p>
                <h1 class="fw-bold text-dark">
                    {{ number_format($total_items_terjual ?? 0, 0, ',', '.') }} Cup
                </h1>
            </div>
        </div>
    </div>

    <div class="card-custom p-0 overflow-hidden">
        <div class="p-4 pb-2">
            <h5 class="fw-bold text-primary-custom">Jadwal Shift Karyawan</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-custom mb-0">
                <thead>
                    <tr>
                        <th>TANGGAL</th>
                        <th>NAMA KARYAWAN</th>
                        <th>LOKASI</th>
                        <th>JAM SHIFT</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- LOOP DATA JADWAL (dari $schedules) --}}
                    @forelse ($schedules ?? [] as $schedule)
                    <tr>
                        <td class="fw-bold">{{ $schedule['tanggal'] }}</td>
                        <td>{{ $schedule['nama'] }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $schedule['lokasi'] }}</span></td>
                        <td>{{ $schedule['jam'] }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">Tidak ada jadwal shift hari ini.</td>
                    </tr>
                    @endforelse
                    
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const timeElement = document.getElementById('current-time');
        
        // Fungsi untuk mendapatkan waktu saat ini dan memformatnya
        function updateTime() {
            const now = new Date();
            // Format waktu menjadi HH:MM:SS
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            
            timeElement.textContent = `${hours}:${minutes}:${seconds}`;
        }

        // Panggil fungsi sekali saat dimuat
        updateTime();
        
        // Perbarui setiap 1 detik (1000 milidetik)
        setInterval(updateTime, 1000);
    });
</script>
@endpush