<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Update table gaji dengan kolom baru
        Schema::table('gaji', function (Blueprint $table) {
            // Ubah tipe data kolom untuk perhitungan berbasis jam
            $table->decimal('TOTAL_GAJI_POKOK', 15, 2)->change();
            $table->decimal('TOTAL_BONUS', 15, 2)->nullable()->change();
            $table->decimal('TOTAL_POTONGAN', 15, 2)->default(0)->nullable();
            $table->decimal('TOTAL_GAJI_AKHIR', 15, 2)->change();
            
            // Kolom untuk tracking detail
            $table->decimal('TOTAL_JAM_KERJA', 8, 2)->default(0);
            $table->integer('TOTAL_PENJUALAN_CUP')->default(0);
            $table->decimal('BONUS_PENJUALAN', 15, 2)->default(0);
            $table->integer('MENIT_TERLAMBAT')->default(0);
            $table->decimal('POTONGAN_KETERLAMBATAN', 15, 2)->default(0);
            $table->integer('JUMLAH_HARI_IZIN')->default(0);
            $table->integer('JUMLAH_HARI_SAKIT')->default(0);
            
            $table->softDeletes();
        });

        // Tabel untuk pengambilan gaji (Kamis)
        Schema::create('gaji_pengambilan', function (Blueprint $table) {
            $table->id();
            $table->string('EMAIL', 50);
            $table->date('TANGGAL_PENGAMBILAN');
            $table->decimal('NOMINAL', 15, 2);
            $table->enum('STATUS', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->text('CATATAN_ADMIN')->nullable();
            $table->string('DIPROSES_OLEH', 50)->nullable();
            $table->timestamp('TANGGAL_DIPROSES')->nullable();
            $table->timestamps();
            
            $table->foreign('EMAIL')->references('EMAIL')->on('karyawan')->cascadeOnDelete();
        });

        // Tabel untuk penyimpanan gaji (Kamis)
        Schema::create('gaji_disimpan', function (Blueprint $table) {
            $table->id();
            $table->string('EMAIL', 50);
            $table->date('TANGGAL_PENYIMPANAN');
            $table->decimal('NOMINAL', 15, 2);
            $table->enum('STATUS', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->text('CATATAN_ADMIN')->nullable();
            $table->string('DIPROSES_OLEH', 50)->nullable();
            $table->timestamp('TANGGAL_DIPROSES')->nullable();
            $table->timestamps();
            
            $table->foreign('EMAIL')->references('EMAIL')->on('karyawan')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('gaji', function (Blueprint $table) {
            $table->dropColumn([
                'TOTAL_POTONGAN',
                'TOTAL_JAM_KERJA',
                'TOTAL_PENJUALAN_CUP',
                'BONUS_PENJUALAN',
                'MENIT_TERLAMBAT',
                'POTONGAN_KETERLAMBATAN',
                'JUMLAH_HARI_IZIN',
                'JUMLAH_HARI_SAKIT',
            ]);
            $table->dropSoftDeletes();
        });

        Schema::dropIfExists('gaji_pengambilan');
        Schema::dropIfExists('gaji_disimpan');
    }
};
