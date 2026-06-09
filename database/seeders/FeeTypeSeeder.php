<?php

namespace Database\Seeders;

use App\Models\FeeType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FeeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FeeType::create([
            'name' => 'Tuition',
            'default_amount' => 5000.00,
        ]);
        FeeType::create([
            'name' => 'Bus',
            'default_amount' => 300.00,
        ]);
        FeeType::create([
            'name' => 'Library Fee',
            'default_amount' => 100.00,
        ]);
        FeeType::create([
            'name' => 'activities',
            'default_amount' => 500.00 ,
        ]);
    }
}
