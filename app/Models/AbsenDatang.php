<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsenDatang extends Model
{
    protected $table = 'absendatang';
    public $timestamps = false;

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'EMAIL');
    }
}
