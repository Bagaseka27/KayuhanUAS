<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokGudang extends Model
{
    protected $table = 'stokgudang';
    protected $primaryKey = 'ID_BARANG';
    public $incrementing = false;
    public $timestamps = false;
}
