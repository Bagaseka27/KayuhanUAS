<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Karyawan;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = Auth::user();
        $karyawan = Karyawan::find($user->email);

        $request->validate([
            'name' => 'required|string|max:255',
            'no_hp' => 'nullable|string|max:12',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Update nama di users
        $user->name = $request->name;
        $user->save();

        // Update karyawan
        if ($karyawan) {
            $karyawan->NAMA = $request->name;
            $karyawan->NO_HP = $request->no_hp;

            // Cek & simpan foto baru
            if ($request->hasFile('foto')) {

                if ($karyawan->FOTO && Storage::disk('public')->exists($karyawan->FOTO)) {
                    Storage::disk('public')->delete($karyawan->FOTO);
                }

                $path = $request->file('foto')->store('profile_fotos', 'public');
                $karyawan->FOTO = $path;
            }

            $karyawan->save();
        }

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

}
