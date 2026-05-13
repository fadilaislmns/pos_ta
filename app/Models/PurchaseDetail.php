<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = [
        'purchase_id',
        'product_id',
        'qty',
        'price'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
