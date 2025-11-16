<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';
    protected $fillable = ['menu_id', 'jumlah', 'total_harga', 'karyawan_id', 'tanggal_transaksi', 'metode_bayar', 'total_bayar'];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}

