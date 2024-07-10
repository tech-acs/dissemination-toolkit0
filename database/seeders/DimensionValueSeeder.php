<?php

namespace Database\Seeders;

use App\Models\Dimension;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DimensionValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dimensions = [
            'sex' => [
                ['code' => '_T', 'name' => 'Both sexes'],
                ['code' => 'm', 'name' => 'Male'],
                ['code' => 'f', 'name' => 'Female'],
            ],
            'five_year_age_group' => [
                ['code' => '_T', 'name' => 'Total'],
                ['code' => '0-4', 'name' => '0-4'],
                ['code' => '5-9', 'name' => '5-9'],
                ['code' => '10-14', 'name' => '10-14'],
                ['code' => '15-19', 'name' => '15-19'],
                ['code' => '20-24', 'name' => '20-24'],
                ['code' => '25-29', 'name' => '25-29'],
                ['code' => '30-34', 'name' => '30-34'],
                ['code' => '35-39', 'name' => '35-39'],
                ['code' => '40-44', 'name' => '40-44'],
                ['code' => '45-49', 'name' => '45-49'],
                ['code' => '50-54', 'name' => '50-54'],
                ['code' => '55-59', 'name' => '55-59'],
                ['code' => '60-64', 'name' => '60-64'],
                ['code' => '65-69', 'name' => '65-69'],
                ['code' => '70-74', 'name' => '70-74'],
                ['code' => '75-79', 'name' => '75-79'],
                ['code' => '80-84', 'name' => '80-84'],
                ['code' => '85+', 'name' => '85+'],
            ],
            'area_of_residence' => [
                ['code' => '_T', 'name' => 'Total'],
                ['code' => 'ur', 'name' => 'Urban'],
                ['code' => 'ru', 'name' => 'Rural'],
            ],
            'tenure_of_household' => [
                ['code' => '_T', 'name' => 'Total'],
                ['code' => '1', 'name' => 'Owner'],
                ['code' => '2', 'name' => 'Tenant'],
                ['code' => '3', 'name' => 'Hire purchase'],
                ['code' => '4', 'name' => 'Free lodging'],
                ['code' => '5', 'name' => 'Staff housing'],
                ['code' => '6', 'name' => 'Temporary camp or settlement'],
                ['code' => '7', 'name' => 'Other'],
                ['code' => '8', 'name' => 'Not stated'],
            ],
            'year' => [
                ['code' => '2010', 'name' => '2010'],
                ['code' => '2022', 'name' => '2022'],
            ]
        ];
        foreach ($dimensions as $dimensionTable => $values) {
            if (Schema::hasTable($dimensionTable)) {
                foreach ($values as $value) {
                    DB::table($dimensionTable)->insert($value);
                }
            } else {
                dump("Dimension table $dimensionTable does not exist");
            }
        }
    }
}
