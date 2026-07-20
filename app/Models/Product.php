<?php

namespace App\Models;

use App\Models\ForecastResult;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    protected $fillable = [
        'product_code',
        'category_id',
        'product_name',
        'unit',
        'purchase_price',
        'selling_price'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'product_suppliers');
    }

    public function stock()
    {
        return $this->hasOne(Stock::class, 'product_id');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function forecastResult()
    {
        return $this->hasOne(ForecastResult::class);
    }

    public function purchaseRecommendation()
    {
        return $this->hasOne(PurchaseRecommendation::class);
    }
    public function discount()
    {
        return $this->hasOne(Discount::class);
    }
}
