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
    Schema::create('jadwal', function (Blueprint $table) {
        $table->string('ID_JADWAL', 20)->primary();
        $table->string('EMAIL', 50)->nullable();
        $table->string('ID_CABANG', 20)->nullable();
        $table->date('TANGGAL')->nullable();
        $table->time('JAM_MULAI')->nullable();
        $table->time('JAM_SELESAI')->nullable();

        $table->foreign('EMAIL')->references('EMAIL')->on('karyawan')->restrictOnDelete()->restrictOnUpdate();
        $table->foreign('ID_CABANG')->references('ID_CABANG')->on('cabang')->restrictOnDelete()->restrictOnUpdate();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal');
    }
};
