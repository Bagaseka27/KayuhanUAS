<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; 

class MenuController extends Controller
{

    public function index()
    {
        $menuItems = Menu::all();
        $categories = ['Coffee', 'Non-Coffee'];
        return view('pages.menu', compact('menuItems', 'categories'));
    }

    public function pos()
    {
        $menuItems = Menu::all();
        return view('pages.dashboard.pos', compact('menuItems'));
    }

    
    public function store(Request $request)
    {

        $validated = $request->validate([
            'ID_PRODUK' => 'required|string|max:10|unique:menu,ID_PRODUK',
            'NAMA_PRODUK' => 'required|string|max:50',
            'KATEGORI' => 'sometimes|string', 
            'HARGA_DASAR' => 'required|integer',
            'HARGA_JUAL' => 'required|integer',
            'FOTO' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', 
        ]);


        if ($request->hasFile('FOTO')) {
            $path = $request->file('FOTO')->store('menu-images', 'public');
            $validated['FOTO'] = $path;
        } else {
            $validated['FOTO'] = null;
        }

        Menu::create($validated);

        return redirect()->route('menu.index')->with('success', 'Menu berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);
        
        $validated = $request->validate([
            'NAMA_PRODUK' => 'sometimes|string|max:50',
            'HARGA_DASAR' => 'sometimes|integer',
            'HARGA_JUAL'  => 'sometimes|integer',
            'KATEGORI' => 'sometimes|string',
            'FOTO' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        
        if ($request->hasFile('FOTO')) {
            
            if ($menu->FOTO && Storage::disk('public')->exists($menu->FOTO)) {
                Storage::disk('public')->delete($menu->FOTO);
            }

            $path = $request->file('FOTO')->store('menu-images', 'public');
            $validated['FOTO'] = $path;

        } else {
            unset($validated['FOTO']);
        }

        $menu->update($validated);
        
        return redirect()->route('menu.index')->with('success', 'Menu berhasil diupdate!');
    }


    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);

    
        if ($menu->FOTO && Storage::disk('public')->exists($menu->FOTO)) {
            Storage::disk('public')->delete($menu->FOTO);
        }

        $menu->delete();
        return redirect()->route('menu.index')->with('success', 'Menu berhasil dihapus!');
    }
}