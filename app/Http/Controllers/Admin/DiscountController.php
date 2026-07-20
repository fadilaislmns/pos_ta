<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\Product;
use Illuminate\Http\Request;

class DiscountController extends Controller
{

    public function index(Request $request)
    {
        $query = Discount::with('product');
        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }
        if ($request->filled('search')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where(
                    'product_name',
                    'like',
                    '%' . $request->search . '%'
                );
            });
        }
        $discounts = $query->latest()->paginate(10);
        $products = Product::orderBy('product_name')
            ->get();

        $availableProducts = Product::whereDoesntHave('discount')
            ->orderBy('product_name')
            ->get();

        return view(
            'master.discounts.index',
            compact(
                'discounts',
                'products',
                'availableProducts'
            )
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|unique:discounts,product_id',
            'discount_type' => 'required|in:percent,nominal',
            'discount_value' => 'required|numeric|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'nullable'
        ]);
        if (
            $request->discount_type == 'percent' &&
            $request->discount_value > 100
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Diskon persen maksimal 100%'
            ], 422);
        }

        Discount::create([
            'product_id' => $request->product_id,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->is_active
        ]);


        return response()->json([
            'success' => true,
            'message' => 'Diskon berhasil ditambahkan'
        ]);
    }

    public function edit($id)
{
    $discount = Discount::with('product')
        ->findOrFail($id);

    return response()->json($discount);
}

    public function update(Request $request, $id)
    {
        $discount = Discount::findOrFail($id);
        $request->validate([
            'product_id' => 'required|unique:discounts,product_id,' . $discount->id,
            'discount_type' => 'required|in:percent,nominal',
            'discount_value' => 'required|numeric|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);
        if (
            $request->discount_type == 'percent' &&
            $request->discount_value > 100
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Diskon persen maksimal 100%'
            ], 422);
        }

        $discount->update([
            'product_id' => $request->product_id,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->is_active
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Diskon berhasil diperbarui'
        ]);
    }

    public function destroy($id)
    {
        $discount = Discount::findOrFail($id);
        $discount->delete();
        return response()->json([
            'success' => true,
            'message' => 'Diskon berhasil dihapus'
        ]);
    }

    public function show($id)
    {
        $discount = Discount::with('product')
            ->findOrFail($id);

        return response()->json($discount);
    }

    public function toggle($id)
    {
        $discount = Discount::findOrFail($id);
        $discount->update([
            'is_active' => !$discount->is_active
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status diskon berhasil diubah'
        ]);
    }
}
