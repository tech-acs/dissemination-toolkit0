<?php

namespace App\Http\Controllers;

use App\Models\Dataset;

class DatasetImportController extends Controller
{
    public function create(Dataset $dataset)
    {
        return view('manage.dataset.import.create', compact('dataset'));
    }
}
