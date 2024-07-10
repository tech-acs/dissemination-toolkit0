<?php

namespace App\Http\Controllers;

use App\Models\Dataset;
use Illuminate\Support\Facades\DB;

class DatasetRemovalController extends Controller
{
    public function __invoke(Dataset $dataset)
    {
        //$dataset->years()->detach();
        //$dataset->dimensions()->detach();
        DB::table($dataset->fact_table)
            //->where('indicator_id', $dataset->indicator_id)
            ->where('dataset_id', $dataset->id)
            ->delete();
        $dataset->delete();
        return redirect()->route('manage.dataset.index')->withMessage('Record deleted');
    }
}
