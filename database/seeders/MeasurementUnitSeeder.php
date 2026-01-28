<?php

namespace Database\Seeders;

use App\Models\MeasurementUnit;
use Illuminate\Database\Seeder;

class MeasurementUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MeasurementUnit::create(['name' => 'Kilogram', 'symbol' => 'Kg']);
        MeasurementUnit::create(['name' => 'Meter', 'symbol' => 'm']);
        MeasurementUnit::create(['name' => 'Centimeter', 'symbol' => 'cm']);
        MeasurementUnit::create(['name' => 'Number of Units', 'symbol' => 'Units']);
    }
}
