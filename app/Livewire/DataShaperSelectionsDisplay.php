<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class DataShaperSelectionsDisplay extends Component
{
    public array $dataShaperSelections = [];

    #[On('dataShaperSelectionMade')]
    public function dataShaperSelectionHandler(array $selection)
    {
        $this->dataShaperSelections = $selection;
    }

    public function render()
    {
        return view('livewire.data-shaper-selections-display');
    }
}
