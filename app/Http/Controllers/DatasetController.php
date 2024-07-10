<?php

namespace App\Http\Controllers;

use App\Http\Requests\DatasetRequest;
use App\Models\Dataset;
use App\Models\Dimension;
use App\Models\Indicator;
use App\Models\Topic;
use App\Models\Year;
use App\Services\AreaTree;
use Illuminate\Http\Request;

class DatasetController extends Controller
{
    public function index()
    {
        $records = Dataset::with('indicators', 'dimensions')->orderByDesc('updated_at')->get();
        return view('manage.dataset.index', compact('records'));
    }

    public function create()
    {
        $indicators = Indicator::orderBy('name')->get();
        $dimensions = Dimension::orderBy('name')->get();
        $topics = Topic::all();
        $factTables = config('dissemination.fact_tables');
        $areaLevels = (new AreaTree())->hierarchies;
        //$years = Year::pluck('name', 'id');
        $dataset = new Dataset();
        return view('manage.dataset.create', compact('dataset', 'indicators', 'dimensions', 'factTables', 'areaLevels', 'topics'));
    }

    public function store(DatasetRequest $request)
    {
        $dataset = Dataset::create($request->only(['fact_table', 'max_area_level', 'name', 'description', 'topic_id']));
        $dataset->indicators()->sync($request->indicators);
        $dataset->dimensions()->sync($request->dimensions);
        //$dataset->years()->sync($request->years);
        return redirect()->route('manage.dataset.index')->withMessage('Record created');
    }

    public function edit(Dataset $dataset)
    {
        $indicators = Indicator::orderBy('name')->get();
        $dimensions = Dimension::orderBy('name')->get();
        $topics = Topic::all();
        $factTables = config('dissemination.fact_tables');
        $areaLevels = (new AreaTree())->hierarchies;
        //$years = Year::pluck('name', 'id');
        return view('manage.dataset.edit', compact('dataset', 'indicators', 'dimensions', 'factTables', 'areaLevels', 'topics'));
    }

    public function update(Dataset $dataset, DatasetRequest $request)
    {
        $dataset->update($request->only(['fact_table', 'max_area_level', 'name', 'description', 'topic_id']));
        $dataset->indicators()->sync($request->indicators);
        $dataset->dimensions()->sync($request->dimensions);
        //$dataset->years()->sync($request->years);
        return redirect()->route('manage.dataset.index')->withMessage('Record updated');
    }

    public function destroy(Dataset $dataset)
    {
        $warning = "The dataset contains data and therefore should not be deleted.
                    If you want to remove the dataset along with the data and other references, visit this url: " . url()->route('manage.dataset.remove', $dataset);
        if ($dataset->observations() > 0) {
            return redirect()->route('manage.dataset.index')->withErrors($warning);
        }
        $dataset->delete();
        return redirect()->route('manage.dataset.index')->withMessage('Record deleted');
    }
}
