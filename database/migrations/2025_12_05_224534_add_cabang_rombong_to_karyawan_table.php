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
        Schema::table('karyawan', function (Blueprint $table) {
            $table->integer('ID_CABANG')->nullable()->after('ROLE');
            $table->integer('ID_ROMBONG')->nullable()->after('ID_CABANG');
        });
    }

    public function down()
    {
        Schema::table('karyawan', function (Blueprint $table) {
            $table->dropColumn(['ID_CABANG', 'ID_ROMBONG']);
        });
    }

};
