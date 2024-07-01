<?php

namespace App\Livewire;

use App\Livewire\DataShaperTraits\DatasetTrait;
use App\Livewire\DataShaperTraits\DimensionTrait;
use App\Livewire\DataShaperTraits\GeographyTrait;
use App\Livewire\DataShaperTraits\IndicatorTrait;
use App\Livewire\DataShaperTraits\PivotingTrait;
use App\Livewire\DataShaperTraits\TopicTrait;
use App\Livewire\DataShaperTraits\YearTrait;
use App\Models\Dataset;
use App\Models\Dimension;
use App\Models\Indicator;
use App\Models\Topic;
use App\Services\DataParam;
use App\Services\QueryBuilder;
use App\Services\Sorter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Stringable;
use Livewire\Attributes\Url;
use Livewire\Component;

class DataShaper extends Component
{
    use TopicTrait, IndicatorTrait, DatasetTrait, GeographyTrait, YearTrait, DimensionTrait, PivotingTrait;

    #[Url]
    public int|null $prefillIndicatorId = 0;
    #[Url]
    public int|null $prefillDatasetId = 0;
    public string $nextSelection = 'topic';
    public array $selections = [];

    public function mount()
    {
        $this->prefillIndicator($this->prefillIndicatorId);
        $this->prefillDataset($this->prefillDatasetId);
    }

    public function resetFilter(): void
    {
        $this->reset();
        $this->topics = Topic::has('indicators')->pluck('name', 'id')->all();
        $this->dispatch('dataShaperSelectionMade', $this->makeReadableDataParams('reset', ''));

    }

    private function prefillIndicator(?int $id): void
    {
        $indicator = Indicator::find($id);
        if ($indicator) {
            $this->selectedTopic = $indicator->topic->id;
            $this->updatedSelectedTopic($this->selectedTopic);
            $this->selectedIndicator = $indicator->id;
            $this->updatedSelectedIndicator($this->selectedIndicator);
            $this->nextSelection = 'dataset';
        }
    }

    private function prefillDataset(?int $id): void
    {
        $dataset = Dataset::find($id);
        if ($dataset) {
            $this->selectedTopic = $dataset->indicator->topic->id;
            $this->updatedSelectedTopic($this->selectedTopic);
            $this->selectedIndicator = $dataset->indicator->id;
            $this->updatedSelectedIndicator($this->selectedIndicator);
            $this->selectedDataset = $dataset->id;
            $this->updatedSelectedDataset($this->selectedDataset);
            $this->nextSelection = 'dimension';
        }
    }

    private function makeIndicatorName(): string
    {
        $dataset = Dataset::with('indicator', 'dimensions')->find($this->selectedDataset);
        return str($dataset->indicator->name)
            ->when(collect($this->selectedDimensions)->isNotEmpty(), function (Stringable $string) use ($dataset) {
                $selectedDimensions = $dataset->dimensions->filter(fn ($dim) => in_array($dim->id, $this->selectedDimensions));
                return $string->append(' by ', $selectedDimensions->pluck('name')->join(', ', ' and '));
            })
            ->lower()->ucfirst()
            ->toString();
    }

    private function makeDataParam(): array
    {
        /*return new DataParam(
            dataset: $this->selectedDataset,
            geographies: [$this->selectedGeographyLevel => $this->selectedGeographies],
            //fetchGeographicalChildren: $this->showFetchGeographicalChildren()?$this->fetchGeographicalChildren:false,
            years: $this->selectedYears,
            dimensions: collect($this->selectedDimensions)
                ->mapWithKeys(fn ($dimensionId) => [$dimensionId => $this->selectedDimensionValues[$dimensionId]])
                ->all(),
            pivotColumn: $this->pivotColumn,
            pivotRow: $this->pivotRow,
            nestingPivotColumn: $this->nestingPivotColumn,
        );*/

        return [
            'dataset' => $this->selectedDataset,
            'geographies' => [$this->selectedGeographyLevel => $this->selectedGeographies],
            'years' => $this->selectedYears,
            'dimensions' => collect($this->selectedDimensions)
                ->mapWithKeys(fn ($dimensionId) => [$dimensionId => $this->selectedDimensionValues[$dimensionId]])
                ->all(),
            'pivotColumn' => $this->pivotColumn,
            'pivotRow' => $this->pivotRow,
            'nestingPivotColumn' => $this->nestingPivotColumn,
        ];
    }

    private function makeReadableDataParams(string $field, string $value): array
    {
        $positionLookup = [
            "reset" => 0,
            "topic" => 1,
            "indicator" => 2,
            "dataset" => 3,
            "geography" => 4,
            "years" => 5,
            "dimensions" => 6,
        ];
        $this->selections[$field] = $value;
        $this->selections = array_slice($this->selections, 0, $positionLookup[$field]);
        return array_filter($this->selections, fn ($v) => ! empty($v));
    }

    private function setPivotableDimensions(): void
    {
        $this->pivotableDimensions = [];
        $dimensions = collect($this->selectedDimensions)
            ->mapWithKeys(fn ($dimensionId) => [$dimensionId => $this->selectedDimensionValues[$dimensionId]])
            ->map(function ($dimensionValueIds, $dimensionId) {
                $dimension = Dimension::find($dimensionId);
                return [
                    'id' => $dimensionId,
                    'label' => $dimension->name,
                ];
            });
        if ($dimensions->isNotEmpty()) {
            $this->pivotableDimensions = [
                ...$dimensions,
                [
                    'id' => 0,
                    'label' => 'Geography',
                ],
            ];
        }
        /*$this->pivotableDimensions = [
            ...$dimensions,
            [
                'id' => 0,
                'label' => 'Geography',
            ],
            [
                'id' => -1,
                'label' => 'Year',
            ],
        ];*/
    }

    public function apply(): void
    {
        $validator = Validator::make(
            array_filter([
                'selectedDataset' => $this->selectedDataset,
                'pivotColumn' => $this->pivotColumn,
                'pivotRow' => $this->pivotRow,
                'nestingPivotColumn' => $this->nestingPivotColumn,
            ], fn ($v) => ! is_null($v)),
            [
                'selectedDataset' => 'integer|min:1',
                'pivotColumn' => 'sometimes|bail|required|different:pivotRow|different:nestingPivotColumn',
                'pivotRow' => 'bail|required_unless:pivotColumn,null|different:nestingPivotColumn'
            ],
            [
                'selectedDataset.min' => 'You must select a dataset before you can see results',
                'pivotRow.required_unless' => 'You must also select a pivot row since you have selected a pivot column',
            ]
        );
        if ($validator->fails()) {
            $errors = $validator->errors();
            foreach ($errors->all() as $error) {
                $this->dispatch('notify', content: $error, type: 'error');
            }
        } else {
            $queryParameters = $this->makeDataParam();
            $query = new QueryBuilder($queryParameters);
            $this->dispatch(
                "changeOccurred",
                rawData: Sorter::sort($query->get()),
                indicatorName: $this->makeIndicatorName(),
                dataParams: $queryParameters
            );
        }
        $this->nextSelection = '';
    }

    public function render()
    {
        return view('livewire.data-shaper');
    }
}
