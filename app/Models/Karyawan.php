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
        'FOTO',
        'ID_CABANG',
        'ID_ROMBONG',
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
        return $this->hasOne(AbsenDatang::class, 'EMAIL','EMAIL');
    }

    public function absenPulang()
    {
        return $this->hasOne(AbsenPulang::class, 'EMAIL','EMAIL');
    }

    public function absensi()
    {
        return $this->hasOne(Absensi::class, 'EMAIL', 'EMAIL');
    }

    public function gajiHarian()
    {
        return $this->hasMany(GajiHarian::class, 'EMAIL', 'EMAIL');
    }

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class, 'EMAIL');
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'EMAIL');
    }

    public function tabungan()
    {
        return $this->hasOne(Tabungan::class, 'EMAIL', 'EMAIL');
    }

    protected static function booted()
    {
        static::created(function ($karyawan) {
            \App\Models\Tabungan::firstOrCreate([
                'EMAIL' => $karyawan->EMAIL
            ], [
                'SALDO' => 0
            ]);
        });
    }
}


