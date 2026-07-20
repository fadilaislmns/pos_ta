<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PurchaseReportExport;
use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with([
            'supplier',
            'user'
        ]);

        // FILTER TANGGAL
        if ($request->start_date) {
            $query->whereDate('purchase_date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('purchase_date', '<=', $request->end_date);
        }

        // FILTER SUPPLIER
        if ($request->supplier_id) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // FILTER STATUS
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // SUMMARY
        $totalPurchase = $query->sum('total');
        $totalPO = $query->count();
        $totalItems = $query
            ->withSum('details', 'qty')
            ->get()
            ->sum('details_sum_qty');
        $averagePurchase = $totalPO > 0
            ? $totalPurchase / $totalPO
            : 0;
        $purchases = $query
            ->latest()
            ->paginate(10);
        $suppliers = Supplier::orderBy('supplier_name')->get();

        return view('laporan.pembelian.index', compact(
            'purchases',
            'suppliers',
            'totalPurchase',
            'totalPO',
            'totalItems',
            'averagePurchase'
        ));
    }

    public function exportPdf(Request $request)
    {
        $query = Purchase::with([
            'supplier',
            'user',
            'details.product'
        ]);

        // FILTER TANGGAL
        if ($request->start_date) {
            $query->whereDate('purchase_date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('purchase_date', '<=', $request->end_date);
        }

        // FILTER SUPPLIER
        if ($request->supplier_id) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // FILTER STATUS
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $purchases = $query
            ->latest()
            ->get();

        $totalPurchase = $purchases->sum('total');

        $pdf = Pdf::loadView(
            'laporan.pembelian.pdf',
            [
                'purchases' => $purchases,
                'totalPurchase' => $totalPurchase,
                'request' => $request
            ]
        )->setPaper('a4', 'landscape');

        return $pdf->download(
            'laporan-pembelian.pdf'
        );
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(
            new PurchaseReportExport($request),
            'laporan-pembelian.xlsx'
        );
    }
}
