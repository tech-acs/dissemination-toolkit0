<?php

namespace App\Http\Controllers;

use App\Models\Dimension;
use App\Services\DynamicDimensionModel;
use Illuminate\Http\Request;

class DimensionValueController extends Controller
{
    public function index(Dimension $dimension)
    {
        $records = collect();
        if ($dimension->table_exists) {
            $records = (new DynamicDimensionModel($dimension->table_name))->all();
        }
        return view('manage.dimension.values.index', compact('dimension', 'records'));
    }

    public function create(Dimension $dimension)
    {
        return view('manage.dimension.values.create', compact('dimension'));
    }

    public function store(Request $request, Dimension $dimension)
    {
        $result = (new DynamicDimensionModel($dimension->table_name))
            ->create(['name' => $request->get('name'), 'code' => $request->get('code')]);
        return redirect()->route('manage.dimension.values.index', $dimension)->withMessage('Record created');
    }

    public function edit(Dimension $dimension, $entryId)
    {
        $entry = (new DynamicDimensionModel($dimension->table_name))->find($entryId);
        return view('manage.dimension.values.edit', compact('dimension', 'entry'));
    }

    public function update(Request $request, Dimension $dimension, $entryId)
    {
        $result = (new DynamicDimensionModel($dimension->table_name, $entryId))
            ->update(['name' => $request->get('name'), 'code' => $request->get('code')]);
        return redirect()->route('manage.dimension.values.index', $dimension)->withMessage('Record updated');
    }

    public function destroy(Dimension $dimension, $entryId)
    {
        $result = (new DynamicDimensionModel($dimension->table_name, $entryId))->delete();
        return redirect()->route('manage.dimension.values.index', $dimension)->withMessage('Record deleted');
    }
}
