<?php

namespace App\Http\Controllers;

use App\Models\Dimension;
use App\Services\DynamicDimensionModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DimensionEntryController extends Controller
{
    public function index(Dimension $dimension)
    {
        $records = (new DynamicDimensionModel($dimension->table_name))->all();
        return view('manage.dimension.entries.index', compact('dimension', 'records'));
    }

    public function create(Dimension $dimension)
    {
        return view('manage.dimension.entries.create', compact('dimension'));
    }

    public function store(Request $request, Dimension $dimension)
    {
        $result = (new DynamicDimensionModel($dimension->table_name))
            ->create(['name' => $request->get('name'), 'code' => $request->get('code')]);
        return redirect()->route('manage.dimension.entries.index', $dimension)->withMessage('Record created');
    }

    public function edit(Dimension $dimension, $entryId)
    {
        $entry = (new DynamicDimensionModel($dimension->table_name))->find($entryId);
        return view('manage.dimension.entries.edit', compact('dimension', 'entry'));
    }

    public function update(Request $request, Dimension $dimension, $entryId)
    {
        $result = (new DynamicDimensionModel($dimension->table_name, $entryId))
            ->update(['name' => $request->get('name'), 'code' => $request->get('code')]);
        return redirect()->route('manage.dimension.entries.index', $dimension)->withMessage('Record updated');
    }

    public function destroy(Dimension $dimension, $entryId)
    {
        $result = (new DynamicDimensionModel($dimension->table_name, $entryId))->delete();
        return redirect()->route('manage.dimension.entries.index', $dimension)->withMessage('Record deleted');
    }
}
