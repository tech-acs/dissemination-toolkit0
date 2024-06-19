<?php

namespace Database\Seeders;

use App\Models\Topic;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $topics = [
            ['name' => 'Geographical', 'description' => 'Population characteristics such as urban/rural residence, density, etc.'],
            ['name' => 'Migration', 'description' => 'Population characteristics concerning both internal and international migration such as in, out and net migration, citizenship, year of arrival, etc.'],
            ['name' => 'Household and family', 'description' => 'Population characteristics relating to family composition, household size, etc. sex, age, marital status'],
            ['name' => 'Demographic and social', 'description' => 'Population characteristics relating to sex, age, marital status, etc.'],
            ['name' => 'Fertility and mortality', 'description' => 'Population characteristics dealing with births, deaths, etc.'],
            ['name' => 'Education', 'description' => 'Population characteristics such as literacy, school attendance, educational attainment and related.'],
            ['name' => 'Economic', 'description' => 'Population characteristics dealing with employment, occupations, industry, etc.'],
            ['name' => 'Disability', 'description' => 'Population characteristics related to various types of disabilities.'],
            ['name' => 'Housing', 'description' => 'Housing characteristics having to do with living quarters, occupancy, ownership, rooms, water supply, toilets, cooking fuel, lighting, sewage, etc.'],
        ];
        foreach ($topics as $topic) {
            Topic::create(['name' => $topic['name'], 'description' => $topic['description']]);
        }
    }
}
