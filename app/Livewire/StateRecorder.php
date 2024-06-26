<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class StateRecorder extends Component
{
    #[On('selectionMade')]
    public function recordSelection()
    {

    }

    #[On('changeOccurred')]
    public function recordChange()
    {

    }

    public function render()
    {
        return view('livewire.state-recorder');
    }
}
