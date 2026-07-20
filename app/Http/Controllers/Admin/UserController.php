<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // GET ALL
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $users = User::with('role')
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('users.index', compact('users'));
    }

    public function toggleStatus($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->status = $user->status == 'active' ? 'inactive' : 'active';
            $user->save();
            return response()->json([
                'success' => true,
                'status' => $user->status
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    // STORE
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:100',
                'email' => 'required|email|unique:users,email',
                'role_id' => 'required|exists:roles,id',
                'password' => 'required|min:6',
                'status' => 'required|in:active,inactive'
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'role_id' => $request->role_id,
                'password' => Hash::make($request->password),
                'status' => $request->status
            ]);

            session()->flash('success_message', 'Data user berhasil ditambahkan!');

            return response()->json([
                'success' => true,
                'message' => 'User berhasil ditambahkan',
                'data' => $user
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // METHOD CEK EMAIL REALTIME
    public function checkEmail(Request $request)
    {
        $exists = User::where('email', $request->email)->exists();

        if ($exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email sudah terdaftar.'
            ]);
        }

        return response()->json(['status' => 'success']);
    }

    // MENAMPILKAN HALAMAN EDIT USER
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();

        return view('users.edit', compact('user', 'roles'));
    }

    // UPDATE DATA
    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:100',
                'email' => 'required|email|unique:users,email,' . $id,
                'role_id' => 'required|exists:roles,id',
                'status' => 'required|in:active,inactive'
            ]);

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'role_id' => $request->role_id,
                'status' => $request->status
            ]);

            session()->flash('success_message', 'Data user berhasil diupdate!');

            return response()->json([
                'success' => true,
                'message' => 'User berhasil diupdate',
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // RESET PASSWORD
    public function resetPassword(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $request->validate([
                'password' => 'required|min:6|confirmed'
            ]);

            $user->update([
                'password' => Hash::make($request->password)
            ]);

            session()->flash('success_message', 'Password berhasil diubah.');

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil direset'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal reset password',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::findOrFail(Auth::id());

        // Cek password lama
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Password saat ini salah.'
            ]);
        }

        // Cek password baru tidak boleh sama dengan password lama
        if (Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'Password baru tidak boleh sama dengan password lama.'
            ]);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password)
        ]);
        return back()->with(
            'success',
            'Password berhasil diperbarui.'
        );
    }
}
