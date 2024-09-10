<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Source;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class ConnectionTestController extends Controller
{
    public function __invoke(Source $source): RedirectResponse
    {
        
        $results = $source->test();
        $passesTest = $results->reduce(function ($carry, $item) {
            return $carry && $item['passes'];
        }, true);
        if ($passesTest) {
            return redirect()->route('manage.data-source.index')
                ->withMessage('Connection test successful');
        } else {
            return redirect()->route('manage.data-source.index')
                ->withErrors($results->pluck('message')->filter()->all());
        }
    }
}
