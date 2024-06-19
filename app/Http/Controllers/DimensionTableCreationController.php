<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class DimensionTableCreationController extends Controller
{
    public function __invoke(Request $request)
    {
        $dimensionId = $request->get('id');
        $exitCode = Artisan::call('data:create-dimensions', [
            'id' => $dimensionId
        ]);
        return redirect()->route('manage.dimension.index')->withMessage('Table created');
    }
}
