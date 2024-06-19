<?php

namespace Database\Seeders;

use App\Models\Indicator;
use App\Models\Year;
use Illuminate\Database\Seeder;

class DatasetsSeeder extends Seeder
{
    public function run(): void
    {
        $datasetsByIndicator = [
            'Population' => [
                'name' => 'Population by area of residence, sex and age',
                'description' => 'Population by area of residence, sex and age',
                'fact_table' => 'population_facts',
                'max_area_level' => 0
            ],
            'Prevalence (%) of disability among the elderly' => [
                'name' => 'Prevalence (%) of disability among the elderly by geography, sex and residence',
                'description' => 'Prevalence (%) of disability among the elderly by geography, sex and residence',
                'fact_table' => 'population_facts',
                'max_area_level' => 2
            ],
            'Private households (Number)' => [
                'name' => 'Private households (Number) by province, district and residence',
                'description' => 'Private households (Number) by province, district and residence',
                'fact_table' => 'housing_facts',
                'max_area_level' => 2
            ],
            'Tenure of household (%)' => [
                'name' => 'Distribution of the private housing units by tenure of household by province and district',
                'description' => 'Distribution of the private housing units by tenure of household by province and district',
                'fact_table' => 'housing_facts',
                'max_area_level' => 2
            ],
        ];
        foreach ($datasetsByIndicator as $indicatorName => $dataset) {
            $indicator = Indicator::where('name->en', $indicatorName)->first();
            if ($indicator) {
                $dataset = $indicator->datasets()->create($dataset);
                $dataset->years()->sync(Year::whereCode('2022')->pluck('id')->all());
            }
        }
    }
}
