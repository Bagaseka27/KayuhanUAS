<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $table = 'jabatan';
    protected $primaryKey = 'ID_JABATAN';

    public $timestamps = false;

    protected $casts = [
        'UPAH_PER_JAM' => 'decimal:2',
        'BONUS_PENJUALAN_PER_CUP' => 'decimal:2',
    ];

    protected $fillable = [
        'NAMA_JABATAN',
        'UPAH_PER_JAM',
        'BONUS_PENJUALAN_PER_CUP'
    ];

    public function karyawan()
    {
        return $this->hasMany(Karyawan::class, 'ID_JABATAN');
    }

    // Bonus tetap per CUP berdasarkan jabatan
    public function getBonusPerCupAttribute()
    {
        return $this->BONUS_PENJUALAN_PER_CUP ?? 0;
    }
}