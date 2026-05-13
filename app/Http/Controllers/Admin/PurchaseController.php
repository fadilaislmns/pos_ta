<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    // public function index()
    // {
    //     $purchases = Purchase::with('supplier')->orderBy('purchase_date', 'desc')->get();
    //     $products = Product::all();
    //     $suppliers = Supplier::all();

    //     return view('transaksi.purchase.index', compact('purchases', 'products', 'suppliers'));
    // }
    public function index(Request $request)
    {
        $query = Purchase::with('supplier');

        // SEARCH (nama supplier / tanggal)
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('purchase_date', 'like', '%' . $request->search . '%')
                    ->orWhereHas('supplier', function ($qq) use ($request) {
                        $qq->where('supplier_name', 'like', '%' . $request->search . '%');
                    });
            });
        }

        // FILTER SUPPLIER
        if ($request->supplier_id) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // FILTER TANGGAL
        if ($request->date_from && $request->date_to) {
            $query->whereBetween('purchase_date', [
                $request->date_from,
                $request->date_to
            ]);
        }

        // URUTAN TERBARU
        $purchases = $query
            ->orderBy('purchase_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $suppliers = Supplier::all();
        $products = Product::all();

        return view('transaksi.purchase.index', compact('purchases', 'suppliers', 'products'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required',
            'purchase_date' => 'required|date',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required',
            'products.*.qty' => 'required|numeric|min:1',
            'products.*.price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $purchase = Purchase::create([
                'supplier_id' => $request->supplier_id,
                'purchase_date' => $request->purchase_date,
                'total' => 0
            ]);

            $total = 0;

            foreach ($request->products as $item) {

                $subtotal = $item['qty'] * $item['price'];

                // SIMPAN DETAIL
                PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                ]);

                // ambil / buat stock
                $stock = Stock::firstOrCreate(
                    ['product_id' => $item['product_id']],
                    [
                        'current_stock' => 0,
                        'safety_stock' => 2,
                        'reorder_point' => 5
                    ]
                );

                // TAMBAH STOCK
                $stock->increment('current_stock', $item['qty']);

                // CATAT MOVEMENT
                StockMovement::create([
                    'product_id' => $item['product_id'],
                    'movement_type' => 'IN',
                    'quantity' => $item['qty'],
                    'source' => 'PURCHASE #' . $purchase->id
                ]);

                $total += $subtotal;
            }

            $purchase->update([
                'total' => $total
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Purchase berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollback();

            return back()->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $purchase = Purchase::with(['supplier', 'details.product'])->findOrFail($id);

        return view('transaksi.purchase.detail', compact('purchase'));
    }

    public function destroy($id)
    {
        Purchase::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Pesanan pembelian berhasil dihapus');
    }
}
