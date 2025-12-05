<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Gaji extends Model
{
    protected $table = 'gaji';
    protected $primaryKey = 'ID_GAJI';
    public $incrementing = true;
    protected $keyType = "int";
    public $timestamps = true;


    protected $fillable = [
        'EMAIL', 
        'PERIODE',
        'JUMLAH_HARI_MASUK',
        'TOTAL_GAJI_POKOK',
        'TOTAL_BONUS',
        'TOTAL_KOMPENSASI',
        'TOTAL_GAJI_AKHIR'
    ];


    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'EMAIL','EMAIL');
    }
}
