<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login()
    {
        return view('Auth.Login');
    }

    public function login_process(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'max:255']
        ],[
            'email.required' => 'Email tidak boleh kosong',
            'email.email' => 'Email tidak valid',
            'email.max' => 'Email maksimal terdiri dari 255 karakter',
            'password.required' => 'Password tidak boleh kosong',
            'password.string' => 'Password tidak valid',
            'password.max' => 'Password maksimal terdiri dari 255 karakter',
        ]);

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        } else {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return back()->with(['errorData' => 'User tidak ditemukan']);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect(route('login'))->with(['success' => 'Logout berhasil dilakukan']);
    }
}
