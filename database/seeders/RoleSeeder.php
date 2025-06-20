<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'slug' => 'admin',
            ],
            [
                'name' => 'agent',
                'slug' => 'agent',
            ],
            [
                'name' => 'cashier',
                'slug' => 'cashier',
            ]
        ];

        foreach ($roles as $key => $role) {
            Role::updateOrCreate($role);
        }
        
    }
}
