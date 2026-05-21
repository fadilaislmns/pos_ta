<?php

namespace App\Http\Controllers\Admin;

use App\Models\CustomerDebt;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CustomerDebtController extends Controller
{
    // GET ALL
    public function index(Request $request)
    {
        $query = CustomerDebt::with('sale.customer');

        // FILTER STATUS
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // FILTER JATUH TEMPO
        if ($request->due_date) {
            $query->whereDate('due_date', $request->due_date);
        }

        // FILTER CUSTOMER
        if ($request->customer_id) {
            $query->whereHas('sale', function ($q) use ($request) {
                $q->where('customer_id', $request->customer_id);
            });
        }

        $debts = $query->get();

        return response()->json([
            'success' => true,
            'data' => $debts
        ]);
    }

    public function view(Request $request)
    {
        $query = CustomerDebt::with([
            'sale.customer'
        ]);

        // SEARCH CUSTOMER
        if ($request->customer) {
            $query->whereHas(
                'sale.customer',
                function($q) use ($request){
                    $q->where(
                        'customer_name',
                        'like',
                        '%'.$request->customer.'%'
                    );
                }
            );
        }

        // FILTER STATUS
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // FILTER TANGGAL
        if ($request->date) {
            $query->whereHas(
                'sale',
                function($q) use ($request){
                    $q->whereDate(
                        'sale_date',
                        $request->date
                    );
                }
            );
        }

        $debts = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'transaksi.customer_debt.index',
            compact('debts')
        );
    }
    
    // BAYAR HUTANG
    public function pay($id)
    {
        $debt = CustomerDebt::findOrFail($id);
        $debt->update([
            'status' => 'paid',
            'amount' => 0
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Hutang berhasil dilunasi',
            'data' => $debt
        ]);
    }

    public function total()
    {
        $total = CustomerDebt::where('status', 'active')->sum('amount');

        return response()->json([
            'success' => true,
            'total_piutang' => $total
        ]);
    }

    public function detail($id)
    {
        $debt = CustomerDebt::with([
            'sale.customer',
            'sale.details.product'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $debt
        ]);
    }
}
