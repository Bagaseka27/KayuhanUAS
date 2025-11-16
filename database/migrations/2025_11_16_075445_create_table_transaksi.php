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
            $table->id();
            $table->unsignedBigInteger('menu_id');
            $table->integer('jumlah');
            $table->integer('total_harga');
            $table->unsignedBigInteger('karyawan_id');
            $table->dateTime('tanggal_transaksi');
            $table->enum('metode_bayar', ['cash', 'qris', 'debit'])->nullable();
            $table->integer('total_bayar')->nullable();
            
            $table->foreign('menu_id')->references('id')->on('menu')->onDelete('cascade');
            $table->foreign('karyawan_id')->references('id')->on('karyawan')->onDelete('cascade');
            
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
