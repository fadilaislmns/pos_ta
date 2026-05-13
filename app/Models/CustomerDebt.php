<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerDebt extends Model
{
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'sale_id',
        'amount',
        'status',
        'due_date'
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }
}
