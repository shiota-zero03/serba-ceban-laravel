<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('Pages.Profile.index', compact(['user']));
    }

    public function store(Request $request)
    {
        $user = User::findOrFail(Auth::user()->id);
        $this->__rules($request);
        try {
            DB::beginTransaction();

            if($user->email !== $request->email) {
                $checkEmail = User::where('email', $request->email)->first();
                if($checkEmail) return back()->with(['errorData' => 'Email sudah pernah digunakan']);
            }

            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->whatsapp,
            ];

            if($request->password) {
                $data['password'] = Hash::make($request->password);
            }

            if($user->role == 'MITRA') {
                $data['nama_bank'] = $request->bank_name;
                $data['nomor_rekening'] = $request->rekening;
                $data['atas_nama'] = $request->atas_nama;
            }

            $user->update($data);

            DB::commit();
            return back()->with(['success' => 'Data berhasil diperbarui']);
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with(['errorData' => $th->getMessage()]);
        }
    }

    public function __rules(Request $request)
    {
        $message = [
            'email.required' => 'Email tidak boleh kosong',
            'email.email' => 'Email tidak valid',
            'email.unique' => 'Email sudah pernah digunakan',
            'email.max' => 'Email maksimal terdiri dari 255 karakter',
            'password.required' => 'Password tidak boleh kosong',
            'password.string' => 'Password tidak valid',
            'password.max' => 'Password maksimal terdiri dari 255 karakter',
            'name.required' => 'Nama mitra tidak boleh kosong',
            'name.string' => 'Nama mitra tidak valid',
            'name.max' => 'Nama mitra maksimal terdiri dari 255 karakter',
            'whatsapp.required' => 'Whatsapp tidak boleh kosong',
            'whatsapp.string' => 'Whatsapp tidak valid',
            'whatsapp.max' => 'Whatsapp maksimal terdiri dari 255 karakter'
        ];

        return $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'whatsapp' => ['required', 'string', 'max:255'],
        ],$message);
    }
}
