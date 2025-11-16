<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gaji extends Model
{
    protected $table = 'gaji';
    protected $fillable = ['karyawan_id', 'total_gaji_pokok', 'total_bonus', 'total_keterlambatan', 'total_gaji_akhir'];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}
