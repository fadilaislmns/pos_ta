<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
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

    public function stock()
    {
        return $this->hasOne(Stock::class, 'product_id');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }
}
