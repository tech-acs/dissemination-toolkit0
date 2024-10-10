<?php

namespace App\Http\Controllers;

use App\Http\Requests\DimensionValuesImportRequest;
use App\Models\Dimension;
use App\Services\DynamicDimensionModel;
use Closure;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Spatie\SimpleExcel\SimpleExcelReader;
use Illuminate\Http\Request;

class DimensionValueImportController extends Controller
{
    public function create(Dimension $dimension)
    {
        return view('manage.dimension.values.import.create', compact('dimension'));
    }

    public function store(Dimension $dimension, DimensionValuesImportRequest $request)
    {
        $file = $request->file('datafile')->store('data');
        $filepath = storage_path("app/$file");
        $dataFile = SimpleExcelReader::create($filepath)->formatHeadersUsing(fn($header) => strtolower(trim($header)));
        $headers = $dataFile->getHeaders();

        $validator = Validator::make(
            ['datafile' => $headers],
            ['datafile' => [
                'array',
                function (string $attribute, mixed $value, Closure $fail) {
                    if (count(array_intersect($value, ['code', 'label'])) < 2) {
                        $fail('The data file is missing some required columns');
                    }
                },
            ]],
        );
        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $inserted = 0; $errors = 0; $totalRows = 0;
        $dataFile->getRows()
            ->each(function(array $row) use (&$errors, &$inserted, &$totalRows, $dimension) {
                $totalRows++;
                try {
                    (new DynamicDimensionModel($dimension->table_name))
                        ->create(['name' => $row['label'], 'code' => $row['code']]);
                    $inserted++;
                } catch (\Exception $e) {
                    $errors++;
                }
        });

        return redirect()->route('manage.dimension.values.index', $dimension)
            ->withMessage("Total rows in file: $totalRows, inserted rows: $inserted, error rows: $errors");

    }
}
