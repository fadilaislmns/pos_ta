<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForecastResult;
use App\Models\ForecastDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ForecastController extends Controller
{
    public function index(Request $request)
    {
        $query = ForecastResult::with('product');
        if ($request->product_id) {
            $query->where('product_id', $request->product_id);
        }
        $data = $query->get();

        $products = Product::whereIn(
            'id',
            ForecastResult::pluck('product_id')
        )
            ->orderBy('product_name')
            ->get();

        $totalForecast = $data->sum('predicted_qty');
        $avgForecast = round($data->avg('predicted_qty'));
        $totalProduk = $data->count();
        $lastUpdate = ForecastResult::max('updated_at');
        $topProduct = $data
            ->sortByDesc('predicted_qty')
            ->first();

        $chartValues = [];
        $chartDatasets = [];
        if ($request->product_id) {
            $forecastChart = ForecastDetail::where('product_id', $request->product_id)
                ->orderBy('forecast_date')
                ->get();

            $chartLabels = [];
            for ($i = 1; $i <= count($forecastChart); $i++) {
                $chartLabels[] = 'Hari-' . $i;
            }

            $chartValues = $forecastChart
                ->pluck('forecast_qty')
                ->toArray();

            $chartTitle = 'Forecast Kebutuhan Produk 14 Hari';
        } else {
            $forecastChart = ForecastDetail::with('product')
                ->orderBy('forecast_date')
                ->get();
            $chartLabels = [];
            for ($i = 1; $i <= 14; $i++) {
                $chartLabels[] = 'Hari-' . $i;
            }

            $colors = [
                '#0d6efd',
                '#dc3545',
                '#198754',
                '#ffc107',
                '#6f42c1',
                '#fd7e14',
                '#20c997',
                '#e83e8c'
            ];

            $chartDatasets = [];

            foreach ($products as $index => $product) {
                $detail = ForecastDetail::where('product_id', $product->id)
                    ->orderBy('forecast_date')
                    ->pluck('forecast_qty')
                    ->toArray();

                $chartDatasets[] = [
                    'label' => $product->product_name,
                    'data' => $detail,
                    'borderColor' => $colors[$index],
                    'backgroundColor' => $colors[$index],
                    'fill' => false,
                    'tension' => 0.4
                ];
            }
            $chartTitle = 'Forecast Kebutuhan Barang 14 Hari';;
        }
        if ($request->product_id) {
            $chartDatasets = [[
                'label' => $data
                    ->first()
                    ->product
                    ->product_name,
                'data' => $chartValues,
                'borderColor' => '#0d6efd',
                'backgroundColor' => '#0d6efd',
                'fill' => false,
                'tension' => 0.4
            ]];
        }

        return view(
            'forecast.index',
            compact(
                'data',
                'products',
                'totalForecast',
                'avgForecast',
                'totalProduk',
                'lastUpdate',
                'topProduct',
                'chartLabels',
                'chartValues',
                'chartTitle',
                'chartDatasets'
            )
        );
    }
}
