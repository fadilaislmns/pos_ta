<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SaleController extends Controller
{
    // GET LIST (INDEX)
    public function index(Request $request)
{
    $query = Sale::with(['user', 'customer']);

    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('id', 'like', '%' . $request->search . '%')
              ->orWhereHas('customer', function ($q2) use ($request) {
                  $q2->where('customer_name', 'like', '%' . $request->search . '%');
              });
        });
    }

    if ($request->filled('start_date') && $request->filled('end_date')) {
        $query->whereBetween('sale_date', [
            $request->start_date,
            $request->end_date
        ]);
    }

    if ($request->filled('payment_type')) {
        $query->where('payment_type', $request->payment_type);
    }

    $limit = $request->limit ?? 10;

    $sales = $query->latest()->paginate($limit);

    return response()->json($sales);
}
    // STORE (TRANSAKSI)
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'sale_date' => 'required|date',
                'payment_type' => 'required|in:cash,credit',
                'user_id' => 'required|exists:users,id',
                'customer_id' => 'nullable|exists:customers,id',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1'
            ]);

            $total = 0;
            $details = [];

            foreach ($request->items as $item) {

                $product = Product::findOrFail($item['product_id']);

                $stock = Stock::where('product_id', $item['product_id'])->first();

                if (!$stock || $stock->current_stock < $item['quantity']) {
                    throw new \Exception("Stok tidak cukup untuk produk {$product->name}");
                }

                $subtotal = $product->selling_price * $item['quantity'];

                $total += $subtotal;

                $details[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal
                ];
            }

            // GENERATE INVOICE
            $invoice = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(4));

            // SIMPAN SALE
            $sale = Sale::create([
                'sale_date' => $request->sale_date,
                'total_amount' => $total,
                'payment_type' => $request->payment_type,
                'user_id' => $request->user_id,
                'customer_id' => $request->customer_id,
            ]);

            // SIMPAN DETAIL + UPDATE STOK
            foreach ($details as $d) {

                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'product_id' => $d['product']->id,
                    'quantity' => $d['quantity'],
                    'selling_price' => $d['product']->selling_price,
                    'subtotal' => $d['subtotal']
                ]);

                // KURANGI STOK
                $stock = Stock::where('product_id', $d['product']->id)->first();
                $stock->decrement('current_stock', $d['quantity']);

                // LOG MOVEMENT
                StockMovement::create([
                    'product_id' => $d['product']->id,
                    'movement_type' => 'OUT',
                    'quantity' => $d['quantity'],
                    'source' => 'SALE #' . $sale->id
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil',
                'data' => $sale->load('details.product')
            ], 201);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Transaksi gagal',
                'error' => $e->getMessage()
            ], 500);
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
