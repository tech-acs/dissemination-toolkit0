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
use App\Services\QueryBuilder;
use App\Services\Sorter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Stringable;
use Livewire\Attributes\Url;
use Livewire\Component;

class DataShaper extends Component
{
    use TopicTrait, DatasetTrait, IndicatorTrait, GeographyTrait, YearTrait, DimensionTrait, PivotingTrait;

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
        $this->topics = Topic::has('datasets')->pluck('name', 'id')->all();
        $this->dispatch('dataShaperSelectionMade', $this->makeReadableDataParams('reset', ''));

    }

    private function prefillIndicator(?int $id): void
    {
        $indicator = Indicator::find($id);
        if ($indicator) {
            $this->selectedTopic = $indicator->datasets->first()->topics->first()->id;
            $this->updatedSelectedTopic($this->selectedTopic);
            $this->selectedDataset = $indicator->datasets->first()->id;
            $this->updatedSelectedDataset($this->selectedDataset);
            $this->selectedIndicators[] = $indicator->id;
            $this->updatedSelectedIndicators($this->selectedIndicators);
            $this->nextSelection = 'geography';
        }
    }

    private function prefillDataset(?int $id): void
    {
        $dataset = Dataset::find($id);
        if ($dataset) {
            $this->selectedTopic = $dataset->topics->first()->id;
            $this->updatedSelectedTopic($this->selectedTopic);
            $this->selectedDataset = $dataset->id;
            $this->updatedSelectedDataset($this->selectedDataset);
            $this->nextSelection = 'indicator';
        }
    }

    private function makeIndicatorName(): string
    {
        $dataset = Dataset::with('indicators', 'dimensions')->find($this->selectedDataset);
        $indicators = Indicator::findMany($this->selectedIndicators);
        return str($indicators->pluck('name')->join(', ', ' and '))
            ->when(collect($this->selectedDimensions)->isNotEmpty(), function (Stringable $string) use ($dataset) {
                $selectedDimensions = $dataset->dimensions->filter(fn ($dim) => in_array($dim->id, $this->selectedDimensions));
                return $string->append(' by ', $selectedDimensions->pluck('name')->join(', ', ' and '));
            })
            ->lower()->ucfirst()
            ->toString();
    }

    private function makeDataParam(): array
    {
        return [
            'dataset' => $this->selectedDataset,
            'indicators' => $this->selectedIndicators,
            'geographies' => array_filter($this->selectedGeographies, fn ($areasOfLevel) => ! empty($areasOfLevel)),
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
            "dataset" => 2,
            "indicators" => 3,
            "geography" => 4,
            "dimensions" => 5,
        ];
        $this->selections[$field] = $value;
        $this->selections = array_slice($this->selections, 0, $positionLookup[$field]);
        return array_filter($this->selections, fn ($v) => ! empty($v));
    }

    private function setPivotableDimensions(): void
    {
        $this->pivotableDimensions = [];
        if (count($this->selectedIndicators) === 1) {
            $this->pivotingNotPossible = false;
            $dimensions = collect($this->selectedDimensions)
                ->mapWithKeys(fn ($dimensionId) => [$dimensionId => $this->selectedDimensionValues[$dimensionId]])
                ->map(function ($dimensionValueIds, $dimensionId) {
                    $dimension = Dimension::find($dimensionId);
                    return [
                        'id' => $dimensionId,
                        'label' => $dimension->name,
                    ];
                });
            /*->when(count($this->selectedIndicators) > 1, function (Collection $pivotableList) {
                return $pivotableList->prepend(['id' => 3, 'label' => 'Indicator']);
            });*/
            if ($dimensions->isNotEmpty()) {
                $this->pivotableDimensions = [
                    ...$dimensions,
                    [
                        'id' => 0,
                        'label' => 'Geography',
                    ],
                ];
            }
        } else {
            $this->pivotingNotPossible = true;
        }
    }

    public function apply(): void
    {
        $queryParameters = $this->makeDataParam();
        //dump($queryParameters);
        $yearDimensionId = Dimension::firstWhere('table_name', 'year')->id;
        $validator = Validator::make(
            array_filter([
                'selectedGeography' => $queryParameters['geographies'],
                'selectedDimensions' => $queryParameters['dimensions'],
                'pivotColumn' => $queryParameters['pivotColumn'],
                'pivotRow' => $queryParameters['pivotRow'],
                'nestingPivotColumn' => $queryParameters['nestingPivotColumn'],
            ], fn ($v) => ! is_null($v)),
            [
                'selectedGeography' => 'array|min:1',
                'selectedDimensions' => "required_array_keys:$yearDimensionId|min:1",
                'pivotColumn' => 'sometimes|bail|required|different:pivotRow|different:nestingPivotColumn',
                'pivotRow' => 'bail|required_unless:pivotColumn,null|different:nestingPivotColumn'
            ],
            [
                'selectedDimensions' => 'Year is a required dimension and must be selected',
                'selectedGeography.min' => 'You must select geographic areas before you can see results',
                'pivotRow.required_unless' => 'You must also select a pivot row since you have selected a pivot column',
            ]
        );
        if ($validator->stopOnFirstFailure()->fails()) {
            $errors = $validator->errors();
            foreach ($errors->all() as $error) {
                $this->dispatch('notify', content: $error, type: 'error');
            }
        } else {
            $query = new QueryBuilder($queryParameters);
            //dump($queryParameters, $query->toSql());
            $this->dispatch(
                "dataShaperEvent",
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
