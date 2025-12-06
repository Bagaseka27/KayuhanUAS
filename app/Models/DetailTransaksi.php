<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailTransaksi extends Model
{
    // Nama tabel di database (sesuai ERD: detailTRX atau detail_transaksi)
    protected $table = 'detailtransaksi';
    
    // Asumsi menggunakan ID auto-increment standar
    protected $primaryKey = 'id'; 
    public $incrementing = true;
    
    // Detail item tidak perlu created_at dan updated_at
    public $timestamps = false; 

    // Kolom yang dapat diisi secara massal (sesuai payload dari POS)
    protected $fillable = [
        'ID_TRANSAKSI', // Foreign Key ke tabel 'transaksi'
        'ID_PRODUK',    // Foreign Key ke tabel 'menu'
        'JML_ITEM',     // Jumlah produk yang dibeli
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