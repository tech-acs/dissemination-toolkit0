<?php

namespace App\Http\Controllers;

use App\Models\Indicator;
use App\Models\Story;
use App\Models\Visualization;
use App\Models\Topic;
use App\Service\StoryHtmlDumper;
use App\Service\VisualizationTypeEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StoryBuilderController extends Controller
{
    public function edit(Story $story)
    {
        $baseUrl = config('app.url');
        //$indicators = Indicator::all();
        $indicators = collect([
            [
                'id' => 5,
                'title' => 'Indicator One',
                'data' => ['x' => [1, 2, 3, 4, 5], 'y' => [1, 2, 4, 8, 16]],
                'layout' => []
            ]
        ]);
        return view('manage.story.builder', compact('story', 'indicators', 'baseUrl'));
    }

    public function update(Request $request, $id)
    {
        $story = Story::find($id);
        $story->update(['html' => html_entity_decode($request->get('data')[0]['story_html'])]);
        $story->update(['gjs_project_data' => html_entity_decode($request->get('data')[1]['story_project_data']), 'css' => $request->get('data')[2]['story_css']]);
        StoryHtmlDumper::write($story);
        return view('manage.story.builder', compact('story'));
    }

    public function uploadImage(Request $request)
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $allowedFileTypes = ['image/png', 'image/jpg', 'image/jpeg'];
            if (!in_array($image->getMimeType(), $allowedFileTypes)) {
                return response()->json(['message' => 'File type not allowed']);
            }
            $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('stories\images', $imageName, 'public');
            $imagePath =  asset('storage/' . $imagePath);
            return response()->json(['image_path' => $imagePath]);
        }
        return response()->json(['message' => 'No image available to upload']);
    }

    public function uploadFile(Request $request)
    {
        if ($request->hasFile('file')) {
            if (!$this->isFileTypeAllowed($request->file('file'))) {
                return response()->json(['message' => 'File type not allowed']);
            }
            $file = $request->file('file');
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('stories/files', $fileName, 'public');
            $filePath =  asset('storage/' . $filePath);
            return response()->json(['file_path' => $filePath]);
        }
        return response()->json(['message' => 'No file available to upload']);
    }

    public function getArtifacts(int $topic_id)
    {
        return Visualization::published()->whereHas('topic',function ($query) use ($topic_id) {
            $query->where('topic_id',$topic_id);
        }
        )->get() ->map(function ($viz) {
            return [
                'id' => $viz->id,
                'title' => $viz->title,
                'description' => $viz->description,
                'type' => $viz->type,
                'icon' => VisualizationTypeEnum::getIcon($viz->type),
                'code' => '<livewire:visualizer vizId="' . $viz->id . '"/>',
            ];
        });
    }

    public function getTopics() {
        return Topic::all()->map (function ($topic) {
            return [
                'id' =>$topic->id,
                'name' =>$topic->name,
            ];
        });
}

    private function isFileTypeAllowed($file)
    {
        $fileExtension = $file->getClientOriginalExtension();
        $allowedFileTypes = ['pdf', 'xls', 'xlsx', 'csv','doc','docx','ppt','pptx'];
        return in_array($fileExtension, $allowedFileTypes);
    }
}
