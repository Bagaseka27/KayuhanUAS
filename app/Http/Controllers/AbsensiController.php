<?php

namespace App\Http\Controllers;

use App\Models\AbsenDatang;
use App\Models\AbsenPulang;
use App\Models\Cabang;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class AbsensiController extends Controller
{
    // TAMPILAN DASHBOARD (AbsensiController@index)
    public function index()
    {
        $user = Auth::user(); 
        $today = Carbon::today()->toDateString();
        
        // AMBIL JADWAL HARI INI BERDASARKAN EMAIL KARYAWAN
        $rawSchedules = Jadwal::where('EMAIL', $user->email) 
                             ->where('TANGGAL', $today)
                             ->with(['karyawan', 'cabang']) 
                             ->get();
        
        // Format data View (Dashboard)
        $schedules = $rawSchedules->map(function ($schedule) {
            
            // Format waktu menjadi HH:MM
            $jamMulai = substr($schedule->JAM_MULAI, 0, 5);
            $jamSelesai = substr($schedule->JAM_SELESAI, 0, 5);
            
            return [
                'tanggal' => Carbon::parse($schedule->TANGGAL)->translatedFormat('Y-m-d'),
                'nama' => $schedule->karyawan->NAMA ?? 'Karyawan Tidak Ditemukan',
                'lokasi' => $schedule->cabang->NAMA_LOKASI ?? 'Lokasi Tidak Dikenal', 
                'jam' => $jamMulai . ' - ' . $jamSelesai,
            ];
        });

        return view('pages.dashboard', [
            'schedules' => $schedules, 
        ]);
    }

    // TAMPILAN ABSENSI (Index Datang/Pulang)
    public function indexDatang()
    {
        $user = Auth::user(); 
        $today = Carbon::today()->toDateString();
        
        // Ambil data karyawan relasi Jadwal hari ini
        $karyawan = Karyawan::where('EMAIL', $user->email)
                            ->with(['jadwal' => function($query) use ($today) {
                                $query->where('TANGGAL', $today)->with('cabang');
                            }])
                            ->first();
        
        // Cek apakah ada jadwal hari ini
        $jadwalHariIni = $karyawan->jadwal->first(); 
        
        // Cek Jadwal Shift
        $jadwal = null;
        if ($jadwalHariIni) {
            $jadwal = (object)[
                'jam_masuk' => substr($jadwalHariIni->JAM_MULAI, 0, 5),
                'jam_pulang' => substr($jadwalHariIni->JAM_SELESAI, 0, 5),
                'lokasi_nama' => $jadwalHariIni->cabang->NAMA_LOKASI ?? 'Tidak Ditemukan',
                'id_cabang' => $jadwalHariIni->ID_CABANG 
            ];
        }
        
        // Ambil data absensi untuk Status HARI INI
        $absenDatangHariIni = AbsenDatang::where('EMAIL', $karyawan->EMAIL)
                                        ->whereDate('DATETIME_DATANG', $today)
                                        ->first(); 
        $absenPulangHariIni = AbsenPulang::where('EMAIL', $karyawan->EMAIL)
                                        ->whereDate('DATETIME_PULANG', $today)
                                        ->first();
        $riwayatAbsensi = AbsenDatang::where('EMAIL', $karyawan->EMAIL)
                                    ->orderBy('DATETIME_DATANG', 'desc')
                                    ->get();

        // KEMBALIKAN VIEW DENGAN DATA
        return view('pages.absensi', [
            'riwayatAbsensi' => $riwayatAbsensi,
            'absen_datang' => $absenDatangHariIni,
            'absen_pulang' => $absenPulangHariIni,
            'jadwal' => $jadwal, 
            'karyawan' => $karyawan
        ]);
    }

    // LOGIKA ABSEN MASUK (STORE DATANG) 
    public function storeDatang(Request $request)
    {
        $email = $request->input('EMAIL');
        $dateTimeDatang = Carbon::parse($request->input('DATETIME_DATANG')); 
        $today = $dateTimeDatang->toDateString(); 
        $fotoFile = $request->input('FOTO_FILE'); 
        $jadwalHariIni = Jadwal::where('EMAIL', $email)
                            ->where('TANGGAL', $today)
                            ->first();
        
        if (!$jadwalHariIni) {
            return redirect()->route('barista.absensi.index')->with('error', 'Absensi Gagal! Anda tidak memiliki Jadwal pada tanggal ' . $today . '.');
        }

        // PEMBATASAN: Cek apakah sudah Absen Masuk
        $sudahAbsen = AbsenDatang::where('EMAIL', $email)
                                 ->whereDate('DATETIME_DATANG', $today)
                                 ->exists();
        if ($sudahAbsen) {
            return redirect()->route('barista.absensi.index')->with('error', 'Anda sudah Absen Masuk hari ini.');
        }

        // Lakukan Penyimpanan Absen Datang
        try {
            AbsenDatang::create([
                'EMAIL' => $email,
                'DATETIME_DATANG' => $dateTimeDatang,
                'TANGGAL' => $today,
                'ID_CABANG' => $jadwalHariIni->ID_CABANG, 
                'FOTO_FILE' => $fotoFile, 
            ]);
        } catch (\Exception $e) {
            \Log::error('Gagal menyimpan Absen Datang: ' . $e->getMessage());
            return redirect()->route('barista.absensi.index')->with('error', 'Terjadi kesalahan saat menyimpan data absensi.');
        }
        
        return redirect()->route('barista.absensi.index')->with('success', 'Absen Masuk berhasil dicatat.');
    }

    // LOGIKA ABSEN PULANG (STORE PULANG)
    public function storePulang(Request $request)
    {
        $email = $request->input('EMAIL');
        $dateTimePulang = Carbon::parse($request->input('DATETIME_PULANG'));
        $today = $dateTimePulang->toDateString();
        $fotoFile = $request->input('FOTO_FILE'); 

        // PEMBATASAN: Cek Jadwal Hari Ini
        $jadwalHariIni = Jadwal::where('EMAIL', $email)
                            ->where('TANGGAL', $today)
                            ->first();
        
        if (!$jadwalHariIni) {
            return redirect()->route('barista.absensi.index')->with('error', 'Absensi Pulang Gagal! Anda tidak memiliki Jadwal pada tanggal ' . $today . '.');
        }

        // PEMBATASAN: Cek apakah sudah Absen Pulang hari ini
        $sudahAbsenPulang = AbsenPulang::where('EMAIL', $email)
                                     ->whereDate('DATETIME_PULANG', $today)
                                     ->exists();
        if ($sudahAbsenPulang) {
            return redirect()->route('barista.absensi.index')->with('error', 'Anda sudah Absen Pulang hari ini.');
        }

        // PEMBATASAN: Pastikan sudah Absen Datang
        $absenDatang = AbsenDatang::where('EMAIL', $email)
                                     ->whereDate('DATETIME_DATANG', $today)
                                     ->first(); 
        if (!$absenDatang) {
            return redirect()->route('barista.absensi.index')->with('error', 'Absen Pulang Gagal! Anda belum Absen Masuk hari ini.');
        }

        // Lakukan Penyimpanan Absen Pulang 
        try {
            AbsenPulang::create([
                'EMAIL' => $email,
                'DATETIME_PULANG' => $dateTimePulang,
                'TANGGAL' => $today,
                'ID_CABANG' => $jadwalHariIni->ID_CABANG, 
                'FOTO_FILE' => $fotoFile, 
            ]);
        } catch (\Exception $e) {
            \Log::error('Gagal menyimpan Absen Pulang: ' . $e->getMessage());
            return redirect()->route('barista.absensi.index')->with('error', 'Terjadi kesalahan saat menyimpan data absensi pulang.');
        }


        return redirect()->route('barista.absensi.index')->with('success', 'Absen Pulang berhasil dicatat.');
    }

    // MONITORING ABSENSI (Index Pulang/Monitoring)
    public function indexPulang(Request $request) 
    {
        // filter dari request atau default hari ini
        $tanggalFilter = $request->input('tanggal', Carbon::today()->toDateString());
        $lokasiFilterId = $request->input('lokasi'); 

        // DATA LOKASI FILTER (DIAMBIL DARI DATABASE UNTUK DROPDOWN)
        $lokasiFilter = Cabang::select('ID_CABANG as id', 'NAMA_LOKASI as nama')
                             ->get()
                             ->map(function ($cabang) {
                                return [
                                    'id' => $cabang->id,
                                    'nama' => $cabang->nama,
                                ];
                            });        
        $karyawanList = Karyawan::query();

        // Filter Lokasi pada Karyawan Query
        if ($lokasiFilterId) {
            if (substr($lokasiFilterId, 0, 1) === 'C') {
                $karyawanList->where('ID_CABANG', $lokasiFilterId);
            } elseif (substr($lokasiFilterId, 0, 1) === 'R') {
                $karyawanList->where('ID_ROMBONG', $lokasiFilterId);
            }
        }
        
        // Relasi Absensi dan Cabang/Rombong
        $karyawanList = $karyawanList
            ->with([
                'absenDatang' => function ($query) use ($tanggalFilter) {
                    $query->whereDate('DATETIME_DATANG', $tanggalFilter);
                },
                'absenPulang' => function ($query) use ($tanggalFilter) {
                    $query->whereDate('DATETIME_PULANG', $tanggalFilter);
                },
                'cabang', 
                'rombong'
            ])
            ->get();

        // KEMBALIKAN VIEW
        return view('pages.absensi_monitoring', compact('karyawanList', 'lokasiFilter', 'tanggalFilter'));
    }
}