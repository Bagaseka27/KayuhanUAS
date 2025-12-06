<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $table = 'jadwal';
    protected $primaryKey = 'ID_JADWAL';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'ID_JADWAL',
        'EMAIL',      
        'ID_CABANG',  
        'TANGGAL',
        'JAM_MULAI',
        'JAM_SELESAI'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'EMAIL');
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'ID_CABANG');
    }
}