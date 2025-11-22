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
    Schema::create('stokakhir', function (Blueprint $table) {
        $table->string('ID_ROMBONG', 10)->nullable();
        $table->integer('JUMLAHAKHIR')->nullable();

        $table->foreign('ID_ROMBONG')->references('ID_ROMBONG')->on('rombong')->restrictOnDelete()->restrictOnUpdate();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stokakhir');
    }
};
