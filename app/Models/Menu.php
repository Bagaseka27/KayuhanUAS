<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menu';
    protected $fillable = ['nama_produk', 'harga_dasar', 'harga_jual', 'foto_produk'];

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class);
    }
}
