<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Tabel untuk breakdown gaji harian per karyawan
        Schema::create('gaji_harian', function (Blueprint $table) {
            $table->id();
            $table->string('EMAIL', 50);
            $table->date('TANGGAL');
            
            // Jam kerja dari jadwal
            $table->decimal('JAM_KERJA_TERJADWAL', 8, 2)->default(0);
            $table->time('JAM_MULAI_JADWAL')->nullable();
            $table->time('JAM_SELESAI_JADWAL')->nullable();
            
            // Absensi
            $table->dateTime('WAKTU_DATANG')->nullable();
            $table->dateTime('WAKTU_PULANG')->nullable();
            $table->integer('MENIT_TERLAMBAT')->default(0);
            
            // Gaji per jam (5000 IDR)
            $table->decimal('GAJI_PER_JAM', 10, 2)->default(5000);
            $table->decimal('GAJI_POKOK_HARIAN', 15, 2)->default(0);
            
            // Penjualan & Bonus
            $table->integer('PENJUALAN_CUP')->default(0);
            $table->integer('CUP_BONUS')->default(0); // Cup di atas 50
            $table->decimal('BONUS_PER_CUP', 10, 2)->default(0); // Berdasarkan jabatan
            $table->decimal('BONUS_HARIAN', 15, 2)->default(0);
            
            // Potongan keterlambatan
            $table->decimal('POTONGAN_TERLAMBAT', 15, 2)->default(0);
            $table->boolean('POTONGAN_50_PCT')->default(false); // Jika terlambat > 15 menit
            
            // Total gaji hari ini
            $table->decimal('TOTAL_GAJI_HARIAN', 15, 2)->default(0);
            
            // Status
            $table->enum('STATUS_ABSENSI', ['HADIR', 'TERLAMBAT', 'TIDAK_HADIR', 'SAKIT', 'IZIN'])->default('HADIR');
            
            $table->timestamps();
            
            // Constraints
            $table->unique(['EMAIL', 'TANGGAL']);
            $table->foreign('EMAIL')->references('EMAIL')->on('karyawan')->cascadeOnDelete();
            $table->index('TANGGAL');
            $table->index(['EMAIL', 'TANGGAL']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gaji_harian');
    }
};
