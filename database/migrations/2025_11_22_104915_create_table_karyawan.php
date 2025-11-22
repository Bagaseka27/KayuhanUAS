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
        Schema::create('karyawan', function (Blueprint $table) {
            $table->string('EMAIL', 50)->primary();
            $table->integer('ID_JABATAN')->nullable();
            $table->string('ID_ROMBONG', 10)->nullable();
            $table->string('ID_CABANG', 20)->nullable();
            $table->string('NAMA', 100)->nullable();
            $table->string('NO_HP', 12)->nullable();
            $table->string('POSISI', 20)->nullable();

            $table->foreign('ID_JABATAN')->references('ID_JABATAN')->on('jabatan')->restrictOnDelete()->restrictOnUpdate();
            $table->foreign('ID_ROMBONG')->references('ID_ROMBONG')->on('rombong')->restrictOnDelete()->restrictOnUpdate();
            $table->foreign('ID_CABANG')->references('ID_CABANG')->on('cabang')->restrictOnDelete()->restrictOnUpdate();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawan');
    }
};
