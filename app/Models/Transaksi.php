<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';
    protected $primaryKey = 'ID_TRANSAKSI';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'ID_TRANSAKSI',
        'EMAIL',
        'JUMLAH_ITEM',
        'HARGA_ITEM',
        'DATETIME',
        'TOTAL_BAYAR',
        'METODE_PEMBAYARAN'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'EMAIL');
    }

    public function menu()
    {
        return $this->belongsToMany(
            Menu::class,
            'detail_transaksi',
            'ID_TRANSAKSI',
            'ID_PRODUK'
        );
    }
}
