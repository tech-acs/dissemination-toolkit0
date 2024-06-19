<?php

namespace Database\Seeders;

use App\Models\Dimension;
use Illuminate\Database\Seeder;

class DimensionsSeeder extends Seeder
{
    public function run(): void
    {
        $dimensions = [
            ['name' => 'Sex', 'table_name' => 'sex', 'for' => ['population_facts']],
            ['name' => 'Five year age group', 'table_name' => 'five_year_age_group', 'for' => ['population_facts']],
            ['name' => 'Area of residence', 'table_name' => 'area_of_residence', 'for' => ['population_facts', 'housing_facts']],
            ['name' => 'Tenure of household', 'table_name' => 'tenure_of_household', 'for' => ['housing_facts']],
        ];
        foreach ($dimensions as $dimension) {
            Dimension::create($dimension);
        }
    }
}
