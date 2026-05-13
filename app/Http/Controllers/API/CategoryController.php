<?php

namespace App\Http\Controllers\API;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    // GET ALL
    public function index()
    {
        try {
            $categories = Category::all();

            return response()->json([
                'success' => true,
                'message' => 'Data kategori berhasil diambil',
                'data' => $categories
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kategori',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // STORE
    public function store(Request $request)
    {
        try {
            $request->validate([
                'category_name' => 'required|string|max:100|unique:categories,category_name',
                'description' => 'nullable|string'
            ]);

            $category = Category::create([
                'category_name' => $request->category_name,
                'description' => $request->description
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil ditambahkan',
                'data' => $category
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan kategori',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // SHOW
    public function show($id)
    {
        try {
            $category = Category::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Detail kategori berhasil diambil',
                'data' => $category
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    // UPDATE (STRICT)
    public function update(Request $request, $id)
    {
        try {
            $category = Category::findOrFail($id);

            $request->validate([
                'category_name' => 'required|string|max:100|unique:categories,category_name,' . $id,
                'description' => 'nullable|string'
            ]);

            $category->update([
                'category_name' => $request->category_name,
                'description' => $request->description
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil diupdate',
                'data' => $category
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update kategori',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // DELETE
    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil dihapus'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kategori',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
