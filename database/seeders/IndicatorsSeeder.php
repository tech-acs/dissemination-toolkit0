<?php

namespace Database\Seeders;

use App\Models\Topic;
use Illuminate\Database\Seeder;

class IndicatorsSeeder extends Seeder
{
    public function run(): void
    {
        $topics = [
            'Population' => [
                ['name' => 'Population', 'description' => 'Population count'],
                ['name' => 'Households', 'description' => 'Number of households'],
                ['name' => 'Average household size', 'description' => 'Average household size'],
                ['name' => 'Sex ratio', 'description' => 'Number of males per 100 females'],
                ['name' => 'Population density', 'description' => 'Population density'],
                ['name' => 'Households population', 'description' => 'Population in non-institution households'],
            ],
            'Education' => [

            ],
            'Household living conditions' => [

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
