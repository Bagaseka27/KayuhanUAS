<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Rombong;
use Illuminate\Routing\Controller;

class LocationController extends Controller
{
    public function index()
    {
        // Mengambil data dari database
        $cabangs = Cabang::all(); 
        $rombongs = Rombong::all();
        $cabangList = Cabang::pluck('NAMA_LOKASI', 'ID_CABANG')->all();

        return view('pages.location', [ 
            'cabangs' => $cabangs,
            'rombongs' => $rombongs,
            'cabangList' => $cabangList,
        ]);
    }
}