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
        Schema::create('detailtransaksi', function (Blueprint $table) {
            $table->string('ID_TRANSAKSI', 10);
            $table->string('ID_PRODUK', 10);
            $table->integer('JML_ITEM'); 
            $table->primary(['ID_TRANSAKSI', 'ID_PRODUK']);

            $table->foreign('ID_TRANSAKSI')->references('ID_TRANSAKSI')->on('transaksi')->restrictOnDelete()->restrictOnUpdate();
            $table->foreign('ID_PRODUK')->references('ID_PRODUK')->on('menu')->restrictOnDelete()->restrictOnUpdate();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detailtransaksi');
    }
};
