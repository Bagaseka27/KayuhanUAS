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
        $validated = $request->validate([
            'ID_ROMBONG' => 'required|string|max:10|unique:rombong,ID_ROMBONG',
        ]);

        return Rombong::create($validated);
    }

    public function show($id)
    {
        return Rombong::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $rombong = Rombong::findOrFail($id);

        $validated = $request->validate([
            'ID_ROMBONG' => 'required|string|max:10|unique:rombong,ID_ROMBONG,'.$id.',ID_ROMBONG',
        ]);

        $rombong->update($validated);
        return $rombong;
    }


    public function destroy($id)
    {
        return Rombong::destroy($id);
    }
}