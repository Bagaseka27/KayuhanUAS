<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokAkhir extends Model
{
    protected $table = 'stokakhir';
    public $timestamps = false;

    public function rombong()
    {
        return $this->belongsTo(Rombong::class, 'ID_ROMBONG');
    }
}
