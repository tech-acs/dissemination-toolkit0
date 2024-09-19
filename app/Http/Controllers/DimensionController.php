<?php

namespace App\Http\Controllers;

use App\Http\Requests\DimensionRequest;
use App\Models\Dimension;
use App\Services\SmartTableColumn;
use App\Services\SmartTableData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DimensionController extends Controller
{
    public function index(Request $request)
    {
        return (new SmartTableData(Dimension::query(), $request))
            ->columns([
                SmartTableColumn::make('name')->sortable(),
                SmartTableColumn::make('table_name')->setLabel('Table Name')->sortable(),
                SmartTableColumn::make('values')->setLabel('Values')
                    ->setBladeTemplate('{{ $row->values_count }}
                                        @if(! $row->is_complete)
                                            <a title="Value set is incomplete (must contain code _T)"><x-icon.exclamation-circle class="-mt-0.5" /></a>
                                        @endif'),
                SmartTableColumn::make('for')->setLabel('Applies to')
                    ->setBladeTemplate('{{ implode(" | ", $row->for) }}'),
            ])
            ->editable('manage.dimension.edit')
            ->searchable(['name', 'table_name'])
            ->sortBy('name')
            //->downloadable()
            ->view('manage.dimension.index');
    }

    public function create()
    {
        $factTables = config('dissemination.fact_tables', []);
        return view('manage.dimension.create', compact('factTables'));
    }

    public function store(DimensionRequest $request)
    {
        if (! $request->has('for')) {
            $request->merge(['for' => []]);
        }
        Dimension::create($request->only(['name', 'description', 'table_name', 'sorting_type', 'for']));
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
