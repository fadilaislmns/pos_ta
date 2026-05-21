<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $query = Stock::with([
            'product',
            'latestMovement'
        ]);

        // SEARCH
        if ($request->search) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('product_name', 'like', '%' . $request->search . '%');
            });
        }

        // FILTER STATUS
        if ($request->status == 'aman') {
            $query->whereColumn('current_stock', '>', 'reorder_point');
        }

        if ($request->status == 'peringatan') {
            $query->whereColumn('current_stock', '<=', 'reorder_point');
        }

        $stocks = $query->paginate(10)->withQueryString();

        return view('inventory.stock.index', compact('stocks'));
    }

    public function show($id)
    {
        $stock = Stock::with([
            'product',
            'latestMovement'
        ])->findOrFail($id);

        // HISTORI MOVEMENT
        $movements = StockMovement::where(
            'product_id',
            $stock->product_id
        )
            ->latest()
            ->take(10)
            ->get();

        return view(
            'inventory.stock.detail',
            compact('stock', 'movements')
        );
    }

    public function update(Request $request, $id)
    {
        $stock = Stock::findOrFail($id);

        $stock->update([
            'safety_stock' => $request->safety_stock,
            'reorder_point' => $request->reorder_point
        ]);

        return back()->with('success', 'Stock updated');
    }


    public function movements(Request $request)
    {
        $query = StockMovement::with('product');
        // SEARCH
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('source', 'like', '%' . $request->search . '%')
                    ->orWhereHas('product', function ($qq) use ($request) {
                        $qq->where('product_name', 'like', '%' . $request->search . '%');
                    });
            });
        }
        // FILTER TYPE
        if ($request->type) {
            $query->where('movement_type', $request->type);
        }

        // FILTER PRODUCT
        if ($request->product_id) {
            $query->where('product_id', $request->product_id);
        }

        // FILTER DATE
        if ($request->date_from && $request->date_to) {
            $query->whereBetween('created_at', [
                $request->date_from,
                $request->date_to
            ]);
        }

        // LIMIT
        $limit = $request->limit ?? 10;

        $movements = $query
            ->latest()
            ->paginate($limit)
            ->withQueryString();

        $products = Product::all();

        return view(
            'inventory.stock.movement',
            compact('movements', 'products')
        );
    }

    public function showMovement($id)
    {
        $movement = StockMovement::with('product')->findOrFail($id);

        return view('inventory.stock.movementDetail', compact('movement'));
    }
}
