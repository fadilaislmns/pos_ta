<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfitAnalysisController extends Controller
{
    public function index(Request $request)
    {
        $year = (int) ($request->year ?? now()->year);
        $fromMonth = (int) ($request->from_month ?? 1);
        $toMonth = (int) ($request->to_month ?? 12);

        // VALIDASI
        if ($fromMonth < 1) $fromMonth = 1;
        if ($toMonth > 12) $toMonth = 12;
        $startDate = Carbon::create($year, $fromMonth, 1)
            ->startOfMonth();
        $endDate = Carbon::create($year, $toMonth, 1)
            ->endOfMonth();
        // SALES
        $sales = Sale::whereBetween('sale_date', [
            $startDate,
            $endDate
        ])->get();
        // PURCHASES
        $purchases = Purchase::whereBetween('purchase_date', [
            $startDate,
            $endDate
        ])->get();
        // TOTAL
        $totalRevenue = $sales->sum('total_amount');
        $totalCost = $purchases->sum('total');
        $totalProfit = $totalRevenue - $totalCost;
        $profitMargin = $totalRevenue > 0
            ? ($totalProfit / $totalRevenue) * 100
            : 0;

        // CHART
        $chartLabels = [];
        $chartRevenue = [];
        $chartCost = [];
        $chartProfit = [];
        $monthlySummary = [];

        for ($m = $fromMonth; $m <= $toMonth; $m++) {
            $monthName = Carbon::create()
                ->month($m)
                ->translatedFormat('F');
            $revenue = Sale::whereYear('sale_date', $year)
                ->whereMonth('sale_date', $m)
                ->sum('total_amount');
            $cost = Purchase::whereYear('purchase_date', $year)
                ->whereMonth('purchase_date', $m)
                ->sum('total');
            $profit = $revenue - $cost;
            $chartLabels[] = $monthName;
            $chartRevenue[] = $revenue;
            $chartCost[] = $cost;
            $chartProfit[] = $profit;
            $monthlySummary[] = [
                'month' => $monthName,
                'revenue' => $revenue,
                'cost' => $cost,
                'profit' => $profit,
            ];
        }
        return view('laporan.analisis_profit.index', compact(
            'year',
            'fromMonth',
            'toMonth',
            'totalRevenue',
            'totalCost',
            'totalProfit',
            'profitMargin',
            'chartLabels',
            'chartRevenue',
            'chartCost',
            'chartProfit',
            'monthlySummary'
        ));
    }

    public function exportPdf(Request $request)
    {
        $year = $request->year ?? now()->year;
        $startMonth = (int) ($request->from_month ?? 1);
        $endMonth = (int) ($request->to_month ?? 12);

        // 1. FILTER DATA (Ini sudah benar)
        $sales = Sale::whereYear('sale_date', $year)
            ->whereMonth('sale_date', '>=', $startMonth)
            ->whereMonth('sale_date', '<=', $endMonth)
            ->get();

        $purchases = Purchase::whereYear('purchase_date', $year)
            ->whereMonth('purchase_date', '>=', $startMonth)
            ->whereMonth('purchase_date', '<=', $endMonth)
            ->get();

        // 2. HITUNG TOTAL BERDASARKAN FILTER (Gunakan $sales dan $purchases di atas)
        $totalRevenue = $sales->sum('total_amount');
        $totalExpense = $purchases->sum('total');
        $totalProfit = $totalRevenue - $totalExpense;
        $marginProfit = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;

        // 3. RINGKASAN BULANAN (Wajib dibatasi rentang bulan)
        $monthlyData = [];
        for ($month = (int)$startMonth; $month <= $endMonth; $month++) {

            // PENTING: Gunakan tahun yang difilter ($year) dan bulan yang sedang di-loop
            $monthlyRevenue = Sale::whereYear('sale_date', $year)
                ->whereMonth('sale_date', $month)
                ->sum('total_amount');

            $monthlyExpense = Purchase::whereYear('purchase_date', $year)
                ->whereMonth('purchase_date', $month)
                ->sum('total');

            $monthlyProfit = $monthlyRevenue - $monthlyExpense;
            $monthlyMargin = $monthlyRevenue > 0 ? ($monthlyProfit / $monthlyRevenue) * 100 : 0;

            $monthlyData[] = [
                'month' => Carbon::create($year, $month, 1)->translatedFormat('F'),
                'revenue' => $monthlyRevenue,
                'expense' => $monthlyExpense,
                'profit' => $monthlyProfit,
                'margin' => $monthlyMargin
            ];
        }
        $pdf = Pdf::loadView('laporan.analisis_profit.pdf', compact(
            'year',
            'startMonth',
            'endMonth',
            'totalRevenue',
            'totalExpense',
            'totalProfit',
            'marginProfit',
            'monthlyData'
        ))->setPaper('A4', 'landscape');

        return $pdf->download('laporan-profit.pdf');
    }
}
