<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Topic;
use App\Services\SmartTableColumn;
use App\Services\SmartTableData;
use Illuminate\Http\Request;
use App\Models\Visualization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VisualizationController extends Controller
{
    public function index(Request $request)
    {
        return (new SmartTableData(Visualization::query(), $request))
            ->columns([
                SmartTableColumn::make('title')->sortable(),
                SmartTableColumn::make('type')
                    ->setBladeTemplate('{{ ucfirst($row->type) }} @if ($row->is_filterable) <span class="text-green-700" title="Filterable by geography"><x-icon.filter /></span> @endif'),
                SmartTableColumn::make('published_at')->setLabel(__('Published'))
                    ->setBladeTemplate('<x-yes-no value="{{ $row->published }}" />'),
                SmartTableColumn::make('author')
                    ->setBladeTemplate('{{ $row->user->name }}'),
                SmartTableColumn::make('updated_at')->setLabel('Last Updated')->sortable()
                    ->setBladeTemplate('{{ $row->updated_at->format("M j, H:i") }}'),
            ])
            ->searchable(['title'])
            ->sortBy('updated_at')
            ->sortDesc()
            ->view('manage.visualization.index');
    }

    public function edit(Visualization $visualization)
    {
        $tags = Tag::all();
        $topics = Topic::pluck('name', 'id');
        return view("manage.visualization.edit", compact('visualization', 'tags', 'topics'));
    }

    public function update(Request $request, Visualization $visualization)
    {
        $visualization->update($request->only(['title', 'description', 'published', 'is_filterable']));
        $updatedTags = Tag::prepareForSync($request->get('tags', ''));
        $visualization->tags()->sync($updatedTags->pluck('id'));
        $visualization->topics()->sync($request->get('topics'));
        return redirect()->route('manage.visualization.index')
            ->withMessage("The visualization has been updated");
    }

    public function destroy(Visualization $visualization)
    {
        $visualization->delete();
        return redirect()->route('manage.visualization.index')
            ->withMessage("The visualization has been deleted");
    }

    public function upload(Visualization $visualization, Request $request)
    {
        if ($request->hasFile('imageData')) {
            $image = $request->file('imageData');
            $fileName = $visualization->id . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('visualizations\images', $fileName, 'public');
            $imagePath =  asset('storage/' . $imagePath);
            return response()->json(['image_path' => $imagePath]);
        } elseif ($request->has('imageData')) {
            $imageData = $request->get('imageData');
            $base64Image = explode(';base64,', $imageData);
            $image = base64_decode('image/', $base64Image[0]);
            $imageType = $image[1];
            $image_base64 = base64_decode($base64Image[1]);
            $fileName = 'visualization/images/'. $visualization->id . '.png';
            Storage::disk('public')->put($fileName, $image_base64);
            $imagePath =  asset('storage/'. $fileName);
            return response()->json(['image_path' => $imagePath]);
        }
        return response()->json(['message' => 'No image available to upload', 'image_path' => $request->all()]);

    }
}
