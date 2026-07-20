<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForecastDetail extends Model
{
    protected $fillable = [
        'product_id',
        'forecast_date',
        'forecast_qty'
    ];

    protected $casts = ['forecast_date' => 'date'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
