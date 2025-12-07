<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsenPulang extends Model
{
    protected $table = 'absenpulang';
    protected $primaryKey = 'EMAIL';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true; 
    
    // ✅ Hanya kolom yang ADA di database
    protected $fillable = [
        'EMAIL',
        'FOTO',
        'DATETIME_PULANG'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'EMAIL', 'EMAIL');
    }
}
