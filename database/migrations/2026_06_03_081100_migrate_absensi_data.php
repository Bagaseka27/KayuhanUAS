<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate data dari absendatang ke tabel absensi
        // Encode FOTO binary ke base64 untuk disimpan di TEXT column
        DB::statement('
            INSERT INTO absensi (EMAIL, TANGGAL, DATETIME_DATANG, FOTO_DATANG, STATUS, ID_CABANG, created_at, updated_at)
            SELECT 
                EMAIL,
                TANGGAL,
                DATETIME_DATANG,
                CONCAT("data:image/png;base64,", TO_BASE64(FOTO)) as FOTO_DATANG,
                "HADIR" as STATUS,
                ID_CABANG,
                created_at,
                updated_at
            FROM absendatang
            WHERE DATETIME_DATANG IS NOT NULL
        ');

        // Migrate data dari absenpulang ke tabel absensi
        // Join dengan absensi yang sudah ada untuk update DATETIME_PULANG
        DB::statement('
            UPDATE absensi a
            JOIN absenpulang ap ON a.EMAIL = ap.EMAIL AND a.TANGGAL = ap.TANGGAL
            SET 
                a.DATETIME_PULANG = ap.DATETIME_PULANG,
                a.FOTO_PULANG = CONCAT("data:image/png;base64,", TO_BASE64(ap.FOTO))
            WHERE ap.DATETIME_PULANG IS NOT NULL
        ');

        // Untuk absensi pulang yang tidak ada absendatang nya, insert sebagai record baru
        DB::statement('
            INSERT INTO absensi (EMAIL, TANGGAL, DATETIME_PULANG, FOTO_PULANG, STATUS, ID_CABANG, created_at, updated_at)
            SELECT 
                EMAIL,
                TANGGAL,
                DATETIME_PULANG,
                CONCAT("data:image/png;base64,", TO_BASE64(FOTO)) as FOTO_PULANG,
                "HADIR" as STATUS,
                ID_CABANG,
                created_at,
                updated_at
            FROM absenpulang
            WHERE DATETIME_PULANG IS NOT NULL
            AND (EMAIL, TANGGAL) NOT IN (SELECT EMAIL, TANGGAL FROM absensi)
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse migration: delete semua data dari absensi
        DB::table('absensi')->truncate();
    }
};
