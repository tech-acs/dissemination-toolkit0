<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use App\Models\Dimension;
use App\Models\Indicator;
use App\Models\Story;
//use App\Models\Visualization;

class LandingController extends Controller
{
    public function index()
    {
        $datasetSummary = [
            'datasets' => Dataset::count(),
            'indicators' => Indicator::count(),
            'dimensions' => Dimension::count(),
            'visualizations' => 0, //Visualization::published()->count(),
            'data_stories' => Story::published()->count(),
        ];
        $featuredStories = Story::published()
            ->featured()
            ->orderBy('updated_at')
            ->take(config('dissemination.featured_stories'))->get();
        return view('guest.landing', compact('datasetSummary', 'featuredStories'));
    }
}
