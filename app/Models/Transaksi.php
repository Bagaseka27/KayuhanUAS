<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';
    protected $primaryKey = 'ID_TRANSAKSI';
    public $incrementing = false;
    public $timestamps = false;

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'EMAIL');
    }

    public function menu()
    {
        return $this->belongsToMany(
            Menu::class,
            'ID_TRANSAKSI',
            'ID_PRODUK'
        );
    }
}