<?php

namespace Database\Seeders;

use App\Models\Period;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $periods = [
            ['name'=> 'session 1', 'start_time' => '08:00', 'end_time' => '08:45'],
            ['name'=> 'session 2', 'start_time' => '08:46', 'end_time' => '09:30'],
            ['name'=> 'brack', 'start_time' => '09:31', 'end_time' => '09:45'],
            ['name'=> 'session 3', 'start_time' => '09:46', 'end_time' => '10:30'],
            ['name'=> 'session 4', 'start_time' => '10:31', 'end_time' => '11:15'],
            ['name'=> 'brack', 'start_time' => '11:16', 'end_time' => '11:30'],
            ['name'=> 'session 5', 'start_time' => '11:31', 'end_time' => '12:15'],
            ['name'=> 'session 6', 'start_time' => '12:16', 'end_time' => '13:00'],
        ];

        foreach ($periods as $period) {
            Period::create($period);
        }
    }
}
