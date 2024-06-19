<?php

namespace Database\Seeders;

use App\Models\Year;
use Illuminate\Database\Seeder;

class YearValueSeeder extends Seeder
{
    public function run(): void
    {
        Year::create(['name' => '2022', 'code' => '2022']);
        Year::create(['name' => '2010', 'code' => '2010']);
    }
}
