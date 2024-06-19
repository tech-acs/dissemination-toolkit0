<?php

namespace App\Http\Controllers;

use App\Enums\CensusTableTypeEnum;
use App\Http\Requests\CensusTableRequest;
use App\Models\CensusTable;
use App\Models\Indicator;
use App\Models\Tag;
use App\Models\Topic;
use App\Service\StorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CensusTableController extends Controller
{
    protected StorageService $storageService;
    public function __construct(StorageService $storageService)
    {
        $this->storageService = $storageService;
    }
    public function index()
    {
        $records = auth()->user()->censusTables()->orderByDesc('updated_at')->get();
        return view('manage.census-table.index', compact('records'));
    }

    public function create()
    {
        $topics = Topic::all();
        $indicators = Indicator::all();
        $types = CensusTableTypeEnum::getTypes();
        return view('manage.census-table.create', compact('topics', 'indicators', 'types'));
    }

    public function store(CensusTableRequest $request)
    {
        if (!($request->hasFile('file') && $request->file('file')->isValid())) {
            return redirect()->back()->withErrors(['file' => 'File is required']);
        }
        $fileInfo = $this->storageService->uploadCensusFile($request, 'public', 'census-tables');

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
            $fileInfo = $this->storageService->uploadCensusFile($request, 'public', 'census-tables');
            $requestUpdate = array_merge($requestUpdate, $fileInfo);
        }

        $censusTable->update($requestUpdate);

        $censusTable->topics()->sync($request->get('topics'));

        $updatedTags = Tag::prepareForSync($request->get('tags', ''));
        $censusTable->tags()->sync($updatedTags->pluck('id'));

        return redirect()->route('manage.census-table.index', $censusTable)->withMessage('Census Table updated');
    }

    public function destroy(CensusTable $censusTable)
    {
        $censusTable->delete();
        Storage::disk('public')->move($censusTable->file_path, 'archive/' . $censusTable->file_name);
        return redirect()->route('manage.census-table.index');
    }
}
