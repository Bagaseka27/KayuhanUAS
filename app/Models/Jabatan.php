<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $table = 'jabatan';
    protected $fillable = ['nama_jabatan', 'gaji_pokok_per_hari', 'bonus'];

    public function karyawan()
    {
        return $this->hasMany(Karyawan::class);
    }
}

