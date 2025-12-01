<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gaji extends Model
{
    protected $table = 'gaji';
    protected $primaryKey = 'ID_GAJI';
    public $timestamps = true;

    protected $fillable = [
        'EMAIL', 
        'PERIODE',
        'TOTAL_GAJI_POKOK',
        'TOTAL_BONUS',
        'TOTAL_KOMPENSASI',
        'TOTAL_GAJI_AKHIR'
    ];


    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'EMAIL');
    }
}
