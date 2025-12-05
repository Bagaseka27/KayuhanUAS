<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\User; // Import Model User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; // Import DB Facade

class KaryawanController extends Controller
{
    public function index()
    {
        return Karyawan::with(['jabatan', 'cabang', 'rombong'])->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'EMAIL'       => 'required|email|unique:karyawan,EMAIL|unique:users,email|max:50', // Tambah unique:users
            'NAMA'        => 'required|string|max:100',
            'ID_JABATAN'  => 'required|exists:jabatan,ID_JABATAN',
            'PASSWORD'    => 'required|string|min:6',
            'NO_HP'       => 'required|string|max:15',
            'ROLE'        => 'required|string|max:10', // Field ROLE harus ada di request form
            'ID_CABANG'   => 'nullable|exists:cabang,ID_CABANG',
            'ID_ROMBONG'  => 'nullable|exists:rombong,ID_ROMBONG',
        ]);

        $hashedPassword = Hash::make($request->PASSWORD);
        $validated['PASSWORD'] = $hashedPassword;
        
        // --- SINKRONISASI MENGGUNAKAN TRANSACTION ---
        DB::beginTransaction();
        try {
            // 1. Buat data Karyawan
            $karyawan = Karyawan::create($validated);
            
            // 2. Buat data User untuk login (sinkronisasi)
            User::create([
                'name'     => $validated['NAMA'],
                'email'    => $validated['EMAIL'],
                'password' => $hashedPassword,
                'role'     => $validated['ROLE'], // Pastikan ROLE diisi
            ]);

            DB::commit(); 
            return response()->json($karyawan, 201); // Gunakan response JSON

        } catch (\Exception $e) {
            DB::rollBack();
            // Penting: Log error ini untuk mengetahui penyebab kegagalan
            \Log::error("Gagal menyimpan Karyawan/User: " . $e->getMessage());
            return response()->json(['error' => 'Gagal menyimpan data karyawan dan user.'], 500);
        }
    }

    public function show($email)
    {
        return Karyawan::with(['jabatan', 'cabang', 'rombong'])->find($email);
    }

    public function update(Request $request, $email)
    {
        $karyawan = Karyawan::find($email);
        $rules = [
            'NAMA'       => 'sometimes|string|max:100',
            'ID_JABATAN' => 'sometimes|exists:jabatan,ID_JABATAN',
            'NO_HP'      => 'sometimes|string|max:15',
            'ROLE'       => 'sometimes|string|max:10', // Tambahkan ROLE
            'PASSWORD'   => 'nullable|string|min:6' // Password nullable untuk edit
        ];
        $validated = $request->validate($rules);
        
        $hashedPassword = null;
        if (isset($validated['PASSWORD'])) {
            $hashedPassword = Hash::make($validated['PASSWORD']);
            $validated['PASSWORD'] = $hashedPassword;
        }

        // --- SINKRONISASI UPDATE ---
        DB::beginTransaction();
        try {
            // 1. Update data Karyawan
            $karyawan->update($validated);

            // 2. Update data User
            $userUpdateData = [
                'name' => $validated['NAMA'] ?? $karyawan->NAMA,
                'role' => $validated['ROLE'] ?? $karyawan->ROLE,
            ];

            if ($hashedPassword) {
                $userUpdateData['password'] = $hashedPassword;
            }

            User::where('email', $email)->update($userUpdateData);
            
            DB::commit();
            return response()->json($karyawan, 200);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Gagal mengupdate Karyawan/User: " . $e->getMessage());
            return response()->json(['error' => 'Gagal mengupdate data karyawan dan user.'], 500);
        }
    }

    public function destroy($email)
    {
        // --- SINKRONISASI DELETE ---
        DB::beginTransaction();
        try {
            // 1. Hapus data User terlebih dahulu
            User::where('email', $email)->delete();

            // 2. Hapus data Karyawan
            $karyawanDeleted = Karyawan::destroy($email);
            
            DB::commit();
            return response()->json(['success' => 'Karyawan dan user berhasil dihapus.'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Gagal menghapus Karyawan/User: " . $e->getMessage());
            return response()->json(['error' => 'Gagal menghapus data karyawan dan user.'], 500);
        }
    }
}