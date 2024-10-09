<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use Illuminate\Http\Request;

class DatasetController extends Controller
{
    public function index(Request $request)
    {
        $records = Dataset::get()->mapWithKeys(fn($dataset) => [ $dataset->id => $dataset->info() ]);
        return view('guest.dataset.index', compact('records'));
    }
}
