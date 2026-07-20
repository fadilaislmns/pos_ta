<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

class PurchaseRecommendation extends Model
{
    protected $fillable = [
        'product_id',
        'recommendation_qty'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
