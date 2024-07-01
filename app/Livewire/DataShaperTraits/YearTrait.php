<?php

namespace App\Livewire\DataShaperTraits;

use App\Models\Dataset;
use App\Models\Year;

trait YearTrait {
    public array $years = [];
    public array $selectedYears = [];

    public function updatedSelectedYears($yearId): void
    {
        $this->nextSelection = 'dimension';

        $this->dispatch('dataShaperSelectionMade', $this->makeReadableDataParams('years', Year::find($this->selectedYears)->pluck('name')->join(', ', ' and ')));
    }
}
