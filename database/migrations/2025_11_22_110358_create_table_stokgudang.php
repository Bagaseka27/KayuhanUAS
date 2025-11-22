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
    Schema::create('stokgudang', function (Blueprint $table) {
        $table->string('ID_BARANG', 10)->primary();
        $table->string('NAMA_BARANG', 50)->nullable();
        $table->integer('JUMLAH')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stokgudang');
    }
};
