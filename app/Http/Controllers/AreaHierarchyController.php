<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\AreaHierarchyRequest;
use App\Models\AreaHierarchy;

class AreaHierarchyController extends Controller
{
    public function index()
    {
        $records = AreaHierarchy::orderBy('index')->get();
        return view('manage.area-hierarchy.index', compact('records'));
    }

    public function create()
    {
        return view('manage.area-hierarchy.create');
    }

    private function validateZoomRange($request)
    {
        $validator = Validator::make(
            [
                'zoom_start' => $request->integer('zoom_start'),
                'zoom_end' => $request->integer('zoom_end')
            ],
            [
                'zoom_start' => 'integer|lte:zoom_end|min:6',
                'zoom_end' => 'integer|gte:zoom_start|min:6',
            ]
        );
        if ($validator->fails()) {
            throw ValidationException::withMessages(['map_zoom_levels' => 'Map zoom levels must be a valid range']);
        }
    }

    public function store(AreaHierarchyRequest $request)
    {
        $this->validateZoomRange($request);
        $zoomLevels = range($request->integer('zoom_start'), $request->integer('zoom_end'));
        try {
            AreaHierarchy::create([
                'index' => AreaHierarchy::count(),
                'name' => $request->get('name'),
                'zero_pad_length' => $request->get('zero_pad_length'),
                'simplification_tolerance' => $request->get('simplification_tolerance'),
            ]);
        } catch (\Exception $e) {
            return redirect()->route('manage.area-hierarchy.index')->withError('There was a problem creating the area hierarchy');
        }
        return redirect()->route('manage.area-hierarchy.index')->withMessage('Area hierarchy created');
    }

    public function edit(AreaHierarchy $areaHierarchy)
    {
        $areaHierarchy->zoom_start = min($areaHierarchy->map_zoom_levels ?? [6]);
        $areaHierarchy->zoom_end = max($areaHierarchy->map_zoom_levels ?? [6]);
        return view('manage.area-hierarchy.edit', compact('areaHierarchy'));
    }

    public function update(AreaHierarchy $areaHierarchy, AreaHierarchyRequest $request)
    {
        $this->validateZoomRange($request);
        $zoomLevels = range($request->integer('zoom_start'), $request->integer('zoom_end'));
        $areaHierarchy->update($request->merge(['map_zoom_levels' => $zoomLevels])->only(['name', 'zero_pad_length', 'simplification_tolerance', 'map_zoom_levels']));
        return redirect()->route('manage.area-hierarchy.index')->withMessage('Area hierarchy updated');
    }

    public function destroy(AreaHierarchy $areaHierarchy)
    {
        $areaHierarchy->delete();
        return redirect()->route('manage.area-hierarchy.index')->withMessage('Area hierarchy deleted');
    }
}
