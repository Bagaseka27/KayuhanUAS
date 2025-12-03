<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;    // Wajib di-import
use Illuminate\Support\Facades\Storage; // Wajib di-import

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Validasi
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:15',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB
        ]);

        // 2. Update Data Teks
        $user->name = $request->name;
        $user->phone = $request->phone;

        // 3. Handle Upload Foto (Jika ada file baru yang diupload)
        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada (dan bukan default)
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
            
            // Simpan foto baru ke folder 'public/profile_photos'
            // Hasilnya path seperti: profile_photos/namafileunik.jpg
            $path = $request->file('photo')->store('profile_photos', 'public');
            $user->photo = $path;
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}