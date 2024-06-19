<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Story;
use App\Models\Topic;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class StoryController extends Controller
{
    public function index(Request $request)
    {
        $records = Story::published()
            ->when($request->has('keyword'), function (Builder $query) use ($request) {
                $locale = app()->getLocale();
                $query->where("title->{$locale}", 'ilike', '%' . $request->get('keyword') . '%');
            })
            ->when(! empty($request->get('topic')), function (Builder $query) use ($request) {
                $query->where('topic_id', $request->get('topic'));
            })
            ->get()->sortByDesc('updated_at');
        $topics = Topic::all();
        return view('guest.story.index', compact('records', 'topics'));
    }

    public function show(Story $story, Request $request)
    {
        if ($story->published || $request->user()) {
            return view('guest.story.show', compact('story'));
        }
        abort(404);
    }
}
