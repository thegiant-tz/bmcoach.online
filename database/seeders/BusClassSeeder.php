<?php

namespace Database\Seeders;

use App\Models\BusClass;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BusClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = ['LUXURY', 'VIP', 'VVIP'];
        foreach ($classes as $key => $class) {
            BusClass::updateOrCreate([
                'name' => $class
            ]);
        }
        
    }
}
