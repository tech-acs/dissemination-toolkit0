<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndicatorRequest;
use App\Models\Indicator;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IndicatorController extends Controller
{
    public function index()
    {
        $records = Indicator::with('topic')->get();
        return view('manage.indicator.index', compact('records'));
    }

    public function create()
    {
        $topics = Topic::all();
        return view('manage.indicator.create', compact('topics'));
    }

    public function store(Request $request)
    {
        $indicator = Indicator::create($request->only(['name', 'description', 'topic_id']));
        return redirect()->route('manage.indicator.index')->withMessage('Record created');
    }

    public function edit(Indicator $indicator)
    {
        $topics = Topic::all();
        return view('manage.indicator.edit', compact('indicator', 'topics'));
    }

    public function update(Indicator $indicator, IndicatorRequest $request)
    {
        $indicator->update($request->only(['name', 'description', 'topic_id']));
        //$indicator->topic()->associate($request->get('topics', []));
        return redirect()->route('manage.indicator.index')->withMessage('Record updated');
    }
    public function destroy(Indicator $indicator)
    {
        //Check if the indicator has any related datasets
        if ($indicator->datasets()->count() > 0) {
            return redirect()->back()->withErrors(['This indicator has related datasets,delete them first.']);
        }
        $indicator->delete();
        return redirect()->route('manage.indicator.index')->withMessage('Record deleted');
    }
}
