<?php

namespace App\Models;

use App\Models\PurchaseDetail;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $primaryKey = 'id';

    protected $fillable = [
        'supplier_id',
        'purchase_date',
        'invoice_no',
        'user_id',
        'status',
        'total'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function details()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
