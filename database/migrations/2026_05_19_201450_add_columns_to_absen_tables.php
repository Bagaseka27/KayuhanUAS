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
        // Tambahkan kolom TANGGAL dan ID_CABANG ke tabel absendatang
        Schema::table('absendatang', function (Blueprint $table) {
            if (!Schema::hasColumn('absendatang', 'TANGGAL')) {
                $table->date('TANGGAL')->nullable()->after('DATETIME_DATANG');
            }
            if (!Schema::hasColumn('absendatang', 'ID_CABANG')) {
                $table->string('ID_CABANG', 50)->nullable()->after('TANGGAL');
            }
        });

        // Tambahkan kolom TANGGAL dan ID_CABANG ke tabel absenpulang
        Schema::table('absenpulang', function (Blueprint $table) {
            if (!Schema::hasColumn('absenpulang', 'TANGGAL')) {
                $table->date('TANGGAL')->nullable()->after('DATETIME_PULANG');
            }
            if (!Schema::hasColumn('absenpulang', 'ID_CABANG')) {
                $table->string('ID_CABANG', 50)->nullable()->after('TANGGAL');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absendatang', function (Blueprint $table) {
            if (Schema::hasColumn('absendatang', 'TANGGAL')) {
                $table->dropColumn('TANGGAL');
            }
            if (Schema::hasColumn('absendatang', 'ID_CABANG')) {
                $table->dropColumn('ID_CABANG');
            }
        });

        Schema::table('absenpulang', function (Blueprint $table) {
            if (Schema::hasColumn('absenpulang', 'TANGGAL')) {
                $table->dropColumn('TANGGAL');
            }
            if (Schema::hasColumn('absenpulang', 'ID_CABANG')) {
                $table->dropColumn('ID_CABANG');
            }
        });
    }
};
