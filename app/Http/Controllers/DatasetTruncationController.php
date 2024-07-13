<?php

namespace App\Http\Controllers;

use App\Models\Dataset;
use Illuminate\Support\Facades\DB;

class DatasetTruncationController extends Controller
{
    public function __invoke(Dataset $dataset)
    {
        //$dataset->years()->detach();
        //$dataset->dimensions()->detach();
        DB::table($dataset->fact_table)
            ->where('dataset_id', $dataset->id)
            ->delete();
        return redirect()->route('manage.dataset.index')->withMessage('Dataset emptied');
    }
}
