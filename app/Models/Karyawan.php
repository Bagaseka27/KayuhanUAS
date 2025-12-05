<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    protected $table = 'karyawan';
    protected $primaryKey = 'EMAIL';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'EMAIL',
        'NAMA',
        'ID_JABATAN',
        'PASSWORD',
        'NO_HP',
        'ROLE',
        'remember_token'
    ];

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'ID_JABATAN');
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'ID_CABANG');
    }

    public function rombong()
    {
        return $this->belongsTo(Rombong::class, 'ID_ROMBONG');
    }

    public function absenDatang()
    {
        return $this->hasMany(AbsenDatang::class, 'EMAIL');
    }

    public function absenPulang()
    {
        return $this->hasMany(AbsenPulang::class, 'EMAIL');
    }

    public function gaji()
    {
        return $this->hasMany(Gaji::class, 'EMAIL');
    }

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class, 'EMAIL');
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'EMAIL');
    }
}


