<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // TOTAL PENJUALAN
        $totalSales = Sale::sum('total_amount');
        // TOTAL PEMBELIAN
        $totalPurchases = Purchase::sum('total');
        // PROFIT
        $profit = 0;
        $saleDetails = SaleDetail::with('product')->get();
        foreach ($saleDetails as $detail) {
            $modal = $detail->product->purchase_price * $detail->quantity;
            $jualan = $detail->subtotal;
            $profit += ($jualan - $modal);
        }
        // TOTAL USER
        $totalUsers = User::count();
        // TRANSAKSI TERBARU
        $latestSales = Sale::latest()->take(5)->get();
        // PENJUALAN BULAN INI
        $currentMonthSales = Sale::whereMonth('sale_date', now()->month)->sum('total_amount');

        // PENJUALAN BULAN LALU
        $lastMonthSales = Sale::whereMonth('sale_date', now()->subMonth()->month)->sum('total_amount');

        // PERSENTASE KENAIKAN
        $salesGrowth = 0;
        if ($lastMonthSales > 0) {
            $salesGrowth = (($currentMonthSales - $lastMonthSales) / $lastMonthSales) * 100;
        }
        // PENJUALAN BULANAN
        // FILTER BULAN
        $months = request('months', 6);

        // CHART LABEL & DATA
        $chartLabels = [];
        $chartSales = [];

        // LOOP BULAN
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);

            // LABEL
            $chartLabels[] = $date->translatedFormat('M');

            // TOTAL SALES
            $chartSales[] = Sale::whereYear('sale_date', $date->year)
                ->whereMonth('sale_date', $date->month)
                ->sum('total_amount');
        }

        // PEMBELIAN BULAN INI
        $currentMonthPurchases = Purchase::whereMonth('purchase_date', now()->month)->sum('total');

        // PEMBELIAN BULAN LALU
        $lastMonthPurchases = Purchase::whereMonth('purchase_date', now()->subMonth()->month)->sum('total');

        // GROWTH PEMBELIAN
        $purchaseGrowth = 0;
        if ($lastMonthPurchases > 0) {
            $purchaseGrowth = (($currentMonthPurchases - $lastMonthPurchases) / $lastMonthPurchases) * 100;
        }

        // PROFIT BULAN INI
        $currentMonthProfit = $currentMonthSales - $currentMonthPurchases;

        // PROFIT BULAN LALU
        $lastMonthProfit = $lastMonthSales - $lastMonthPurchases;

        // GROWTH PROFIT
        $profitGrowth = 0;
        if ($lastMonthProfit != 0) {
            $profitGrowth = (($currentMonthProfit - $lastMonthProfit) / abs($lastMonthProfit)) * 100;
        }

        // CATEGORY CHART
        $categories = Category::withCount('products')->get();
        $categoryLabels = $categories->pluck('category_name');
        $categoryTotals = $categories->pluck('products_count');

        return view(
            'dashboard.index',
            compact(
                'totalSales',
                'totalPurchases',
                'profit',
                'totalUsers',
                'latestSales',
                'salesGrowth',
                'chartLabels',
                'chartSales',
                'months',
                'purchaseGrowth',
                'profitGrowth',
                'categoryLabels',
                'categoryTotals'
            )
        );
    }
}
