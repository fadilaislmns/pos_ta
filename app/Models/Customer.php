<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'customer_name',
        'phone'
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class, 'customer_id');
    }

    
}
