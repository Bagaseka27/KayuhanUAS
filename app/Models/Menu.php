<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menu';
    protected $primaryKey = 'ID_PRODUK';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'ID_PRODUK', 
        'NAMA_PRODUK', 
        'HARGA_DASAR', 
        'HARGA_JUAL'
    ];

    public function transaksi()
    {
        return $this->belongsToMany(
            Transaksi::class,
            'detail_transaksi',
            'ID_PRODUK',
            'ID_TRANSAKSI'
        );
    }
}


