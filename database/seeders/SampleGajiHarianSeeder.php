<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Karyawan;
use App\Models\Jadwal;
use App\Services\GajiService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SampleGajiHarianSeeder extends Seeder
{
    public function run()
    {
        // 1. Pastikan nilai bonus dan upah per jam di table jabatan sudah sesuai aturan
        DB::table('jabatan')->where('ID_JABATAN', 6)->update([
            'NAMA_JABATAN' => 'Senior',
            'UPAH_PER_JAM' => 5000,
            'BONUS_PENJUALAN_PER_CUP' => 1000
        ]);
        DB::table('jabatan')->where('ID_JABATAN', 5)->update([
            'NAMA_JABATAN' => 'Junior',
            'UPAH_PER_JAM' => 5000,
            'BONUS_PENJUALAN_PER_CUP' => 500
        ]);
        DB::table('jabatan')->where('ID_JABATAN', 4)->update([
            'NAMA_JABATAN' => 'Traineer',
            'UPAH_PER_JAM' => 5000,
            'BONUS_PENJUALAN_PER_CUP' => 0
        ]);

        // 2. Data Barista yang disiapkan
        $baristas = [
            'frans@gmail.com' => [
                'nama' => 'Frans',
                'jabatan' => 6, // Senior
                'cabang' => 'CBG-KDR1',
                'rombong' => 'RM-001'
            ],
            'dyah@gmail.com' => [
                'nama' => 'Dyah',
                'jabatan' => 5, // Junior
                'cabang' => 'CBG-KDR3',
                'rombong' => 'RM-003'
            ],
            'daniel@gmail.com' => [
                'nama' => 'Daniel Suryana',
                'jabatan' => 4, // Traineer
                'cabang' => 'CBG-KDR2',
                'rombong' => 'RM-002'
            ]
        ];

        foreach ($baristas as $email => $dataBarista) {
            $karyawan = Karyawan::where('EMAIL', $email)->first();
            if ($karyawan) {
                $karyawan->update([
                    'ID_JABATAN' => $dataBarista['jabatan'],
                    'ROLE' => 'Barista',
                    'ID_CABANG' => $dataBarista['cabang'],
                    'ID_ROMBONG' => $dataBarista['rombong']
                ]);
            } else {
                $karyawan = Karyawan::create([
                    'EMAIL' => $email,
                    'NAMA' => $dataBarista['nama'],
                    'ID_JABATAN' => $dataBarista['jabatan'],
                    'ROLE' => 'Barista',
                    'ID_CABANG' => $dataBarista['cabang'],
                    'ID_ROMBONG' => $dataBarista['rombong'],
                    'PASSWORD' => bcrypt('password'),
                    'NO_HP' => '08123456789'
                ]);
            }
        }

        // 3. Konfigurasi data dump harian
        $dates = [
            '2026-06-09' => ['hours' => 8, 'penjualan' => 45, 'terlambat' => 0],
            '2026-06-10' => ['hours' => 10, 'penjualan' => 55, 'terlambat' => 5],
            '2026-06-11' => ['hours' => 10, 'penjualan' => 62, 'terlambat' => 0],
        ];

        $gajiService = app(GajiService::class);

        // 4. Bersihkan data lama agar tidak bentrok
        foreach (array_keys($baristas) as $email) {
            $transaksiIds = DB::table('transaksi')
                ->where('EMAIL', $email)
                ->whereIn(DB::raw('DATE(DATETIME)'), array_keys($dates))
                ->pluck('ID_TRANSAKSI');

            if ($transaksiIds->isNotEmpty()) {
                DB::table('detailtransaksi')->whereIn('ID_TRANSAKSI', $transaksiIds)->delete();
                DB::table('transaksi')->whereIn('ID_TRANSAKSI', $transaksiIds)->delete();
            }

            DB::table('absensi')->where('EMAIL', $email)->whereIn('TANGGAL', array_keys($dates))->delete();
            DB::table('jadwal')->where('EMAIL', $email)->whereIn('TANGGAL', array_keys($dates))->delete();
            DB::table('gaji_harian')->where('EMAIL', $email)->whereIn('TANGGAL', array_keys($dates))->delete();
        }

        // 5. Seed data jadwal, absensi, transaksi, lalu hitung gaji harian
        foreach ($baristas as $email => $dataBarista) {
            foreach ($dates as $date => $data) {
                // A. Jadwal
                $jamMulai = '08:00:00';
                $jamSelesai = $data['hours'] == 8 ? '16:00:00' : '18:00:00';
                $idJadwal = 'JD' . str_replace('-', '', $date) . substr(md5($email), 0, 5);

                Jadwal::create([
                    'ID_JADWAL' => $idJadwal,
                    'EMAIL' => $email,
                    'ID_CABANG' => $dataBarista['cabang'],
                    'TANGGAL' => $date,
                    'JAM_MULAI' => $jamMulai,
                    'JAM_SELESAI' => $jamSelesai,
                ]);

                // B. Absensi
                $datetimeDatang = Carbon::parse($date . ' ' . $jamMulai);
                if ($data['terlambat'] > 0) {
                    $datetimeDatang->addMinutes($data['terlambat']);
                }
                $datetimePulang = Carbon::parse($date . ' ' . $jamSelesai);

                DB::table('absensi')->insert([
                    'EMAIL' => $email,
                    'TANGGAL' => $date,
                    'DATETIME_DATANG' => $datetimeDatang,
                    'DATETIME_PULANG' => $datetimePulang,
                    'STATUS' => $data['terlambat'] > 0 ? 'TERLAMBAT' : 'HADIR',
                    'KOMPENSASI' => 0,
                    'ID_CABANG' => $dataBarista['cabang'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // C. Transaksi & Detail Transaksi
                if ($data['penjualan'] > 0) {
                    $monthDay = date('md', strtotime($date));
                    $uniq = substr(md5($email . $date), 0, 4);
                    $idTransaksi = substr('T' . $monthDay . $uniq, 0, 10);

                    DB::table('transaksi')->insert([
                        'ID_TRANSAKSI' => $idTransaksi,
                        'EMAIL' => $email,
                        'DATETIME' => Carbon::parse($date . ' 12:00:00'),
                        'TOTAL_BAYAR' => 15000 * $data['penjualan'],
                        'METODE_PEMBAYARAN' => 'CASH',
                        'STATUS' => 'PAID',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    DB::table('detailtransaksi')->insert([
                        'ID_TRANSAKSI' => $idTransaksi,
                        'ID_PRODUK' => 'M001', // Matcha
                        'JML_ITEM' => $data['penjualan'],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }

                // D. Hitung Gaji Harian menggunakan GajiService
                $gajiService->hitungGajiHarian($email, $date);
            }
            $this->command->info("Data jadwal, absensi, transaksi, dan gaji harian untuk {$email} berhasil dibuat.");
        }

        $this->command->info('Seeder Sample Gaji Harian Karyawan selesai dijalankan.');
    }
}

