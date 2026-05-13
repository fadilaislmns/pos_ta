<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Role::create([
            'id' => Str::uuid(),
            'role_name' => 'Admin',
            'description' => 'Administrator Sistem'
        ]);

        Role::create([
            'id' => Str::uuid(),
            'role_name' => 'Kasir',
            'description' => 'Pengelola Transaksi'
        ]);
    }
}
