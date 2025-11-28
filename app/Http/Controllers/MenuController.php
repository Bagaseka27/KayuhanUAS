<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        return Menu::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ID_PRODUK' => 'required|string|max:10|unique:menu,ID_PRODUK',
            'NAMA_PRODUK' => 'required|string|max:50',
            'HARGA_DASAR' => 'required|integer',
            'HARGA_JUAL' => 'required|integer',
        ]);

        return Menu::create($validated);
    }

    public function show($id)
    {
        return Menu::find($id);
    }

    public function update(Request $request, $id)
    {
        $menu = Menu::find($id);
        $validated = $request->validate([
            'NAMA_PRODUK' => 'sometimes|string|max:50',
            'HARGA_DASAR' => 'sometimes|integer',
            'HARGA_JUAL'  => 'sometimes|integer',
        ]);
        $menu->update($validated);
        return $menu;
    }

    public function destroy($id)
    {
        return Menu::destroy($id);
    }
}
