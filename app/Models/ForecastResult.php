<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

class ForecastResult extends Model
{
     protected $fillable = [
        'product_id',
        'forecast_period',
        'predicted_qty'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
