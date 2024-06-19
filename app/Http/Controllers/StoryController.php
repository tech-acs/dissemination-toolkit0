<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoryRequest;
use App\Models\Tag;
use App\Models\Topic;
use App\Service\StoryHtmlDumper;
use App\Service\TemplateStores\StoryTemplateStore;
use Illuminate\Http\Request;
use App\Models\Story;
use Illuminate\Support\Facades\Auth;

class StoryController extends Controller
{
    public function index()
    {
        $records = Auth::user()->stories;
        $records = Story::orderByDesc('updated_at')->get();
        return view('manage.story.index', compact('records'));
    }

    public function create()
    {
        $tags = Tag::all();
        $topics = Topic::all();
        $templates = collect(); //(new StoryTemplateStore())->getAll();
        return view('manage.story.create', compact('tags', 'topics', 'templates'));
    }

    public function store(StoryRequest $request)
    {
        if ($request->file('image')) {
            $path = $request->file('image')->storeAs('stories', $request->file('image')->getClientOriginalName(), 'public');
            $request->merge(['featured_image' => "storage/$path"]);
        }
        $story = Auth::user()->stories()->create($request->only(['title',  'description', 'published', 'featured', 'featured_image', 'topic_id']));
        $story->update(['html' => '']);//(new StoryTemplateStore)->get($request->get('template_id'))->getHtml()]);
        $updatedTags = Tag::prepareForSync($request->get('tags', ''));
        $story->tags()->sync($updatedTags->pluck('id'));
        return redirect()->route('manage.story.index')->withMessage('Story created');
    }

    public function edit(Story $story)
    {
        $tags = Tag::all();
        $topics = Topic::all();
        return view('manage.story.edit', compact('story', 'tags', 'topics'));
    }

    public function update(Request $request, Story $story)
    {
        if ($request->file('image')) {
            $path = $request->file('image')->storeAs('stories', $request->file('image')->getClientOriginalName(), 'public');
            $request->merge(['featured_image' => "storage/$path"]);
        }
        $story->update($request->only(['title', 'description', 'published', 'featured', 'featured_image', 'topic_id']));
        $updatedTags = Tag::prepareForSync($request->get('tags'));
        $story->tags()->sync($updatedTags->pluck('id'));
        StoryHtmlDumper::write($story);
        return redirect()->route('manage.story.index')->withMessage('Story updated');
    }

    public function destroy(Story $story)
    {
        $story->delete();
        return redirect()->route('manage.story.index')->withMessage('Story deleted');
    }
}
