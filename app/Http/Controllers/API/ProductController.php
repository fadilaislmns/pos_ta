<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    // GET ALL
    public function index()
    {
        try {
            $products = Product::with('category')->get();

            return response()->json([
                'success' => true,
                'message' => 'Data produk berhasil diambil',
                'data' => $products
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // STORE
    public function store(Request $request)
    {
        try {
            $request->validate([
                'product_code' => 'required|unique:products,product_code',
                'product_name' => 'required|string|max:100',
                'unit' => 'required|string|max:50',
                'purchase_price' => 'required|numeric',
                'selling_price' => 'required|numeric',
                'category_id' => 'required|exists:categories,id'
            ]);

            $product = Product::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan',
                'data' => $product
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // SHOW
    public function show($id)
    {
        try {
            $product = Product::with('category')->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Detail produk berhasil diambil',
                'data' => $product
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    // UPDATE (STRICT)
    public function update(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);

            $request->validate([
                'product_code' => 'required|unique:products,product_code,' . $id,
                'product_name' => 'required|string|max:100',
                'unit' => 'required|string|max:50',
                'purchase_price' => 'required|numeric',
                'selling_price' => 'required|numeric',
                'category_id' => 'required|exists:categories,id'
            ]);

            $product->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil diupdate',
                'data' => $product
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // DELETE
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dihapus'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
