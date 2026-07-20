<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);

        $products = Product::with('category', 'suppliers', 'discount')
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        $categories = Category::all();
        $suppliers = Supplier::all();

        return view('master.product.index', compact('products', 'categories', 'suppliers'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'product_code' => 'required|unique:products,product_code',
                'product_name' => 'required|unique:products,product_name',
                'category_id' => 'required',
                'unit' => 'required',
                'purchase_price' => 'required|numeric',
                'selling_price' => 'required|numeric',
            ], [
                'product_code.unique' => 'Kode produk sudah ada!',
                'product_name.unique' => 'Nama produk sudah ada!',
            ]);

            $product = Product::create([
                'product_code' => $request->product_code,
                'product_name' => $request->product_name,
                'category_id' => $request->category_id,
                'unit' => $request->unit,
                'purchase_price' => $request->purchase_price,
                'selling_price' => $request->selling_price,
            ]);

            $product->suppliers()->attach($request->supplier_id);

            return response()->json([
                'status' => 'success',
                'message' => 'Produk berhasil ditambahkan'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'errors' => $e->validator->errors()
            ], 422);
        } catch (\Exception $e) {

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'product_code' => 'required|unique:products,product_code,' . $id,
            'product_name' => 'required|unique:products,product_name,' . $id,
            'unit' => 'required',
            'purchase_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'category_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $product = Product::findOrFail($id);
        $product->update($request->all());
        $product->suppliers()->sync([
            $request->supplier_id
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Produk berhasil diupdate'
        ]);
    }
    public function destroy($id)
    {
        Product::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Produk berhasil dihapus');
    }
}
