<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cabang extends Model
{
    protected $table = 'cabang';
    protected $primaryKey = 'ID_CABANG';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    public function karyawan()
    {
        return $this->hasMany(Karyawan::class, 'ID_CABANG');
    }

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class, 'ID_CABANG');
    }
}
