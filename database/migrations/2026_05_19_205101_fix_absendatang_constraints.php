<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tambahkan unique constraint pada (EMAIL, TANGGAL) untuk absendatang
        // agar hanya ada 1 absen datang per karyawan per hari
        Schema::table('absendatang', function (Blueprint $table) {
            try {
                $table->unique(['EMAIL', 'TANGGAL'], 'unique_absendatang_email_tanggal');
            } catch (\Exception $e) {
                // Constraint mungkin sudah ada
                \Log::warning('Unique constraint absendatang mungkin sudah ada: ' . $e->getMessage());
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absendatang', function (Blueprint $table) {
            try {
                $table->dropUnique('unique_absendatang_email_tanggal');
            } catch (\Exception $e) {
                // Skip jika tidak ada
            }
        });
    }
};
