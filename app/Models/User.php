<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property \App\Models\Role $role
 */
class User extends Authenticatable
{
    use HasFactory;
    use HasApiTokens;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'role_id',
        'name',
        'email',
        'password',
        'status'
    ];

    protected $hidden = ['password'];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}

