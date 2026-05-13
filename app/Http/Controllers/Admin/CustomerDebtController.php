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
    // BAYAR HUTANG
    public function pay($id)
    {
        $debt = CustomerDebt::findOrFail($id);

        $debt->update([
            'status' => 'paid'
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
}
