<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsenPulang extends Model
{
    protected $table = 'absenpulang';
    protected $primaryKey = 'EMAIL';
    protected $keyType = 'string';
    public $timestamps = true; 
    
    protected $fillable = [
        'EMAIL',
        'FOTO',
        'DATETIME_PULANG' 
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'EMAIL');
    }
}