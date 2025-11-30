<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsenDatang extends Model
{
    protected $table = 'absendatang';
    protected $primaryKey = 'EMAIL';
    protected $keyType = 'string';
    public $timestamps = true;
    
    protected $fillable = [
        'EMAIL',
        'FOTO',
        'DATETIME_DATANG'
    ];
    
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'EMAIL');
    }
}