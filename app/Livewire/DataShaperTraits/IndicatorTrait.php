<?php

namespace App\Livewire\DataShaperTraits;

use App\Models\Area;
use App\Models\Dataset;
use App\Models\Indicator;
use App\Services\AreaTree;

trait IndicatorTrait {
    public array $indicators = [];
    public array $selectedIndicators = [];

    public function updatedSelectedIndicators($indicatorIds): void
    {
        $this->reset('selectedGeographyLevels', 'geographyLevels', 'geographies', 'selectedGeographies',
            'years', 'selectedYears', 'dimensions', 'selectedDimensions', 'selectedDimensionValues', 'pivotableDimensions', 'pivotColumn', 'pivotRow', 'nestingPivotColumn');

        $indicators = Indicator::findMany($indicatorIds);
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

        $this->dimensions = $dataset->dimensions->map(function ($dimension) use ($dataset) {
            $values = ($dimension->name == 'Year') ?
                $dataset->availableValuesForDimension($dimension)->map(fn ($v) => ['id' => $v->id, 'name' => $v->name])->all() :
                $dimension->values()->map(fn ($v) => ['id' => $v->id, 'name' => $v->name])->all();
            return [
                'id' => $dimension->id,
                'label' => $dimension->name,
                'values' => $values,
            ];
        })->all();

        $this->selectedDimensionValues = collect($this->dimensions)
            ->keyBy('id')
            ->map(fn ($dimension) => [])
            ->all();

        $this->setPivotableDimensions();

        $this->nextSelection = 'geography';

        $this->dispatch('dataShaperSelectionMade', $this->makeReadableDataParams('indicators', $indicators->pluck('name')->join(', ', ' and ')));
    }
}
