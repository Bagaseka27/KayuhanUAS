<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rombong_stok', function (Blueprint $table) {
            $table->id();
            $table->string('barang_id', 10);
            $table->string('rombong_id', 10);
            $table->integer('stok_awal')->nullable();
            $table->integer('stok_akhir')->nullable();
            $table->timestamps();

            $table->foreign('barang_id')
                ->references('ID_BARANG')->on('stokgudang')
                ->restrictOnDelete()->restrictOnUpdate();

            $table->foreign('rombong_id')
                ->references('ID_ROMBONG')->on('rombong')
                ->restrictOnDelete()->restrictOnUpdate();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rombong_stok');
    }
};
