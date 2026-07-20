<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'supplier_name',
        'lead_time',
        'MOQ'
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_suppliers');
    }
}
