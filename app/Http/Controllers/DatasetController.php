<?php

namespace App\Http\Controllers;

use App\Http\Requests\DatasetRequest;
use App\Models\Dataset;
use App\Models\Indicator;
use App\Models\Year;
use App\Services\AreaTree;
use Illuminate\Http\Request;

class DatasetController extends Controller
{
    public function index()
    {
        $records = Dataset::with('indicator', 'dimensions')->orderByDesc('updated_at')->get();
        return view('manage.dataset.index', compact('records'));
    }

    public function create()
    {
        $indicators = Indicator::all();
        $factTables = config('dissemination.fact_tables');
        $areaLevels = (new AreaTree())->hierarchies;
        $years = Year::pluck('name', 'id');
        return view('manage.dataset.create', compact('indicators', 'factTables', 'areaLevels', 'years'));
    }

    public function store(DatasetRequest $request)
    {
        $dataset = Dataset::create($request->only(['indicator_id', 'fact_table', 'max_area_level', 'name', 'description']));
        $dataset->years()->sync($request->years);
        return redirect()->route('manage.dataset.index')->withMessage('Record created');
    }

    public function edit(Dataset $dataset)
    {
        $indicators = Indicator::all();
        $factTables = config('dissemination.fact_tables');
        $areaLevels = (new AreaTree())->hierarchies;
        $years = Year::pluck('name', 'id');
        return view('manage.dataset.edit', compact('dataset', 'indicators', 'factTables', 'areaLevels', 'years'));
    }

    public function update(Dataset $dataset, Request $request)
    {
        $dataset->update($request->only(['indicator_id', 'fact_table', 'max_area_level', 'name', 'description']));
        $dataset->years()->sync($request->years);
        return redirect()->route('manage.dataset.index')->withMessage('Record updated');
    }

    public function destroy(Dataset $dataset)
    {
        $warning = "The dataset contains data and therefore should not be deleted.
                    If you want to remove the dataset along with the data and other references, visit this url: " . url()->route('manage.dataset.remove', $dataset);
        if ($dataset->observations() > 0) {
            return redirect()->route('manage.dataset.index')
                ->withErrors($warning);
        }
        $dataset->delete();
        return redirect()->route('manage.dataset.index')->withMessage('Record deleted');
    }
}
