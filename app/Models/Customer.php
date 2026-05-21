<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{

    protected $fillable = [
        'customer_name',
        'phone'
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class, 'customer_id');
    }
    public function debts()
    {
        return $this->hasMany(CustomerDebt::class, 'customer_id');
    }


}
