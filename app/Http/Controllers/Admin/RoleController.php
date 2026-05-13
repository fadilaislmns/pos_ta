<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    // GET ALL
    public function index()
    {
        try {
            $roles = Role::all();

            return response()->json([
                'success' => true,
                'message' => 'Data role berhasil diambil',
                'data' => $roles
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data role',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // STORE
    public function store(Request $request)
    {
        try {
            $request->validate([
                'role_name' => 'required|string|max:100|unique:roles,role_name',
                'description' => 'nullable|string',
                'status' => 'required|in:active,inactive'
            ]);

            $role = Role::create([
                'role_name' => $request->role_name,
                'description' => $request->description,
                'status' => $request->status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Role berhasil ditambahkan',
                'data' => $role
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan role',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // SHOW
    public function show($id)
    {
        try {
            $role = Role::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Detail role berhasil diambil',
                'data' => $role
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Role tidak ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        try {
            $role = Role::findOrFail($id);

            $request->validate([
                'role_name' => 'required|string|max:100|unique:roles,role_name,' . $id,
                'description' => 'nullable|string',
                'status' => 'required|in:active,inactive'
            ]);

            $role->update([
                'role_name' => $request->role_name,
                'description' => $request->description,
                'status' => $request->status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Role berhasil diupdate',
                'data' => $role
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update role',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    // DELETE
    public function destroy($id)
    {
        try {
            $role = Role::findOrFail($id);
            $role->delete();

            return response()->json([
                'success' => true,
                'message' => 'Role berhasil dihapus'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus role',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
