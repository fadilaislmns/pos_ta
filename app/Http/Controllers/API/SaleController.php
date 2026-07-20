<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CustomerDebt;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SaleController extends Controller
{
    // GET LIST (INDEX)
    public function index(Request $request)
    {
        $query = Sale::with(['user', 'customer']);

        // CASHIER HANYA DATA DIA
        if (Auth::user()->role->role_name == 'cashier') {
            $query->where('user_id', Auth::id());
        }

        // SEARCH
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('invoice_no', 'like', '%' . $request->search . '%')
                    ->orWhereHas('customer', function ($q2) use ($request) {
                        $q2->where(
                            'customer_name',
                            'like',
                            '%' . $request->search . '%'
                        );
                    });
            });
        }

        // FILTER TANGGAL
        if (
            $request->filled('start_date')
            && $request->filled('end_date')
        ) {
            $query->whereBetween('sale_date', [
                $request->start_date,
                $request->end_date
            ]);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // FILTER METODE
        if ($request->filled('payment_type')) {
            $query->where(
                'payment_type',
                $request->payment_type
            );
        }

        $limit = $request->limit ?? 10;

        $sales = $query
            ->latest()
            ->paginate($limit);

        return response()->json($sales);
    }

    // STORE (TRANSAKSI)
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            // VALIDATION
            $request->validate([
                'sale_date' => 'required|date',
                'payment_type' => 'required|in:cash,qris,card,credit',
                'user_id' => 'required|exists:users,id',
                'customer_id' => 'nullable|exists:customers,id',
                'discount' => 'nullable|numeric|min:0',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1'

            ]);

            // CREDIT WAJIB CUSTOMER
            if (
                $request->payment_type == 'credit'
                && !$request->customer_id
            ) {
                return response()->json([
                    'success' => false,
                    'message' =>
                    'Customer wajib dipilih untuk pembayaran hutang'
                ], 422);
            }
            $total = 0;
            $details = [];

            // HITUNG TOTAL
            foreach ($request->items as $item) {
                $product = Product::with('discount')->findOrFail(
                    $item['product_id']
                );
                $stock = Stock::where(
                    'product_id',
                    $item['product_id']
                )->first();
                // VALIDASI STOK
                if (
                    !$stock ||
                    $stock->current_stock < $item['quantity']
                ) {
                    throw new \Exception(
                        "Stok tidak cukup untuk produk {$product->product_name}"
                    );
                }

                $discountAmount = 0;
                $finalPrice = $product->selling_price;
                $discount = $product->discount;
                if (
                    $discount &&
                    $discount->is_active &&
                    now()->between(
                        $discount->start_date,
                        $discount->end_date
                    )
                ) {

                    if ($discount->discount_type == 'percent') {
                        $discountAmount = ($product->selling_price * $discount->discount_value) / 100;
                    } else {
                        $discountAmount = $discount->discount_value;
                    }
                    $finalPrice = $product->selling_price - $discountAmount;
                }

                $subtotal = $finalPrice * $item['quantity'];
                $total += $subtotal;
                $details[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal,
                    'discount_amount' => $discountAmount,
                    'selling_price' => $finalPrice
                ];
            }

            // DISKON
            $discount = $request->discount ?? 0;
            $totalAfterDiscount = $total - $discount;
            if ($totalAfterDiscount < 0) {
                $totalAfterDiscount = 0;
            }

            // GENERATE INVOICE
            $invoice =
                'INV-' .
                date('Ymd') .
                '-' .
                strtoupper(Str::random(4));

            // CREATE SALE
            $sale = Sale::create([
                'sale_date' => $request->sale_date,
                'invoice_no' => $invoice,
                'discount' => $discount,
                'total_amount' => $totalAfterDiscount,
                'payment_type' => $request->payment_type,
                'paid_amount' => $request->paid_amount ?? 0,
                'change_amount' => $request->payment_type == 'cash'
                    ? max(0, ($request->paid_amount ?? 0) - $totalAfterDiscount) : 0,
                'user_id' => $request->user_id,
                'customer_id' => $request->customer_id,

            ]);

            // CREATE SALE DETAIL
            foreach ($details as $d) {
                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'product_id' => $d['product']->id,
                    'quantity' => $d['quantity'],
                    'selling_price' => $d['selling_price'],
                    'discount_amount' => $d['discount_amount'],
                    'subtotal' => $d['subtotal']
                ]);

                // UPDATE STOCK
                $stock = Stock::where(
                    'product_id',
                    $d['product']->id
                )->first();

                $stock->decrement(
                    'current_stock',
                    $d['quantity']
                );

                // STOCK MOVEMENT
                StockMovement::create([
                    'product_id' => $d['product']->id,
                    'movement_type' => 'OUT',
                    'quantity' => $d['quantity'],
                    'source' => 'SALE #' . $sale->id
                ]);
            }

            // CREATE CUSTOMER DEBT
            if ($request->payment_type == 'credit') {
                $paidAmount = $request->paid_amount ?? 0;
                $remainingDebt = $totalAfterDiscount - $paidAmount;

                if ($remainingDebt < 0) {
                    $remainingDebt = 0;
                }

                CustomerDebt::create([
                    'sale_id' => $sale->id,
                    'customer_id' => $request->customer_id,
                    'amount' => $remainingDebt,
                    'status' => $remainingDebt > 0 ? 'active' : 'paid',
                    'due_date' => now()->addDays(30)
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil',
                'data' =>
                $sale->load(
                    'details.product',
                    'customer',
                    'debt'
                )
            ], 201);
        } catch (\Exception $e) {

            DB::rollBack();
            dd($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Transaksi gagal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //Update
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $sale = Sale::with('details')->findOrFail($id);

            //KEMBALIKAN STOK LAMA
            foreach (
                $sale->details as $detail
            ) {
                Stock::where('product_id', $detail->product_id)
                    ->increment('current_stock', $detail->quantity);
            }
            //HAPUS DETAIL LAMA
            $sale->details()->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }

    // DETAIL
    public function show($id)
    {
        $sale = Sale::with(['details.product', 'customer', 'user'])->find($id);

        if (!$sale) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $sale
        ]);
    }

    // DELETE
    public function destroy($id)
    {
        $sale = Sale::find($id);

        if (!$sale) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $sale->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil dihapus'
        ]);
    }
}
