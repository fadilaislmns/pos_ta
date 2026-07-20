<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForecastResult;
use App\Models\Product;
use Illuminate\Http\Request;

class PurchaseRecommendationController extends Controller
{
    public function index(Request $request)
    {
        $query = ForecastResult::with([
            'product.stock',
            'product.suppliers',
            'product.purchaseRecommendation'
        ]);

        if ($request->product_id) {
            $query->where('product_id', $request->product_id);
        }

        $data = $query->get();
        $data = $data->sortByDesc(function ($item) {
            return $item->product->purchaseRecommendation->recommendation_qty ?? 0;
        });
        $totalForecast = $data->sum('predicted_qty');
        $avgForecast = round($data->avg('predicted_qty'));
        $totalProduk = $data->count();
        $lastUpdate = ForecastResult::max('updated_at');
        // Total rekomendasi pembelian (jumlah unit)
        $totalRecommendationQty = $data->sum(function ($item) {
            return $item->product->purchaseRecommendation->recommendation_qty ?? 0;
        });
        // Jumlah produk yang direkomendasikan dibeli
        $totalRecommendedProducts = $data->filter(function ($item) {
            return ($item->product->purchaseRecommendation->recommendation_qty ?? 0) > 0;
        })->count();
        $products = Product::whereIn('id', $data->pluck('product_id'))
            ->orderBy('product_name')
            ->get();
        return view(
            'forecast.recommendation',
            compact(
                'data',
                'products',
                'totalForecast',
                'avgForecast',
                'totalProduk',
                'lastUpdate',
                'totalRecommendationQty',
                'totalRecommendedProducts'
            )
        );
    }

    public function generateForecast(Request $request)
    {
        $pythonFile = base_path('python/forecast.py');

        exec(
            'python "' . $pythonFile . '" 2>&1',
            $output,
            $status
        );

        if ($status !== 0) {
            return back()->with(
                'error',
                implode("\n", $output)
            );
        }

        if ($request->source == 'recommendation') {
            return redirect()
                ->route('forecast.recommendation')
                ->with(
                    'success',
                    'Forecast berhasil diperbarui'
                );
        }

        return redirect()
            ->route('forecast.index')
            ->with(
                'success',
                'Forecast berhasil diperbarui'
            );
    }
}
