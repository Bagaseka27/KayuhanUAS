<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StokGudang;
use App\Models\RombongStok;
use App\Models\Rombong;

class InventoryController extends Controller
{

    public function index()
    {
        return view('pages.inventory', [
            'master' => StokGudang::all(),
            'rombong' => RombongStok::with('barang')->get(),
            'rombongList' => Rombong::all(),
        ]);
    }
}