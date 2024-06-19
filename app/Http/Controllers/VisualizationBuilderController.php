<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;

class VisualizationBuilderController extends Controller
{
    public function __invoke(Request $request)
    {
        $topics = Topic::all();
        return view('manage.visualization.build', compact('topics'));
    }
}
