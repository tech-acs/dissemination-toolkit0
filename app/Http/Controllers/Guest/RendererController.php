<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Story;
use App\Models\Visualization;

class RendererController extends Controller
{
    public function __invoke(Visualization $visualization)
    {
        $story = Story::find(20);
        $view = view('guest.story.show', compact('story'))->render();
        return view('guest.renderer', compact('visualization'));
    }
}
