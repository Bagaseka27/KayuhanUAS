<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $table = 'jabatan';
    protected $primaryKey = 'ID_JABATAN';

    public $timestamps = false;

    protected $fillable = [
        'NAMA_JABATAN',
        'GAJI_POKOK_PER_HARI',
        'BONUS_PER_HARI'
    ];

    public function karyawan()
    {
        return $this->hasMany(Karyawan::class, 'ID_JABATAN');
    }
}