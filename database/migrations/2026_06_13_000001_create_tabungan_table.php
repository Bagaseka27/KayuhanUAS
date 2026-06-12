<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tabungan', function (Blueprint $table) {
            $table->string('EMAIL', 50)->primary();
            $table->decimal('SALDO', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('EMAIL')->references('EMAIL')->on('karyawan')->cascadeOnDelete();
        });

        // Initialize savings accounts for existing employees
        $karyawans = DB::table('karyawan')->get();
        foreach ($karyawans as $karyawan) {
            DB::table('tabungan')->insert([
                'EMAIL' => $karyawan->EMAIL,
                'SALDO' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tabungan');
    }
};
