<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('gaji');
    }

    public function down()
    {
        Schema::create('gaji', function (Blueprint $table) {
            $table->id('ID_GAJI');
            $table->string('EMAIL', 50);
            $table->string('PERIODE', 20);
            $table->decimal('TOTAL_GAJI_POKOK', 15, 2);
            $table->decimal('TOTAL_BONUS', 15, 2)->nullable();
            $table->decimal('TOTAL_POTONGAN', 15, 2)->default(0)->nullable();
            $table->decimal('TOTAL_GAJI_AKHIR', 15, 2);
            $table->decimal('TOTAL_JAM_KERJA', 8, 2)->default(0);
            $table->integer('TOTAL_PENJUALAN_CUP')->default(0);
            $table->decimal('BONUS_PENJUALAN', 15, 2)->default(0);
            $table->integer('MENIT_TERLAMBAT')->default(0);
            $table->decimal('POTONGAN_KETERLAMBATAN', 15, 2)->default(0);
            $table->integer('JUMLAH_HARI_IZIN')->default(0);
            $table->integer('JUMLAH_HARI_SAKIT')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('EMAIL')->references('EMAIL')->on('karyawan')->cascadeOnDelete();
        });
    }
};
