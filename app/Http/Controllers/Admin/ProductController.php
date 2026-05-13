<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $query = Product::with('category');
        $products = $query->paginate(10)->withQueryString();
        $categories = Category::all();

        return view('master.product.index', compact('products', 'categories'));
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

        Product::create($request->all());

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
