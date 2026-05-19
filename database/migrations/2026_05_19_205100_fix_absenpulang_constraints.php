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
        // Tambahkan unique constraint pada (EMAIL, TANGGAL) untuk absenpulang
        // agar hanya ada 1 absen pulang per karyawan per hari
        Schema::table('absenpulang', function (Blueprint $table) {
            try {
                $table->unique(['EMAIL', 'TANGGAL'], 'unique_absenpulang_email_tanggal');
            } catch (\Exception $e) {
                // Constraint mungkin sudah ada
                \Log::warning('Unique constraint absenpulang mungkin sudah ada: ' . $e->getMessage());
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absenpulang', function (Blueprint $table) {
            try {
                $table->dropUnique('unique_absenpulang_email_tanggal');
            } catch (\Exception $e) {
                // Skip jika tidak ada
            }
        });
    }
};

