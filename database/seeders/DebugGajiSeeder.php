<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GajiHarian;
use App\Models\Karyawan;

class DebugGajiSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('=== DEBUG GAJI DATA ===');
        
        // Check all gaji_harian records
        $allGaji = GajiHarian::all();
        $this->command->info('Total gaji_harian records: ' . $allGaji->count());
        
        foreach ($allGaji as $g) {
            $this->command->info("ID: {$g->id}, EMAIL: {$g->EMAIL}, TANGGAL: {$g->TANGGAL}, TOTAL: {$g->TOTAL_GAJI_HARIAN}");
        }
        
        // Check karyawan
        $karyawan = Karyawan::where('EMAIL', 'karyawan1@gmail.com')->first();
        if ($karyawan) {
            $this->command->info('Karyawan found: ' . $karyawan->NAMA);
        } else {
            $this->command->error('Karyawan karyawan1@gmail.com NOT found');
        }
        
        // Check gaji_harian for karyawan1
        $gajiKaryawan1 = GajiHarian::where('EMAIL', 'karyawan1@gmail.com')->get();
        $this->command->info('Gaji for karyawan1: ' . $gajiKaryawan1->count() . ' records');
        
        // Check date range
        $startDate = '2026-06-01';
        $endDate = '2026-06-30';
        $gajiInRange = GajiHarian::whereBetween('TANGGAL', [$startDate, $endDate])->get();
        $this->command->info("Gaji in range {$startDate} to {$endDate}: " . $gajiInRange->count() . ' records');
    }
}
