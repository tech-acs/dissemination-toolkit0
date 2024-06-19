<?php

namespace Database\Seeders;

use App\Models\Topic;
use Illuminate\Database\Seeder;

class IndicatorsSeeder extends Seeder
{
    public function run(): void
    {
        $topics = [
            'Geographical' => [],
            'Migration' => [],
            'Household and family' => [
                ['name' => 'Population', 'description' => 'Population count']
            ],
            'Demographic and social' => [],
            'Fertility and mortality' => [],
            'Education' => [],
            'Economic' => [],
            'Disability' => [
                ['name' => 'Prevalence (%) of disability among the elderly', 'description' => 'Prevalence (%) of disability among the elderly']
            ],
            'Housing' => [
                ['name' => 'Private households (Number)', 'description' => 'Private households (Number)'],
                ['name' => 'Tenure of household (%)', 'description' => 'Distribution of the private housing units by tenure of household']
            ],
        ];
        foreach ($topics as $topicName => $indicators) {
            $topic = Topic::where('name->en', $topicName)->first();
            if ($topic) {
                foreach ($indicators as $indicator) {
                    $topic->indicators()->create($indicator);
                }
            }
        }
    }
}
