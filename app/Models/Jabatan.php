<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $table = 'jabatan';
    protected $primaryKey = 'ID_JABATAN';
    public $timestamps = false;

    public function karyawan()
    {
        return $this->hasMany(Karyawan::class, 'ID_JABATAN');
    }
}
