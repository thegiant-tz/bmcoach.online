<?php

namespace Database\Seeders;

use App\Models\BusLayout;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BusLayoutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $layouts = [
            [
                'label' => '2x2',
                'name' => '2x2',
                'capacity' => '57'
            ],
            [
                'label' => '2x2_toilet',
                'name' => '2x2',
                'capacity' => '47'
            ],
            [
                'label' => '2x2_toilet_full',
                'name' => '2x2',
                'capacity' => '49'
            ],
            [
                'label' => '2x1',
                'name' => '2x1',
                'capacity' => '35'
            ]
        ];
        foreach ($layouts as $key => $layout) {
            BusLayout::updateOrCreate(
                [
                    'label' => $layout['label']
                ],
                $layout
            );
        }
    }
}
