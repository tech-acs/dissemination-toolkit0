<?php

namespace App\Livewire;

use App\Jobs\ImportAreaSpreadsheetJob;
use App\Models\Area;
use App\Models\Dataset;
use App\Models\Dimension;
use App\Models\Year;
use App\Notifications\TaskCompletedNotification;
use App\Notifications\TaskFailedNotification;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\SimpleExcel\SimpleExcelReader;

class DatasetImporter extends Component
{
    use WithFileUploads;

    public $spreadsheet;
    public bool $fileAccepted = false;
    public Dataset $dataset;
    public Collection $indicators;
    public Collection $dimensions;
    public array $columnHeaders = [];
    public array $columnMapping = [];
    public string $filePath = '';
    public string $message = '';
    const CHUNK_SIZE = 500;
    public string $importError = '';

    private function mappings(): array
    {
        return [
            'indicators' => collect($this->indicators)->mapWithKeys(fn ($indicator) => [$indicator->id  => ''])->all(),
            'dimensions' => collect($this->dimensions)->mapWithKeys(fn ($dimension) => [$dimension->id  => ''])->all(),
            'others' => collect(['geography'])->mapWithKeys(fn ($other) => [$other  => ''])->all(),
        ];
    }

    protected function rules()
    {
        return array_merge(['spreadsheet' => 'required|file|mimes:csv,xlsx'], $this->messages());
    }

    protected function messages()
    {
        return Arr::dot(collect($this->mappings())->mapWithKeys(fn ($group, $groupName) => ["columnMapping.$groupName" => collect($group)->map(fn ($item) => 'required')->all()]));
    }

    public function mount()
    {
        $this->indicators = $this->dataset->indicators;
        $this->dimensions = $this->dataset->dimensions;
        $this->columnMapping = $this->mappings();
    }

    public function updatedSpreadsheet()
    {
        $this->validateOnly('spreadsheet');
        $filename = collect([Str::random(40), $this->spreadsheet->getClientOriginalExtension()])->join('.');
        $this->spreadsheet->storeAs('/spreadsheets', $filename, 'imports');
        $this->filePath = Storage::disk('imports')->path('spreadsheets/' . $filename);
        $this->columnHeaders = SimpleExcelReader::create($this->filePath)->getHeaders();
        $this->fileAccepted = true;
    }

    private function makeLookupTables(): array
    {
        $lookups = [];
        foreach ($this->columnMapping['dimensions'] as $dimensionId => $columnName) {
            $dimension = Dimension::find($dimensionId);
            $lookups[$dimensionId] = [
                'lookup' => DB::table($dimension->table_name)->pluck('id', 'code')->all(),
                'fk' => $dimension->foreign_key,
            ];
        }
        $lookups['geography'] = [
            'lookup' => Area::pluck('id', 'code')->all(),
            'fk' => 'area_id'
        ];
        return $lookups;
    }

    private function lookItUp($key, $dimension, $lookups): array
    {
        $map = $lookups[$dimension];
        return [$map['fk'], $map['lookup'][$key] ?? null];
    }

    public function import()
    {
        $lookups = $this->makeLookupTables();
        //dump($this->columnMapping, $lookups);
        $this->validate();
        //dump($result);

        $this->importError = '';
        try {
            $dataFile = SimpleExcelReader::create($this->filePath);//->formatHeadersUsing(fn($header) => strtolower(trim($header)));
            $rows = $dataFile->getRows();


            /*$dimensionsInCsv = collect($headers)->mapWithKeys(function ($header) use ($locale) {
                return [$header => Dimension::where("name->{$locale}", 'ilike', $header)->first()];
            })->filter();
            $dataset->dimensions()->sync($dimensionsInCsv->pluck('id')->all());

            $lookups = $this->dimensions->map(function ($model) {
                return [
                    'table' => $model->table_name,
                    'fk' => str($model->name)->lower()->snake()->append('_id')->toString(),
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
            ];*/


            $inserted = 0;

            $rows->chunk(self::CHUNK_SIZE)->each(function ($chunk, $chunkIndex) use (&$inserted, $lookups) {

                $entries = [];
                $chunk->each(function(array $row, $rowIndexWithinAChunk) use ($chunkIndex, $inserted, &$entries, $lookups) {

                    $commonForMultipleIndicators = ['dataset_id' => $this->dataset->id];
                    foreach ($this->columnMapping['dimensions'] as $dimensionId => $dimensionColumn) {
                        list($foreignKeyCol, $valueId) = $this->lookItUp($row[$dimensionColumn], $dimensionId, $lookups);
                        $commonForMultipleIndicators[$foreignKeyCol] = $valueId;
                    }
                    foreach ($this->columnMapping['others'] as $dimensionId => $dimensionColumn) {
                        list($foreignKeyCol, $valueId) = $this->lookItUp($row[$dimensionColumn], $dimensionId, $lookups);
                        $commonForMultipleIndicators[$foreignKeyCol] = $valueId;
                    }
                    foreach ($this->columnMapping['indicators'] as $indicatorId => $valueColumn) {
                        $entry = [...$commonForMultipleIndicators];

                        $entry['indicator_id'] = $indicatorId;
                        $entry['value'] = (float)$row[$valueColumn];

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
                        //logger('looping', ['chunk' => $chunkIndex, 'chunk-row' => $rowIndexWithinAChunk, 'i-id' => $indicatorId, 'col' => $valueColumn, 'val' => $row[$valueColumn]]);
                    }

                    /*$entry = [...$common];
                    foreach ($lookups as $col => $lookup) {
                        $query = $lookup['lookup']->clone();
                        $dimension = $query->where('code', 'ilike', trim($row[$col]))->first();
                        $entry[$lookup['fk']] = $dimension->id ?? null;
                    }
                    $entry['value'] = $row['value'];*/

                    //logger('loop', ['entries' => $entries]);
                });
                $result = DB::table($this->dataset->fact_table)->insertOrIgnore($entries);
                $inserted += $result;

            });
            return redirect()->route('manage.dataset.index')->withMessage("$inserted observations imported for dataset");

        } catch (\Exception $exception) {

            logger('Exception: ' . $exception->getMessage());
            //return back()->withErrors(['datafile' => $exception->getMessage()]);
            $this->importError = str($exception->getMessage())->limit(500);
        }
    }

    public function render()
    {
        $this->dataset->loadCount(['dimensions', 'indicators']);
        return view('livewire.dataset-importer');
    }
}
