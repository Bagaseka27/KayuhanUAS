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
        Schema::create('transaksi', function (Blueprint $table) {
            $table->string('ID_TRANSAKSI', 10)->primary();
            $table->string('EMAIL', 50)->nullable();
            $table->dateTime('DATETIME')->nullable();
            $table->integer('TOTAL_BAYAR')->nullable();
            $table->string('METODE_PEMBAYARAN', 20)->nullable();
            $table->string('STATUS', 20)->default('PENDING');
            $table->string('XENDIT_ID')->nullable();
            $table->foreign('EMAIL')->references('EMAIL')->on('karyawan')->restrictOnDelete()->restrictOnUpdate();
    
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
