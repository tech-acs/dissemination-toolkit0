<?php

namespace App\Http\Controllers;

use App\Enums\CensusTableTypeEnum;
use App\Http\Requests\CensusTableRequest;
use App\Models\CensusTable;
use App\Models\Indicator;
use App\Models\Tag;
use App\Models\Topic;
use App\Services\SmartTableColumn;
use App\Services\SmartTableData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CensusTableController extends Controller
{
    public function uploadCensusFile($request, string $disk, string $directory): array
    {
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            if ($request->has('file_path')) {
                Storage::disk($disk)->delete($request->get('file_path'));
            }
        }
        $file_path = $request->file('file')->store($directory, $disk);

        return [
            'file_path' => $file_path,
            'file_name' => $request->file('file')->getClientOriginalName(),
            'file_size' => $request->file('file')->getSize(),
            'file_type' => $request->file('file')->getClientOriginalExtension(),
        ];
    }

    public function index(Request $request)
    {
        return (new SmartTableData(auth()->user()->censusTables(), $request))
            ->columns([
                SmartTableColumn::make('title')->sortable()->tdClasses('w-1/3'),
                SmartTableColumn::make('type')->sortable()
                    ->setBladeTemplate('<x-dataset-type-badge :text="$row->dataset_type" class="{{\App\Enums\CensusTableTypeEnum::getTypeClass($row->dataset_type)}}"/>'),
                SmartTableColumn::make('publisher')->sortable(),
                SmartTableColumn::make('author')->sortable()
                    ->setBladeTemplate('{{ $row->user->name }}'),
                SmartTableColumn::make('published')
                    ->setBladeTemplate('<x-yes-no value="{{$row->published}}"/>'),
                SmartTableColumn::make('updated')
                    ->setBladeTemplate('{{ $row->updated_at->diffForHumans() }}'),
            ])
            ->showable('census-table.show')
            ->editable('manage.census-table.edit')
            ->deletable('manage.census-table.destroy')
            ->searchable(['title', 'type'])
            ->sortBy('title')
            ->downloadable()
            ->view('manage.census-table.index');
    }

    public function create()
    {
        $topics = Topic::pluck('name', 'id');
        $indicators = Indicator::all();
        $types = CensusTableTypeEnum::getTypes();
        $censusTable = (new CensusTable());
        return view('manage.census-table.create', compact('topics', 'indicators', 'types', 'censusTable'));
    }

    public function store(CensusTableRequest $request)
    {
        if (!($request->hasFile('file') && $request->file('file')->isValid())) {
            return redirect()->back()->withErrors(['file' => 'File is required']);
        }
        $fileInfo = $this->uploadCensusFile($request, 'public', 'census-tables');

        $request->merge($fileInfo);
        $request->merge(['user_id' => Auth::id()]);

        $censusTable = CensusTable::create($request->all());
        $censusTable->topics()->sync($request->get('topics'));

        $updatedTags = Tag::prepareForSync($request->get('tags', ''));
        $censusTable->tags()->sync($updatedTags->pluck('id'));
        return redirect()->route('manage.census-table.index', $censusTable)->withMessage('Census Table created');
    }

    public function edit(CensusTable $censusTable)
    {
        $topics = Topic::all();
        $indicators = Indicator::all();
        $types = CensusTableTypeEnum::getTypes();
        $censusTable->load(['topics', 'tags']);
        $selectedTopics = $censusTable->topics->pluck('id')->toArray();
        return view('manage.census-table.edit', compact(
            'censusTable',
            'topics',
            'indicators',
            'selectedTopics',
            'types'
        ));
    }
    public function update(CensusTableRequest $request, CensusTable $censusTable)
    {
        $requestUpdate = $request->only(['title',
            'description',
            'producer',
            'publisher',
            'published_date',
            'published',
            'data_source',
            'comment',
            'dataset_type']);
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $fileInfo = $this->uploadCensusFile($request, 'public', 'census-tables');
            $requestUpdate = array_merge($requestUpdate, $fileInfo);
        }

        $censusTable->update($requestUpdate);

        $censusTable->topics()->sync($request->get('topics'));

        $updatedTags = Tag::prepareForSync($request->get('tags', ''));
        $censusTable->tags()->sync($updatedTags->pluck('id'));

        return redirect()->route('manage.census-table.index', $censusTable)->withMessage('Census table updated');
    }

    public function destroy(CensusTable $censusTable)
    {
        $censusTable->delete();
        Storage::disk('public')->move($censusTable->file_path, 'archive/' . $censusTable->file_name);
        return redirect()->route('manage.census-table.index');
    }
}
