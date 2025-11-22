<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokAwal extends Model
{
    protected $table = 'stokawal';
    public $timestamps = false;

    public function rombong()
    {
        return $this->belongsTo(Rombong::class, 'ID_ROMBONG');
    }
}
