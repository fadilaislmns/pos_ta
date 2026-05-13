<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'category_name',
        'description'
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}
