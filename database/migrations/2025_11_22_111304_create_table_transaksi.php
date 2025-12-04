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
            $table->string('ID_PRODUK',10)->nullable();
            $table->integer('JUMLAH_ITEM')->nullable();
            $table->integer('HARGA_ITEM')->nullable();
            $table->dateTime('DATETIME')->nullable();
            $table->integer('TOTAL_BAYAR')->nullable();
            $table->string('METODE_PEMBAYARAN', 20)->nullable();

            $table->foreign('EMAIL')->references('EMAIL')->on('karyawan')->restrictOnDelete()->restrictOnUpdate();
            $table->foreign('ID_PRODUK')->references('ID_PRODUK')->on('menu')->restrickOnDelete()->restrickOnUpdate();
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
