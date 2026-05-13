<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'user_id',
        'customer_id',
        'sale_date',
        'total_amount',
        'payment_type'
    ];

    public function details()
    {
        return $this->hasMany(SaleDetail::class, 'sale_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function debt()
    {
        return $this->hasOne(CustomerDebt::class, 'sale_id');
    }
}
