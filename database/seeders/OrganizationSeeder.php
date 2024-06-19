<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Organization::create([
            'name' => 'UN Women',
            'website' => 'https://www.unwomen.org',
            'email' => 'support@unwormen.org',
            'logo_path' => 'images/unwomen.png',
            'slogan' => 'Making data accessible to everyone.',
            'blurb' => "Our goal is to help you <strong>find</strong> the data you need, <strong>explore</strong> it in depth, and create <strong>visualizations</strong> that help you to understand the data.",
            'hero_image_path' => 'images/hero.svg',
            'social_media' => [
                'twitter' => 'https://twitter.com/unwomen',
                'facebook' => 'https://facebook.com/unwomen',
                'instagram' => 'https://instagram.com/unwomen',
                'linkedin' => 'https://linkedin.com/unwomen',
            ],
            'address' => "742 Evergreen Terrace<br>Good Stats Drive<br>Tel: +251 123345679",
        ]);
    }
}
