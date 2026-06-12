<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Update table jabatan dengan bonus per jabatan dan upah per jam
        Schema::table('jabatan', function (Blueprint $table) {
            // Ganti GAJI_POKOK_PER_HARI dengan UPAH_PER_JAM (5000)
            if (Schema::hasColumn('jabatan', 'GAJI_POKOK_PER_HARI')) {
                $table->dropColumn('GAJI_POKOK_PER_HARI');
            }
            
            $table->decimal('UPAH_PER_JAM', 10, 2)->default(5000)->comment('Upah per jam kerja');
            $table->decimal('BONUS_PENJUALAN_PER_CUP', 10, 2)->default(0)->comment('Bonus per cup penjualan di atas 50');
        });
    }

    public function down(): void
    {
        Schema::table('jabatan', function (Blueprint $table) {
            $table->dropColumn(['UPAH_PER_JAM', 'BONUS_PENJUALAN_PER_CUP']);
        });
    }
};
