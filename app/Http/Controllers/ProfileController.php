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
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $karyawan = Karyawan::find($user->email); // sinkron karyawan

        // VALIDASI
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:15',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // UPDATE NAMA + NO HP PADA USER
        $user->name = $request->name;
        $user->phone = $request->phone;

        // JIKA ADA FOTO BARU
        if ($request->hasFile('photo')) {

            // Hapus foto lama (jika ada dan bukan default)
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }

            // Simpan foto baru
            $path = $request->file('photo')->store('profile_photos', 'public');
            $user->photo = $path;
        }

        $user->save();

        if ($karyawan) {

            $karyawan->NAMA = $request->name;
            $karyawan->NO_HP = $request->phone;

            // Sinkronkan foto juga
            if (isset($path)) {
                $karyawan->FOTO = $path;
            }

            $karyawan->save();
        }

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}
