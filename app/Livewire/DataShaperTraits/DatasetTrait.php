<?php

namespace App\Livewire\DataShaperTraits;

use App\Models\Dataset;
use App\Models\Area;
use App\Services\AreaTree;

trait DatasetTrait
{
    public array $datasets = [];
    public int $selectedDataset = 0;

    public function updatedSelectedDataset($datasetId): void
    {
        $this->reset(
            'selectedGeographyLevel',
            'selectedGeographies',
            'geographyLevels',
            'geographies',
            'years',
            'selectedYears',
            'dimensions',
            'selectedDimensions',
            'selectedDimensionValues',
            'pivotableDimensions',
            'pivotColumn',
            'pivotRow',
            'nestingPivotColumn'
        );

        $dataset = Dataset::with('dimensions')->find($datasetId);

        $allLevels = (new AreaTree())->hierarchies;
        $this->geographyLevels = $dataset ? array_slice($allLevels, 0,$dataset->max_area_level + 1) : $allLevels;
        /*$this->max_area_level = $dataset->max_area_level;
        $this->geographyLevels = $dataset ? array_slice($allLevels, 0, $dataset->max_area_level + 1) : $allLevels;*/
        $this->geographies = Area::ofLevel(0)->pluck('name', 'id')->all();

        $this->years = $dataset->years->pluck('name', 'id')->all();

        $this->dimensions = $dataset->dimensions->map(function ($dimension) {
            return [
                'id' => $dimension->id,
                'label' => $dimension->name,
                'values' => $dimension->values()->map(fn ($v) => ['id' => $v->id, 'name' => $v->name])->all(),
            ];
        })->all();

        $this->selectedDimensionValues = collect($this->dimensions)
            ->keyBy('id')
            ->map(fn ($dimension) => [])
            ->all();
        $this->nextSelection = 'geography';

        $this->dispatch('dataShaperSelectionMade', $this->makeReadableDataParams('dataset', $dataset->name));
    }
}
