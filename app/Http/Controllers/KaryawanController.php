<?php
namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class KaryawanController extends Controller
{
    public function index()
    {
        return Karyawan::with(['jabatan','cabang','rombong'])->get();
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'EMAIL' => 'required|email|unique:karyawan,EMAIL|unique:users,email|max:50',
            'NAMA' => 'required|string|max:100',
            'ID_JABATAN' => 'required|exists:jabatan,ID_JABATAN',
            'PASSWORD' => 'required|string|min:6',
            'NO_HP' => 'required|string|max:15',
            'ROLE' => 'required|string|in:Admin,Barista',
            'ID_CABANG' => 'nullable|exists:cabang,ID_CABANG',
            'ID_ROMBONG' => 'nullable|exists:rombong,ID_ROMBONG',
        ]);


    // Jika role Admin, kosongkan lokasi
        if (($validated['ROLE'] ?? '') === 'Admin') {
            $validated['ID_CABANG'] = null;
            $validated['ID_ROMBONG'] = null;
        }


        $hashedPassword = Hash::make($validated['PASSWORD']);
        $validated['PASSWORD'] = $hashedPassword;


        DB::beginTransaction();
        try {
            $karyawan = Karyawan::create($validated);


            User::create([
                'name' => $validated['NAMA'],
                'email' => $validated['EMAIL'],
                'password' => $hashedPassword,
                'role' => $validated['ROLE'],
            ]);


            DB::commit();
            return redirect()->route('employee.index')->with('success', 'Data karyawan berhasil ditambahkan!');


        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Gagal menyimpan Karyawan/User: '.$e->getMessage());
            return redirect()->route('employee.index')->with('error','Gagal menyimpan data karyawan.');
        }
    }
    public function show($email)
    {
        return Karyawan::with(['jabatan','cabang','rombong'])->find($email);
    }


    public function update(Request $request, $email)
    {
        $karyawan = Karyawan::findOrFail($email);
        $rules = [
            'NAMA' => 'sometimes|string|max:100',
            'ID_JABATAN' => 'sometimes|exists:jabatan,ID_JABATAN',
            'NO_HP' => 'sometimes|string|max:15',
            'ROLE' => 'sometimes|string|in:Admin,Barista',
            'PASSWORD' => 'nullable|string|min:6',
            'ID_CABANG' => 'nullable|exists:cabang,ID_CABANG',
            'ID_ROMBONG' => 'nullable|exists:rombong,ID_ROMBONG',
        ];


        $validated = $request->validate($rules);


        // Jika role berubah/terset ke Admin, kosongkan lokasi
        if (isset($validated['ROLE']) && $validated['ROLE'] === 'Admin') {
            $validated['ID_CABANG'] = null;
            $validated['ID_ROMBONG'] = null;
        }


        if (!empty($validated['PASSWORD'])) {
            $validated['PASSWORD'] = Hash::make($validated['PASSWORD']);
        } else {
            unset($validated['PASSWORD']);
        }


        DB::beginTransaction();
        try {
            $karyawan->update($validated);


            $userUpdateData = [
            'name' => $validated['NAMA'] ?? $karyawan->NAMA,
            'role' => $validated['ROLE'] ?? $karyawan->ROLE,
            ];


            if (isset($validated['PASSWORD'])) {
                $userUpdateData['password'] = $validated['PASSWORD'];
            }


            User::where('email', $email)->update($userUpdateData);


            DB::commit();
            return redirect()->route('employee.index')->with('success','Data karyawan berhasil diupdate');


        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Gagal update Karyawan/User: '.$e->getMessage());
            return redirect()->route('employee.index')->with('error','Gagal mengupdate karyawan');
        }
    }


    public function destroy($email)
    {
        DB::beginTransaction();
        try {
            User::where('email', $email)->delete();
            Karyawan::destroy($email);
            DB::commit();


            return redirect()->route('employee.index')->with('success','Karyawan dan user berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Gagal hapus Karyawan/User: '.$e->getMessage());
            return redirect()->route('employee.index')->with('error','Gagal menghapus karyawan');
        }
    }
}

