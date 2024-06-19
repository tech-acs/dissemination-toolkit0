<?php

namespace App\Livewire\DataShaperTraits;

use App\Models\Indicator;

trait IndicatorTrait {
    public array $indicators = [];
    public int $selectedIndicator = 0;

    public function updatedSelectedIndicator(int $indicatorId): void
    {
        $this->reset('selectedDataset', 'selectedGeographyLevel', 'geographyLevels', 'geographies', 'selectedGeographies',
            'years', 'selectedYears', 'dimensions', 'selectedDimensions', 'selectedDimensionValues', 'pivotableDimensions', 'pivotColumn', 'pivotRow', 'nestingPivotColumn');
        $indicator = Indicator::with('datasets')->find($indicatorId);
        $this->datasets = $indicator->datasets->all();
        $this->nextSelection = 'dataset';

        $this->dispatch('selectionMade', $this->makeReadableDataParams('indicator', $indicator->name));
    }
}
