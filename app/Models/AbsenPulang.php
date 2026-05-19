<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsenPulang extends Model
{
    protected $table = 'absenpulang';
    public $incrementing = false;
    public $timestamps = true; 
    
    // ✅ Hanya kolom yang ADA di database
    protected $fillable = [
        'EMAIL',
        'FOTO',
        'DATETIME_PULANG',
        'TANGGAL',
        'ID_CABANG'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'EMAIL', 'EMAIL');
    }
}
