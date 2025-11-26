<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use Illuminate\Http\Request;

class JabatanController extends Controller
{

    public function index()
    {
        return Jabatan::all();
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'NAMA_JABATAN' => 'required|string|max:50',
            'GAJI_POKOK_PER_HARI' => 'required|numeric',
            'BONUS_PER_HARI' => 'required|numeric',
        ]);

    
        return Jabatan::create($validated);
    }

    public function show($id)
    {
        return Jabatan::find($id);
    }

    public function update(Request $request, $id)
    {
        $jabatan = Jabatan::find($id);

        $validated = $request->validate([
            'NAMA_JABATAN' => 'sometimes|string|max:50',
            'GAJI_POKOK_PER_HARI' => 'sometimes|numeric',
            'BONUS_PER_HARI' => 'sometimes|numeric',
        ]);

        $jabatan->update($validated);
        return $jabatan;
    }

        public function destroy($id)
    {
        return Jabatan::destroy($id);
    }

}