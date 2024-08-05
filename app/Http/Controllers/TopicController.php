<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use App\Services\SmartTableColumn;
use App\Services\SmartTableData;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

class TopicController extends Controller
{
    public function index(Request $request)
    {
        /*$records = Topic::withCount(['indicators', 'visualizations', 'stories'])->get();
        return view('manage.topic.index', compact('records'));*/

        return (new SmartTableData(Topic::query()->withCount(['indicators', 'visualizations', 'stories']), $request))
            ->columns([
                SmartTableColumn::make('name')->sortable(),
                SmartTableColumn::make('coverage')->setLabel('Coverage')
                    ->setBladeTemplate('{{ $row?->indicators_count }} indicators, {{ $row?->visualizations_count }} visualizations, {{ $row?->stories_count }} stories'),
            ])
            ->editable('manage.topic.edit')
            ->deletable('manage.topic.destroy')
            ->searchable(['name', 'description'])
            ->sortBy('name')
            ->downloadable()
            ->view('manage.topic.index');
    }

    public function create()
    {
        return view('manage.topic.create');
    }

    public function store(Request $request)
    {
        Topic::create($request->only(['name',  'description']));
        return redirect()->route('manage.topic.index')->withMessage('Record created');
    }

    public function edit(Topic $topic)
    {
        return view('manage.topic.edit', compact('topic'));
    }

    public function update(Topic $topic, Request $request)
    {
        $topic->update($request->only(['name', 'description']));
        return redirect()->route('manage.topic.index')->withMessage('Record updated');
    }

    public function destroy(Topic $topic)
    {
        if ($topic->indicators->count() > 0) {
            return redirect()->back()->withErrors(
                new MessageBag(['There are indicators that belong to this topic and thus can not be deleted. Move the indicators to another topic before trying again.'])
            );
        }
        $topic->delete();
        return redirect()->route('manage.topic.index')->withMessage('Record deleted');
    }
}
