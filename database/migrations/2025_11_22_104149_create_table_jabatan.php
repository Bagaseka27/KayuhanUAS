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
        Schema::create('jabatan', function (Blueprint $table) {
            $table->integer('ID_JABATAN')->primary();
            $table->string('NAMA_JABATAN', 50)->nullable();
            $table->integer('GAJI_POKOK_PER_HARI')->nullable();
            $table->integer('BONUS_PER_HARI')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jabatan');
    }
};
