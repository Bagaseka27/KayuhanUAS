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
            'no_hp' => 'nullable|string|max:15',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // update user
        $user->name = $request->name;
        $user->no_hp = $request->no_hp;

        if ($request->hasFile('foto')) {

            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }

            $path = $request->file('foto')->store('profile_fotos', 'public');
            $user->foto = $path;
        }

        $user->save();

        // sync ke tabel karyawan
        if ($karyawan) {
            $karyawan->NAMA = $request->name;
            $karyawan->NO_HP = $request->no_hp;

            if (isset($path)) {
                $karyawan->FOTO = $path;
            }

            $karyawan->save();
        }

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

}
