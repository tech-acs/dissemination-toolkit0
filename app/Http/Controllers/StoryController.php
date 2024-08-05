<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoryRequest;
use App\Models\Tag;
use App\Models\Topic;
use App\Services\SmartTableColumn;
use App\Services\SmartTableData;
use Illuminate\Http\Request;
use App\Models\Story;
use Illuminate\Support\Facades\Auth;

class StoryController extends Controller
{
    public function index(Request $request)
    {
        /*$records = Auth::user()->stories;
        $records = Story::orderByDesc('updated_at')->get();
        return view('manage.story.index', compact('records'));*/

        return (new SmartTableData(Story::query(), $request))
            ->columns([
                SmartTableColumn::make('title')->sortable()
                    ->setBladeTemplate('{{ $row->title }} @if($row->featured) <x-yes-no value="{{ $row->featured }}" true-label="Featured" /> @endif'),
                SmartTableColumn::make('topic')
                    ->setBladeTemplate('{{ $row->topic?->name }}'),
                SmartTableColumn::make('published_at')->setLabel('Status')
                    ->setBladeTemplate('<x-yes-no value="{{ $row->published }}" true-label="Published" false-label="Draft" />'),
                SmartTableColumn::make('author')
                    ->setBladeTemplate('{{ $row->user->name }}<br><p class="text-xs text-gray-500">(updated {{ $row->updated_at->diffForHumans() }})</p>'),
            ])
            ->searchable(['title'])
            ->sortBy('title')
            //->downloadable()
            ->view('manage.story.index');
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
        $story = Auth::user()->stories()->create($request->only(['title',  'description', 'published', 'featured', 'is_filterable', 'featured_image', 'topic_id']));
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

    public function update(StoryRequest $request, Story $story)
    {
        if ($request->file('image')) {
            $path = $request->file('image')->storeAs('stories', $request->file('image')->getClientOriginalName(), 'public');
            $request->merge(['featured_image' => "storage/$path"]);
        }
        $story->update($request->only(['title', 'description', 'published', 'featured', 'is_filterable', 'featured_image', 'topic_id']));
        $updatedTags = Tag::prepareForSync($request->get('tags'));
        $story->tags()->sync($updatedTags->pluck('id'));
        return redirect()->route('manage.story.index')->withMessage('Story updated');
    }

    public function destroy(Story $story)
    {
        $story->delete();
        return redirect()->route('manage.story.index')->withMessage('Story deleted');
    }
}
