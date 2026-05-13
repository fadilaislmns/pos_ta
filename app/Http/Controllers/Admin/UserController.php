<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // GET ALL
    public function index()
    {
        try {
            $users = User::with('role')->get();

            return response()->json([
                'success' => true,
                'message' => 'Data user berhasil diambil',
                'data' => $users
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data user',
                'error' => $e->getMessage()
            ], 500);
        }
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

    // SHOW
    public function show($id)
    {
        try {
            $user = User::with('role')->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Detail user berhasil diambil',
                'data' => $user
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    // UPDATE
    public function update(Request $request, $id)
{
    try {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $id,
            'role_id' => 'required|exists:roles,id',
            'password' => 'nullable|min:6',
            'status' => 'required|in:active,inactive'
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'status' => $request->status
        ];

        // 🔥 kalau password diisi → update
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

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
    // DELETE
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus user',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
