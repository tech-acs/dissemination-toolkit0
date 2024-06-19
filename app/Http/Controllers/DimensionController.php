<?php

namespace App\Http\Controllers;

use App\Http\Requests\DimensionRequest;
use App\Models\Dimension;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DimensionController extends Controller
{
    public function index()
    {
        $dimensions = Dimension::orderBy('name')->get();
        $records = $dimensions->map(function ($dimension) {
            $dimension->entries = $dimension->table_exists ? DB::table($dimension->table_name)->count() : 'N/A';
            return $dimension;
        });
        return view('manage.dimension.index', compact('records'));
    }

    public function create()
    {
        $factTables = config('dissemination.fact_tables');
        return view('manage.dimension.create', compact('factTables'));
    }

    public function store(DimensionRequest $request)
    {
        if (! $request->has('for')) {
            $request->merge(['for' => []]);
        }
        $dimension = Dimension::create($request->only(['name', 'description', 'table_name', 'sorting_type', 'for']));
        return redirect()->route('manage.dimension.index')->withMessage('Record created');
    }

    public function edit(Dimension $dimension)
    {
        $factTables = config('dissemination.fact_tables');
        return view('manage.dimension.edit', compact('dimension', 'factTables'));
    }

    public function update(Dimension $dimension, DimensionRequest $request)
    {
        $dimension->update($request->only(['name', 'description', 'table_name', 'sorting_type', 'for']));
        return redirect()->route('manage.dimension.index')->withMessage('Record updated');
    }

    public function destroy(Dimension $dimension)
    {
        if (is_null($dimension->table_created_at)) {
            $dimension->delete();
        }
        return redirect()->route('manage.dimension.index')->withMessage('Record deleted');
    }
}
