<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $table = 'jadwal';
    protected $primaryKey = 'ID_JADWAL';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
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