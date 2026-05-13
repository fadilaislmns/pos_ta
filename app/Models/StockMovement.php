<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id',
        'movement_type',
        'quantity',
        'source'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
