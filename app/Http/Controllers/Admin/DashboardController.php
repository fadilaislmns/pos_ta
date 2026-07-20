<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CustomerDebt;
use App\Models\ForecastResult;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Role;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Stock;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $role = Auth::user()->role->role_name;

        if ($role == 'super_admin') {
            $totalUsers = User::count();
            $totalRoles = Role::count();
            $totalAdmins = User::whereHas('role', function ($q) {
                $q->where('role_name', 'admin');
            })->count();
            $totalCashiers = User::whereHas('role', function ($q) {
                $q->where('role_name', 'cashier');
            })->count();
            $activeUsers = User::where('status', 'active')->count();
            $inactiveUsers = User::where('status', 'inactive')->count();
            $latestUsers = User::latest()->take(5)->get();
            return view('dashboard.super-admin', compact(
                'totalUsers',
                'totalRoles',
                'totalAdmins',
                'totalCashiers',
                'activeUsers',
                'inactiveUsers',
                'latestUsers'
            ));
        }

        if ($role == 'cashier') {

            // TRANSAKSI HARI INI MILIK KASIR LOGIN
            $todaySales = Sale::where('user_id', Auth::id())
                ->whereDate('sale_date', today())
                ->count();

            // PENDAPATAN HARI INI MILIK KASIR LOGIN
            $todayRevenue = Sale::where('user_id', Auth::id())
                ->whereDate('sale_date', today())
                ->sum('total_amount');

            // TOTAL TRANSAKSI
            $totalTransactions = Sale::where(
                'user_id',
                Auth::id()
            )->count();

            // TOTAL PENJUALAN
            $cashierSales = Sale::where(
                'user_id',
                Auth::id()
            )->sum('total_amount');

            // TOTAL PIUTANG CUSTOMER
            $customerDebts = CustomerDebt::where(
                'status',
                'active'
            )->count();

            // TRANSAKSI TERBARU MILIK KASIR LOGIN
            $latestSales = Sale::with('customer')
                ->where('user_id', Auth::id())
                ->latest()
                ->take(5)
                ->get();

            // STOK MENIPIS
            $lowStocks = Stock::with('product')
                ->whereColumn('current_stock', '<=', 'reorder_point')
                ->limit(5)
                ->get();

            return view('dashboard.cashier', compact(
                'todaySales',
                'todayRevenue',
                'latestSales',
                'lowStocks',
                'totalTransactions',
                'cashierSales',
                'customerDebts'
            ));
        }

        if ($role == 'admin') {
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
            // TOTAL produk
            $totalProducts = Product::count();
            // TRANSAKSI TERBARU
            $latestSales = Sale::with([
                'customer',
                'user'
            ])
                ->latest()
                ->take(5)
                ->get();
            // PENJUALAN BULAN INI
            $currentMonthSales = Sale::whereMonth(
                'sale_date',
                now()->month
            )->sum('total_amount');
            // PENJUALAN BULAN LALU
            $lastMonthSales = Sale::whereMonth(
                'sale_date',
                now()->subMonth()->month
            )->sum('total_amount');
            // PERSENTASE KENAIKAN
            $salesGrowth = 0;
            if ($lastMonthSales > 0) {
                $salesGrowth = (($currentMonthSales - $lastMonthSales) / $lastMonthSales) * 100;
            }
            // FILTER BULAN
            $months = request('months', 6);
            // CHART
            $chartLabels = [];
            $chartSales = [];
            for ($i = $months - 1; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $chartLabels[] = $date->translatedFormat('M');

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

            // STOK PRODUK TERENDAH
            $lowStocks = Stock::with('product')
                ->orderBy('current_stock', 'asc')
                ->take(5)
                ->get();

            // RINGKASAN FORECAST & REKOMENDASI PEMBELIAN
            $forecastAlerts = ForecastResult::with([
                'product.purchaseRecommendation'
            ])
                ->get()
                ->map(function ($item) {
                    $recommendation =
                        $item->product->purchaseRecommendation->recommendation_qty ?? 0;
                    return (object)[
                        'product_name'   => $item->product->product_name,
                        'forecast'       => $item->predicted_qty,
                        'recommendation' => $recommendation
                    ];
                });

            // TOTAL PRODUK YANG MEMILIKI REKOMENDASI PEMBELIAN
            $totalRecommendation = $forecastAlerts
                ->where('recommendation', '>', 0)
                ->count();

            return view(
                'dashboard.admin',
                compact(
                    'totalSales',
                    'totalPurchases',
                    'profit',
                    'totalProducts',
                    'latestSales',
                    'salesGrowth',
                    'chartLabels',
                    'chartSales',
                    'months',
                    'purchaseGrowth',
                    'profitGrowth',
                    'categoryLabels',
                    'categoryTotals',
                    'lowStocks',
                    'forecastAlerts',
                    'totalRecommendation',
                )
            );
        }

        abort(403);
    }
}
