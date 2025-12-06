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
    // =========================================================
    // 🟢 BAGIAN 1: TAMPILAN DASHBOARD (AbsensiController@index)
    // =========================================================
    public function index()
    {
        // 1. Ambil data user yang sedang login
        $user = Auth::user(); 
        $today = Carbon::today()->toDateString();
        
        // 2. AMBIL JADWAL HARI INI BERDASARKAN EMAIL KARYAWAN
        //    PASTIKAN QUERY INI BENAR-BENAR HANYA MENGAMBIL JADWAL PENGGUNA YANG LOGIN HARI INI
        $rawSchedules = Jadwal::where('EMAIL', $user->email) // 👈 FILTER UTAMA: Berdasarkan email pengguna
                             ->where('TANGGAL', $today)
                             ->with(['karyawan', 'cabang']) 
                             ->get();
        
        // 3. Format data agar sesuai dengan kebutuhan View (Dashboard)
        $schedules = $rawSchedules->map(function ($schedule) {
            
            // Format waktu menjadi HH:MM
            $jamMulai = substr($schedule->JAM_MULAI, 0, 5);
            $jamSelesai = substr($schedule->JAM_SELESAI, 0, 5);
            
            // Variabel $schedules ini yang di-loop di view dashboard Anda
            return [
                'tanggal' => Carbon::parse($schedule->TANGGAL)->translatedFormat('Y-m-d'),
                'nama' => $schedule->karyawan->NAMA ?? 'Karyawan Tidak Ditemukan',
                'lokasi' => $schedule->cabang->NAMA_LOKASI ?? 'Lokasi Tidak Dikenal', 
                'jam' => $jamMulai . ' - ' . $jamSelesai,
            ];
        });

        // Catatan: Jika Dashboard Anda memiliki KPI Penjualan, Anda harus ambil datanya di sini
        // Misalnya: $penjualan_shift_ini = ... ; $total_items_terjual = ... ;

        // 4. Kirim data ke View Dashboard Anda
        return view('pages.dashboard', [
            'schedules' => $schedules, // Variabel ini yang harus konsisten!
            // 'penjualan_shift_ini' => 0, // Tambahkan variabel KPI jika dibutuhkan
            // 'total_items_terjual' => 0,
        ]);
    }

    // =========================================================
    // 🟢 BAGIAN 2: TAMPILAN ABSENSI (Index Datang/Pulang)
    // =========================================================
    public function indexDatang()
    {
        $user = Auth::user(); 
        $today = Carbon::today()->toDateString();
        
        // 1. Ambil data karyawan lengkap dengan relasi Jadwal hari ini
        $karyawan = Karyawan::where('EMAIL', $user->email)
                            ->with(['jadwal' => function($query) use ($today) {
                                $query->where('TANGGAL', $today)->with('cabang');
                            }])
                            ->first();
        
        // Cek apakah ada jadwal hari ini
        $jadwalHariIni = $karyawan->jadwal->first(); 
        
        // 2. Tentukan jadwal shift (jika ada)
        $jadwal = null;
        if ($jadwalHariIni) {
            $jadwal = (object)[
                'jam_masuk' => substr($jadwalHariIni->JAM_MULAI, 0, 5),
                'jam_pulang' => substr($jadwalHariIni->JAM_SELESAI, 0, 5),
                'lokasi_nama' => $jadwalHariIni->cabang->NAMA_LOKASI ?? 'Tidak Ditemukan',
                'id_cabang' => $jadwalHariIni->ID_CABANG // Tambahkan ID Cabang untuk keperluan store
            ];
        }
        
        // 3. Ambil data absensi untuk Status HARI INI
        $absenDatangHariIni = AbsenDatang::where('EMAIL', $karyawan->EMAIL)
                                        ->whereDate('DATETIME_DATANG', $today)
                                        ->first(); 
        $absenPulangHariIni = AbsenPulang::where('EMAIL', $karyawan->EMAIL)
                                        ->whereDate('DATETIME_PULANG', $today)
                                        ->first();
        
        // Ambil riwayat (opsional, untuk tampilan list)
        $riwayatAbsensi = AbsenDatang::where('EMAIL', $karyawan->EMAIL)
                                    ->orderBy('DATETIME_DATANG', 'desc')
                                    ->get();

        // 🟢 KEMBALIKAN VIEW DENGAN DATA
        return view('pages.absensi', [
            'riwayatAbsensi' => $riwayatAbsensi,
            'absen_datang' => $absenDatangHariIni,
            'absen_pulang' => $absenPulangHariIni,
            'jadwal' => $jadwal, 
            'karyawan' => $karyawan
        ]);
    }

    // =========================================================
    // 🛑 BAGIAN 3: LOGIKA ABSEN MASUK (STORE DATANG) - Diperbaiki
    // =========================================================
    public function storeDatang(Request $request)
    {
        $email = $request->input('EMAIL');
        $dateTimeDatang = Carbon::parse($request->input('DATETIME_DATANG')); // Waktu absen saat ini
        $today = $dateTimeDatang->toDateString(); // Tanggal absen
        $fotoFile = $request->input('FOTO_FILE'); // Asumsi ini berisi path/nama file foto

        // 🛑 1. PEMBATASAN: Cek Jadwal Hari Ini
        $jadwalHariIni = Jadwal::where('EMAIL', $email)
                            ->where('TANGGAL', $today)
                            ->first();
        
        if (!$jadwalHariIni) {
            return redirect()->route('barista.absensi.index')->with('error', 'Absensi Gagal! Anda tidak memiliki Jadwal pada tanggal ' . $today . '.');
        }

        // 🛑 2. PEMBATASAN: Cek apakah sudah Absen Masuk
        $sudahAbsen = AbsenDatang::where('EMAIL', $email)
                                 ->whereDate('DATETIME_DATANG', $today)
                                 ->exists();
        if ($sudahAbsen) {
            return redirect()->route('barista.absensi.index')->with('error', 'Anda sudah Absen Masuk hari ini.');
        }

        // ✅ 3. Lakukan Penyimpanan Absen Datang (LOGIKA HILANG, KINI DITAMBAHKAN)
        try {
            AbsenDatang::create([
                'EMAIL' => $email,
                'DATETIME_DATANG' => $dateTimeDatang,
                'TANGGAL' => $today,
                'ID_CABANG' => $jadwalHariIni->ID_CABANG, // Ambil dari data Jadwal yang sudah dicek
                'FOTO_FILE' => $fotoFile, // Simpan nama/path file foto
                // Tambahkan field lain sesuai kebutuhan tabel AbsenDatang Anda
            ]);
        } catch (\Exception $e) {
            // Log error jika penyimpanan gagal
            \Log::error('Gagal menyimpan Absen Datang: ' . $e->getMessage());
            return redirect()->route('barista.absensi.index')->with('error', 'Terjadi kesalahan saat menyimpan data absensi.');
        }
        
        return redirect()->route('barista.absensi.index')->with('success', 'Absen Masuk berhasil dicatat.');
    }

    // =========================================================
    // 🛑 BAGIAN 4: LOGIKA ABSEN PULANG (STORE PULANG) - Diperbaiki
    // =========================================================
    public function storePulang(Request $request)
    {
        $email = $request->input('EMAIL');
        $dateTimePulang = Carbon::parse($request->input('DATETIME_PULANG'));
        $today = $dateTimePulang->toDateString();
        $fotoFile = $request->input('FOTO_FILE'); // Asumsi ini berisi path/nama file foto

        // 🛑 1. PEMBATASAN: Cek Jadwal Hari Ini
        $jadwalHariIni = Jadwal::where('EMAIL', $email)
                            ->where('TANGGAL', $today)
                            ->first();
        
        if (!$jadwalHariIni) {
            return redirect()->route('barista.absensi.index')->with('error', 'Absensi Pulang Gagal! Anda tidak memiliki Jadwal pada tanggal ' . $today . '.');
        }

        // 🛑 2. PEMBATASAN: Cek apakah sudah Absen Pulang hari ini
        $sudahAbsenPulang = AbsenPulang::where('EMAIL', $email)
                                     ->whereDate('DATETIME_PULANG', $today)
                                     ->exists();
        if ($sudahAbsenPulang) {
            return redirect()->route('barista.absensi.index')->with('error', 'Anda sudah Absen Pulang hari ini.');
        }

        // 🛑 3. PEMBATASAN: Pastikan sudah Absen Datang
        $absenDatang = AbsenDatang::where('EMAIL', $email)
                                     ->whereDate('DATETIME_DATANG', $today)
                                     ->first(); // Ambil record-nya untuk menghitung durasi
        if (!$absenDatang) {
            // Ini akan memicu pesan error yang Anda lihat di screenshot
            return redirect()->route('barista.absensi.index')->with('error', 'Absen Pulang Gagal! Anda belum Absen Masuk hari ini.');
        }

        // ✅ 4. Lakukan Penyimpanan Absen Pulang (LOGIKA HILANG, KINI DITAMBAHKAN)
        try {
            AbsenPulang::create([
                'EMAIL' => $email,
                'DATETIME_PULANG' => $dateTimePulang,
                'TANGGAL' => $today,
                'ID_CABANG' => $jadwalHariIni->ID_CABANG, // Ambil dari data Jadwal
                'FOTO_FILE' => $fotoFile, // Simpan nama/path file foto
                // Anda bisa menambahkan logika hitung durasi kerja di sini
            ]);
        } catch (\Exception $e) {
            \Log::error('Gagal menyimpan Absen Pulang: ' . $e->getMessage());
            return redirect()->route('barista.absensi.index')->with('error', 'Terjadi kesalahan saat menyimpan data absensi pulang.');
        }


        return redirect()->route('barista.absensi.index')->with('success', 'Absen Pulang berhasil dicatat.');
    }

    // =========================================================
    // 🟢 BAGIAN 5: MONITORING ABSENSI (Index Pulang/Monitoring)
    // =========================================================
    public function indexPulang(Request $request) 
    {
        // 1. Dapatkan filter dari request atau default hari ini
        $tanggalFilter = $request->input('tanggal', Carbon::today()->toDateString());
        $lokasiFilterId = $request->input('lokasi'); 

        // 2. DATA LOKASI FILTER (DIAMBIL DARI DATABASE UNTUK DROPDOWN)
        $lokasiFilter = Cabang::select('ID_CABANG as id', 'NAMA_LOKASI as nama')
                             ->get()
                             ->map(function ($cabang) {
                                return [
                                    'id' => $cabang->id,
                                    'nama' => $cabang->nama,
                                ];
                            });
        
        // 3. DATA KARYAWAN LIST (QUERY)
        $karyawanList = Karyawan::query();

        // 4. Terapkan Filter Lokasi pada Karyawan Query
        if ($lokasiFilterId) {
            if (substr($lokasiFilterId, 0, 1) === 'C') {
                $karyawanList->where('ID_CABANG', $lokasiFilterId);
            } elseif (substr($lokasiFilterId, 0, 1) === 'R') {
                $karyawanList->where('ID_ROMBONG', $lokasiFilterId);
            }
        }
        
        // 5. Muat Relasi Absensi dan Cabang/Rombong
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

        // 🟢 KEMBALIKAN VIEW
        return view('pages.absensi_monitoring', compact('karyawanList', 'lokasiFilter', 'tanggalFilter'));
    }
}