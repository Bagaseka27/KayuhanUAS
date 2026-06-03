<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop old migrations if they exist
        Schema::dropIfExists('absenpulang');
        Schema::dropIfExists('absendatang');
        
        // Drop old absensi if it exists with wrong structure
        if (Schema::hasTable('absensi')) {
            Schema::drop('absensi');
        }

        // Create fresh absensi table
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            
            // Employee & Date
            $table->string('EMAIL', 50);
            $table->date('TANGGAL');
            
            // Absen Datang
            $table->dateTime('DATETIME_DATANG')->nullable();
            $table->longText('FOTO_DATANG')->nullable(); // Base64 encoded image
            $table->string('LOKASI_DATANG', 255)->nullable();
            $table->decimal('LAT_DATANG', 10, 8)->nullable();
            $table->decimal('LNG_DATANG', 11, 8)->nullable();
            
            // Absen Pulang
            $table->dateTime('DATETIME_PULANG')->nullable();
            $table->longText('FOTO_PULANG')->nullable(); // Base64 encoded image
            $table->string('LOKASI_PULANG', 255)->nullable();
            $table->decimal('LAT_PULANG', 10, 8)->nullable();
            $table->decimal('LNG_PULANG', 11, 8)->nullable();
            
            // Status
            $table->enum('STATUS', ['HADIR', 'TERLAMBAT', 'TIDAK_HADIR'])->default('HADIR');
            
            // Kompensasi (untuk terlambat, negative value)
            $table->integer('KOMPENSASI')->default(0);
            
            // Tidak Hadir (Sakit/Izin)
            $table->enum('ALASAN_TIDAK_HADIR', ['SAKIT', 'IZIN'])->nullable();
            $table->longText('SURAT_IZIN')->nullable(); // Base64 encoded file
            
            // Cabang
            $table->string('ID_CABANG', 50)->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Constraints
            $table->unique(['EMAIL', 'TANGGAL']);
            $table->foreign('EMAIL')->references('EMAIL')->on('karyawan')->restrictOnDelete()->restrictOnUpdate();
            $table->foreign('ID_CABANG')->references('ID_CABANG')->on('cabang')->nullOnDelete()->cascadeOnUpdate();
            
            // Indexes untuk query yang sering
            $table->index('TANGGAL');
            $table->index('STATUS');
            $table->index(['EMAIL', 'TANGGAL']);
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
