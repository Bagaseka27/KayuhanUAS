<?php

namespace App\Http\Controllers;

use App\Models\Rombong;
use Illuminate\Http\Request;

class RombongController extends Controller
{
    public function index()
    {
        return Rombong::all();
    }

    public function store(Request $request)
    {
        if (!$request->filled('ID_ROMBONG')) {
            $request->merge(['ID_ROMBONG' => 'RM-' . time()]);
        }

        $request->validate([
            'ID_ROMBONG' => 'required|string|max:10|unique:rombong,ID_ROMBONG',
            'ID_CABANG'  => 'required|exists:cabang,ID_CABANG',
        ]);

        Rombong::create($request->all());

        return back()->with('success', 'Rombong berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $rombong = Rombong::findOrFail($id);

        $request->validate([
            'ID_CABANG' => 'required|exists:cabang,ID_CABANG',
        ]);

        $rombong->update($request->only('ID_CABANG'));

        return back()->with('success', 'Rombong berhasil diperbarui');
    }

    public function destroy($id)
    {
        Rombong::destroy($id);
        return back()->with('success','Rombong dihapus');
    }
}