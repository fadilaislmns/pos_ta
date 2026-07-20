<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SalePageController extends Controller
{
    // INDEX (LIST PENJUALAN)
    public function index()
    {
        $users = User::with('role')
            ->whereHas('role', function ($q) {
                $q->where('role_name', 'cashier');
            })
            ->orderBy('name')
            ->get();

        return view(
            'transaksi.sale.index',
            compact('users')
        );
    }

    // CREATE (POS TRANSAKSI)
    public function create()
    {
        $user = Auth::user();

        if (!$user || !$user->role) {
            abort(403);
        }

        if ($user->role->role_name !== 'cashier') {
            abort(403, 'Hanya kasir yang dapat membuat transaksi penjualan');
        }
        $products = Product::with('stock', 'discount')
            ->get()
            ->map(function ($product) {
                $discount = $product->discount;
                return [
                    'id' => $product->id,
                    'category_id' => $product->category_id,
                    'product_name' => $product->product_name,
                    'selling_price' => $product->selling_price,
                    'discount_type' => $discount->discount_type ?? null,
                    'discount_value' => $discount->discount_value ?? 0,
                    'discount_start' => $discount->start_date ?? null,
                    'discount_end' => $discount->end_date ?? null,
                    'discount_active' => $discount->is_active ?? 0,
                    'current_stock' => $product->stock->current_stock ?? 0,
                    'reorder_point' => $product->stock->reorder_point ?? 0
                ];
            });

        $customers = Customer::select('id', 'customer_name')->get();
        $users = User::select('id', 'name')->get();
        $categories = Category::select('id', 'category_name')->get();

        // GENERATE INVOICE
        $invoiceNo =
            'INV-' .
            now()->format('Ymd') .
            '-' .
            str_pad(
                Sale::count() + 1,
                4,
                '0',
                STR_PAD_LEFT
            );

        return view(
            'transaksi.sale.create',
            compact(
                'products',
                'customers',
                'users',
                'categories',
                'invoiceNo'
            )
        );
    }

    public function edit($id)
    {
        $user = Auth::user();

        if ($user->role->role_name != 'admin') {
            abort(
                403,
                'Hanya admin yang dapat mengubah transaksi'
            );
        }
        $sale = Sale::with('details.product')->findOrFail($id);
        $products = Product::all();

        return view(
            'transaksi.sale.edit',
            compact('sale', 'products')
        );
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();

        if ($user->role->role_name != 'admin') {
            abort(
                403,
                'Hanya admin yang dapat mengubah transaksi'
            );
        }
        DB::beginTransaction();
        try {

            $sale = Sale::with('details')
                ->findOrFail($id);

            foreach ($sale->details as $detail) {
                $stock = Stock::where(
                    'product_id',
                    $detail->product_id
                )->first();
                if ($stock) {
                    $stock->increment(
                        'current_stock',
                        $detail->quantity
                    );
                }
            }

            SaleDetail::where('sale_id', $sale->id)->delete();
            $total = 0;
            $products = $request->input('products', []);

            foreach ($products as $item) {
                $product = Product::findOrFail($item['product_id']);
                $qty = $item['quantity'];
                $subtotal = $qty * $product->selling_price;
                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'selling_price' => $product->selling_price,
                    'subtotal' => $subtotal
                ]);

                $stock = Stock::where(
                    'product_id',
                    $product->id
                )->first();

                if ($stock) {
                    $stock->decrement(
                        'current_stock',
                        $qty
                    );
                }
                $total += $subtotal;
            }

            $changeAmount =
                max(
                    0,
                    $sale->paid_amount - $total
                );

            $sale->update([
                'sale_date' => $request->sale_date,
                'total_amount' => $total,
                'change_amount' => $changeAmount
            ]);

            DB::commit();

            return redirect()
                ->route('sales.show', $sale->id)
                ->with(
                    'success',
                    'Transaksi berhasil diperbarui'
                );
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with(
                'error',
                $e->getMessage()
            );
        }
    }

    // DETAIL PENJUALAN
    public function show($id)
    {
        $sale = Sale::with([
            'details.product',
            'customer',
            'user',
            'debt'
        ])->findOrFail($id);

        return view('transaksi.sale.detail', compact('sale'));
    }

    public function receipt($id)
    {
        $sale = Sale::with([
            'details.product',
            'customer',
            'user',
            'debt'
        ])->findOrFail($id);

        return view(
            'transaksi.sale.receipt',
            compact('sale')
        );
    }
}
