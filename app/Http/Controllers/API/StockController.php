<?php

namespace App\Http\Controllers\API;

use App\Models\Stock;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StockController extends Controller
{
    // GET ALL
    public function index()
    {
        $stocks = Stock::with('product')->get();

        return response()->json([
            'success' => true,
            'data' => $stocks
        ]);
    }

    // STORE (INIT STOK)
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id|unique:stocks,product_id',
            'current_stock' => 'required|integer',
            'safety_stock' => 'required|integer',
            'reorder_point' => 'required|integer'
        ]);

        $stock = Stock::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Stok berhasil dibuat',
            'data' => $stock
        ]);
    }

    // UPDATE STOK MANUAL
    public function update(Request $request, $id)
    {
        $stock = Stock::findOrFail($id);

        $request->validate([
            'current_stock' => 'required|integer',
            'safety_stock' => 'required|integer',
            'reorder_point' => 'required|integer'
        ]);

        $stock->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Stok berhasil diupdate',
            'data' => $stock
        ]);
    }

    public function show($id)
{
    try {
        $stock = Stock::with('product')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $stock
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Data tidak ditemukan'
        ], 404);
    }
}
}
