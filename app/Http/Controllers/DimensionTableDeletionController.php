<?php

namespace App\Http\Controllers;

use App\Models\Dimension;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;

class DimensionTableDeletionController extends Controller
{
    public function __invoke(Request $request)
    {
        $dimensionId = $request->get('id');
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:dimensions,id'
        ]);
        if ($validator->fails()) {
            return redirect()->route('manage.dimension.index')->withErrors($validator);
        }
        $dimension = Dimension::find($dimensionId);
        if ($dimension->datasets()->count() > 0) {
            return redirect()->route('manage.dimension.index')->withErrors('Dimension is used in a dataset');
        }

        $exitCode = Artisan::call('data:drop-dimension', [
            'id' => $dimensionId
        ]);
        return redirect()->route('manage.dimension.index')->withMessage('Table created');
    }
}
