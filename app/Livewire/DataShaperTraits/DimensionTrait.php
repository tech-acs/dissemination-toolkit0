<?php

namespace App\Livewire\DataShaperTraits;

use App\Models\Dimension;

trait DimensionTrait {
    public array $dimensions = [];
    public array $selectedDimensions = [];
    public array $selectedDimensionValues = [];

    public function updatedSelectedDimensions($key)
    {
        $this->reset('pivotableDimensions', 'pivotColumn', 'pivotRow', 'nestingPivotColumn');
        $this->setPivotableDimensions();
        $this->nextSelection = 'apply';

        $this->dispatch('selectionMade', $this->makeReadableDataParams('dimensions', Dimension::find($this->selectedDimensions)->pluck('name')->join(', ', ' and ')));
    }
}
