<?php

namespace App\Http\Controllers;

use App\Http\Requests\MapRequest;
use App\Jobs\ImportShapefileJob;
use App\Models\Area;
use App\Services\AreaTree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\SmartTableColumn;
use App\Services\SmartTableData;

class AreaController extends Controller
{
    public function index(Request $request)
    {
        $areaCounts = Area::select('level', DB::raw('count(*) AS count'))->groupBy('level')->get()->keyBy('level');
        $hierarchies = (new AreaTree())->hierarchies;
        $summary = collect($hierarchies)->map(function ($levelName, $level) use ($areaCounts) {
            return ($areaCounts[$level]?->count ?? 0) . ' ' . str($levelName)->plural();
        })->join(', ', ' and ');
        view()->share('hierarchies', $hierarchies);

        return (new SmartTableData(Area::query(), $request))
            ->columns([
                SmartTableColumn::make('name')->sortable(),
                SmartTableColumn::make('code')->sortable(),
                SmartTableColumn::make('level')->sortable()
                    ->setBladeTemplate('{{ ucfirst($hierarchies[$row->level] ?? $row->level) }}'),
                SmartTableColumn::make('path'),
                SmartTableColumn::make('geom')->setLabel('Has Map')
                    ->setBladeTemplate('<x-yes-no value="{{ $row->geom }}" />'),
            ])
            ->editable('manage.area.edit')
            ->searchable(['name', 'code'])
            ->sortBy('level')
            ->downloadable()
            ->view('manage.area.index', compact('summary'));
    }

    public function create()
    {
        $levels = (new AreaTree)->hierarchies;
        return view('manage.area.create', ['levels' => array_map(fn ($level) => ucfirst($level), $levels)]);
    }

    public function store(MapRequest $request)
    {
        $level = $request->integer('level');
        $files = $request->file('shapefile');
        $filename = Str::random(40);
        foreach ($files as $file) {
            $filenameWithExt = collect([$filename, $file->getClientOriginalExtension()])->join('.');
            $file->storeAs('/shapefiles', $filenameWithExt, 'imports');
        }
        $shpFile = collect([$filename, 'shp'])->join('.');
        $filePath = Storage::disk('imports')->path('shapefiles/' . $shpFile);

        ImportShapefileJob::dispatch($filePath, $level, auth()->user(), app()->getLocale());

        return redirect()->route('manage.area.index')
            ->withMessage("Importing is in progress. You will be notified when it is complete.");
    }

    public function edit(Area $area)
    {
        return view('manage.area.edit', compact('area'));
    }

    public function update(Area $area, Request $request)
    {
        $area->update($request->only(['name', 'code']));
        return redirect()->route('manage.area.index')
            ->withMessage("The area has been updated");
    }

    public function destroy()
    {
        Area::truncate();
        return redirect()->route('manage.area.index')
            ->withMessage("The areas table has been truncated");
    }
}
