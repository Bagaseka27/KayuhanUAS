<?php

namespace App\Services;

use App\Models\GajiHarian;
use App\Models\Karyawan;
use App\Models\Jadwal;
use App\Models\Absensi;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GajiService
{
    /**
     * Hitung gaji harian untuk karyawan pada tanggal tertentu
     */
    public function hitungGajiHarian($email, $tanggal)
    {
        $karyawan = Karyawan::with('jabatan')->where('EMAIL', $email)->first();
        if (!$karyawan || !$karyawan->jabatan) {
            throw new \Exception('Data karyawan atau jabatan tidak ditemukan');
        }

        $tanggalObj = Carbon::parse($tanggal);
        
        // Get jadwal untuk tanggal tersebut
        $jadwal = Jadwal::where('EMAIL', $email)
            ->whereDate('TANGGAL', $tanggalObj->toDateString())
            ->first();

        if (!$jadwal) {
            // Tidak ada jadwal, tidak ada gaji
            return null;
        }

        // Hitung jam kerja terjadwal dari shift
        $jamKerjaTerjadwal = 0;
        if ($jadwal->JAM_MULAI && $jadwal->JAM_SELESAI) {
            $mulai = Carbon::createFromTimeString($jadwal->JAM_MULAI);
            $selesai = Carbon::createFromTimeString($jadwal->JAM_SELESAI);
            if ($selesai->lessThan($mulai)) {
                $selesai->addDay();
            }
            $jamKerjaTerjadwal = abs($selesai->diffInMinutes($mulai)) / 60;
        }

        // Get absensi untuk tanggal tersebut
        $absensi = Absensi::where('EMAIL', $email)
            ->whereDate('TANGGAL', $tanggalObj->toDateString())
            ->first();

        // Inisialisasi variabel perhitungan
        $menitTerlambat = 0;
        $waktuDatang = null;
        $waktuPulang = null;
        $statusAbsensi = 'TIDAK_HADIR';

        if ($absensi) {
            $waktuDatang = $absensi->DATETIME_DATANG;
            $waktuPulang = $absensi->DATETIME_PULANG;
            
            if ($absensi->STATUS === 'TIDAK_HADIR') {
                $statusAbsensi = $absensi->ALASAN_TIDAK_HADIR ?? 'TIDAK_HADIR';
            } else {
                $statusAbsensi = $absensi->STATUS ?? 'HADIR';
            }

            if ($absensi->DATETIME_DATANG && $jadwal->JAM_MULAI) {
                $jamMulaiJadwal = Carbon::parse($jadwal->TANGGAL . ' ' . $jadwal->JAM_MULAI);
                $jamDatang = Carbon::parse($absensi->DATETIME_DATANG);

                if ($jamDatang->greaterThan($jamMulaiJadwal)) {
                    $menitTerlambat = abs($jamDatang->diffInMinutes($jamMulaiJadwal));
                }
            }
        }

        // Hitung penjualan cup untuk tanggal tersebut
        $penjualanCup = DB::table('transaksi')
            ->join('detailtransaksi', 'transaksi.ID_TRANSAKSI', '=', 'detailtransaksi.ID_TRANSAKSI')
            ->where('transaksi.EMAIL', $email)
            ->whereDate('transaksi.DATETIME', $tanggalObj->toDateString())
            ->sum('detailtransaksi.JML_ITEM');

        $penjualanCup = (int) $penjualanCup;

        // Hitung bonus per cup berdasarkan posisi jabatan
        // senior = 1000, junior = 500, training = 0
        $namaJabatan = strtolower($karyawan->jabatan->NAMA_JABATAN ?? '');
        $bonusPerCup = 0;
        if (str_contains($namaJabatan, 'senior')) {
            $bonusPerCup = 1000;
        } elseif (str_contains($namaJabatan, 'junior')) {
            $bonusPerCup = 500;
        } elseif (str_contains($namaJabatan, 'training')) {
            $bonusPerCup = 0;
        } else {
            // fallback ke database
            $bonusPerCup = $karyawan->jabatan->BONUS_PENJUALAN_PER_CUP ?? 0;
        }

        // Hitung bonus harian (penjualan di atas 50 cup)
        $cupBonus = max(0, $penjualanCup - 50);
        $bonusHarian = $cupBonus * $bonusPerCup;

        // Gaji per jam (5000 IDR)
        $gajiPerJam = 5000;
        $gajiPokokHarian = $jamKerjaTerjadwal * $gajiPerJam;

        // Hitung potongan keterlambatan
        $potonganTerlambat = 0;
        $potongan50Pct = false;

        // Aturan absensi: Jika sakit, izin, atau tidak hadir (termasuk tidak ada record absen masuk), gaji hari itu adalah 0
        if (in_array($statusAbsensi, ['TIDAK_HADIR', 'SAKIT', 'IZIN']) || !$waktuDatang) {
            $gajiPokokHarian = 0;
            $bonusHarian = 0;
            $potonganTerlambat = 0;
            $totalGajiHarian = 0;
            $menitTerlambat = 0;
        } else {
            if ($menitTerlambat > 0) {
                if ($menitTerlambat <= 15) {
                    $potonganTerlambat = $menitTerlambat * 1000;
                } else {
                    // Jika > 15 menit, potong 50% dari total gaji (pokok + bonus)
                    $potonganTerlambat = ($gajiPokokHarian + $bonusHarian) * 0.5;
                    $potongan50Pct = true;
                }
            }
            
            // Total gaji harian
            $totalGajiHarian = $gajiPokokHarian + $bonusHarian - $potonganTerlambat;
        }

        // Update absensi kompensasi di database jika absensi ada
        if ($absensi) {
            $absensi->KOMPENSASI = -$potonganTerlambat;
            // Sinkronkan status absensi jika terlambat
            if ($menitTerlambat > 0 && !in_array($absensi->STATUS, ['TIDAK_HADIR'])) {
                $absensi->STATUS = 'TERLAMBAT';
            }
            $absensi->save();
        }

        // Simpan atau update gaji harian
        $gajiHarian = GajiHarian::updateOrCreate(
            [
                'EMAIL' => $email,
                'TANGGAL' => $tanggalObj->toDateString()
            ],
            [
                'JAM_KERJA_TERJADWAL' => $jamKerjaTerjadwal,
                'JAM_MULAI_JADWAL' => $jadwal->JAM_MULAI,
                'JAM_SELESAI_JADWAL' => $jadwal->JAM_SELESAI,
                'WAKTU_DATANG' => $waktuDatang,
                'WAKTU_PULANG' => $waktuPulang,
                'MENIT_TERLAMBAT' => $menitTerlambat,
                'GAJI_PER_JAM' => $gajiPerJam,
                'GAJI_POKOK_HARIAN' => $gajiPokokHarian,
                'PENJUALAN_CUP' => $penjualanCup,
                'CUP_BONUS' => $cupBonus,
                'BONUS_PER_CUP' => $bonusPerCup,
                'BONUS_HARIAN' => $bonusHarian,
                'POTONGAN_TERLAMBAT' => $potonganTerlambat,
                'POTONGAN_50_PCT' => $potongan50Pct,
                'TOTAL_GAJI_HARIAN' => $totalGajiHarian,
                'STATUS_ABSENSI' => $statusAbsensi
            ]
        );

        return $gajiHarian;
    }

    /**
     * Hitung gaji harian untuk periode tertentu
     */
    public function hitungGajiHarianPeriode($email, $periode)
    {
        $tahun = (int) explode('-', $periode)[0];
        $bulan = (int) explode('-', $periode)[1];
        
        $startDate = Carbon::create($tahun, $bulan, 1)->startOfDay();
        $endDate = Carbon::create($tahun, $bulan, 1)->endOfMonth()->endOfDay();

        // Get semua jadwal dalam periode
        $jadwalList = Jadwal::where('EMAIL', $email)
            ->whereBetween('TANGGAL', [$startDate->toDateString(), $endDate->toDateString()])
            ->get();

        $results = [];
        foreach ($jadwalList as $jadwal) {
            try {
                $gajiHarian = $this->hitungGajiHarian($email, $jadwal->TANGGAL);
                if ($gajiHarian) {
                    $results[] = $gajiHarian;
                }
            } catch (\Exception $e) {
                Log::error("Error menghitung gaji harian untuk {$email} pada {$jadwal->TANGGAL}: {$e->getMessage()}");
            }
        }

        return $results;
    }

    /**
     * Hitung gaji untuk semua karyawan dalam periode
     */
    public function hitungGajiSemuaKaryawan($periode)
    {
        $karyawan = Karyawan::all();
        
        foreach ($karyawan as $k) {
            try {
                $this->hitungGajiHarianPeriode($k->EMAIL, $periode);
            } catch (\Exception $e) {
                Log::error("Error menghitung gaji untuk {$k->EMAIL}: {$e->getMessage()}");
            }
        }
    }

    /**
     * Auto save unclaimed salaries for Friday payroll cycles
     */
    public function autoSaveUnclaimedSalaries()
    {
        $firstGaji = GajiHarian::orderBy('TANGGAL', 'asc')->first();
        $startDate = $firstGaji ? Carbon::parse($firstGaji->TANGGAL)->startOfWeek() : Carbon::now()->subMonths(3)->startOfWeek();
        
        $today = Carbon::today();
        $currentDayOfWeek = $today->dayOfWeek; // 0 = Sunday, 1 = Monday, ..., 5 = Friday, 6 = Saturday
        
        // Friday of this week
        $thisWeeksFriday = Carbon::now()->startOfWeek()->addDays(4);
        
        // We can process the current week's Friday if it is Friday late night (>=23:00) or Saturday/Sunday
        $canProcessThisWeek = ($currentDayOfWeek == Carbon::FRIDAY && Carbon::now()->hour >= 23) || 
                              ($currentDayOfWeek == Carbon::SATURDAY) || 
                              ($currentDayOfWeek == Carbon::SUNDAY);
                              
        $endFriday = $canProcessThisWeek ? $thisWeeksFriday : $thisWeeksFriday->copy()->subWeek();
        
        // Generate all Friday dates from startDate to endFriday
        $targetFridays = [];
        $currentFriday = $startDate->copy()->addDays(4); // Friday of start week
        
        while ($currentFriday->lessThanOrEqualTo($endFriday)) {
            $targetFridays[] = $currentFriday->toDateString();
            $currentFriday->addWeek();
        }
        
        $baristas = Karyawan::where('ROLE', 'Barista')->get();
        
        foreach ($baristas as $barista) {
            $email = $barista->EMAIL;
            
            foreach ($targetFridays as $fridayDate) {
                // Check if they made any request on this Friday date
                $hasPengambilan = \App\Models\GajiPengambilan::where('EMAIL', $email)
                    ->whereDate('created_at', $fridayDate)
                    ->exists();
                    
                $hasPenyimpanan = \App\Models\GajiDisimpan::where('EMAIL', $email)
                    ->whereDate('created_at', $fridayDate)
                    ->exists();
                    
                // Check if already auto-saved for this Friday
                $alreadyAutoSaved = \App\Models\GajiDisimpan::where('EMAIL', $email)
                    ->where('TANGGAL_PENYIMPANAN', $fridayDate)
                    ->where('CATATAN_ADMIN', 'like', 'Gaji otomatis disimpan%')
                    ->exists();
                    
                if (!$hasPengambilan && !$hasPenyimpanan && !$alreadyAutoSaved) {
                    // Calculate sisaGajiHarian as of that Friday
                    $totalGajiHarianUpToFriday = GajiHarian::where('EMAIL', $email)
                        ->where('TANGGAL', '<=', $fridayDate)
                        ->sum('TOTAL_GAJI_HARIAN');
                        
                    $totalPengambilanDisetujui = \App\Models\GajiPengambilan::where('EMAIL', $email)
                        ->where('STATUS', 'disetujui')
                        ->where('TANGGAL_PENGAMBILAN', '<=', $fridayDate)
                        ->sum('NOMINAL');
                        
                    $totalPenyimpananDisetujui = \App\Models\GajiDisimpan::where('EMAIL', $email)
                        ->where('STATUS', 'disetujui')
                        ->where('TANGGAL_PENYIMPANAN', '<=', $fridayDate)
                        ->sum('NOMINAL');
                        
                    $sisaGajiHarianUpToFriday = $totalGajiHarianUpToFriday - $totalPengambilanDisetujui - $totalPenyimpananDisetujui;
                    
                    if ($sisaGajiHarianUpToFriday > 0) {
                        \App\Models\GajiDisimpan::create([
                            'EMAIL' => $email,
                            'TANGGAL_PENYIMPANAN' => $fridayDate,
                            'NOMINAL' => $sisaGajiHarianUpToFriday,
                            'STATUS' => 'disetujui',
                            'CATATAN_ADMIN' => 'Gaji otomatis disimpan karena tidak ada klaim pada hari Jumat',
                            'DIPROSES_OLEH' => 'System',
                            'TANGGAL_DIPROSES' => Carbon::parse($fridayDate)->endOfDay(),
                            'created_at' => Carbon::parse($fridayDate)->endOfDay(),
                            'updated_at' => Carbon::parse($fridayDate)->endOfDay(),
                        ]);
                    }
                }
            }
            
            // Sync tabungan saldo for this employee
            \App\Models\Tabungan::syncTabungan($email);
        }
    }
}
