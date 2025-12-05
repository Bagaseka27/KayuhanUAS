<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        // QUERY 1: Mengambil semua data menu dari tabel 'menu'
        $menuItems = Menu::all();
        
        // Data categories untuk dropdown filter
        // (Asumsi data kategori masih hardcoded karena tidak ada tabel kategori di Model)
        $categories = ['Coffee', 'Non-Coffee'];
        
        // Mengirim data ke view. 
        // Ganti 'pages.menu' dengan path view Anda yang sebenarnya jika berbeda.
        return view('pages.menu', compact('menuItems', 'categories'));
    }

    public function store(Request $request)
    {
        // QUERY 2: Menjalankan INSERT INTO menu (...)
        $validated = $request->validate([
            'ID_PRODUK' => 'required|string|max:10|unique:menu,ID_PRODUK',
            'NAMA_PRODUK' => 'required|string|max:50',
            'HARGA_DASAR' => 'required|integer',
            'HARGA_JUAL' => 'required|integer',
            'CATEGORY' => 'sometimes|string', // Menambahkan CATEGORY jika Anda memasukkannya ke form
        ]);

        Menu::create($validated);
        return redirect()->route('menu.index')->with('success', 'Menu berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        // QUERY 3: Mencari data berdasarkan ID
        $menu = Menu::findOrFail($id);
        
        // QUERY 4: Validasi input dan menjalankan UPDATE menu SET ... WHERE ID_PRODUK = $id
        $validated = $request->validate([
            'NAMA_PRODUK' => 'sometimes|string|max:50',
            'HARGA_DASAR' => 'sometimes|integer',
            'HARGA_JUAL'  => 'sometimes|integer',
            'CATEGORY' => 'sometimes|string',
        ]);
        
        $menu->update($validated);
        return redirect()->route('menu.index')->with('success', 'Menu berhasil diupdate!');
    }

    public function destroy($id)
    {
        // QUERY 5: Menghapus data berdasarkan Primary Key
        Menu::destroy($id);
        return redirect()->route('menu.index')->with('success', 'Menu berhasil dihapus!');
    }
}