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
        // Drop tabel lama jika ada
        Schema::dropIfExists('absensi');
        
        // Create tabel absensi baru yang simple & sesuai requirements
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            
            // Employee & Date
            $table->string('EMAIL', 50);
            $table->date('TANGGAL');
            
            // Absen Datang
            $table->dateTime('DATETIME_DATANG')->nullable();
            $table->longText('FOTO_DATANG')->nullable(); // Base64 encoded
            $table->string('LOKASI_DATANG', 255)->nullable();
            $table->decimal('LAT_DATANG', 10, 8)->nullable();
            $table->decimal('LNG_DATANG', 11, 8)->nullable();
            
            // Absen Pulang
            $table->dateTime('DATETIME_PULANG')->nullable();
            $table->longText('FOTO_PULANG')->nullable(); // Base64 encoded
            $table->string('LOKASI_PULANG', 255)->nullable();
            $table->decimal('LAT_PULANG', 10, 8)->nullable();
            $table->decimal('LNG_PULANG', 11, 8)->nullable();
            
            // Status & Kompensasi
            $table->enum('STATUS', ['HADIR', 'TERLAMBAT', 'TIDAK_HADIR'])->default('HADIR');
            $table->integer('KOMPENSASI')->default(0); // -10000 untuk terlambat
            
            // Tidak Hadir (Sakit/Izin)
            $table->enum('ALASAN_TIDAK_HADIR', ['SAKIT', 'IZIN'])->nullable();
            $table->longText('SURAT_IZIN')->nullable(); // Base64 encoded
            
            // Metadata
            $table->string('ID_CABANG', 50)->nullable();
            $table->timestamps();
            
            // Constraints
            $table->unique(['EMAIL', 'TANGGAL']);
            $table->foreign('EMAIL')->references('EMAIL')->on('karyawan')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('ID_CABANG')->references('ID_CABANG')->on('cabang')->nullOnDelete()->cascadeOnUpdate();
            
            // Indexes
            $table->index(['EMAIL', 'TANGGAL']);
            $table->index('TANGGAL');
            $table->index('STATUS');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};
