<?php

namespace App\Livewire\DataShaperTraits;

use App\Models\Area;
use App\Models\Dataset;
use App\Models\Dimension;
use App\Models\Indicator;
use App\Services\AreaTree;

trait IndicatorTrait {
    public array $indicators = [];
    public int $selectedIndicator = 0;

    public function updatedSelectedIndicator(int $indicatorId): void
    {
        $this->reset('selectedGeographyLevels', 'geographyLevels', 'geographies', 'selectedGeographies',
            'years', 'selectedYears', 'dimensions', 'selectedDimensions', 'selectedDimensionValues', 'pivotableDimensions', 'pivotColumn', 'pivotRow', 'nestingPivotColumn');

        $indicator = Indicator::find($indicatorId);
        $dataset = Dataset::find($this->selectedDataset);
        $allLevels = (new AreaTree())->hierarchies;
        $this->geographyLevels = $dataset ? array_slice($allLevels, 0,$dataset->max_area_level + 1) : $allLevels;

        foreach($this->geographyLevels as $level => $levelName) {
            $areas = Area::ofLevel($level)->select(['id', 'name', 'path'])
                ->get()
                ->map(function ($area) {
                    $area->group = str($area->path)
                        ->beforeLast('.')
                        ->value();
                    return $area;
                })
                ->groupBy("group")
                ->all();
            $this->geographies[$level] = $areas;
            $this->selectedGeographies[$level] = [];
        }

        $this->dimensions = $dataset->dimensions->map(function ($dimension) {
            return [
                'id' => $dimension->id,
                'label' => $dimension->name,
                'values' => $dimension->values()->map(fn ($v) => ['id' => $v->id, 'name' => $v->name])->all(),
            ];
        })->all();

        //$this->selectedDimensions = [Dimension::where('table_name', 'year')->first()->id];

        $this->selectedDimensionValues = collect($this->dimensions)
            ->keyBy('id')
            ->map(fn ($dimension) => [])
            ->all();

        $this->setPivotableDimensions();

        $this->nextSelection = 'geography';

        $this->dispatch('dataShaperSelectionMade', $this->makeReadableDataParams('indicator', $indicator->name));
    }
}
