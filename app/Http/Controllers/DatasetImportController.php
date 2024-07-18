<?php

namespace App\Http\Controllers;

use App\Http\Requests\DatasetImportRequest;
use App\Models\Dataset;
use App\Models\Dimension;
use App\Models\Year;
use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;
use Spatie\SimpleExcel\SimpleExcelReader;
use App\Models\Area;

class DatasetImportController extends Controller
{
    const CHUNK_SIZE = 1000;

    public function create(Dataset $dataset)
    {
        return view('manage.dataset.import.create', compact('dataset'));
    }

    public function store(Dataset $dataset, DatasetImportRequest $request)
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
                    if (count(array_intersect($value, ['geography', 'year', 'value'])) < 3) {
                        $fail('The data file is missing some required columns');
                    }
                },
            ]],
        );
        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        try {
            $rows = $dataFile->getRows();
            $locale = app()->getLocale();
            $dimensionsInCsv = collect($headers)->mapWithKeys(function ($header) use ($locale) {
                return [$header => Dimension::where("name->{$locale}", 'ilike', $header)->first()];
            })->filter();
            $dataset->dimensions()->sync($dimensionsInCsv->pluck('id')->all());

            $lookups = $dimensionsInCsv->map(function ($model, $key) {
                return [
                    'table' => $model->table_name,
                    'fk' => str($key)->lower()->snake()->append('_id')->toString(),
                    'lookup' => DB::table($model->table_name),
                ];
            });
            $lookups['geography'] = [
                'table' => 'areas',
                'fk' => 'area_id',
                'lookup' => Area::query(),
            ];
            $lookups['year'] = [
                'table' => 'years',
                'fk' => 'year_id',
                'lookup' => Year::query(),
            ];

            $inserted = 0;
            $rows->chunk(self::CHUNK_SIZE)->each(function ($chunk, $chunkIndex) use (&$inserted, $dataset, $lookups) {
                $entries = [];
                $common = [
                    'indicator_id' => $dataset->indicator_id,
                    'dataset_id' => $dataset->id,
                ];
                $chunk->each(function(array $row, $rowIndexWithinAChunk) use ($chunkIndex, $inserted, &$entries, $lookups, $common) {
                    // Invert the loop so that we iterate on the $lookup->keys()
                    $entry = [...$common];
                    foreach ($lookups as $col => $lookup) {
                        $query = $lookup['lookup']->clone();
                        $dimension = $query->where('code', 'ilike', trim($row[$col]))->first();
                        $entry[$lookup['fk']] = $dimension->id ?? null;
                    }
                    $entry['value'] = $row['value'];
                    //dump($row, $entry);
                    if (in_array(null, $entry, true)) {
                        $lineNo = self::CHUNK_SIZE * $chunkIndex + $rowIndexWithinAChunk + 2;
                        throw ValidationException::withMessages([
                            'datafile' => "The data seems to contain invalid data at the following row (around line $lineNo).<br><br>" .
                                implode(', ', $row) .
                                "<br><br>" .
                                "$inserted rows were imported. Please correct and reimport.<br>Remember to empty the dataset first to avoid duplicates."
                        ]);
                    } else {
                        array_push($entries, $entry);
                    }
                });
                $result = DB::table($dataset->fact_table)->insertOrIgnore($entries);
                $inserted += $result;
            });
            return redirect()->route('manage.dataset.index')->withMessage("$inserted observations imported for dataset");
        } catch (\Exception $exception) {
            return redirect()->back()->withErrors(new MessageBag(['datafile' => $exception->getMessage()]));
        }
    }
}
