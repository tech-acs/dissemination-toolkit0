<?php

namespace App\Http\Controllers\Guest;

use App\Actions\BuildDatasetAction;
use App\Http\Controllers\Controller;
use App\Models\Dataset;
use Spatie\SimpleExcel\SimpleExcelWriter;

class DatasetDownloadController extends Controller
{
    public function __invoke(Dataset $dataset)
    {
        $datasetRows = (new BuildDatasetAction($dataset))->handle();
        $filename = 'Dataset.xlsx';
        SimpleExcelWriter::streamDownload($filename)
            ->addRows($datasetRows)
            ->toBrowser();
    }
}
