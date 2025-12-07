<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailTransaksi extends Model
{
    protected $table = 'detailtransaksi';
    protected $primaryKey = 'id'; 
    public $incrementing = true;
    public $timestamps = false; 

    protected $fillable = [
        'ID_TRANSAKSI', 
        'ID_PRODUK',    
        'JML_ITEM',     
    ];

    // Relasi: DetailTransaksi dimiliki oleh satu Transaksi
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'ID_TRANSAKSI', 'ID_TRANSAKSI');
    }

    // Relasi: DetailTransaksi merujuk ke satu item Menu
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'ID_PRODUK', 'ID_PRODUK');
    }
}