<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsenDatang extends Model
{
    protected $table = 'absendatang';
    public $incrementing = false;
    public $timestamps = true;
    
    // ✅ Hanya kolom yang ADA di database
    protected $fillable = [
        'EMAIL',
        'FOTO',
        'DATETIME_DATANG',
        'TANGGAL',
        'ID_CABANG'
    ];
    
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'EMAIL', 'EMAIL');
    }
}

