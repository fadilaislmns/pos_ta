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
                function ($q) use ($request) {
                    $q->where(
                        'customer_name',
                        'like',
                        '%' . $request->customer . '%'
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
                function ($q) use ($request) {
                    $q->whereDate(
                        'sale_date',
                        $request->date
                    );
                }
            );
        }

        $perPage = $request->query('per_page', 10);

        $debts = $query->latest()->paginate($perPage)->withQueryString();

        return view(
            'transaksi.customer_debt.index',
            compact('debts')
        );
    }

    // BAYAR HUTANG
    public function pay(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);

        $debt = CustomerDebt::with('sale')->findOrFail($id);

        $payAmount = $request->amount;

        // Tidak boleh melebihi sisa hutang
        if ($payAmount > $debt->amount) {
            return response()->json([
                'success' => false,
                'message' => 'Nominal pembayaran melebihi sisa hutang.'
            ], 422);
        }

        // Tambah total pembayaran pada transaksi
        $debt->sale->paid_amount += $payAmount;
        $debt->sale->save();

        // Kurangi sisa hutang
        $debt->amount -= $payAmount;

        // Jika lunas
        if ($debt->amount == 0) {
            $debt->status = 'paid';
        }

        $debt->save();

        return response()->json([
            'success' => true,
            'message' => $debt->status == 'paid'
                ? 'Hutang telah lunas.'
                : 'Pembayaran berhasil disimpan.',
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
            'sale.details.product',
            'sale.user'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $debt
        ]);
    }
}
