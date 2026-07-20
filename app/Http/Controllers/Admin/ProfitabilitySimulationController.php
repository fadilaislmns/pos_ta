<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForecastResult;
use App\Models\Product;
use Illuminate\Http\Request;

class ProfitabilitySimulationController extends Controller
{
    public function index(Request $request)
    {
        $query = ForecastResult::with('product');
        if ($request->product_id) {
            $query->where(
                'product_id',
                $request->product_id
            );
        }
        $data = $query->get();
        $products = Product::whereIn(
            'id',
            ForecastResult::pluck('product_id')
        )
            ->orderBy('product_name')
            ->get();

        $profitData = collect();

        foreach ($data as $item) {
            $forecastQty = $item->predicted_qty;
            $revenue = $forecastQty * $item->product->selling_price;
            $cost = $forecastQty * $item->product->purchase_price;
            $profit = $revenue - $cost;
            $margin = $revenue > 0 ? ($profit / $revenue) * 100 : 0;

            $profitData->push([
                'product_name' =>  $item->product->product_name,
                'forecast_qty' => $forecastQty,
                'revenue' => $revenue,
                'cost' => $cost,
                'profit' => $profit,
                'margin' => round($margin, 2)
            ]);
        }

        $totalForecast = $profitData->sum('forecast_qty');
        $totalRevenue = $profitData->sum('revenue');
        $totalCost = $profitData->sum('cost');
        $totalProfit = $profitData->sum('profit');

        //   Chart
        $chartLabels = $profitData->pluck('product_name');
        $revenueChart = $profitData->pluck('revenue');
        $costChart = $profitData->pluck('cost');
        $profitChart = $profitData->pluck('profit');

        return view(
            'forecast.simulation',
            compact(
                'products',
                'profitData',
                'totalForecast',
                'totalRevenue',
                'totalCost',
                'totalProfit',
                'chartLabels',
                'revenueChart',
                'costChart',
                'profitChart'
            )
        );
    }
}
