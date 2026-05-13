<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = [
        'product_id',
        'current_stock',
        'safety_stock',
        'reorder_point'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
