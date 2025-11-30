<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use Illuminate\Http\Request;

class CabangController extends Controller
{
    public function index()
    {
        return Cabang::all();
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'ID_CABANG'   => 'required|string|max:10|unique:cabang,ID_CABANG',
            'NAMA_LOKASI' => 'required|string|max:100',
        ]);

        return Cabang::create($validated);
    }

    public function show($id)
    {
        return Cabang::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $cabang = Cabang::findOrFail($id);

        $validated = $request->validate([
            'NAMA_LOKASI' => 'sometimes|string|max:100',
        ]);

        $cabang->update($validated);
        return $cabang;
    }

    public function destroy($id)
    {
        return Cabang::destroy($id);
    }
}