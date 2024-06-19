<?php

namespace App\Http\Controllers;

use App\Models\Subtopic;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

class TopicController extends Controller
{
    public function index()
    {
        $records = Topic::withCount(['indicators', 'visualizations', 'stories'])->get();
        return view('manage.topic.index', compact('records'));
    }

    public function create()
    {
        //$subtopics = Subtopic::all();
        return view('manage.topic.create');
    }

    public function store(Request $request)
    {
        $topic = Topic::create($request->only(['name',  'description']));
        return redirect()->route('manage.topic.index')->withMessage('Record created');
    }

    public function edit(Topic $topic, Request $request)
    {
        //$subtopics = Subtopic::all();
        return view('manage.topic.edit', compact('topic'));
    }

    public function update(Topic $topic, Request $request)
    {
        $topic->update($request->only(['name', 'description']));
        //$topic->indicators()->sync($request->get('indicators', []));
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
