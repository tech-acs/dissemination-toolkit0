<?php

namespace App\Livewire;

use Livewire\Component;

class TableOptionsShaper extends Component
{
    public array $options = [
        'pagination' => false,
        'suppressMovableColumns' => false,
        'unSortIcon' => false,
        'columnHoverHighlight' => false
    ];

    public array $optionLabels = [
        'pagination' => 'Pagination',
        'suppressMovableColumns' => 'Movable columns',
        'unSortIcon' => 'Show unsort icon',
        'columnHoverHighlight' => 'Highlight columns on hover'
    ];

    public function mount()
    {

    }

    public function apply()
    {
        $this->dispatch('tableOptionsShaperEvent', options: $this->options);
    }

    public function render()
    {
        return view('livewire.table-options-shaper');
    }
}
