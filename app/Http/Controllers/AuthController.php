<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::with('role')->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah'
            ], 401);
        }

        if ($user->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'User tidak aktif'
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => $user
        ]);
    }

    // LOGOUT
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    // GET USER LOGIN
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()->load('role')
        ]);
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($credentials)) {
            return back()->with(
                'error',
                'Email atau password salah'
            );
        }

        $request->session()->regenerate();
        $user = Auth::user();
        // USER NONAKTIF
        if ($user->status != 'active') {
            Auth::logout();
            return back()->with(
                'error',
                'User tidak aktif'
            );
        }

        // SUPER ADMIN
        if ($user->role->role_name == 'super_admin') {
            return redirect('/dashboard');
        }

        // ADMIN
        if ($user->role->role_name == 'admin') {
            return redirect('/dashboard');
        }

        // CASHIER
        if ($user->role->role_name == 'cashier') {
            return redirect('/dashboard');
        }

        return redirect('/');
    }
}
