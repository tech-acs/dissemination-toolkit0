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
            'name' => 'My Organization',
            'website' => '#',
            'email' => 'support@example.org',
            'logo_path' => '',
            'slogan' => 'Making data accessible to everyone.',
            'blurb' => "Our goal is to help you <strong>find</strong> the data you need, <strong>explore</strong> it in depth, and create <strong>visualizations</strong> that help you to understand the data.",
            'hero_image_path' => 'images/hero.svg',
            'social_media' => [
                'twitter' => 'https://twitter.com/my_org',
                'facebook' => 'https://facebook.com/my_org',
                'instagram' => 'https://instagram.com/my_org',
                'linkedin' => 'https://linkedin.com/my_org',
            ],
            'address' => "742 Evergreen Terrace<br>Good Stats Drive<br>Tel: +251 123345679",
        ]);
    }
}
