<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndicatorRequest;
use App\Models\Indicator;
use App\Models\Topic;
use App\Services\SmartTableColumn;
use App\Services\SmartTableData;
use Illuminate\Http\Request;

class IndicatorController extends Controller
{
    public function index(Request $request)
    {
        return (new SmartTableData(Indicator::query()->with('topics'), $request))
            ->columns([
                SmartTableColumn::make('name')->sortable(),
                SmartTableColumn::make('topic')->setLabel('Topic')
                    ->setBladeTemplate('{{ $row?->topics?->pluck("name")->join(", ") }}'),
            ])
            ->editable('manage.indicator.edit')
            ->deletable('manage.indicator.destroy')
            ->searchable(['name', 'description'])
            ->sortBy('name')
            ->downloadable()
            ->view('manage.indicator.index');
    }

    public function create()
    {
        $topics = Topic::pluck('name', 'id');
        return view('manage.indicator.create', compact('topics'));
    }

    public function store(IndicatorRequest $request)
    {
        Indicator::create($request->only(['name', 'description', 'topic_id']));
        return redirect()->route('manage.indicator.index')->withMessage('Record created');
    }

    public function edit(Indicator $indicator)
    {
        $topics = Topic::pluck('name', 'id');
        return view('manage.indicator.edit', compact('indicator', 'topics'));
    }

    public function update(Indicator $indicator, IndicatorRequest $request)
    {
        $indicator->update($request->only(['name', 'description']));
        $indicator->topics()->sync($request->get('topics'));
        return redirect()->route('manage.indicator.index')->withMessage('Record updated');
    }
    public function destroy(Indicator $indicator)
    {
        //Check if the indicator has any related datasets
        if ($indicator->datasets()->count() > 0) {
            return redirect()->back()->withErrors(['This indicator has related datasets, please delete those first.']);
        }
        $indicator->delete();
        return redirect()->route('manage.indicator.index')->withMessage('Record deleted');
    }
}
