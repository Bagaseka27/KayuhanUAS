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
            $table->string('NAMA', 100)->nullable();
            $table->integer('ID_JABATAN')->nullable();
            $table->string('PASSWORD', 255)->nullable();
            $table->string('NO_HP', 12)->nullable();
            $table->string('ROLE',10)->nullable();
            $table->rememberToken()->nullable();
            $table->foreign('ID_JABATAN')->references('ID_JABATAN')->on('jabatan')->restrictOnDelete()->restrictOnUpdate();
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
