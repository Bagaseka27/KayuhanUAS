<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('gaji', function (Blueprint $table) {
            $table->char('ID_GAJI', 10)->primary();
            $table->string('EMAIL', 50)->nullable();
            $table->char('PERIODE', 10)->nullable();
            $table->char('TOTAL_GAJI_POKOK', 10)->nullable();
            $table->char('TOTAL_BONUS', 10)->nullable();
            $table->char('TOTAL_KOMPENSASI', 10)->nullable();
            $table->char('TOTAL_GAJI_AKHIR', 10)->nullable();

            $table->foreign('EMAIL')->references('EMAIL')->on('karyawan')->restrictOnDelete()->restrictOnUpdate();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gaji');
    }
};
