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
    Schema::create('absenpulang', function (Blueprint $table) {
        $table->string('EMAIL', 50);
        $table->binary('FOTO')->nullable();
        $table->dateTime('DATETIME_PULANG')->nullable();

        $table->foreign('EMAIL')->references('EMAIL')->on('karyawan')->restrictOnDelete()->restrictOnUpdate();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absenpulang');
    }
};
