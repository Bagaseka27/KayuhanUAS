<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsenPulang extends Model
{
    protected $table = 'absenpulang';
    public $timestamps = false;

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'EMAIL');
    }
}
