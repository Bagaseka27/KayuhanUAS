<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GajiHarian;
use App\Models\Karyawan;
use Carbon\Carbon;

class GajiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil karyawan1
        $karyawan = Karyawan::where('EMAIL', 'karyawan1@gmail.com')->first();
        
        if (!$karyawan) {
            $karyawan = Karyawan::first();
        }

        if (!$karyawan) {
            echo "Tidak ada karyawan untuk seeding gaji\n";
            return;
        }

        // Panggil SampleGajiHarianSeeder untuk menginisialisasi data gaji harian
        $seeder = new SampleGajiHarianSeeder();
        $seeder->setCommand($this->command);
        $seeder->run();

        echo "✅ Seeding data gaji harian berhasil!\n";
    }
}
