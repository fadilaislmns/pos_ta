<?php

namespace App\Http\Controllers\Admin;

use App\Exports\SalesReportExport;
use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class SaleReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with([
            'customer',
            'user'
        ]);
        if (Auth::user()->role->role_name == 'cashier') {
    $query->where('user_id', Auth::id());
}

        // FILTER TANGGAL
        if ($request->filled('start_date')) {
            $query->whereDate('sale_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('sale_date', '<=', $request->end_date);
        }
        // FILTER CUSTOMER
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        // FILTER METODE
        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }
        // DATA
        $sales = $query
            ->latest()
            ->paginate(10);
        // SUMMARY
        $totalSales = $query->sum('total_amount');
        $totalTransactions = $query->count();
        $averageTransaction = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;

        $totalProducts =
            $query->withSum(
                'details',
                'quantity'
            )
            ->get()->sum('details_sum_quantity');

        $cashiers = User::whereHas('role', function ($q) {
            $q->where('role_name', 'cashier');
        })->get();

        return view(
            'laporan.penjualan.index',
            compact(
                'sales',
                'cashiers',
                'totalSales',
                'totalTransactions',
                'averageTransaction',
                'totalProducts'
            )
        );
    }

    public function exportPdf(Request $request)
    {
        $query = Sale::with([
            'customer',
            'user',
            'details.product'
        ]);
        if (Auth::user()->role->role_name == 'cashier') {
    $query->where('user_id', Auth::id());
}
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->start_date) {
            $query->whereDate(
                'sale_date',
                '>=',
                $request->start_date
            );
        }

        if ($request->end_date) {
            $query->whereDate(
                'sale_date',
                '<=',
                $request->end_date
            );
        }

        if ($request->customer_id) {
            $query->where(
                'customer_id',
                $request->customer_id
            );
        }

        if ($request->payment_type) {
            $query->where(
                'payment_type',
                $request->payment_type
            );
        }

        $sales = $query->latest()->get();

        $totalSales = $sales->sum('total_amount');

        $pdf = Pdf::loadView(
            'laporan.penjualan.pdf',
            compact(
                'sales',
                'totalSales',
                'request'
            )
        )->setPaper('a4', 'landscape');

        return $pdf->stream(
            'laporan-penjualan.pdf'
        );
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(
            new SalesReportExport($request),
            'laporan-penjualan.xlsx'
        );

    }
}
