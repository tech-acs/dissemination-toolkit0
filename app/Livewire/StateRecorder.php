<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class StateRecorder extends Component
{
    #[On('changeOccurred')]
    public function recordChange(array $rawData, string $indicatorName, array $dataParams)
    {
        session()->put('step1.data', $rawData);
        session()->put('step1.indicatorName', $indicatorName);
        session()->put('step1.dataParams', $dataParams);
    }

    public function render()
    {
        return view('livewire.state-recorder');
    }
}
