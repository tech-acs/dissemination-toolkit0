<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use App\Models\Visualization;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class VisualizationController extends Controller
{
    public function index(Request $request)
    {
        //order by desc updated_at
        $records = Visualization::published()
            ->when($request->has('keyword'), function (Builder $query) use ($request) {
                $locale = app()->getLocale();
                $query->where("title->{$locale}", 'ilike', '%' . $request->get('keyword') . '%');
            })
            ->when(! empty($request->get('topic')), function (Builder $query) use ($request) {
                $query->where('topic_id', $request->get('topic'));
            })
            ->get()->sortByDesc('updated_at');
        $topics = Topic::all();
        return view('guest.visualization.index', compact('records', 'topics'));
    }

    public function show(Visualization $visualization, Request $request)
    {
        if ($visualization->published || $request->user()) {
            if ($request->has('embed')) {
                return view('guest.visualization.embed', compact('visualization'));
            }
            return view('guest.visualization.show', compact('visualization'));
        }
        abort(403);
    }
}
