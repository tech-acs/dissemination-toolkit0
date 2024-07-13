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
            ['name' => 'Population', 'description' => 'Population characteristics such as urban/rural, sex, age, density, etc.'],
            ['name' => 'Education', 'description' => 'Population characteristics such as literacy, school attendance, educational attainment and related.'],
            ['name' => 'Household living conditions', 'description' => 'Housing characteristics having to do with living quarters, occupancy, ownership, rooms, water supply, toilets, cooking fuel, lighting, sewage, etc.'],

            ['name' => 'Migration', 'description' => 'Population characteristics concerning both internal and international migration such as in, out and net migration, citizenship, year of arrival, etc.'],
            ['name' => 'Fertility and mortality', 'description' => 'Population characteristics dealing with births, deaths, etc.'],
            ['name' => 'Disability', 'description' => 'Population characteristics related to various types of disabilities.'],
        ];
        foreach ($topics as $topic) {
            Topic::create(['name' => $topic['name'], 'description' => $topic['description']]);
        }
    }
}
