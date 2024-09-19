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
        return (new SmartTableData(Story::query(), $request))
            ->columns([
                SmartTableColumn::make('title')->sortable()
                    ->setBladeTemplate('{{ $row->title }} <x-icon.featured class="text-amber-600 -mt-2" :value="$row->featured" />'),
                SmartTableColumn::make('topic')
                    ->setBladeTemplate('{{ $row->topics?->pluck("name")->join(", ") }}'),
                SmartTableColumn::make('published_at')->setLabel('Status')
                    ->setBladeTemplate('<x-yes-no value="{{ $row->published }}" true-label="Published" false-label="Draft" />'),
                SmartTableColumn::make('author')
                    ->setBladeTemplate('{{ $row->user->name }}'),
                SmartTableColumn::make('updated_at')->setLabel('Last Updated')->sortable()
                    ->setBladeTemplate('{{ $row->updated_at->format("M j, H:i") }}'),
            ])
            ->searchable(['title'])
            ->sortBy('updated_at')
            ->view('manage.story.index');
    }

    public function create()
    {
        $tags = Tag::all();
        $topics = Topic::pluck('name', 'id');
        $templates = collect(); //(new StoryTemplateStore())->getAll();
        $story = (new Story());
        return view('manage.story.create', compact('tags', 'topics', 'templates', 'story'));
    }

    public function store(StoryRequest $request)
    {
        if ($request->file('image')) {
            $path = $request->file('image')->storeAs('stories', $request->file('image')->getClientOriginalName(), 'public');
            $request->merge(['featured_image' => "storage/$path"]);
        }
        $story = Auth::user()->stories()->create($request->only(['title',  'description', 'published', 'featured', 'is_filterable', 'featured_image']));
        $story->update(['html' => '']);//(new StoryTemplateStore)->get($request->get('template_id'))->getHtml()]);
        $updatedTags = Tag::prepareForSync($request->get('tags', ''));
        $story->tags()->sync($updatedTags->pluck('id'));
        $story->topics()->sync($request->get('topics'));
        return redirect()->route('manage.story.index')->withMessage('Story created');
    }

    public function edit(Story $story)
    {
        $tags = Tag::all();
        $topics = Topic::pluck('name', 'id');
        return view('manage.story.edit', compact('story', 'tags', 'topics'));
    }

    public function update(StoryRequest $request, Story $story)
    {
        if ($request->file('image')) {
            $path = $request->file('image')->storeAs('stories', $request->file('image')->getClientOriginalName(), 'public');
            $request->merge(['featured_image' => "storage/$path"]);
        }
        $story->update($request->only(['title', 'description', 'published', 'featured', 'is_filterable', 'featured_image']));
        $updatedTags = Tag::prepareForSync($request->get('tags'));
        $story->tags()->sync($updatedTags->pluck('id'));
        $story->topics()->sync($request->get('topics'));
        return redirect()->route('manage.story.index')->withMessage('Story updated');
    }

    public function destroy(Story $story)
    {
        $story->delete();
        return redirect()->route('manage.story.index')->withMessage('Story deleted');
    }
}
