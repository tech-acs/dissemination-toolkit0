<?php

namespace Database\Seeders;

use App\Models\Indicator;
use App\Models\Year;
use Illuminate\Database\Seeder;

class DatasetsSeeder extends Seeder
{
    public function run(): void
    {
        $datasetsWithIndicator = [
            [
                'name' => 'Namibia population, hhs, avg. hh size for 1991 - 2023 censuses',
                'description' => 'Figure 2.1.1: Namibia population, hhs, avg. hh size for 1991 - 2023  censuses',
                'fact_table' => 'population_facts',
                'max_area_level' => 0,
                'indicators' => ['Average household size', 'Households', 'Population'],
                'dimensions' => '',
                'topic' => 'Population'
            ],
            [
                'name' => 'Population by sex and 5 year age group',
                'description' => 'Figure 2.1.4: Population pyramid, Namibia 2023 census',
                'fact_table' => 'population_facts',
                'max_area_level' => 0,
                'indicators' => ['Population'],
                'topic' => 'Population'
            ],
            [
                'name' => 'Private households (Number) by province, district and residence',
                'description' => 'Private households (Number) by province, district and residence',
                'fact_table' => 'housing_facts',
                'max_area_level' => 2
            ],
            [
                'name' => 'Distribution of the private housing units by tenure of household by province and district',
                'description' => 'Distribution of the private housing units by tenure of household by province and district',
                'fact_table' => 'housing_facts',
                'max_area_level' => 2
            ],
        ];
        foreach ($datasetsWithIndicator as $indicatorName => $dataset) {
            $indicator = Indicator::where('name->en', $indicatorName)->first();
            if ($indicator) {
                $dataset = $indicator->datasets()->create($dataset);
                $dataset->years()->sync(Year::whereCode('2022')->pluck('id')->all());
            }
        }
    }
}
