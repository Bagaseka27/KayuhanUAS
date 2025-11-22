<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menu';
    protected $primaryKey = 'ID_PRODUK';
    public $incrementing = false;
    public $timestamps = false;

    public function transaksi()
    {
        return $this->belongsToMany(
            Transaksi::class,
            'ID_PRODUK',
            'ID_TRANSAKSI'
        );
    }
}
